<?php
/**
 * Stabilize Softkom Campaign admin redirects.
 *
 * Some live admin plugin combinations leave the browser on a blank response
 * after publishing/updating the softkom_campaign post type even though the
 * campaign is saved successfully. Force a clean redirect back to the campaign
 * edit screen and avoid the problematic default post-save message redirect.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_campaign_admin_redirect( $location, $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'softkom_campaign' !== get_post_type( $post_id ) ) {
		return $location;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $location;
	}

	return add_query_arg(
		array(
			'post'                   => $post_id,
			'action'                 => 'edit',
			'softkom_campaign_saved' => '1',
		),
		admin_url( 'post.php' )
	);
}
add_filter( 'redirect_post_location', 'softkom_campaign_admin_redirect', 999, 2 );

function softkom_campaign_admin_saved_notice() {
	if ( ! is_admin() || empty( $_GET['softkom_campaign_saved'] ) || empty( $_GET['post'] ) ) {
		return;
	}

	$post_id = absint( $_GET['post'] );
	if ( ! $post_id || 'softkom_campaign' !== get_post_type( $post_id ) ) {
		return;
	}

	echo '<div class="notice notice-success is-dismissible"><p><strong>Softkom campaign saved successfully.</strong></p></div>';
}
add_action( 'admin_notices', 'softkom_campaign_admin_saved_notice' );
