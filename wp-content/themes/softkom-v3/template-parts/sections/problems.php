<?php
/**
 * Section: problems we solve.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'problems';
$title = isset( $title ) ? $title : 'Where growing companies get stuck';
$body  = isset( $body ) ? $body : "Most operators do not lack effort. They lack systems that keep pace with volume, channels and reporting demands.\n\nWhen work lives in email and spreadsheets, every handoff is a delay and every re-entry is a risk.";
$note  = isset( $note ) ? $note : '';

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( softkom_v3_data_problems() as $item ) {
	echo softkom_v3_component(
		'card',
		array(
			'icon_svg'   => softkom_v3_icon( $item['icon'] ),
			'title'      => $item['title'],
			'body'       => $item['body'],
			'link_label' => 'See Softkom’s approach →',
			'link_url'   => $item['url'],
		)
	);
}
echo '</div>';
echo '<p class="section-foot"><a class="link-more" href="/services/">Explore solutions →</a></p>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
