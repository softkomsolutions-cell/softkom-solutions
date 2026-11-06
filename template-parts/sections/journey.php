<?php
/**
 * Section: Softkom Transformation Framework™ — Transformation Journey™.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'transformation-journey';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'How Softkom sequences the work';
$body  = isset( $body ) ? $body : "Softkom Transformation Framework™ is Softkom’s complete methodology. Inside it, the Transformation Journey™ is the eight-stage client sequence Softkom uses from how work moves today to a next step leadership can approve.\n\nDiagnose before prescribing tools. Integrate before automate. Keep people accountable where judgment matters. Project execution then follows the Delivery Lifecycle (discovery through support).";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<p class="sk-journey-kicker">Softkom Transformation Framework™ · Transformation Journey™</p>';
echo '<div class="sk-journey" role="list">';
foreach ( softkom_v3_data_journey_stages() as $stage ) {
	echo '<div class="sk-journey-stage" role="listitem" tabindex="0">';
	echo '<span class="sk-diagram-num">' . esc_html( $stage[0] ) . '</span>';
	echo '<strong>' . esc_html( $stage[1] ) . '</strong>';
	echo '<p class="sk-journey-hint">' . esc_html( $stage[2] ) . '</p>';
	echo '</div>';
}
echo '</div>';
echo '<p class="section-foot"><a class="link-more" href="/contact/#strategy-call">Start with stage 03 — Assessment →</a></p>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
