<?php
/**
 * Section: technologies grid.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'technologies';
$muted = isset( $muted ) ? (bool) $muted : true;
$title = isset( $title ) ? $title : 'Technologies we use';
$body  = isset( $body ) ? $body : 'Modern tools Softkom genuinely works with — not partnership theatre.';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( softkom_v3_data_technologies() as $item ) {
	echo softkom_v3_component(
		'card',
		array(
			'mark'  => $item['mark'],
			'title' => $item['title'],
			'body'  => $item['body'],
		)
	);
}
echo '</div>';
echo '<p class="section-foot"><a class="link-more" href="/services/">Explore Softkom solutions →</a></p>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
