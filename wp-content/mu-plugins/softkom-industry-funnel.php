<?php
/**
 * Softkom Industry Funnel Profiles.
 *
 * Adds a reusable profile layer on top of the proven Business Systems
 * Assessment. The scoring engine and question IDs stay stable while the
 * customer-facing wording, CTA and lead classification can be customised per
 * industry. Softkom is the first reference implementation.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return available industry funnel profiles.
 *
 * New industries should be added here without duplicating the assessment,
 * scoring, security, attribution or commercial pipeline engines.
 *
 * @return array<string,array<string,mixed>>
 */
function softkom_industry_funnel_profiles() {
	return array(
		'softkom' => array(
			'name'    => 'Softkom Solutions',
			'version' => 1,
			'copy'    => array(
				'eyebrow'            => 'Softkom Pilot Assessment',
				'title'              => 'How scalable are Softkom\'s business systems?',
				'lead'               => 'Identify where lead generation, project delivery, support, reporting and automation need to improve before Softkom scales further.',
				'start_label'        => 'Start the Softkom Assessment',
				'note'               => 'Internal pilot. The same engine will become the reusable foundation for industry-specific assessments.',
				'complete_eyebrow'   => 'Softkom Pilot Complete',
				'complete_title'     => 'Softkom\'s systems score is ready.',
				'complete_body'      => 'Enter the test details to create a complete lead, commercial recommendation and pipeline record.',
				'results_eyebrow'    => 'Softkom Pilot Results',
				'next_step_title'    => 'Turn the findings into Softkom\'s practical systems roadmap',
				'next_step_body'     => 'Use this pilot to validate the complete journey before creating industry editions.',
				'next_step_label'    => 'Review Softkom Solutions',
				'next_step_url'      => home_url( '/services/' ),
			),
			'questions' => array(
				array(
					'id'       => 'visibility-01',
					'section'  => 'Strategy & Planning',
					'question' => 'Can Softkom leadership see the current sales pipeline, project delivery, support workload and recurring revenue without assembling reports manually?',
					'help'     => 'Consider whether decisions can be made from live information instead of separate spreadsheets and delayed updates.',
				),
				array(
					'id'       => 'reporting-03',
					'section'  => 'Strategy & Planning',
					'question' => 'Are Softkom\'s KPIs clearly defined around lead conversion, project margin, delivery quality, support and monthly recurring revenue?',
					'help'     => 'Focus on measures that directly guide commercial and operational decisions.',
				),
				array(
					'id'       => 'process-01',
					'section'  => 'Process & Automation',
					'question' => 'How well are Softkom\'s recurring sales, proposal, delivery, support and follow-up processes documented?',
					'help'     => 'Consider whether each process has clear ownership, steps and expected outcomes.',
				),
				array(
					'id'       => 'automation-01',
					'section'  => 'Process & Automation',
					'question' => 'How much repetitive work in lead capture, follow-up, onboarding, project updates and support is already automated?',
					'help'     => 'Think about notifications, approvals, reminders, data movement and recurring administration.',
				),
				array(
					'id'       => 'integration-01',
					'section'  => 'Technology',
					'question' => 'How automatically do Softkom\'s website, lead system, proposals, project delivery, support and finance tools exchange data?',
					'help'     => 'Look for duplicate entry, manual exports and information that becomes disconnected between tools.',
				),
				array(
					'id'       => 'ai-01',
					'section'  => 'Technology',
					'question' => 'How reliable and well-managed is the Softkom data foundation needed for safe AI automation?',
					'help'     => 'Consider data quality, ownership, permissions and whether AI output can be checked against trusted sources.',
				),
				array(
					'id'       => 'process-03',
					'section'  => 'People & Culture',
					'question' => 'How resilient would Softkom\'s sales, delivery and support processes be if a key person were unavailable?',
					'help'     => 'Consider shared knowledge, documented procedures and cross-training.',
				),
				array(
					'id'       => 'risk-01',
					'section'  => 'People & Culture',
					'question' => 'How comfortably could Softkom absorb a sudden increase in qualified leads, projects or support demand?',
					'help'     => 'Imagine growth without relying on overtime, emergency workarounds or one key person.',
				),
				array(
					'id'       => 'data-01',
					'section'  => 'Data & Reporting',
					'question' => 'How consistently is lead, client, project and revenue information captured once and reused across Softkom\'s systems?',
					'help'     => 'Look for repeated entry and inconsistent records between sales, delivery, support and finance.',
				),
				array(
					'id'       => 'reporting-02',
					'section'  => 'Data & Reporting',
					'question' => 'How confidently can Softkom act on pipeline, delivery and recurring-revenue reports without rechecking the figures offline?',
					'help'     => 'Consider trust in definitions, dashboards and sources of truth.',
				),
				array(
					'id'       => 'visibility-03',
					'section'  => 'Customer Experience',
					'question' => 'How reliably are project or support exceptions identified before a Softkom client needs to escalate them?',
					'help'     => 'Consider proactive alerts, ownership, delivery risks and support visibility.',
				),
				array(
					'id'       => 'automation-03',
					'section'  => 'Customer Experience',
					'question' => 'How automatically are Softkom client onboarding, progress updates, internal handoffs and follow-ups triggered?',
					'help'     => 'Think about the full journey from signed proposal through delivery and ongoing support.',
				),
				array(
					'id'       => 'governance-01',
					'section'  => 'Governance & Compliance',
					'question' => 'How clear are approval rights for Softkom pricing, proposals, deployments, system changes and access?',
					'help'     => 'Consider decision ownership, change control and who can approve commercial or technical risk.',
				),
				array(
					'id'       => 'compliance-02',
					'section'  => 'Governance & Compliance',
					'question' => 'How consistently is POPIA, security, backup, deployment and support evidence captured during normal Softkom work?',
					'help'     => 'Consider whether evidence is built into normal workflows or assembled manually when needed.',
				),
			),
		),
	);
}

