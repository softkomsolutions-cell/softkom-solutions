<?php
/**
 * Section: Systems Maturity Model™ (About / workshops).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'systems-maturity';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Where is the business today?';
$body  = isset( $body ) ? $body : "Softkom’s Systems Maturity Model™ is a calm diagnostic Softkom uses in assessment conversations — not a score used to shame clients.\n\nMost mid-market companies Softkom meets sit between spreadsheet dependence and fragmented tools.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo softkom_v3_graphic_maturity();
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
