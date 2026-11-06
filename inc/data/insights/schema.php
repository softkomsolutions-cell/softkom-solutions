<?php
/**
 * Insights platform — article model & accessors.
 *
 * Articles teach from real operational observation. Do not invent content.
 * Structured sections support executive decision pages when Softkom writes them.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical insight section keys (article page jobs).
 *
 * @return string[]
 */
function softkom_v3_insight_section_keys() {
	return array(
		'executive_summary',
		'business_context',
		'operational_challenge',
		'common_mistakes',
		'recommended_approach',
		'planning_considerations',
		'framework_references',
		'practical_checklist',
		'faq',
		'related_articles',
		'cta',
	);
}

/**
 * Empty insight shell Softkom fills from genuine encounters.
 *
 * @param string $slug Insight slug.
 * @return array<string, mixed>
 */
function softkom_v3_insight_blank( $slug = '' ) {
	return array(
		'slug'              => $slug,
		'title'             => '',
		'status'            => 'draft', // draft | internal | published
		'origin'            => '', // real observation Softkom can defend
		'author'            => softkom_v3_insight_default_author(),
		'published_at'      => '',
		'updated_at'        => '',
		'url'               => '',
		'excerpt'           => '',
		'categories'        => array(),
		'topics'            => array(),
		'reading_time_min'  => 0,
		'frameworks'        => array(),
		'services'          => array(),
		'industries'        => array(),
		'assessment_questions' => array(),
		'hub_card'          => array(
			'eyebrow' => '',
			'title'   => '',
			'body'    => '',
		),
		'sections'          => array(
			'executive_summary'       => '',
			'business_context'        => '',
			'operational_challenge'   => '',
			'common_mistakes'         => array(),
			'recommended_approach'    => '',
			'planning_considerations' => array(),
			'framework_references'    => array(),
			'practical_checklist'     => array(),
			'faq'                     => array(),
			'related_articles'        => array(),
			'cta'                     => array(
				'title' => '',
				'body'  => '',
				'url'   => '',
			),
		),
		'source'            => 'phase4-registry', // phase4-registry | wordpress-post
		'public'            => false,
		'notes'             => '',
	);
}

/**
 * Default author metadata (verified Softkom attribution only).
 *
 * @return array{name:string,role:string,bio:string}
 */
function softkom_v3_insight_default_author() {
	return array(
		'name' => 'Softkom Solutions',
		'role' => 'Business systems consultancy',
		'bio'  => '', // Fill only with verified leadership profile content.
	);
}

/**
 * Estimate reading time from plain text (words / 200).
 *
 * @param string $text Body text.
 * @return int
 */
function softkom_v3_insight_estimate_reading_time( $text ) {
	$words = str_word_count( wp_strip_all_tags( (string) $text ) );
	if ( $words < 1 ) {
		return 0;
	}
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Whether an insight may appear on public surfaces.
 *
 * @param array<string, mixed> $insight Insight row.
 * @return bool
 */
function softkom_v3_insight_is_public( $insight ) {
	return ! empty( $insight['public'] ) && isset( $insight['status'] ) && 'published' === $insight['status'];
}

/**
 * Project registry rows into Insights hub cards (catalog-compatible).
 *
 * @return array<int, array{eyebrow:string,title:string,body:string,url:string}>
 */
function softkom_v3_insights_as_hub_cards() {
	$cards = array();
	foreach ( softkom_v3_insights_registry() as $insight ) {
		if ( ! softkom_v3_insight_is_public( $insight ) ) {
			continue;
		}
		$card = isset( $insight['hub_card'] ) && is_array( $insight['hub_card'] ) ? $insight['hub_card'] : array();
$cards[] = array(
			'eyebrow'      => isset( $card['eyebrow'] ) ? $card['eyebrow'] : '',
			'title'        => isset( $card['title'] ) && $card['title'] ? $card['title'] : (string) $insight['title'],
			'body'         => isset( $card['body'] ) ? $card['body'] : (string) $insight['excerpt'],
			'url'          => isset( $insight['url'] ) ? (string) $insight['url'] : '',
			'author'       => ! empty( $insight['author']['name'] ) ? (string) $insight['author']['name'] : '',
			'published_at' => isset( $insight['published_at'] ) ? (string) $insight['published_at'] : '',
			'updated_at'   => isset( $insight['updated_at'] ) ? (string) $insight['updated_at'] : '',
			'reading_time_min' => ! empty( $insight['reading_time_min'] ) ? (int) $insight['reading_time_min'] : 0,
			'categories'   => isset( $insight['categories'] ) && is_array( $insight['categories'] ) ? $insight['categories'] : array(),
			'services'     => isset( $insight['services'] ) && is_array( $insight['services'] ) ? $insight['services'] : array(),
			'frameworks'   => isset( $insight['frameworks'] ) && is_array( $insight['frameworks'] ) ? $insight['frameworks'] : array(),
		);
	}
	return $cards;
}

/**
 * Single insight by slug.
 *
 * @param string $slug Slug.
 * @return array<string, mixed>|null
 */
function softkom_v3_insight( $slug ) {
	$slug = sanitize_title( (string) $slug );
	foreach ( softkom_v3_insights_registry() as $insight ) {
		if ( isset( $insight['slug'] ) && $insight['slug'] === $slug ) {
			return $insight;
		}
	}
	return null;
}

/**
 * Public sections only — omit empty fields (do not display unavailable information).
 *
 * @param array<string, mixed> $insight Insight row.
 * @return array<string, mixed>
 */
function softkom_v3_insight_public_sections( $insight ) {
	$out = array();
	if ( empty( $insight['sections'] ) || ! is_array( $insight['sections'] ) ) {
		return $out;
	}
	foreach ( $insight['sections'] as $key => $value ) {
		if ( softkom_v3_authority_value_empty( $value ) ) {
			continue;
		}
		$out[ $key ] = $value;
	}
	return $out;
}
