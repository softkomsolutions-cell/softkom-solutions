<?php
/**
 * Section: Softkom products.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id         = isset( $id ) ? $id : 'products';
$muted      = isset( $muted ) ? (bool) $muted : false;
$show_links = ! empty( $show_links );
$title      = isset( $title ) ? $title : 'Softkom products (in development)';
$body       = isset( $body ) ? $body : 'Internal product lines Softkom is building — clearly marked so they are never confused with client delivery.';
$note       = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--2">';
foreach ( softkom_v3_data_products() as $item ) {
	$args = array(
		'variant' => 'project',
		'title'   => $item['title'],
		'body'    => $item['body'],
		'pills'   => $item['pills'],
	);
	if ( $show_links && ! empty( $item['url'] ) ) {
		$args['link_label'] = ! empty( $item['cta'] ) ? $item['cta'] . ' →' : 'View product →';
		$args['link_url']   = $item['url'];
	}
	echo softkom_v3_component( 'card', $args );
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
