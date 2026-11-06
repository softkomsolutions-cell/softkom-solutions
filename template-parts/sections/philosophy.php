<?php
/**
 * Section: philosophy — alternating storytelling.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'philosophy';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'A Different Way of Building Software';
$body  = isset( $body ) ? $body : 'Softkom starts with understanding markets — not building feature lists.';
?>
<section class="section<?php echo $muted ? ' section-muted' : ''; ?>" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <div class="sk-philosophy-head sk-reveal">
      <p class="eyebrow">Philosophy</p>
      <h2 class="sk-display sk-display--wide"><?php echo esc_html( $title ); ?></h2>
      <p class="lead sk-lead-narrow"><?php echo esc_html( $body ); ?></p>
    </div>
    <div class="sk-philosophy">
      <?php foreach ( softkom_v3_data_philosophy() as $i => $block ) : ?>
        <article class="sk-philosophy-block sk-reveal<?php echo 1 === ( $i % 2 ) ? ' sk-philosophy-block--reverse' : ''; ?>">
          <div class="sk-philosophy-copy">
            <span class="sk-philosophy-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
            <h3 class="sk-display-sm"><?php echo esc_html( $block['title'] ); ?></h3>
            <p><?php echo esc_html( $block['body'] ); ?></p>
          </div>
          <div class="sk-philosophy-media">
            <?php echo softkom_v3_graphic_product_ui( $block['visual'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
