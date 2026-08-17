<?php
/**
 * Section: how to choose / paths (3 cards).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'how-to-choose';
$title = isset( $title ) ? $title : 'How to choose a path';
$body  = isset( $body ) ? $body : "Most engagements start in one of three places. Softkom helps you pick the right entry point — then designs the system around your operating reality.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( softkom_v3_data_paths() as $item ) {
	echo softkom_v3_component(
		'card',
		array(
			'icon_svg' => softkom_v3_icon( $item['icon'] ),
			'title'    => $item['title'],
			'body'     => $item['body'],
		)
	);
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
