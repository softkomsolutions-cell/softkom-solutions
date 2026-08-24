<?php
/**
 * Softkom WordPress AJAX compatibility bootstrap.
 *
 * Some frontend bundles (including the notifications UI) expect the core
 * `wp.ajax` helper to exist. WordPress exposes that helper through `wp-util`,
 * but third-party/generated bundles do not always declare the dependency.
 *
 * Loading the registered core handle early keeps the dependency explicit and
 * prevents race/order failures such as "WordPress ajax utilities not available".
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure WordPress AJAX utilities are available to frontend bundles.
 *
 * `wp-util` provides window.wp.ajax and pulls its own core dependencies.
 */
function softkom_enqueue_wp_ajax_utilities() {
	wp_enqueue_script( 'wp-util' );
}
add_action( 'wp_enqueue_scripts', 'softkom_enqueue_wp_ajax_utilities', 1 );
