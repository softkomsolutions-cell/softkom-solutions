<?php
/**
 * Component: cta-row.
 *
 * Args: primary_label, primary_url, secondary_label, secondary_url,
 *       primary_class (default sk-btn-primary), secondary_class (default sk-btn-secondary).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$primary_label   = isset( $primary_label ) ? $primary_label : 'Book a Strategy Call';
$primary_url     = isset( $primary_url ) ? $primary_url : '/contact/#strategy-call';
$primary_class   = isset( $primary_class ) ? $primary_class : 'sk-btn-primary';
$secondary_label = isset( $secondary_label ) ? $secondary_label : '';
$secondary_url   = isset( $secondary_url ) ? $secondary_url : '';
$secondary_class = isset( $secondary_class ) ? $secondary_class : 'sk-btn-secondary';
?>
<div class="cta-row">
  <a class="sk-btn <?php echo esc_attr( $primary_class ); ?>" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_label ); ?></a>
  <?php if ( $secondary_label && $secondary_url ) : ?>
    <a class="sk-btn <?php echo esc_attr( $secondary_class ); ?>" href="<?php echo esc_url( $secondary_url ); ?>"><?php echo esc_html( $secondary_label ); ?></a>
  <?php endif; ?>
</div>
