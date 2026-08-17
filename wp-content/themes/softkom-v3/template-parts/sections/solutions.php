<?php
/**
 * Section: solutions grid.
 * Args: show_links (bool), muted (bool), id, title, body, note.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_links = ! empty( $show_links );
$show_tags  = ! isset( $show_tags ) || $show_tags;
$muted      = isset( $muted ) ? (bool) $muted : true;
$id         = isset( $id ) ? $id : 'solutions';
$limit      = isset( $limit ) ? (int) $limit : 0;
$title      = isset( $title ) ? $title : 'How Softkom approaches the work';
$body       = isset( $body ) ? $body : "Softkom is a systems partner for operators who need reliability, visibility and room to scale.\n\nEach offering starts from a business problem — Softkom maps how work moves, then builds or integrates only what earns its place.";
$note       = isset( $note ) ? $note : '';
$ids_filter = isset( $ids ) && is_array( $ids ) ? $ids : array();

$items = softkom_v3_data_solutions();
if ( $ids_filter ) {
	$filtered = array();
	foreach ( $items as $item ) {
		if ( in_array( $item['id'], $ids_filter, true ) ) {
			$filtered[] = $item;
		}
	}
	$items = $filtered;
}
if ( $limit > 0 ) {
	$items = array_slice( $items, 0, $limit );
}

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--4">';
foreach ( $items as $item ) {
	$args = array(
		'id'       => $item['id'],
		'icon_svg' => softkom_v3_icon( $item['icon'] ),
		'title'    => $item['title'],
		'outcome'  => $item['outcome'],
		'body'     => $item['body'],
	);
	if ( $show_tags ) {
		$args['tags'] = $item['tags'];
	}
	if ( $show_links ) {
		$has_depth = function_exists( 'softkom_v3_service_depth_slugs' )
			&& in_array( $item['id'], softkom_v3_service_depth_slugs(), true );
		$args['link_label'] = $has_depth ? 'Decision guide →' : 'Learn more →';
		$args['link_url']   = $item['url'];
	}
	echo softkom_v3_component( 'card', $args );
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component(
	'section',
	array(
		'id'      => $id,
		'muted'   => $muted,
		'content' => $content,
	)
);
