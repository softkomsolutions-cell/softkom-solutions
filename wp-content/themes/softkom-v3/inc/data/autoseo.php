<?php
/**
 * AutoSEO opportunity engine.
 *
 * Pure scoring and demo-data helpers. Live connectors are intentionally
 * separate so product logic can be tested without external credentials.
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
function softkom_v3_autoseo_clamp( $value ) {
	$value = is_numeric( $value ) ? (float) $value : 0.0;
	return max( 0.0, min( 100.0, $value ) );
}

/**
 * Score an SEO opportunity by commercial value, not content volume.
 *
 * Inputs are 0-100:
 * - demand: reachable search / AI demand
 * - intent: buyer intent
 * - conversion_value: likely business value when the visitor converts
 * - visibility_gap: how much room there is to improve
 * - evidence_gap: content/entity/authority weakness
 * - effort: implementation effort (higher effort lowers the score)
 *
 * @param array<string,mixed> $signals Opportunity signals.
 * @return int Score from 0-100.
 */
function softkom_v3_autoseo_opportunity_score( $signals ) {
	$demand           = softkom_v3_autoseo_clamp( $signals['demand'] ?? 0 );
	$intent           = softkom_v3_autoseo_clamp( $signals['intent'] ?? 0 );
	$conversion_value = softkom_v3_autoseo_clamp( $signals['conversion_value'] ?? 0 );
	$visibility_gap   = softkom_v3_autoseo_clamp( $signals['visibility_gap'] ?? 0 );
	$evidence_gap     = softkom_v3_autoseo_clamp( $signals['evidence_gap'] ?? 0 );
	$effort           = softkom_v3_autoseo_clamp( $signals['effort'] ?? 50 );
	$ease             = 100.0 - $effort;

	$score =
		( $demand * 0.20 ) +
		( $intent * 0.25 ) +
		( $conversion_value * 0.20 ) +
		( $visibility_gap * 0.15 ) +
		( $evidence_gap * 0.10 ) +
		( $ease * 0.10 );

	return (int) round( max( 0.0, min( 100.0, $score ) ) );
}

/**
 * Rank label for a score.
 *
 * @param int $score Opportunity score.
 * @return string
 */
function softkom_v3_autoseo_priority_label( $score ) {
	$score = (int) $score;
	if ( $score >= 80 ) {
		return 'Do next';
	}
	if ( $score >= 65 ) {
		return 'High';
	}
	if ( $score >= 45 ) {
		return 'Medium';
	}
	return 'Low';
}

/**
 * Demo dashboard data for product validation.
 *
 * No values below are presented as live Softkom performance data.
 *
 * @return array<string,mixed>
 */
function softkom_v3_autoseo_demo_dashboard() {
	$opportunities = array(
		array(
			'type'    => 'Service page refresh',
			'title'   => 'Strengthen a high-intent service page already earning impressions',
			'reason'  => 'Good buyer intent, existing visibility and a conversion path make this more valuable than publishing another generic article.',
			'signals' => array( 'demand' => 78, 'intent' => 92, 'conversion_value' => 88, 'visibility_gap' => 65, 'evidence_gap' => 58, 'effort' => 28 ),
		),
		array(
			'type'    => 'AI visibility',
			'title'   => 'Create an answer-ready comparison page for a repeated buyer prompt',
			'reason'  => 'Competitors are likely to be easier for AI assistants to cite because their pages answer the question more directly.',
			'signals' => array( 'demand' => 68, 'intent' => 86, 'conversion_value' => 82, 'visibility_gap' => 90, 'evidence_gap' => 84, 'effort' => 42 ),
		),
		array(
			'type'    => 'Technical fix',
			'title'   => 'Repair internal-link and canonical signals on commercially important pages',
			'reason'  => 'Low implementation effort with a direct effect on crawl clarity and page authority.',
			'signals' => array( 'demand' => 62, 'intent' => 74, 'conversion_value' => 76, 'visibility_gap' => 52, 'evidence_gap' => 35, 'effort' => 18 ),
		),
		array(
			'type'    => 'Authority',
			'title'   => 'Earn third-party corroboration for a claim AI assistants currently cannot verify',
			'reason'  => 'The site explains the offer, but external evidence is weak. A relevant directory, partner mention, review or earned citation may be more useful than more onsite copy.',
			'signals' => array( 'demand' => 55, 'intent' => 72, 'conversion_value' => 80, 'visibility_gap' => 76, 'evidence_gap' => 94, 'effort' => 58 ),
		),
		array(
			'type'    => 'New content',
			'title'   => 'Publish a focused article only where a real information gap exists',
			'reason'  => 'New content remains an option, but it competes against page improvements, technical fixes and authority work on the same score.',
			'signals' => array( 'demand' => 60, 'intent' => 52, 'conversion_value' => 48, 'visibility_gap' => 72, 'evidence_gap' => 66, 'effort' => 46 ),
		),
	);

	foreach ( $opportunities as &$opportunity ) {
		$opportunity['score']    = softkom_v3_autoseo_opportunity_score( $opportunity['signals'] );
		$opportunity['priority'] = softkom_v3_autoseo_priority_label( $opportunity['score'] );
	}
	unset( $opportunity );

	usort(
		$opportunities,
		function ( $a, $b ) {
			return (int) $b['score'] <=> (int) $a['score'];
		}
	);

	return array(
		'label' => 'Simulated product preview',
		'kpis'  => array(
			array( 'label' => 'AI prompt visibility', 'value' => '35%', 'note' => 'Example citation frequency across a controlled buyer-prompt set.' ),
			array( 'label' => 'High-value opportunities', 'value' => '4', 'note' => 'Example items scoring 65+ on the commercial opportunity model.' ),
			array( 'label' => 'Content actions', 'value' => '2', 'note' => 'New content is only one action type, not the default recommendation.' ),
			array( 'label' => 'Approval queue', 'value' => '3', 'note' => 'Example fixes waiting for human review before publication.' ),
		),
		'ai_engines' => array(
			array( 'name' => 'ChatGPT', 'frequency' => 40, 'status' => 'Mixed' ),
			array( 'name' => 'Gemini', 'frequency' => 30, 'status' => 'Weak' ),
			array( 'name' => 'Perplexity', 'frequency' => 50, 'status' => 'Strongest' ),
			array( 'name' => 'Google AI', 'frequency' => 20, 'status' => 'Weak' ),
			array( 'name' => 'Claude', 'frequency' => 35, 'status' => 'Mixed' ),
		),
		'opportunities' => $opportunities,
		'workflow' => array(
			'Discover from GSC, GA4, crawl data, AI prompts and competitor evidence',
			'Prioritise using demand, buyer intent, conversion value, visibility gap and effort',
			'Prepare the smallest useful fix',
			'Require human approval for material changes',
			'Publish or hand off',
			'Retest search visibility, AI citations and lead outcomes',
		),
	);
}
