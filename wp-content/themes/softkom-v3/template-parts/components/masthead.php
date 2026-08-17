<?php
/**
 * Component: masthead — page and home-split variants (one component).
 *
 * Args: variant (page|split), eyebrow, title, lead, primary_label, primary_url,
 *       secondary_label, secondary_url, media (html string for split visual).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant         = isset( $variant ) ? $variant : 'page';
$eyebrow         = isset( $eyebrow ) ? $eyebrow : '';
$title           = isset( $title ) ? $title : '';
$lead            = isset( $lead ) ? $lead : '';
$primary_label   = isset( $primary_label ) ? $primary_label : 'Book a Discovery Call';
$primary_url     = isset( $primary_url ) ? $primary_url : '/contact/#discovery';
$secondary_label = isset( $secondary_label ) ? $secondary_label : '';
$secondary_url   = isset( $secondary_url ) ? $secondary_url : '';
$media           = isset( $media ) ? $media : '';

$mod = ( 'split' === $variant ) ? ' sk-masthead--split sk-masthead--hero' : '';
?>
<section class="sk-masthead sk-surface<?php echo esc_attr( $mod ); ?>">
  <?php if ( 'split' === $variant ) : ?>
    <div class="sk-hero-ambient" aria-hidden="true">
      <span class="sk-hero-orb sk-hero-orb--a"></span>
      <span class="sk-hero-orb sk-hero-orb--b"></span>
      <span class="sk-hero-orb sk-hero-orb--c"></span>
      <span class="sk-hero-beam"></span>
      <span class="sk-hero-grid"></span>
    </div>
  <?php endif; ?>
  <div class="container<?php echo ( 'split' === $variant ) ? ' sk-masthead-grid' : ''; ?>">
    <div class="sk-masthead-copy sk-hero-enter">
      <?php if ( $eyebrow ) : ?>
        <p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
      <?php endif; ?>
      <?php if ( $title ) : ?>
        <h1><?php echo esc_html( $title ); ?></h1>
      <?php endif; ?>
      <?php if ( $lead ) : ?>
        <p class="lead"><?php echo esc_html( $lead ); ?></p>
      <?php endif; ?>
      <?php
      echo softkom_v3_component(
        'cta-row',
        array(
          'primary_label'   => $primary_label,
          'primary_url'     => $primary_url,
          'secondary_label' => $secondary_label,
          'secondary_url'   => $secondary_url,
        )
      );
      ?>
    </div>
    <?php if ( 'split' === $variant && $media ) : ?>
      <div class="hero-visual sk-hero-enter sk-hero-enter--delay" aria-hidden="true">
        <?php echo $media; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    <?php endif; ?>
  </div>
</section>
