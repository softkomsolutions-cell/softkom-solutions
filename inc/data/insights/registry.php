<?php
/**
 * Insights registry — published and planned entries.
 *
 * Only rows Softkom can defend. Pre–Phase 4 posts are registered as
 * wordpress-post sources with hub-card metadata already on the live site.
 * Structured Phase 4 sections stay empty until Softkom rewrites from experience.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All insight records.
 *
 * @return array<int, array<string, mixed>>
 */
function softkom_v3_insights_registry() {
	return array(
		array(
			'slug'             => 'why-most-businesses-are-still-running-on-spreadsheets',
			'title'            => 'Why Most Businesses Are Still Running on Spreadsheets',
			'status'           => 'published',
			'origin'           => 'Pre–Phase 4 published post — review for experience depth before expanding structured sections.',
			'author'           => softkom_v3_insight_default_author(),
			'published_at'     => '',
			'updated_at'       => '',
			'url'              => '/why-most-businesses-are-still-running-on-spreadsheets/',
			'excerpt'          => 'Where spreadsheet dependence actually starts, why it persists past headcount growth, and what a controlled systems path looks like when you are ready to leave the files behind.',
			'categories'       => array( 'operations', 'business-systems' ),
			'topics'           => array( 'spreadsheet-dependence' ),
			'reading_time_min' => 0, // Set when Softkom measures or rewrites under Phase 4 structure.
			'frameworks'       => array( 'systems-maturity', 'connected-business' ),
			'services'         => array( 'business-systems', 'process-automation' ),
			'industries'       => array(),
			'assessment_questions' => array( 'visibility-01', 'process-01', 'data-01' ),
			'hub_card'         => array(
				'eyebrow' => 'Operations',
				'title'   => 'Why Most Businesses Are Still Running on Spreadsheets',
				'body'    => 'Where spreadsheet dependence actually starts, why it persists past headcount growth, and what a controlled systems path looks like when you are ready to leave the files behind.',
			),
			'sections'         => softkom_v3_insight_blank()['sections'],
			'source'           => 'wordpress-post',
			'public'           => true,
			'notes'            => 'Hub card copy matches live catalog. Do not invent structured section body until Softkom rewrites from defendable experience.',
		),
		array(
			'slug'             => 'how-to-identify-automation-opportunities',
			'title'            => 'How to Identify Automation Opportunities',
			'status'           => 'published',
			'origin'           => 'Pre–Phase 4 published post — review before expanding structured sections.',
			'author'           => softkom_v3_insight_default_author(),
			'published_at'     => '',
			'updated_at'       => '',
			'url'              => '/how-to-identify-automation-opportunities/',
			'excerpt'          => 'A practical filter for spotting repetitive work worth automating — volume, rule clarity, risk of error — without trying to fix every process at once or automating a broken workflow.',
			'categories'       => array( 'ai', 'operations' ),
			'topics'           => array( 'when-not-to-automate', 'process-handoffs' ),
			'reading_time_min' => 0,
			'frameworks'       => array( 'ai-opportunity-map', 'systems-maturity' ),
			'services'         => array( 'ai-automation', 'process-automation' ),
			'industries'       => array(),
			'assessment_questions' => array( 'automation-01', 'ai-01' ),
			'hub_card'         => array(
				'eyebrow' => 'Automation',
				'title'   => 'How to Identify Automation Opportunities',
				'body'    => 'A practical filter for spotting repetitive work worth automating — volume, rule clarity, risk of error — without trying to fix every process at once or automating a broken workflow.',
			),
			'sections'         => softkom_v3_insight_blank()['sections'],
			'source'           => 'wordpress-post',
			'public'           => true,
			'notes'            => 'Hub card copy matches live catalog.',
		),
		array(
			'slug'             => 'automating-compliance-soc2-iso-popia',
			'title'            => 'Automating Compliance for SOC 2, ISO 27001 & POPIA',
			'status'           => 'published',
			'origin'           => 'Pre–Phase 4 published post — verify Softkom can defend every claim before deepening.',
			'author'           => softkom_v3_insight_default_author(),
			'published_at'     => '',
			'updated_at'       => '',
			'url'              => '/how-saas-companies-can-automate-compliance-and-achieve-soc-2-iso-27001-popia-faster/',
			'excerpt'          => 'How digital businesses reduce compliance friction by designing evidence capture into workflows — so audit readiness is continuous, not a scramble.',
			'categories'       => array( 'compliance', 'governance' ),
			'topics'           => array( 'audit-evidence-design', 'popia-in-workflows' ),
			'reading_time_min' => 0,
			'frameworks'       => array( 'delivery-lifecycle', 'systems-maturity' ),
			'services'         => array( 'compliance-platforms', 'process-automation' ),
			'industries'       => array(),
			'assessment_questions' => array( 'compliance-01', 'governance-01' ),
			'hub_card'         => array(
				'eyebrow' => 'Compliance',
				'title'   => 'Automating Compliance for SOC 2, ISO 27001 & POPIA',
				'body'    => 'How digital businesses reduce compliance friction by designing evidence capture into workflows. Softkom supports operational controls and audit preparation — Softkom does not certify organisations or provide legal advice.',
			),
			'sections'         => softkom_v3_insight_blank()['sections'],
			'source'           => 'wordpress-post',
			'public'           => true,
			'notes'            => 'Hub card copy matches live catalog. Claims must remain defendable.',
		),
	);
}
