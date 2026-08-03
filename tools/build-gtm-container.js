/*
 * Generate the Google Tag Manager import file for the theme's measurement layer.
 *
 *   node tools/build-gtm-container.js
 *
 * Writes tools/gtm-container-estecapelli.json — import it into GTM with
 * "Merge / Rename conflicting", never "Overwrite".
 *
 * Generated rather than hand-written because the container needs one Data Layer
 * Variable per parameter and one tag row mapping each: forty-odd near-identical
 * JSON blocks that must agree exactly with the names in assets/js/analytics.js.
 * Kept as a script so re-running it after adding an event is a one-liner, and
 * the parameter list below stays the single place the names are spelled.
 */
const fs = require('fs');
const path = require('path');

/* Page context — pushed once per page, above the container (inc/analytics.php). */
const CONTEXT_PARAMS = [
	'page_type',
	'page_key',
	'page_language',
	'content_group',
	'user_type',
	'treatment_category',
	'treatment_name',
	'doctor_name',
	'blog_category',
	'search_term',
	'not_found_path',
];

/* Event parameters — must match the PARAMS array in assets/js/analytics.js,
   plus the three the consent banner pushes. */
const EVENT_PARAMS = [
	'cta_text', 'cta_location', 'cta_destination',
	'form_location', 'lead_treatment', 'lead_language',
	'error_message',
	'link_location', 'link_url', 'link_text',
	'message_length',
	'analysis_mode', 'photo_step', 'capture_method', 'step_index',
	'norwood_stage', 'graft_estimate', 'zone_count',
	'from_language', 'to_language',
	'story_name', 'video_title', 'video_provider',
	'gallery_name', 'slide_index',
	'faq_question', 'tab_name', 'tab_group',
	'percent',
	'consent_analytics', 'consent_marketing', 'consent_method',
];

/* Every event name analytics.js can push. */
const EVENTS = [
	'cta_click', 'lead_form_open', 'form_start', 'generate_lead', 'form_error',
	'whatsapp_click', 'whatsapp_chat_open', 'whatsapp_chat_send',
	'phone_click', 'email_click',
	'ai_analysis_start', 'ai_analysis_contact', 'ai_photo_captured',
	'ai_analysis_complete', 'ai_analysis_error',
	'hair_lab_select', 'self_assessment_zones',
	'language_switch', 'story_open', 'video_start', 'video_complete',
	'before_after_navigate', 'faq_open', 'tab_select', 'toc_click',
	'scroll_depth', 'consent_update',
];

const ALL_PARAMS = CONTEXT_PARAMS.concat(EVENT_PARAMS);
const FOLDER_ID = '1';

/* Placeholder account/container ids: GTM remaps these to the container you
   import into, so they only have to be present and consistent. */
const base = { accountId: '0', containerId: '0', fingerprint: '0', parentFolderId: FOLDER_ID };

let nextId = 1;

function dataLayerVariable(name) {
	return Object.assign({}, base, {
		variableId: String(nextId++),
		name: 'DLV - ' + name,
		type: 'v',
		parameter: [
			// No default value: an unset parameter must stay undefined so GA4
			// omits it, rather than becoming an empty string in every report.
			{ type: 'BOOLEAN', key: 'setDefaultValue', value: 'false' },
			{ type: 'INTEGER', key: 'dataLayerVersion', value: '2' },
			{ type: 'TEMPLATE', key: 'name', value: name },
		],
	});
}

const variables = ALL_PARAMS.map(dataLayerVariable);

/* The one field that has to be filled in by hand after importing. */
variables.push(Object.assign({}, base, {
	variableId: String(nextId++),
	name: 'GA4 Measurement ID',
	type: 'c',
	parameter: [{ type: 'TEMPLATE', key: 'value', value: 'G-XXXXXXXXXX' }],
	notes: 'Replace with the Measurement ID of the GA4 web data stream (Admin -> Data streams).',
}));

