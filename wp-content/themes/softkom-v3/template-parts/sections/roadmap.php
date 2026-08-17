<?php
/**
 * Section: premium product roadmap timeline.
 *
 * Extensible — add nodes in softkom_v3_data_roadmap() without redesigning IA.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'roadmap';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Product Roadmap';
$body  = isset( $body ) ? $body : 'A clear path from platforms shipping today to the Softkom ecosystem ahead.';
$nodes = softkom_v3_data_roadmap();
?>
<section class="section<?php echo $muted ? ' section-muted' : ''; ?>" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <?php
    echo softkom_v3_component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      'section-head',
      array(
        'title' => $title,
        'body'  => $body,
      )
    );
    ?>
    <ol class="sk-roadmap sk-reveal" aria-label="Softkom product roadmap">
      <?php foreach ( $nodes as $i => $node ) : ?>
        <li class="sk-roadmap-node sk-roadmap-node--<?php echo esc_attr( $node['status'] ); ?>">
          <?php if ( $i > 0 ) : ?>
            <span class="sk-roadmap-rail" aria-hidden="true"></span>
          <?php endif; ?>
          <span class="sk-roadmap-dot" aria-hidden="true">
            <span class="sk-roadmap-index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
          </span>
          <?php if ( ! empty( $node['url'] ) && 'active' === $node['status'] ) : ?>
            <a class="sk-roadmap-card" href="<?php echo esc_url( $node['url'] ); ?>">
              <strong><?php echo esc_html( $node['title'] ); ?></strong>
              <span><?php echo esc_html( $node['body'] ); ?></span>
            </a>
          <?php else : ?>
            <div class="sk-roadmap-card">
              <strong><?php echo esc_html( $node['title'] ); ?></strong>
              <span><?php echo esc_html( $node['body'] ); ?></span>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
