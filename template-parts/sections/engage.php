<?php
/**
 * Section: engage steps (About how we engage).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'how-we-engage';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Delivery Lifecycle';
$body  = isset( $body ) ? $body : "The Delivery Lifecycle is how Softkom executes a scoped engagement inside Softkom Transformation Framework™: Discovery → Design → Build → Support.\n\nIt is the project path — distinct from the eight-stage Transformation Journey™ Softkom uses to sequence the client conversation from today’s operations to a next step leadership can approve.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--4">';
foreach ( softkom_v3_data_engage() as $item ) {
	echo softkom_v3_component(
		'card',
		array(
			'outcome' => $item['outcome'],
			'title'   => $item['title'],
			'body'    => $item['body'],
		)
	);
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
