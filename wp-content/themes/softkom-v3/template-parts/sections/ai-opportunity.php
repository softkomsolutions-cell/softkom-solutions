<?php
/**
 * Section: AI Opportunity Map™ + delivery lifecycle (Services support).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'ai-opportunity';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Where AI belongs — and where it does not';
$body  = isset( $body ) ? $body : "Softkom’s AI Opportunity Map™ keeps intelligence controlled. Delivery follows a clear lifecycle so foundations come before automation.\n\nIf a process is unclear, Softkom fixes that first — automating confusion only scales the confusion.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-framework-board sk-framework-board--2">';
echo softkom_v3_graphic_ai_map();
echo softkom_v3_graphic_delivery();
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
