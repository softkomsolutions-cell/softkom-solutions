<?php
/**
 * Business Systems Assessment — scoring engine (architecture).
 *
 * Scores are diagnostic aids for Softkom conversations — not vanity grades.
 * Do not invent client scores. Do not publish public scoreboards.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Score band definitions (1–5 per question; section average → maturity hint).
 *
 * @return array<int, array{score:int,label:string,meaning:string}>
 */
function softkom_v3_assessment_score_bands() {
	return array(
		1 => array(
			'score'   => 1,
			'label'   => 'Fragile',
			'meaning' => 'Work depends on individuals, files or undocumented habit.',
		),
		2 => array(
			'score'   => 2,
			'label'   => 'Fragmented',
			'meaning' => 'Tools exist but handoffs and ownership are weak.',
		),
		3 => array(
			'score'   => 3,
			'label'   => 'Emerging control',
			'meaning' => 'Some processes and data have ownership; gaps remain.',
		),
		4 => array(
			'score'   => 4,
			'label'   => 'Connected',
			'meaning' => 'Core flows move with control Softkom can build on.',
		),
		5 => array(
			'score'   => 5,
			'label'   => 'Intelligent-ready',
			'meaning' => 'Stable foundations where automation/AI can be introduced with accountability.',
		),
	);
}

/**
 * Map average section score (1–5) toward Systems Maturity™ level (01–04).
 *
 * @param float $average Section or overall average.
 * @return array{level:string,title:string}
 */
function softkom_v3_assessment_maturity_hint( $average ) {
	$average = (float) $average;
	if ( $average < 1.75 ) {
		return array( 'level' => '01', 'title' => 'Spreadsheet dependent' );
	}
	if ( $average < 2.75 ) {
		return array( 'level' => '02', 'title' => 'Fragmented tools' );
	}
	if ( $average < 3.75 ) {
		return array( 'level' => '03', 'title' => 'Connected operations' );
	}
	return array( 'level' => '04', 'title' => 'Intelligent operations' );
}

/**
 * Score a set of answers.
 *
 * @param array<string, int|float> $answers Map of question_id => score (1–5).
 * @param array<int, array<string, mixed>>|null $questions Optional question bank subset.
 * @return array<string, mixed>
 */
function softkom_v3_assessment_score( $answers, $questions = null ) {
	if ( null === $questions ) {
		$questions = softkom_v3_assessment_question_bank();
	}

	$by_section = array();
	foreach ( softkom_v3_assessment_section_ids() as $section_id ) {
		$by_section[ $section_id ] = array(
			'scores'  => array(),
			'average' => null,
			'maturity_hint' => null,
		);
	}

	foreach ( $questions as $question ) {
		$id = $question['id'];
		if ( ! isset( $answers[ $id ] ) ) {
			continue;
		}
		$score = (int) $answers[ $id ];
		if ( $score < 1 || $score > 5 ) {
			continue;
		}
		$section = $question['section'];
		if ( ! isset( $by_section[ $section ] ) ) {
			continue;
		}
		$by_section[ $section ]['scores'][] = $score;
	}

	$all = array();
	foreach ( $by_section as $section_id => &$row ) {
		if ( $row['scores'] ) {
			$row['average'] = array_sum( $row['scores'] ) / count( $row['scores'] );
			$row['maturity_hint'] = softkom_v3_assessment_maturity_hint( $row['average'] );
			$all[] = $row['average'];
		}
	}
	unset( $row );

	$overall = $all ? array_sum( $all ) / count( $all ) : null;

	return array(
		'sections'       => $by_section,
		'overall_average'=> $overall,
		'maturity_hint'  => null === $overall ? null : softkom_v3_assessment_maturity_hint( $overall ),
		'scored_at'      => '', // Fill when Softkom runs a live assessment.
		'disclaimer'     => 'Diagnostic aid for Softkom conversations. Not a certification, benchmark or guarantee.',
	);
}

/**
 * CRM-ready output envelope (no CRM integration yet).
 *
 * @param array<string, mixed> $payload Assessment result payload.
 * @return array<string, mixed>
 */
function softkom_v3_assessment_crm_payload( $payload ) {
	return array(
		'schema_version' => '1.0',
		'source'         => 'softkom-business-systems-assessment',
		'crm_ready'      => true,
		'crm_integrated' => false,
		'payload'        => $payload,
		'fields'         => array(
			'organisation',
			'contact',
			'section_scores',
			'maturity_hint',
			'recommendations',
			'next_step',
			'consent',
		),
	);
}

/**
 * PDF export contract (architecture only — no renderer yet).
 *
 * @return array<string, mixed>
 */
function softkom_v3_assessment_pdf_export_contract() {
	return array(
		'status'   => 'planned',
		'sections' => array(
			'cover',
			'executive_summary',
			'maturity_placement',
			'section_observations',
			'recommendations',
			'framework_references',
			'next_step',
		),
		'rules'    => array(
			'No invented metrics or ROI.',
			'Omit empty sections.',
			'Map explicitly to Systems Maturity™.',
			'Include Softkom participation expectations for any recommended next step.',
		),
	);
}
