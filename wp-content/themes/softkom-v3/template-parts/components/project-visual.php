<?php
/**
 * Component: project-visual — rounded diagram panel (one image approach).
 *
 * Args: title, widths (array of % strings), nodes (array of labels).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = isset( $title ) ? $title : '';
$widths = isset( $widths ) && is_array( $widths ) ? $widths : array( '100%', '78%', '92%', '64%' );
$nodes  = isset( $nodes ) && is_array( $nodes ) ? $nodes : array();
?>
<div class="project-visual" aria-hidden="true">
  <?php if ( $title ) : ?>
    <div class="pv-title"><?php echo esc_html( $title ); ?></div>
  <?php endif; ?>
  <div class="pv-layers">
    <?php foreach ( $widths as $w ) : ?>
      <span style="--pv-w:<?php echo esc_attr( $w ); ?>"></span>
    <?php endforeach; ?>
  </div>
  <?php if ( $nodes ) : ?>
    <div class="pv-arch">
      <?php foreach ( $nodes as $node ) : ?>
        <i><?php echo esc_html( $node ); ?></i>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
