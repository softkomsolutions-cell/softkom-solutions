<?php
/**
 * Section: insights topic chips.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'topics';
$title = isset( $title ) ? $title : 'Topics Softkom writes about';
$body  = isset( $body ) ? $body : "Editorial pillars for Softkom Insights — the same themes that guide Softkom’s delivery work.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="topic-chips" aria-label="Insight topics">';
foreach ( softkom_v3_data_topics() as $topic ) {
	echo '<span class="topic-chip">' . esc_html( $topic ) . '</span>';
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
