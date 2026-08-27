<?php
/**
 * Softkom direct strategy-call request endpoint.
 *
 * Lets an assessment prospect request follow-up directly from the results
 * screen, without depending on the WordPress contact-page form.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the newest Softkom lead for an email address.
 */
function softkom_strategy_request_find_lead( $email ) {
	if ( function_exists( 'softkom_public_acquisition_find_lead_by_email' ) ) {
		return softkom_public_acquisition_find_lead_by_email( $email );
	}

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
 * Add an activity item without requiring the public-acquisition helper.
 */
function softkom_strategy_request_activity( $lead_id, $event, $to = '' ) {
	if ( function_exists( 'softkom_public_acquisition_activity' ) ) {
		softkom_public_acquisition_activity( $lead_id, $event, '', $to );
		return;
	}

	$history = get_post_meta( $lead_id, '_softkom_pipeline_history', true );
	if ( ! is_array( $history ) ) {
		$history = array();
	}

	$history[] = array(
		'timestamp_gmt' => current_time( 'mysql', true ),
		'event'         => sanitize_key( $event ),
		'from'          => '',
		'to'            => sanitize_text_field( $to ),
		'user_id'       => 0,
	);

	update_post_meta( $lead_id, '_softkom_pipeline_history', $history );
}

/**
 * Handle a direct strategy-call request from assessment results.
 */
function softkom_strategy_request_submit() {
	check_ajax_referer( 'softkom_public_conversion', 'nonce' );

	$email = isset( $_POST['email'] ) && is_scalar( $_POST['email'] )
		? sanitize_email( wp_unslash( (string) $_POST['email'] ) )
		: '';
	$industry = isset( $_POST['industry'] ) && is_scalar( $_POST['industry'] )
		? sanitize_key( wp_unslash( (string) $_POST['industry'] ) )
		: 'softkom';

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'A valid email address is required.' ), 400 );
	}

	$lead_id = softkom_strategy_request_find_lead( $email );
	if ( ! $lead_id ) {
		wp_send_json_error( array( 'message' => 'We could not match your assessment. Please try again.' ), 404 );
	}

	$already_requested = 'yes' === get_post_meta( $lead_id, '_softkom_strategy_call_requested', true );

	update_post_meta( $lead_id, '_softkom_strategy_call_requested', 'yes' );
	update_post_meta( $lead_id, '_softkom_strategy_call_requested_time_gmt', current_time( 'mysql', true ) );
	update_post_meta( $lead_id, '_softkom_conversion_industry', $industry );
	update_post_meta( $lead_id, '_softkom_last_conversion_event', 'strategy_call_requested' );
	update_post_meta( $lead_id, '_softkom_last_conversion_time_gmt', current_time( 'mysql', true ) );

	if ( ! $already_requested ) {
		softkom_strategy_request_activity( $lead_id, 'strategy_call_requested', $industry );
	}

	$first       = (string) get_post_meta( $lead_id, '_softkom_first_name', true );
	$last        = (string) get_post_meta( $lead_id, '_softkom_last_name', true );
	$company     = (string) get_post_meta( $lead_id, '_softkom_company', true );
	$temperature = (string) get_post_meta( $lead_id, '_softkom_lead_temperature', true );
	$score       = (int) get_post_meta( $lead_id, '_softkom_score_overall_lead', true );
	$source      = (string) get_post_meta( $lead_id, '_softkom_traffic_source', true );
	$campaign    = (string) get_post_meta( $lead_id, '_softkom_utm_campaign', true );

	$admin_email = sanitize_email( get_option( 'admin_email' ) );
	$mail_sent = true;

	if ( ! $already_requested && is_email( $admin_email ) ) {
		$name = trim( $first . ' ' . $last );
		$subject = sprintf(
			'[Softkom Strategy Call] %s%s',
			$company ? $company : ( $name ? $name : $email ),
			$temperature ? ' - ' . $temperature : ''
		);

		$body = implode(
			"\n",
			array(
				'A prospect has requested a Softkom strategy call from their assessment results.',
				'',
				'Name: ' . ( $name ? $name : '-' ),
				'Company: ' . ( $company ? $company : '-' ),
				'Email: ' . $email,
				'Lead temperature: ' . ( $temperature ? $temperature : '-' ),
				'Lead score: ' . $score . '/100',
				'Industry profile: ' . ( $industry ? $industry : 'softkom' ),
				'Traffic source: ' . ( $source ? $source : 'direct/unknown' ),
				'Campaign: ' . ( $campaign ? $campaign : '-' ),
				'',
				'Open lead: ' . admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ),
			)
		);

		$mail_sent = wp_mail( $admin_email, $subject, $body );
		update_post_meta( $lead_id, '_softkom_strategy_request_notification', $mail_sent ? 'sent' : 'failed' );
		softkom_strategy_request_activity(
			$lead_id,
			$mail_sent ? 'strategy_request_notification_sent' : 'strategy_request_notification_failed',
			$admin_email
		);
	}

	wp_send_json_success(
		array(
			'recorded'          => true,
			'already_requested' => $already_requested,
			'notification_sent' => $mail_sent,
			'message'           => 'Your strategy call request has been received. Softkom will contact you shortly.',
		)
	);
}
add_action( 'wp_ajax_softkom_strategy_request', 'softkom_strategy_request_submit' );
add_action( 'wp_ajax_nopriv_softkom_strategy_request', 'softkom_strategy_request_submit' );
