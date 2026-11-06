<?php
/**
 * Section: ecosystem — expanding product portfolio timeline.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'ecosystem';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'An Expanding Portfolio';
$body  = isset( $body ) ? $body : '';
$nodes = softkom_v3_data_ecosystem();
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
    <ol class="sk-ecosystem sk-ecosystem--flow sk-reveal" aria-label="Softkom product ecosystem">
      <?php foreach ( $nodes as $i => $node ) : ?>
        <li class="sk-ecosystem-node sk-ecosystem-node--<?php echo esc_attr( $node['status'] ); ?>">
          <?php if ( $i > 0 ) : ?>
            <span class="sk-ecosystem-connector" aria-hidden="true">
              <span class="sk-ecosystem-arrow"></span>
            </span>
          <?php endif; ?>
          <?php if ( ! empty( $node['url'] ) && 'active' === $node['status'] ) : ?>
            <a class="sk-ecosystem-card" href="<?php echo esc_url( $node['url'] ); ?>">
              <strong><?php echo esc_html( $node['title'] ); ?></strong>
              <span><?php echo esc_html( $node['body'] ); ?></span>
            </a>
          <?php else : ?>
            <div class="sk-ecosystem-card">
              <strong><?php echo esc_html( $node['title'] ); ?></strong>
              <span><?php echo esc_html( $node['body'] ); ?></span>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
