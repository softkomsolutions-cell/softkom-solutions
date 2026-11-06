<?php
/**
 * Section: vision — long-term ambition (concise).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'vision';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Building the next generation of specialised software platforms.';
$body  = isset( $body ) ? $body : 'We are building a growing portfolio of software platforms.';
?>
<section class="section sk-vision-section<?php echo $muted ? ' section-muted' : ''; ?>" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <div class="sk-vision-panel sk-reveal">
      <p class="eyebrow">Vision</p>
      <h2 class="sk-display sk-display--wide"><?php echo esc_html( $title ); ?></h2>
      <?php if ( $body ) : ?>
        <p class="lead sk-lead-narrow"><?php echo esc_html( $body ); ?></p>
      <?php endif; ?>
      <div class="cta-row sk-vision-cta">
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'explore-platforms' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'explore-platforms' ) ); ?></a>
      </div>
    </div>
  </div>
</section>
