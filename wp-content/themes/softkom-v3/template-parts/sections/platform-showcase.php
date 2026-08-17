<?php
/**
 * Section: platform showcase — product launch cards.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'platforms';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Our Platforms';
$body  = isset( $body ) ? $body : '';
$note  = isset( $note ) ? $note : '';
?>
<section class="section<?php echo $muted ? ' section-muted' : ''; ?>" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <?php
    echo softkom_v3_component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      'section-head',
      array(
        'title' => $title,
        'body'  => $body,
        'note'  => $note,
      )
    );
    ?>
    <div class="sk-platform-grid">
      <?php foreach ( softkom_v3_data_products() as $product ) : ?>
        <?php
        $is_link = ! empty( $product['url'] );
        $tag     = $is_link ? 'a' : 'div';
        $href    = $is_link ? ' href="' . esc_url( $product['url'] ) . '"' : '';
        ?>
        <<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          class="sk-platform-card sk-platform-card--<?php echo esc_attr( $product['accent'] ); ?> sk-reveal"
          <?php echo $href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        >
          <div class="sk-platform-card-media">
            <?php echo softkom_v3_graphic_product_ui( $product['id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
          <div class="sk-platform-card-body">
            <div class="sk-platform-card-meta">
              <span class="eyebrow eyebrow--tight"><?php echo esc_html( $product['eyebrow'] ); ?></span>
              <span class="sk-status sk-status--<?php echo esc_attr( $product['status'] ); ?>"><?php echo esc_html( $product['status_label'] ); ?></span>
            </div>
            <h3 class="sk-display-sm"><?php echo esc_html( $product['title'] ); ?></h3>
            <p class="sk-platform-tagline"><?php echo esc_html( $product['tagline'] ); ?></p>
            <p><?php echo esc_html( $product['body'] ); ?></p>
            <?php if ( ! empty( $product['cta'] ) ) : ?>
              <span class="sk-platform-cta"><?php echo esc_html( $product['cta'] ); ?> →</span>
            <?php endif; ?>
          </div>
        </<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>
