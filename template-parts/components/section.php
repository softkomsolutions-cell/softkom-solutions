<?php
/**
 * Component: section — wrapper shell.
 *
 * Args: id, muted (bool), reveal (bool), content (html string).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id      = isset( $id ) ? $id : '';
$muted   = ! empty( $muted );
$reveal  = ! isset( $reveal ) || $reveal;
$content = isset( $content ) ? $content : '';

$classes = array( 'section' );
if ( $muted ) {
	$classes[] = 'section-muted';
}
if ( $reveal ) {
	$classes[] = 'sk-reveal';
}
?>
<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
  <div class="container">
    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  </div>
</section>
