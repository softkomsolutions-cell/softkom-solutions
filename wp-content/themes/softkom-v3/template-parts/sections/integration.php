<?php
/**
 * Section: Integration ecosystem (Services support).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'integration-ecosystem';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Connect the tools you keep';
$body  = isset( $body ) ? $body : "Softkom designs an integration fabric around the systems already carrying your operations — then extends only where it creates control.\n\nStart with the handoffs that create the most re-entry and error.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo softkom_v3_graphic_integration();
echo '<p class="section-foot"><a class="link-more" href="/contact/#strategy-call">Map your integration priorities →</a></p>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