function customEventTrigger(id, name, regex, filter) {
	const trigger = Object.assign({}, base, {
		triggerId: id,
		name: name,
		type: 'CUSTOM_EVENT',
		customEventFilter: [{
			type: 'MATCH_REGEX',
			parameter: [
				{ type: 'TEMPLATE', key: 'arg0', value: '{{_event}}' },
				{ type: 'TEMPLATE', key: 'arg1', value: regex },
			],
		}],
	});
	if (filter) trigger.filter = filter;
	return trigger;
}

const staffFilter = [{
	type: 'EQUALS',
	parameter: [
		{ type: 'TEMPLATE', key: 'arg0', value: '{{DLV - user_type}}' },
		{ type: 'TEMPLATE', key: 'arg1', value: 'staff' },
	],
}];

const triggers = [
	customEventTrigger('1', 'CE - theme events', '^(' + EVENTS.join('|') + ')$'),
	// Blocks the event tag for logged-in staff. A custom-event exception cannot
	// block page_view, which fires on Initialisation — the Google tag needs the
	// page-view exception below adding to it by hand.
	customEventTrigger('2', 'Exception - staff (events)', '.*', staffFilter),
	Object.assign({}, base, {
		triggerId: '3',
		name: 'Exception - staff (page view)',
		type: 'PAGEVIEW',
		filter: staffFilter,
		notes: 'Add manually as an exception on the existing Google tag (GA4 configuration).',
	}),
];

/* One GA4 Event tag covers every event: the event name comes from the built-in
   Event variable, and each parameter maps to its Data Layer Variable. Safe only
   because analytics.js clears unused parameters on every push. */
const tags = [Object.assign({}, base, {
	tagId: '1',
	name: 'GA4 - Theme events',
	type: 'gaawe',
	parameter: [
		{ type: 'BOOLEAN', key: 'sendEcommerceData', value: 'false' },
		{ type: 'TEMPLATE', key: 'eventName', value: '{{_event}}' },
		{
			type: 'LIST',
			key: 'eventSettingsTable',
			list: ALL_PARAMS.map(function (name) {
				return {
					type: 'MAP',
					map: [
						{ type: 'TEMPLATE', key: 'parameter', value: name },
						{ type: 'TEMPLATE', key: 'parameterValue', value: '{{DLV - ' + name + '}}' },
					],
				};
			}),
		},
		{ type: 'TEMPLATE', key: 'measurementIdOverride', value: '{{GA4 Measurement ID}}' },
	],
	firingTriggerId: ['1'],
	blockingTriggerId: ['2'],
	tagFiringOption: 'ONCE_PER_EVENT',
	monitoringMetadata: { type: 'MAP' },
})];

const container = {
	exportFormatVersion: 2,
	exportTime: new Date().toISOString().replace('T', ' ').slice(0, 19),
	containerVersion: {
		path: 'accounts/0/containers/0/versions/0',
		accountId: '0',
		containerId: '0',
		containerVersionId: '0',
		name: 'Estecapelli theme measurement',
		description: 'Variables, trigger and tag for the events pushed by assets/js/analytics.js. See docs/GA4-SETUP.md.',
		container: {
			path: 'accounts/0/containers/0',
			accountId: '0',
			containerId: '0',
			name: 'estecapelli.com',
			publicId: 'GTM-WVPFK35',
			usageContext: ['WEB'],
			fingerprint: '0',
		},
		// Enables {{_event}}, which the tag uses as its event name.
		builtInVariable: [
			{ accountId: '0', containerId: '0', type: 'EVENT', name: 'Event' },
		],
		folder: [{
			accountId: '0',
			containerId: '0',
			folderId: FOLDER_ID,
			name: 'Estecapelli — Theme measurement',
			fingerprint: '0',
		}],
		variable: variables,
		trigger: triggers,
		tag: tags,
		fingerprint: '0',
	},
};

const out = path.join(__dirname, 'gtm-container-estecapelli.json');
fs.writeFileSync(out, JSON.stringify(container, null, 2) + '\n');

console.log('Wrote ' + out);
console.log('  variables: ' + variables.length + ' (' + ALL_PARAMS.length + ' data layer + 1 constant)');
console.log('  triggers:  ' + triggers.length);
console.log('  tags:      ' + tags.length);
console.log('  events covered: ' + EVENTS.length);
