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

function softkom_public_acquisition_isolate_core_ui() {
	if ( ! softkom_public_acquisition_enabled() ) {
		return;
	}

	wp_dequeue_script( 'softkom-industry-funnel' );
}
add_action( 'wp_enqueue_scripts', 'softkom_public_acquisition_isolate_core_ui', 55 );

function softkom_public_acquisition_enqueue() {
	if ( ! softkom_public_acquisition_enabled() ) {
		return;
	}

	$path = WPMU_PLUGIN_DIR . '/softkom-public-acquisition.js';
	$src  = content_url( '/mu-plugins/softkom-public-acquisition.js' );
	$industry = isset( $_GET['industry'] ) ? sanitize_key( wp_unslash( $_GET['industry'] ) ) : 'softkom';
	if ( '' === $industry ) {
		$industry = 'softkom';
	}

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
				array(
					'source'   => 'assessment',
					'industry' => $industry,
				),
				home_url( '/contact/' )
			),
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'softkom_public_conversion' ),
			'industry'   => $industry,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_public_acquisition_enqueue', 60 );

/**
 * Append an item to the existing lead activity timeline.
 */
function softkom_public_acquisition_activity( $lead_id, $event, $from = '', $to = '' ) {
	$history = get_post_meta( $lead_id, '_softkom_pipeline_history', true );
	if ( ! is_array( $history ) ) {
		$history = array();
	}

	$history[] = array(
		'timestamp_gmt' => current_time( 'mysql', true ),
		'event'         => sanitize_key( $event ),
		'from'          => sanitize_text_field( $from ),
		'to'            => sanitize_text_field( $to ),
		'user_id'       => 0,
	);

	update_post_meta( $lead_id, '_softkom_pipeline_history', $history );
}

/**
 * Resolve the most recent assessment lead by email for public conversion events.
 */
function softkom_public_acquisition_find_lead_by_email( $email ) {
	$email = sanitize_email( $email );
	if ( ! is_email( $email ) ) {
		return 0;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'softkom_lead',
			'post_status'    => 'private',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'   => '_softkom_email',
					'value' => $email,
				),
			),
		)
	);

	return $ids ? (int) $ids[0] : 0;
}

/**
 * Record prospect conversion intent from the results CTA.
 */
function softkom_public_acquisition_track_conversion() {
	check_ajax_referer( 'softkom_public_conversion', 'nonce' );

	$email = isset( $_POST['email'] ) && is_scalar( $_POST['email'] )
		? sanitize_email( wp_unslash( (string) $_POST['email'] ) )
		: '';
	$event = isset( $_POST['event'] ) && is_scalar( $_POST['event'] )
		? sanitize_key( wp_unslash( (string) $_POST['event'] ) )
		: '';
	$industry = isset( $_POST['industry'] ) && is_scalar( $_POST['industry'] )
		? sanitize_key( wp_unslash( (string) $_POST['industry'] ) )
		: 'softkom';

	$allowed = array( 'strategy_call_clicked' );
	if ( ! is_email( $email ) || ! in_array( $event, $allowed, true ) ) {
		wp_send_json_error( array( 'message' => 'Invalid conversion event.' ), 400 );
	}

	$lead_id = softkom_public_acquisition_find_lead_by_email( $email );
	if ( ! $lead_id ) {
		wp_send_json_error( array( 'message' => 'Lead not found.' ), 404 );
	}

	update_post_meta( $lead_id, '_softkom_last_conversion_event', $event );
	update_post_meta( $lead_id, '_softkom_last_conversion_time_gmt', current_time( 'mysql', true ) );
	update_post_meta( $lead_id, '_softkom_conversion_industry', $industry );
	update_post_meta( $lead_id, '_softkom_strategy_call_clicked', 'yes' );
	softkom_public_acquisition_activity( $lead_id, $event, '', $industry );

	wp_send_json_success( array( 'recorded' => true ) );
}
add_action( 'wp_ajax_softkom_public_conversion', 'softkom_public_acquisition_track_conversion' );
add_action( 'wp_ajax_nopriv_softkom_public_conversion', 'softkom_public_acquisition_track_conversion' );

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

	$strong_buyer        = ( $commercial >= 75 && $intent >= 85 && $overall >= 70 );
	$high_value_ai_buyer = ( $commercial >= 70 && $intent >= 90 && $ai >= 75 );

	if ( ! $strong_buyer && ! $high_value_ai_buyer ) {
		return;
	}

	$old_temperature = (string) get_post_meta( $lead_id, '_softkom_lead_temperature', true );
	update_post_meta( $lead_id, '_softkom_lead_temperature', 'HOT' );
	update_post_meta( $lead_id, '_softkom_fast_track_reason', 'high-commercial-fit-and-purchase-intent' );

	softkom_public_acquisition_activity( $lead_id, 'lead_fast_tracked', $old_temperature, 'HOT' );

	if ( function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) ) {
		softkom_v3_auto_pipeline_hot_lead( $lead_id, $result, $security );
	}
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_fast_track', 19, 3 );

/**
 * Always record the completed assessment in Lead Activity.
 */
function softkom_public_acquisition_record_submission( $lead_id ) {
	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	$stage = (string) get_post_meta( $lead_id, '_softkom_pipeline_stage', true );
	$temp  = (string) get_post_meta( $lead_id, '_softkom_lead_temperature', true );

	softkom_public_acquisition_activity(
		$lead_id,
		'assessment_submitted',
		$temp,
		$stage ? $stage : 'new'
	);
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_record_submission', 90, 1 );

/**
 * Alert Softkom immediately when an assessment creates a sales-eligible lead.
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
	$mrr         = (float) get_post_meta( $lead_id, '_softkom_estimated_mrr', true );

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
	softkom_public_acquisition_activity( $lead_id, $sent ? 'internal_notification_sent' : 'internal_notification_failed', '', $admin_email );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_notify', 100, 3 );

/**
 * Send a simple prospect acknowledgement after a successful submission.
 */
function softkom_public_acquisition_acknowledge( $lead_id ) {
	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	if ( get_post_meta( $lead_id, '_softkom_prospect_ack_sent', true ) ) {
		return;
	}

	$email = sanitize_email( get_post_meta( $lead_id, '_softkom_email', true ) );
	if ( ! is_email( $email ) ) {
		return;
	}

	$first = sanitize_text_field( get_post_meta( $lead_id, '_softkom_first_name', true ) );
	$name  = $first ? $first : 'there';

	$subject = 'Your Softkom Business Systems & AI Readiness Assessment';
	$body = implode(
		"\n",
		array(
			'Hi ' . $name . ',',
			'',
			'Thanks for completing the Softkom Business Systems & AI Readiness Assessment.',
			'',
			'Your results have been recorded. If you would like help turning the findings into a practical systems, automation or AI improvement plan, you can book a strategy conversation with Softkom.',
			'',
			'Book a strategy conversation: ' . add_query_arg( array( 'source' => 'assessment-follow-up' ), home_url( '/contact/' ) ),
			'',
			'Regards,',
			'Softkom Solutions',
		)
	);

	$sent = wp_mail( $email, $subject, $body );
	update_post_meta( $lead_id, '_softkom_prospect_ack_sent', $sent ? 'yes' : 'failed' );
	update_post_meta( $lead_id, '_softkom_prospect_ack_time_gmt', current_time( 'mysql', true ) );
	softkom_public_acquisition_activity( $lead_id, $sent ? 'prospect_ack_sent' : 'prospect_ack_failed', '', $email );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_public_acquisition_acknowledge', 110, 1 );
