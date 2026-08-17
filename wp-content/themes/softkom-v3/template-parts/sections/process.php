<?php
/**
 * Section: delivery process.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'delivery-process';
$title = isset( $title ) ? $title : 'Our delivery process';
$body  = isset( $body ) ? $body : 'A simple, professional path from discovery to ongoing support.';
$steps = array( 'Discovery', 'Process Mapping', 'UX Design', 'Development', 'Testing', 'Deployment', 'Support' );

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body ) );
echo '<div class="process-flow" aria-label="Delivery process">';
$last = count( $steps ) - 1;
foreach ( $steps as $i => $step ) {
	echo '<div class="process-step">' . esc_html( $step ) . '</div>';
	if ( $i < $last ) {
		echo '<span class="process-arrow" aria-hidden="true">↓</span>';
	}
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