/**
 * Resolve the active profile from ?industry=, defaulting to Softkom.
 *
 * @return array<string,mixed>
 */
function softkom_industry_funnel_active_profile() {
	$profiles = softkom_industry_funnel_profiles();
	$key      = isset( $_GET['industry'] ) ? sanitize_key( wp_unslash( $_GET['industry'] ) ) : 'softkom';

	if ( ! isset( $profiles[ $key ] ) ) {
		$key = 'softkom';
	}

	$profile        = $profiles[ $key ];
	$profile['key'] = $key;

	return $profile;
}

/**
 * Load the profile adapter only on the assessment page.
 */
function softkom_industry_funnel_enqueue() {
	if ( ! is_page( 'assessment' ) ) {
		return;
	}

	$profile = softkom_industry_funnel_active_profile();
	$handle  = 'softkom-industry-funnel';
	$src     = content_url( '/mu-plugins/softkom-industry-funnel.js' );
	$path    = WPMU_PLUGIN_DIR . '/softkom-industry-funnel.js';
	$version = is_readable( $path ) ? (string) filemtime( $path ) : '1';

	wp_enqueue_script( $handle, $src, array(), $version, true );
	wp_localize_script(
		$handle,
		'softkomIndustryFunnel',
		array(
			'key'       => $profile['key'],
			'name'      => $profile['name'],
			'version'   => (int) $profile['version'],
			'copy'      => $profile['copy'],
			'questions' => $profile['questions'],
		)
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_industry_funnel_enqueue', 40 );

/**
 * Persist the profile on every newly stored lead.
 *
 * @param int   $lead_id  Lead post ID.
 * @param array $result   Funnel result.
 * @param array $security Security result.
 */
function softkom_industry_funnel_store_profile( $lead_id, $result, $security ) {
	unset( $result, $security );

	$profiles = softkom_industry_funnel_profiles();
	$key      = isset( $_POST['industry_key'] ) && is_scalar( $_POST['industry_key'] )
		? sanitize_key( wp_unslash( (string) $_POST['industry_key'] ) )
		: 'softkom';

	if ( ! isset( $profiles[ $key ] ) ) {
		$key = 'softkom';
	}

	update_post_meta( $lead_id, '_softkom_industry_key', $key );
	update_post_meta( $lead_id, '_softkom_industry_name', $profiles[ $key ]['name'] );
	update_post_meta( $lead_id, '_softkom_industry_profile_version', (int) $profiles[ $key ]['version'] );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_industry_funnel_store_profile', 35, 3 );

/**
 * Add industry visibility to the lead list.
 *
 * @param array<string,string> $columns Existing columns.
 * @return array<string,string>
 */
function softkom_industry_funnel_lead_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'softkom_company' === $key ) {
			$new['softkom_industry'] = 'Industry Profile';
		}
	}
	return $new;
}
add_filter( 'manage_softkom_lead_posts_columns', 'softkom_industry_funnel_lead_columns', 30 );

/**
 * Render industry profile column.
 */
function softkom_industry_funnel_render_lead_column( $column, $post_id ) {
	if ( 'softkom_industry' !== $column ) {
		return;
	}

	$name = get_post_meta( $post_id, '_softkom_industry_name', true );
	echo '' !== $name ? esc_html( $name ) : '&mdash;';
}
add_action( 'manage_softkom_lead_posts_custom_column', 'softkom_industry_funnel_render_lead_column', 30, 2 );
