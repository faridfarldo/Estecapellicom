# Estecapelli WordPress Theme Project

## Project Overview
- Custom WordPress theme for estecapelli.com
- Hair transplant clinic website
- English-only build, WPML handles other languages

## Tech Stack
- WordPress (classic theme, not FSE)
- PHP, vanilla JS, CSS
- WPML-ready (all strings translatable)

## Conventions
- Use __() and _e() for all strings
- Mobile-first CSS
- BEM naming for CSS classes

## Folder Structure
estecapelli-theme/
├── style.css (theme header)
├── functions.php
├── header.php
├── footer.php
├── index.php
├── page.php
├── single.php
├── archive.php
├── /template-parts/
├── /assets/ (css, js, images)
├── /inc/ (helper functions)
└── /languages/ 

## Current Phase
Phase 1: Theme skeleton
## Reference
- Visual design reference: https://estecapelli.com/
- We are REBUILDING the look, NOT importing code from there

## Build Order
1. Theme skeleton (style.css, functions.php)
2. Header + Footer
3. Homepage
4. Treatment pages template
5. Doctors/About
6. Contact
7. Blog (index, single, archive)
8. WPML compatibility
9. SEO basics
10. Performance optimization

## Important Notes
- Do NOT copy code from the live site, only replicate visual design
- Every text string must use __() or _e() for translation
- Test mobile-first