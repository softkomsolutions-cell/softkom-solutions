<?php
/**
 * Softkom homepage conversion repair.
 *
 * Turns the legacy Elementor consultation form into a persisted Softkom lead,
 * emails the sales inboxes, and exposes the AJAX endpoint used by the homepage.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_homepage_conversion_register_lead_type() {
	if ( post_type_exists( 'softkom_lead' ) ) {
		return;
	}

	register_post_type(
		'softkom_lead',
		array(
			'labels' => array(
				'name'          => 'Softkom Leads',
				'singular_name' => 'Softkom Lead',
				'menu_name'     => 'Softkom Leads',
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'supports'            => array( 'title' ),
		)
	);
}

function softkom_homepage_conversion_enqueue() {
	if ( is_admin() || ! is_front_page() ) {
		return;
	}

	$path = WPMU_PLUGIN_DIR . '/softkom-homepage-conversion.js';
	$src  = content_url( '/mu-plugins/softkom-homepage-conversion.js' );

	wp_enqueue_script(
		'softkom-homepage-conversion',
		$src,
		array(),
		is_readable( $path ) ? (string) filemtime( $path ) : '1',
		true
	);

	wp_localize_script(
		'softkom-homepage-conversion',
		'softkomHomepageConversion',
		array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'softkom_homepage_lead' ),
			'assessmentUrl' => add_query_arg(
				array(
					'utm_source'   => 'softkom-homepage',
					'utm_medium'   => 'website',
					'utm_campaign' => 'homepage-conversion',
				),
				home_url( '/assessment/' )
			),
			'calendlyUrl'    => 'https://calendly.com/softkomsolutions/30mins',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_homepage_conversion_enqueue', 80 );

function softkom_homepage_conversion_submit() {
	check_ajax_referer( 'softkom_homepage_lead', 'nonce' );

	$name = isset( $_POST['name'] ) && is_scalar( $_POST['name'] )
		? sanitize_text_field( wp_unslash( (string) $_POST['name'] ) )
		: '';

	$email = isset( $_POST['email'] ) && is_scalar( $_POST['email'] )
		? sanitize_email( wp_unslash( (string) $_POST['email'] ) )
		: '';

	$details = isset( $_POST['details'] ) && is_scalar( $_POST['details'] )
		? sanitize_textarea_field( wp_unslash( (string) $_POST['details'] ) )
		: '';

	$website = isset( $_POST['website'] ) && is_scalar( $_POST['website'] )
		? trim( (string) wp_unslash( $_POST['website'] ) )
		: '';

	if ( '' !== $website ) {
		wp_send_json_success( array( 'received' => true ) );
	}

	if ( '' === $name || ! is_email( $email ) || '' === $details ) {
		wp_send_json_error(
			array( 'message' => 'Please complete your name, email and project details.' ),
			400
		);
	}

	if ( strlen( $details ) < 10 ) {
		wp_send_json_error(
			array( 'message' => 'Please add a little more detail so we can route your enquiry correctly.' ),
			400
		);
	}

	softkom_homepage_conversion_register_lead_type();

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'softkom_lead',
			'post_status' => 'private',
			'post_title'  => sprintf(
				'Homepage enquiry - %s - %s',
				$name,
				current_time( 'Y-m-d H:i' )
			),
		),
		true
	);

	if ( is_wp_error( $lead_id ) ) {
		wp_send_json_error(
			array( 'message' => 'We could not save your enquiry. Please email info@softkomsolutions.com.' ),
			500
		);
	}

	$meta = array(
		'_softkom_first_name'           => $name,
		'_softkom_email'                => $email,
		'_softkom_company'              => '',
		'_softkom_home_project_details' => $details,
		'_softkom_source'               => 'homepage-consultation',
		'_softkom_traffic_source'       => 'website',
		'_softkom_utm_source'           => 'softkom-homepage',
		'_softkom_utm_medium'           => 'website',
		'_softkom_utm_campaign'         => 'homepage-conversion',
		'_softkom_lead_temperature'     => 'WARM',
		'_softkom_pipeline_stage'       => 'new',
		'_softkom_created_at_gmt'       => current_time( 'mysql', true ),
	);

	foreach ( $meta as $key => $value ) {
		update_post_meta( $lead_id, $key, $value );
	}

	$subject = sprintf( '[Softkom Lead] Homepage enquiry - %s', $name );
	$body = implode(
		"\n",
		array(
			'A new enquiry was submitted from the Softkom homepage.',
			'',
			'Name: ' . $name,
			'Email: ' . $email,
			'',
			'Project details:',
			$details,
			'',
			'Open lead: ' . admin_url( 'post.php?post=' . (int) $lead_id . '&action=edit' ),
		)
	);

	$primary_sent = wp_mail( 'info@softkomsolutions.com', $subject, $body );
	$backup_sent  = wp_mail( 'softkomsolutions@gmail.com', $subject, $body );

	update_post_meta( $lead_id, '_softkom_home_notification_primary', $primary_sent ? 'yes' : 'failed' );
	update_post_meta( $lead_id, '_softkom_home_notification_backup', $backup_sent ? 'yes' : 'failed' );

	update_option(
		'softkom_home_lead_last',
		array(
			'lead_id'      => (int) $lead_id,
			'primary_sent' => (bool) $primary_sent,
			'backup_sent'  => (bool) $backup_sent,
			'time_gmt'     => current_time( 'mysql', true ),
		),
		false
	);

	wp_send_json_success(
		array(
			'received'      => true,
			'leadId'        => (int) $lead_id,
			'assessmentUrl' => add_query_arg(
				array(
					'utm_source'   => 'homepage-enquiry',
					'utm_medium'   => 'website',
					'utm_campaign' => 'post-enquiry-assessment',
				),
				home_url( '/assessment/' )
			),
		)
	);
}
add_action( 'wp_ajax_softkom_homepage_lead', 'softkom_homepage_conversion_submit' );
add_action( 'wp_ajax_nopriv_softkom_homepage_lead', 'softkom_homepage_conversion_submit' );
