<?php
/**
 * Section: insights grid — RC2.4 metadata-aware cards.
 *
 * Displays author / dates / reading time only when verified values exist.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'insights';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Featured articles';
$body  = isset( $body ) ? $body : 'Selected pieces on operations, automation and compliance Softkom maintains as published posts.';
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( softkom_v3_data_insights() as $item ) {
	$meta_parts = array();
	if ( ! empty( $item['author'] ) ) {
		$meta_parts[] = $item['author'];
	}
	if ( ! empty( $item['published_at'] ) ) {
		$meta_parts[] = $item['published_at'];
	}
	if ( ! empty( $item['updated_at'] ) ) {
		$meta_parts[] = 'Updated ' . $item['updated_at'];
	}
	if ( ! empty( $item['reading_time_min'] ) ) {
		$meta_parts[] = (int) $item['reading_time_min'] . ' min read';
	}
	$card_body = $item['body'];
	if ( $meta_parts ) {
		$card_body = implode( ' · ', $meta_parts ) . "\n\n" . $card_body;
	}
	echo softkom_v3_component(
		'card',
		array(
			'eyebrow'    => $item['eyebrow'],
			'title'      => $item['title'],
			'body'       => $card_body,
			'link_label' => 'Read article →',
			'link_url'   => $item['url'],
		)
	);
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
