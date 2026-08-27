<?php
/**
 * Softkom sales notification routing.
 *
 * Keeps lead and strategy-call alerts independent from WordPress's generic
 * Administration Email Address, which may require a confirmation workflow.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the mailbox that should receive Softkom sales alerts.
 */
function softkom_sales_notification_email() {
	$email = apply_filters( 'softkom_sales_notification_email', 'info@softkomsolutions.com' );
	$email = sanitize_email( $email );

	return is_email( $email ) ? $email : '';
}

/**
 * Route only Softkom sales-alert messages to the dedicated sales mailbox.
 * Prospect acknowledgements and unrelated WordPress email remain untouched.
 */
function softkom_route_sales_notifications( $args ) {
	if ( ! is_array( $args ) ) {
		return $args;
	}

	$subject = isset( $args['subject'] ) ? (string) $args['subject'] : '';
	$is_sales_alert = 0 === strpos( $subject, '[Softkom Lead]' )
		|| 0 === strpos( $subject, '[Softkom Strategy Call]' );

	if ( ! $is_sales_alert ) {
		return $args;
	}

	$email = softkom_sales_notification_email();
	if ( $email ) {
		$args['to'] = $email;
	}

	return $args;
}
add_filter( 'wp_mail', 'softkom_route_sales_notifications', 20, 1 );
