<?php
/**
 * Softkom public acquisition funnel.
 *
 * Turns the internal Softkom pilot assessment into a prospect-facing lead
 * acquisition experience while preserving the proven scoring, attribution,
 * security, recommendation and commercial engines underneath.
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

	wp_enqueue_script(
		'softkom-public-acquisition',
		$src,
		array(),
		is_readable( $path ) ? (string) filemtime( $path ) : '1',
		true
	);

	wp_localize_script(
		'softkom-public-acquisition',
		'softkomPublicAcquisition',
		array(
			'contactUrl' => add_query_arg(
				array( 'source' => 'assessment' ),
				home_url( '/contact/' )
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_public_acquisition_enqueue', 60 );

function softkom_public_acquisition_fast_track( $lead_id, $result, $security ) {
	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	update_post_meta( $lead_id, '_softkom_acquisition_mode', 'public-assessment' );

	$risk = isset( $security['risk_level'] )
		? strtoupper( trim( (string) $security['risk_level'] ) )
		: '';

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

	$strong_buyer = ( $commercial >= 75 && $intent >= 85 && $overall >= 70 );
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
