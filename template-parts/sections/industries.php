<?php
/**
 * Section: industries grid — RC2.4 evidence groups.
 *
 * Args:
 * - grouped (bool): split experience vs adjacent (default true on industries page via composer).
 * - group: experience|adjacent|all — when grouped=false, filter single group.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id      = isset( $id ) ? $id : 'industries';
$muted   = isset( $muted ) ? (bool) $muted : false;
$grouped = isset( $grouped ) ? (bool) $grouped : true;
$group   = isset( $group ) ? (string) $group : 'all';
$title   = isset( $title ) ? $title : 'Operating environments Softkom works across';
$body    = isset( $body ) ? $body : "Softkom applies business-systems, integration and automation methods across operational environments. Direct experience varies by sector, so verified delivery environments and adjacent applications are shown separately.";
$note    = isset( $note ) ? $note : '';
$foot_label = isset( $foot_label ) ? $foot_label : 'Discuss an operational problem →';
$foot_url   = isset( $foot_url ) ? $foot_url : softkom_v3_cta_url( 'discuss-problem' );

/**
 * Render one industry card (short form).
 *
 * @param array<string,string> $item Industry row.
 * @return string
 */
$softkom_industry_card = static function ( $item ) {
	$lines = array();
	if ( ! empty( $item['pressure'] ) ) {
		$lines[] = $item['pressure'];
	}
	if ( ! empty( $item['capability'] ) ) {
		$lines[] = $item['capability'];
	}
	if ( ! empty( $item['evidence_status'] ) ) {
		$lines[] = $item['evidence_status'];
	}
	$body = $lines ? implode( ' ', $lines ) : ( isset( $item['body'] ) ? $item['body'] : '' );
	return softkom_v3_component(
		'card',
		array(
			'icon_svg' => softkom_v3_icon( $item['icon'] ),
			'title'    => $item['title'],
			'body'     => $body,
		)
	);
};

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );

if ( $grouped ) {
	$experience = softkom_v3_data_industries_by_group( 'experience' );
	$adjacent   = softkom_v3_data_industries_by_group( 'adjacent' );

	echo '<div class="sk-industry-group" id="experience-backed">';
	echo '<p class="eyebrow eyebrow--tight">Experience-backed environments</p>';
	echo '<h3 class="sk-industry-group__title">Verified delivery environments</h3>';
	echo '<p class="sk-industry-group__lead">Sectors supported by Softkom project records Softkom can defend.</p>';
	echo '<div class="sk-grid sk-grid--4">';
	foreach ( $experience as $item ) {
		echo $softkom_industry_card( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div></div>';

	echo '<div class="sk-industry-group sk-industry-group--adjacent" id="adjacent-environments">';
	echo '<p class="eyebrow eyebrow--tight">Adjacent operational environments</p>';
	echo '<h3 class="sk-industry-group__title">Where methods may apply</h3>';
	echo '<p class="sk-industry-group__lead">The operational patterns below may be relevant to these environments. Their inclusion does not represent a claim of specialist sector experience or completed delivery in every sector.</p>';
	echo '<div class="sk-grid sk-grid--4">';
	foreach ( $adjacent as $item ) {
		echo $softkom_industry_card( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div></div>';
} else {
	$items = ( 'all' === $group ) ? softkom_v3_data_industries() : softkom_v3_data_industries_by_group( $group );
	echo '<div class="sk-grid sk-grid--4">';
	foreach ( $items as $item ) {
		echo $softkom_industry_card( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

if ( $foot_label && $foot_url ) {
	echo '<p class="section-foot"><a class="link-more" href="' . esc_url( $foot_url ) . '">' . esc_html( $foot_label ) . '</a></p>';
}
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
