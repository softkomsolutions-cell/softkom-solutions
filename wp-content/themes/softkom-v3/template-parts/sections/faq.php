<?php
/**
 * Section: FAQ list.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'faq';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Questions companies ask before building';
$body  = isset( $body ) ? $body : "Straight answers on timeline, ownership, AI, integration, pricing and support — the same points Softkom covers on a strategy call.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-stack sk-stack--measure">';
foreach ( softkom_v3_data_faq() as $item ) {
	echo softkom_v3_component(
		'card',
		array(
			'variant' => 'faq',
			'title'   => $item['title'],
			'body'    => $item['body'],
		)
	);
}
echo '</div>';
echo '<p class="section-foot"><a class="link-more" href="/contact/#strategy-call">Still unsure? Bring your process to a strategy call →</a></p>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
