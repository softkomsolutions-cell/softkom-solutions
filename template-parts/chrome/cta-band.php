<?php
/**
 * Softkom V3 closing CTA band — thin wrapper to library component.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $sk_cta_title ) ? $sk_cta_title : ( isset( $title ) ? $title : null );
$body  = isset( $sk_cta_body ) ? $sk_cta_body : ( isset( $body ) ? $body : null );
$args  = array();
if ( $title ) {
	$args['title'] = $title;
}
if ( $body ) {
	$args['body'] = $body;
}
echo softkom_v3_component( 'cta-band', $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
