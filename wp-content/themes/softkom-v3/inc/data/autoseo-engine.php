<?php
/**
 * AutoSEO opportunity engine.
 *
 * Provides deterministic scoring and action classification used by the
 * AutoSEO product UI. No external API calls are made here.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clamp an AutoSEO signal to the 0-100 range.
 *
 * @param mixed $value Signal value.
 * @return float
 */
function softkom_v3_autoseo_signal( $value ) {
	$value = is_numeric( $value ) ? (float) $value : 0.0;
	return max( 0.0, min( 100.0, $value ) );
}

/**
 * Calculate a commercial opportunity score.
 *
 * The score rewards traffic potential, buyer intent and conversion value,
 * while reducing priority as implementation effort increases.
 *
 * @param array<string,mixed> $signals Input signals on a 0-100 scale.
 * @return int Score from 0-100.
 */
function softkom_v3_autoseo_opportunity_score( $signals ) {
	$traffic = softkom_v3_autoseo_signal( $signals['traffic_potential'] ?? 0 );
	$intent  = softkom_v3_autoseo_signal( $signals['buyer_intent'] ?? 0 );
	$value   = softkom_v3_autoseo_signal( $signals['conversion_value'] ?? 0 );
	$effort  = softkom_v3_autoseo_signal( $signals['implementation_effort'] ?? 50 );

	// Higher effort reduces priority without making worthwhile work disappear.
	$effort_factor = max( 0.20, ( 100.0 - $effort ) / 100.0 );

	// Geometric-style score keeps one weak commercial dimension from being
	// hidden by two strong ones.
	$commercial = pow(
		max( 0.01, $traffic / 100.0 )
		* max( 0.01, $intent / 100.0 )
		* max( 0.01, $value / 100.0 ),
		1.0 / 3.0
	);

	return (int) round( min( 100.0, 100.0 * $commercial * $effort_factor ) );
}

/**
 * Select the most appropriate next action for an SEO opportunity.
 *
 * @param array<string,mixed> $signals Boolean/numeric opportunity signals.
 * @return string Action id.
 */
function softkom_v3_autoseo_recommended_action( $signals ) {
	if ( ! empty( $signals['technical_blocker'] ) ) {
		return 'technical-fix';
	}
	if ( ! empty( $signals['ranking_page'] ) && ! empty( $signals['low_ctr'] ) ) {
		return 'improve-ranking-page';
	}
	if ( ! empty( $signals['ranking_page'] ) && ! empty( $signals['content_decay'] ) ) {
		return 'refresh-existing-page';
	}
	if ( ! empty( $signals['service_intent_gap'] ) ) {
		return 'create-or-improve-service-page';
	}
	if ( ! empty( $signals['comparison_intent_gap'] ) ) {
		return 'create-comparison-page';
	}
	if ( ! empty( $signals['local_intent_gap'] ) ) {
		return 'create-location-page';
	}
	if ( ! empty( $signals['schema_gap'] ) || ! empty( $signals['entity_gap'] ) ) {
		return 'improve-schema-and-entities';
	}
	if ( ! empty( $signals['authority_gap'] ) ) {
		return 'authority-and-digital-pr';
	}
	if ( ! empty( $signals['conversion_gap'] ) ) {
		return 'conversion-improvement';
	}
	if ( ! empty( $signals['content_gap'] ) ) {
		return 'publish-new-content';
	}
	return 'monitor-and-retest';
}

/**
 * AutoSEO action labels used in product/dashboard UI.
 *
 * @return array<string,string>
 */
function softkom_v3_autoseo_action_labels() {
	return array(
		'technical-fix'                  => 'Fix a technical SEO blocker',
		'improve-ranking-page'           => 'Improve a ranking page for CTR and conversion',
		'refresh-existing-page'          => 'Refresh an existing page before creating more content',
		'create-or-improve-service-page' => 'Create or improve a commercial service page',
		'create-comparison-page'         => 'Create a comparison / alternatives page',
		'create-location-page'           => 'Create a location page where real local demand exists',
		'improve-schema-and-entities'     => 'Strengthen schema, entities and answer structure',
		'authority-and-digital-pr'       => 'Earn stronger third-party mentions and authority',
		'conversion-improvement'         => 'Improve conversion on an already-visible page',
		'publish-new-content'            => 'Publish new content for a validated gap',
		'monitor-and-retest'             => 'Monitor and retest before making another change',
	);
}

/**
 * Product-level AutoSEO operating workflow.
 *
 * @return array<int,array<string,string>>
 */
function softkom_v3_autoseo_workflow() {
	return array(
		array(
			'id'    => 'discover',
			'title' => 'Discover',
			'body'  => 'Pull technical, Search Console, analytics, AI-visibility, content and competitor signals into one queue.',
		),
		array(
			'id'    => 'prioritise',
			'title' => 'Prioritise',
			'body'  => 'Score opportunities by traffic potential, buyer intent, conversion value and implementation effort.',
		),
		array(
			'id'    => 'prepare',
			'title' => 'Prepare',
			'body'  => 'Generate the smallest useful fix: update, brief, page, schema change, internal links or conversion improvement.',
		),
		array(
			'id'    => 'approve',
			'title' => 'Approve',
			'body'  => 'Keep a human approval gate before material website or publishing changes.',
		),
		array(
			'id'    => 'publish',
			'title' => 'Publish',
			'body'  => 'Push approved changes to WordPress with a recorded before/after state.',
		),
		array(
			'id'    => 'measure',
			'title' => 'Measure',
			'body'  => 'Track rankings, AI citation frequency, enquiries, calls, conversions and attributable revenue.',
		),
		array(
			'id'    => 'repeat',
			'title' => 'Repeat',
			'body'  => 'Retest, compare movement against baseline and feed the next highest-value opportunity back into the queue.',
		),
	);
}

/**
 * Search Console quick-win rules represented for UI and future collectors.
 *
 * @return array<int,array<string,string>>
 */
function softkom_v3_autoseo_search_console_rules() {
	return array(
		array(
			'id'    => 'near-page-one',
			'title' => 'Near-page-one opportunity',
			'body'  => 'Queries/pages already close to strong visibility but not yet earning their likely click share.',
		),
		array(
			'id'    => 'high-impression-low-ctr',
			'title' => 'High impressions, weak CTR',
			'body'  => 'Pages receiving visibility but underperforming on title, snippet, intent match or offer clarity.',
		),
		array(
			'id'    => 'content-decay',
			'title' => 'Content decay',
			'body'  => 'Previously useful pages losing clicks or impressions and worth refreshing before publishing something new.',
		),
		array(
			'id'    => 'new-query-demand',
			'title' => 'New buyer-language demand',
			'body'  => 'Queries appearing in Search Console that reveal a service, comparison or problem the site does not answer well yet.',
		),
	);
}

/**
 * Measurement dimensions for AutoSEO.
 *
 * @return array<int,array<string,string>>
 */
function softkom_v3_autoseo_measurement_model() {
	return array(
		array( 'id' => 'organic', 'title' => 'Google organic', 'body' => 'Clicks, impressions, CTR and query/page movement.' ),
		array( 'id' => 'ai', 'title' => 'AI visibility', 'body' => 'Prompt-level mentions, citation frequency, accuracy and competitor displacement.' ),
		array( 'id' => 'conversion', 'title' => 'Lead conversion', 'body' => 'Forms, calls, booked meetings and other qualified website enquiries.' ),
		array( 'id' => 'revenue', 'title' => 'Revenue signal', 'body' => 'Where CRM or transaction data is available, tie organic and AI-assisted journeys back to commercial outcomes.' ),
	);
}
