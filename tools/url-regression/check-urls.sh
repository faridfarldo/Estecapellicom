#!/usr/bin/env bash
# ===========================================================================
# 404 regression test for estecapelli.com
#
# Guarantees no live URL ever starts returning 404 after a change (WPML setup,
# rewrite changes, theme edits, ...). It does NOT modify the site — only reads.
#
#   ./check-urls.sh snapshot   Record the CURRENT live status of every URL into
#                              baseline.tsv. Run this BEFORE making changes.
#   ./check-urls.sh check      Re-request every URL in baseline.tsv and fail if
#                              any that used to resolve now 404s. Run AFTER.
#
# Env overrides:
#   BASE_URL=https://staging.estecapelli.com ./check-urls.sh check
#   CRAWL=0       skip link discovery (only test urls.txt)
#   MAX_CRAWL=600 cap on discovered URLs
#
# A URL "resolves" if its final status (after redirects) is < 400 and a blog
# article did not collapse to the language's blog landing page.
# A regression = it resolved in the baseline but is now >= 400 (or unreachable).
# ===========================================================================
set -uo pipefail

BASE_URL="${BASE_URL:-https://estecapelli.com}"
BASE_URL="${BASE_URL%/}"
DIR="$(cd "$(dirname "$0")" && pwd)"
URLS_FILE="$DIR/urls.txt"
BASELINE="$DIR/baseline.tsv"
CRAWL="${CRAWL:-1}"
MAX_CRAWL="${MAX_CRAWL:-600}"
UA='Mozilla/5.0 (estecapelli-url-regression-bot)'

# Final HTTP status after following redirects; 000 = unreachable. A synthetic
# 409 marks a blog article that was redirected to its blog landing page: the
# landing returns 200, but the requested content is still unavailable.
http_status() {
  local result status effective language
  result="$(curl -s -o /dev/null -L --max-redirs 10 --connect-timeout 15 --max-time 45 \
       -A "$UA" -w $'%{http_code}\t%{url_effective}' "$1" 2>/dev/null)" || {
    echo "000"
    return
  }
  IFS=$'\t' read -r status effective <<< "$result"

  if [[ "$1" =~ /([a-z]{2})/blog/.+ ]]; then
    language="${BASH_REMATCH[1]}"
    if [ "${effective%/}" = "$BASE_URL/$language/blog" ]; then
      echo "409"
      return
    fi
  fi

  echo "${status:-000}"
}

resolves() { [ "$1" -ge 200 ] 2>/dev/null && [ "$1" -lt 400 ] 2>/dev/null; }

# Expand a content path from urls.txt into its live URL variants.
expand_variants() {
  local p="$1"
  if [ "$p" = "/" ]; then
    printf '%s/en/\n%s/\n' "$BASE_URL" "$BASE_URL"
  else
    printf '%s/en%s\n%s%s\n' "$BASE_URL" "$p" "$BASE_URL" "$p"
  fi
}

seed_urls() {
  [ -f "$URLS_FILE" ] || { echo "missing $URLS_FILE" >&2; exit 2; }
  while IFS= read -r line; do
    line="${line%%#*}"
    line="$(printf '%s' "$line" | tr -d '[:space:]')"
    [ -z "$line" ] && continue
    expand_variants "$line"
  done < "$URLS_FILE"
}

# Discover same-host links from a list of pages (one discovery pass).
crawl_from() {
  while IFS= read -r u; do
    [ -z "$u" ] && continue
    curl -s -L --max-time 45 -A "$UA" "$u" 2>/dev/null \
      | grep -oiE 'href="[^"#?]+"' \
      | sed -E 's/^href="//I; s/"$//' \
      | while IFS= read -r href; do
          case "$href" in
            "$BASE_URL"/*) printf '%s\n' "${href%/}" ;;
            /*)            printf '%s%s\n' "$BASE_URL" "${href%/}" ;;
          esac
        done
  done
}

cmd_snapshot() {
  local tmp; tmp="$(mktemp)"
  seed_urls >> "$tmp"
  if [ "$CRAWL" = "1" ]; then
    echo "Discovering linked URLs (crawl pass)..." >&2
    local disc; disc="$(mktemp)"
    sort -u "$tmp" > "$disc"
    # sort -u fully buffers crawl_from before head reads, so the crawl never
    # gets a broken pipe when head trims to MAX_CRAWL.
    crawl_from < "$disc" 2>/dev/null | sort -u | head -n "$MAX_CRAWL" >> "$tmp"
    rm -f "$disc"
  fi
  sort -u "$tmp" -o "$tmp"

  echo "Snapshotting $(wc -l < "$tmp" | tr -d ' ') URLs at $BASE_URL ..." >&2
  : > "$BASELINE"
  local n=0 ok=0 broke=0
  while IFS= read -r u; do
    local st; st="$(http_status "$u")"
    printf '%s\t%s\n' "$st" "$u" >> "$BASELINE"
    n=$((n+1))
    if resolves "$st"; then ok=$((ok+1)); else broke=$((broke+1)); fi
    printf '\r  %d checked (%d ok, %d non-resolving)   ' "$n" "$ok" "$broke" >&2
  done < "$tmp"
  rm -f "$tmp"
  echo >&2
  echo "Baseline written: $BASELINE ($n URLs, $ok resolving, $broke non-resolving)." >&2
  echo "Commit baseline.tsv so the 'known-good' set is shared." >&2
}

cmd_check() {
  [ -f "$BASELINE" ] || { echo "No baseline. Run: $0 snapshot" >&2; exit 2; }
  local regressions=0 rechecked=0 recovered=0
  echo "Re-checking $(wc -l < "$BASELINE" | tr -d ' ') URLs at $BASE_URL ..." >&2
  echo
  printf '%-7s %-7s  %s\n' "WAS" "NOW" "URL"
  printf '%s\n' "-------------------------------------------------------------"
  while IFS=$'\t' read -r old url; do
    [ -z "${url:-}" ] && continue
    local new; new="$(http_status "$url")"
    rechecked=$((rechecked+1))
    if resolves "$old" && ! resolves "$new"; then
      printf '%-7s %-7s  %s   <-- REGRESSION\n' "$old" "$new" "$url"
      regressions=$((regressions+1))
    elif ! resolves "$old" && resolves "$new"; then
      printf '%-7s %-7s  %s   (recovered)\n' "$old" "$new" "$url"
      recovered=$((recovered+1))
    elif [ "$old" != "$new" ]; then
      printf '%-7s %-7s  %s   (changed)\n' "$old" "$new" "$url"
    fi
  done < "$BASELINE"
  echo
  echo "Re-checked: $rechecked   Recovered: $recovered   Regressions: $regressions"
  if [ "$regressions" -gt 0 ]; then
    echo "FAIL: $regressions URL(s) that used to resolve now 404/error." >&2
    exit 1
  fi
  echo "PASS: no URL that used to resolve is broken."
}

case "${1:-}" in
  snapshot) cmd_snapshot ;;
  check|diff) cmd_check ;;
  *) echo "Usage: $0 {snapshot|check}" >&2; exit 2 ;;
esac
