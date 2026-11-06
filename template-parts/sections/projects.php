<?php
/**
 * Section: projects list.
 * Args: limit, show_technologies, show_links, variant (full|teaser), muted, id, title, body.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$limit              = isset( $limit ) ? (int) $limit : 0;
$show_technologies  = ! isset( $show_technologies ) || $show_technologies;
$show_links         = ! empty( $show_links );
$variant            = isset( $variant ) ? $variant : 'full';
$muted              = isset( $muted ) ? (bool) $muted : true;
$id                 = isset( $id ) ? $id : 'projects';
$title              = isset( $title ) ? $title : 'Featured client projects';
$body               = isset( $body ) ? $body : "Softkom delivery for client organisations — labelled Client Project throughout.\n\nSoftkom does not invent ROI percentages or fabricate delivery metrics.";
$note               = isset( $note ) ? $note : '';

$items = softkom_v3_data_projects();
if ( $limit > 0 ) {
	$items = array_slice( $items, 0, $limit );
}

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );

if ( 'teaser' === $variant ) {
	echo '<div class="sk-grid sk-grid--3">';
	foreach ( $items as $item ) {
		$teaser_body = '';
		if ( ! empty( $item['facts'] ) && is_array( $item['facts'] ) ) {
			foreach ( $item['facts'] as $fact ) {
				if ( isset( $fact['label'] ) && in_array( $fact['label'], array( 'Challenge', 'Work delivered' ), true ) ) {
					$teaser_body = $fact['value'];
					break;
				}
			}
			if ( ! $teaser_body && isset( $item['facts'][2]['value'] ) ) {
				$teaser_body = $item['facts'][2]['value'];
			}
		}
		echo softkom_v3_component(
			'card',
			array(
				'pills'       => $item['pills'],
				'title'       => $item['title'],
				'body'        => $teaser_body,
				'link_label'  => 'Review selected work →',
				'link_url'    => '/case-studies/',
			)
		);
	}
	echo '</div>';
} else {
	echo '<div class="sk-stack">';
	foreach ( $items as $item ) {
		$facts = $item['facts'];
		if ( ! $show_technologies ) {
			$facts = array_values(
				array_filter(
					$facts,
					function ( $f ) {
						return 'Technologies' !== $f['label'];
					}
				)
			);
		}
		$args = array(
			'variant' => 'project',
			'title'   => $item['title'],
			'pills'   => $item['pills'],
			'facts'   => $facts,
			'visual'  => $item['visual'],
		);
		if ( $show_links ) {
			$args['link_label'] = 'View project →';
			$args['link_url']   = $item['url'];
		}
		echo softkom_v3_component( 'card', $args );
	}
	echo '</div>';
}
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
