<?php
/**
 * Section: location & contact cards.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'location';
$title = isset( $title ) ? $title : 'Location & contact';
$body  = isset( $body ) ? $body : "Softkom is based in Glensan, Johannesburg. Reach Softkom by email, phone or WhatsApp — Softkom responds to serious enquiries with a practical next step.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
echo softkom_v3_component( 'card', array( 'title' => 'Email', 'body' => 'info@softkomsolutions.com', 'link_label' => 'Email Softkom →', 'link_url' => 'mailto:info@softkomsolutions.com' ) );
echo softkom_v3_component( 'card', array( 'title' => 'Phone', 'body' => '+27 74 993 3805', 'link_label' => 'Call Softkom →', 'link_url' => 'tel:+27749933805' ) );
echo softkom_v3_component( 'card', array( 'title' => 'Office', 'body' => 'Glensan, Johannesburg, South Africa' ) );
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
