<?php
/**
 * Business Systems Assessment — recommendations engine (architecture).
 *
 * Recommendations are templates Softkom adapts per client. Never invent outcomes.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recommendation templates keyed by pattern.
 *
 * @return array<string, array<string, mixed>>
 */
function softkom_v3_assessment_recommendation_templates() {
	return array(
		'stabilize-foundations' => array(
			'id'          => 'stabilize-foundations',
			'title'       => 'Stabilise foundations before automation',
			'when'        => 'Low process maturity or data quality with automation/AI appetite.',
			'approach'    => 'Map ownership and source-of-truth for core entities before scripting volume work.',
			'frameworks'  => array( 'systems-maturity', 'connected-business' ),
			'services'    => array( 'business-systems', 'process-automation' ),
			'insights'    => array( 'why-most-businesses-are-still-running-on-spreadsheets', 'how-to-identify-automation-opportunities' ),
		),
		'integrate-before-replace' => array(
			'id'          => 'integrate-before-replace',
			'title'       => 'Integrate before replacing core systems',
			'when'        => 'Systems exist but handoffs force re-entry; replacement is not yet justified.',
			'approach'    => 'Connect must-keep systems with clear ownership; defer rip-and-replace until friction is proven.',
			'frameworks'  => array( 'connected-business', 'delivery-lifecycle' ),
			'services'    => array( 'process-automation', 'business-systems' ),
			'insights'    => array(),
		),
		'controlled-ai' => array(
			'id'          => 'controlled-ai',
			'title'       => 'Introduce AI only where rules and review exist',
			'when'        => 'High volume repetitive work with clearer foundations and named human accountability.',
			'approach'    => 'Use AI Opportunity Map™ bands — classify/assist first; escalate exceptions to people.',
			'frameworks'  => array( 'ai-opportunity-map' ),
			'services'    => array( 'ai-automation' ),
			'insights'    => array( 'how-to-identify-automation-opportunities' ),
		),
		'evidence-in-workflow' => array(
			'id'          => 'evidence-in-workflow',
			'title'       => 'Design compliance evidence into workflows',
			'when'        => 'Compliance pressure with ad hoc evidence assembly.',
			'approach'    => 'Capture approvals, access and retention as part of operational flow — not as a scramble before audit.',
			'frameworks'  => array( 'delivery-lifecycle', 'systems-maturity' ),
			'services'    => array( 'compliance-platforms', 'process-automation' ),
			'insights'    => array( 'automating-compliance-soc2-iso-popia' ),
		),
		'leadership-visibility' => array(
			'id'          => 'leadership-visibility',
			'title'       => 'Build one operating picture for leadership',
			'when'        => 'Reporting and visibility scores lag connected operations.',
			'approach'    => 'Agree the decisions directors must make weekly; design reporting from those decisions — not from leftover exports.',
			'frameworks'  => array( 'connected-business', 'systems-maturity' ),
			'services'    => array( 'business-intelligence', 'business-systems' ),
			'insights'    => array(),
		),
		'early-decline' => array(
			'id'          => 'early-decline',
			'title'       => 'Decline or defer Softkom engagement',
			'when'        => 'No decision rights, no willingness to change ownership, or Softkom cannot honour the scope.',
			'approach'    => 'Say no early. Softkom does not sell tools into environments leadership will not sponsor.',
			'frameworks'  => array( 'transformation-framework' ),
			'services'    => array(),
			'insights'    => array(),
		),
	);
}

/**
 * Build recommendation list from a scored result (heuristic architecture).
 *
 * Softkom consultants remain accountable for what is sent to a client.
 *
 * @param array<string, mixed> $score_result From softkom_v3_assessment_score().
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_assessment_recommend( $score_result ) {
	$templates = softkom_v3_assessment_recommendation_templates();
	$picked    = array();
	$sections  = isset( $score_result['sections'] ) ? $score_result['sections'] : array();

	$avg = function ( $id ) use ( $sections ) {
		return isset( $sections[ $id ]['average'] ) ? (float) $sections[ $id ]['average'] : null;
	};

	$process = $avg( 'process-maturity' );
	$data    = $avg( 'data-quality' );
	$integ   = $avg( 'systems-integration' );
	$auto    = $avg( 'automation' );
	$ai      = $avg( 'ai-readiness' );
	$comp    = $avg( 'compliance' );
	$vis     = $avg( 'business-visibility' );
	$rep     = $avg( 'reporting' );

	if ( ( null !== $process && $process < 3 ) || ( null !== $data && $data < 3 ) ) {
		$picked[] = $templates['stabilize-foundations'];
	}
	if ( null !== $integ && $integ < 3.5 && ( null === $process || $process >= 2.5 ) ) {
		$picked[] = $templates['integrate-before-replace'];
	}
	if ( ( null !== $auto && $auto >= 3 ) && ( null !== $ai && $ai >= 3 ) && ( null === $data || $data >= 3 ) ) {
		$picked[] = $templates['controlled-ai'];
	}
	if ( null !== $comp && $comp < 3.5 ) {
		$picked[] = $templates['evidence-in-workflow'];
	}
	if ( ( null !== $vis && $vis < 3.5 ) || ( null !== $rep && $rep < 3.5 ) ) {
		$picked[] = $templates['leadership-visibility'];
	}

	if ( ! $picked ) {
		$picked[] = $templates['stabilize-foundations'];
	}

	return $picked;
}

/**
 * Observation shell per section (fill from live assessment — never invent).
 *
 * @param string $section_id Section id.
 * @return array<string, mixed>
 */
function softkom_v3_assessment_observation_blank( $section_id ) {
	return array(
		'section'         => $section_id,
		'observation'     => '',
		'recommendation'  => '',
		'frameworks'      => array(),
		'services'        => array(),
		'insights'        => array(),
		'score'           => null,
	);
}
