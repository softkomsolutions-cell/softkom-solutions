<?php
/**
 * Section: why Softkom.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'why-softkom';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Why businesses choose Softkom';
$body  = isset( $body ) ? $body : "Practical reasons Softkom can defend — not unverifiable statistics.\n\nSoftkom publishes how Softkom works and what you should expect from delivery and support.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( softkom_v3_data_why() as $item ) {
	echo softkom_v3_component( 'card', array( 'title' => $item['title'], 'body' => $item['body'] ) );
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
