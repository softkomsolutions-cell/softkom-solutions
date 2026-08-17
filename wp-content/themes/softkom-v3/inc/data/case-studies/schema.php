<?php
/**
 * Case study model & accessors.
 *
 * Metrics are optional. Never invent metrics. Omit empty public fields.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Empty case study shell.
 *
 * @param string $slug Slug.
 * @return array<string, mixed>
 */
function softkom_v3_case_study_blank( $slug = '' ) {
	return array(
		'slug'                  => $slug,
		'title'                 => '',
		'status'                => 'draft', // draft | internal | published
		'evidence_level'        => 'internal',
		'permission'            => softkom_v3_case_permission_defaults(),
		'client'                => array(
			'internal_name' => '',
			'public_name'   => '',
			'label'         => 'Client Project', // Client Project | Softkom Product
		),
		'industry'              => '',
		'region'                => '',
		'engagement_type'       => '',
		'business_problem'      => '',
		'client_context'        => '',
		'operational_environment' => '',
		'approach'              => '',
		'frameworks'            => array(),
		'implementation_stages' => array(),
		'lessons_learned'       => array(),
		'outcomes'              => array(), // qualitative preferred
		'metrics'               => array(), // optional; only with permission
		'technologies'          => '',
		'visual'                => array(
			'title'  => '',
			'widths' => array(),
			'nodes'  => array(),
		),
		'url'                   => '',
		'public'                => false,
		'notes'                 => '',
	);
}

/**
 * Whether a case may appear on public surfaces.
 *
 * @param array<string, mixed> $case Case row.
 * @return bool
 */
function softkom_v3_case_study_is_public( $case ) {
	if ( empty( $case['public'] ) || empty( $case['status'] ) || 'published' !== $case['status'] ) {
		return false;
	}
	$levels = softkom_v3_case_evidence_levels();
	$level  = isset( $case['evidence_level'] ) ? $case['evidence_level'] : 'internal';
	if ( ! isset( $levels[ $level ] ) || empty( $levels[ $level ]['public_ok'] ) ) {
		return false;
	}
	return true;
}

/**
 * Public projection — strips metrics/quotes/screenshots without permission
 * and omits empty fields.
 *
 * @param array<string, mixed> $case Case row.
 * @return array<string, mixed>
 */
function softkom_v3_case_study_public_view( $case ) {
	if ( ! softkom_v3_case_study_is_public( $case ) ) {
		return array();
	}

	$permission = isset( $case['permission'] ) && is_array( $case['permission'] )
		? array_merge( softkom_v3_case_permission_defaults(), $case['permission'] )
		: softkom_v3_case_permission_defaults();

	$public_name = '';
	if ( ! empty( $permission['public_name'] ) && ! empty( $case['client']['public_name'] ) ) {
		$public_name = $case['client']['public_name'];
	} elseif ( 'anonymous' === $case['evidence_level'] ) {
		$public_name = 'Confidential client';
	}

	$view = array(
		'slug'     => $case['slug'],
		'title'    => $public_name ? $public_name : $case['title'],
		'industry' => $case['industry'],
		'label'    => isset( $case['client']['label'] ) ? $case['client']['label'] : 'Client Project',
		'url'      => isset( $case['url'] ) ? $case['url'] : '',
	);

	$map = array(
		'business_problem'        => 'business_problem',
		'client_context'          => 'client_context',
		'operational_environment' => 'operational_environment',
		'approach'                => 'approach',
		'frameworks'              => 'frameworks',
		'implementation_stages'   => 'implementation_stages',
		'lessons_learned'         => 'lessons_learned',
		'outcomes'                => 'outcomes',
		'technologies'            => 'technologies',
	);
	foreach ( $map as $from => $to ) {
		if ( ! softkom_v3_authority_value_empty( $case[ $from ] ) ) {
			$view[ $to ] = $case[ $from ];
		}
	}

	if ( ! empty( $permission['metrics'] ) && ! softkom_v3_authority_value_empty( $case['metrics'] ) ) {
		$view['metrics'] = $case['metrics'];
	}

	if ( ! empty( $case['visual'] ) && ! softkom_v3_authority_value_empty( $case['visual']['title'] ) ) {
		$view['visual'] = $case['visual'];
	}

	$view['evidence_level'] = $case['evidence_level'];
	return $view;
}

/**
 * Single case by slug.
 *
 * @param string $slug Slug.
 * @return array<string, mixed>|null
 */
function softkom_v3_case_study( $slug ) {
	$slug = sanitize_title( (string) $slug );
	foreach ( softkom_v3_case_studies_registry() as $case ) {
		if ( isset( $case['slug'] ) && $case['slug'] === $slug ) {
			return $case;
		}
	}
	return null;
}

/**
 * Project public cases into Projects page cards (catalog-compatible).
 *
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_case_studies_as_project_cards() {
	$cards = array();
	foreach ( softkom_v3_case_studies_registry() as $case ) {
		if ( empty( $case['hub_card'] ) || ! is_array( $case['hub_card'] ) ) {
			continue;
		}
		if ( empty( $case['hub_card_public'] ) ) {
			continue;
		}
		$cards[] = $case['hub_card'];
	}
	return $cards;
}
