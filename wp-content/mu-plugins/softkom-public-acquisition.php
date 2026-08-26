<?php
/**
 * Softkom public acquisition funnel.
 *
 * Prospect-facing acquisition layer on top of the proven assessment, scoring,
 * attribution and commercial pipeline engines.
 *
 * Add ?pilot=1 to the assessment URL to keep the internal pilot wording.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_public_acquisition_enabled() {
	if ( is_admin() || ! is_page( 'assessment' ) ) {
		return false;
	}

	$pilot = isset( $_GET['pilot'] ) ? sanitize_key( wp_unslash( $_GET['pilot'] ) ) : '';
	return '1' !== $pilot && 'true' !== $pilot;
}

function softkom_public_acquisition_enqueue() {
	if ( ! softkom_public_acquisition_enabled() ) {
		return;
	}

	$path = WPMU_PLUGIN_DIR . '/softkom-public-acquisition.js';
	$src  = content_url( '/mu-plugins/softkom-public-acquisition.js' );
	$dependencies = array();

	if ( wp_script_is( 'softkom-industry-funnel', 'registered' ) || wp_script_is( 'softkom-industry-funnel', 'enqueued' ) ) {
		$dependencies[] = 'softkom-industry-funnel';
	}

	wp_enqueue_script(
		'softkom-public-acquisition',
		$src,
		$dependencies,
		is_readable( $path ) ? (string) filemtime( $path ) : '1',
		true
	);

	wp_localize_script(
		'softkom-public-acquisition',
		'softkomPublicAcquisition',
		array(
			'contactUrl' => add_query_arg( array( 'source' => 'assessment' ), home_url( '/contact/' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_public_acquisition_enqueue', 60 );

/**
 * Fast-track genuinely sales-ready prospects.
 */
function softkom_public_acquisition_fast_track( $lead_id, $result, $security ) {
	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	update_post_meta( $lead_id, '_softkom_acquisition_mode', 'public-assessment' );

	$risk = isset( $security['risk_level'] ) ? strtoupper( trim( (string) $security['risk_level'] ) ) : '';
	if ( in_array( $risk, array( 'BLOCK', 'HIGH RISK', 'REVIEW' ), true ) ) {
		return;
	}

	$routing = get_post_meta( $lead_id, '_softkom_lead_routing', true );
	if ( is_string( $routing ) ) {
		$routing = json_decode( $routing, true );
	}
	if ( ! is_array( $routing ) || empty( $routing['sales_eligible'] ) ) {
		return;
	}

	$commercial = (int) get_post_meta( $lead_id, '_softkom_score_commercial_fit', true );
	$intent     = (int) get_post_meta( $lead_id, '_softkom_score_purchase_intent', true );
	$overall    = (int) get_post_meta( $lead_id, '_softkom_score_overall_lead', true );
	$ai         = (int) get_post_meta( $lead_id, '_softkom_score_ai_opportunity', true );

	$strong_buyer       = ( $commercial >= 75 && $intent >= 85 && $overall >= 70 );
	$high_value_ai_buyer = ( $commercial >= 70 && $intent >= 90 && $ai >= 75 );

	if ( ! $strong_buyer && ! $high_value_ai_buyer ) {
		return;
	}

	update_post_meta( $lead_id, '_softkom_lead_temperature', 'HOT' );
	update_post_meta( $lead_id, '_softkom_fast_track_reason', 'high-commercial-fit-and-purchase-intent' );

	if ( function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) ) {
		softkom_v3_auto_pipeline_hot_lead( $lead_id, $result, $security );
	}
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_fast_track', 19, 3 );

/**
 * Alert Softkom immediately when an assessment creates a sales-eligible lead.
 * The notification deliberately runs after fast-track/auto-pipeline processing
 * so the email reflects the final temperature and commercial state.
 */
function softkom_public_acquisition_notify( $lead_id, $result, $security ) {
	unset( $result );

	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	if ( get_post_meta( $lead_id, '_softkom_acquisition_notification_sent', true ) ) {
		return;
	}

	$risk = isset( $security['risk_level'] ) ? strtoupper( trim( (string) $security['risk_level'] ) ) : '';
	if ( in_array( $risk, array( 'BLOCK', 'HIGH RISK' ), true ) ) {
		return;
	}

	$routing = get_post_meta( $lead_id, '_softkom_lead_routing', true );
	if ( is_string( $routing ) ) {
		$routing = json_decode( $routing, true );
	}
	if ( is_array( $routing ) && isset( $routing['sales_eligible'] ) && ! $routing['sales_eligible'] ) {
		return;
	}

	$first       = (string) get_post_meta( $lead_id, '_softkom_first_name', true );
	$last        = (string) get_post_meta( $lead_id, '_softkom_last_name', true );
	$email       = (string) get_post_meta( $lead_id, '_softkom_email', true );
	$company     = (string) get_post_meta( $lead_id, '_softkom_company', true );
	$temperature = (string) get_post_meta( $lead_id, '_softkom_lead_temperature', true );
	$score       = (int) get_post_meta( $lead_id, '_softkom_score_overall_lead', true );
	$commercial  = (int) get_post_meta( $lead_id, '_softkom_score_commercial_fit', true );
	$intent      = (int) get_post_meta( $lead_id, '_softkom_score_purchase_intent', true );
	$source      = (string) get_post_meta( $lead_id, '_softkom_traffic_source', true );
	$campaign    = (string) get_post_meta( $lead_id, '_softkom_utm_campaign', true );
	$offer       = (string) get_post_meta( $lead_id, '_softkom_assigned_offer', true );
	$mrr         = (float) get_post_meta( $lead_id, '_softkom_estimated_monthly_revenue', true );

	$name = trim( $first . ' ' . $last );
	if ( '' === $name ) {
		$name = 'Unknown prospect';
	}

	$admin_email = sanitize_email( get_option( 'admin_email' ) );
	if ( ! is_email( $admin_email ) ) {
		return;
	}

	$subject = sprintf(
		'[Softkom Lead] %s - %s - score %d',
		$temperature ? $temperature : 'NEW',
		$company ? $company : $name,
		$score
	);

	$lines = array(
		'A new Softkom assessment lead is ready for review.',
		'',
		'Name: ' . $name,
		'Company: ' . ( $company ? $company : '-' ),
		'Email: ' . ( $email ? $email : '-' ),
		'Temperature: ' . ( $temperature ? $temperature : '-' ),
		'Lead score: ' . $score . '/100',
		'Commercial fit: ' . $commercial . '/100',
		'Purchase intent: ' . $intent . '/100',
		'Assigned offer: ' . ( $offer ? $offer : 'Pending recommendation' ),
		'Estimated monthly revenue: R' . number_format_i18n( $mrr, 0 ),
		'Traffic source: ' . ( $source ? $source : 'direct/unknown' ),
		'Campaign: ' . ( $campaign ? $campaign : '-' ),
		'',
		'Open lead: ' . admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ),
	);

	$sent = wp_mail( $admin_email, $subject, implode( "\n", $lines ) );
	update_post_meta( $lead_id, '_softkom_acquisition_notification_sent', $sent ? 'yes' : 'failed' );
	update_post_meta( $lead_id, '_softkom_acquisition_notification_time_gmt', current_time( 'mysql', true ) );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_notify', 100, 3 );
