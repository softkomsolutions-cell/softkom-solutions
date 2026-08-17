<?php
/**
 * Component: section-head.
 *
 * Args: title, body (plain text; blank lines become separate paragraphs), note (optional call-out).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $title ) ? $title : '';
$body  = isset( $body ) ? $body : '';
$note  = isset( $note ) ? $note : '';
?>
<div class="section-head">
  <?php if ( $title ) : ?>
    <h2 class="sk-display"><?php echo esc_html( $title ); ?></h2>
  <?php endif; ?>
  <?php
  if ( $body ) {
	$paragraphs = preg_split( "/\n\s*\n/", trim( $body ) );
	if ( false === $paragraphs ) {
		$paragraphs = array( $body );
	}
	foreach ( $paragraphs as $paragraph ) {
		$paragraph = trim( $paragraph );
		if ( '' === $paragraph ) {
			continue;
		}
		echo '<p>' . esc_html( $paragraph ) . '</p>';
	}
  }
  ?>
  <?php if ( $note ) : ?>
    <aside class="sk-callout" role="note">
      <p><?php echo esc_html( $note ); ?></p>
    </aside>
  <?php endif; ?>
</div>
