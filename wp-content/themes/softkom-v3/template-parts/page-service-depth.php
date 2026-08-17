<?php
/**
 * Softkom V3 — dedicated service decision page (RC2.3 P0).
 * Set $softkom_service_slug before include, or via shortcode attribute.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slug = isset( $softkom_service_slug ) ? sanitize_title( (string) $softkom_service_slug ) : '';
$data = $slug ? softkom_v3_data_service_depth( $slug ) : null;

if ( ! $data ) {
	echo '<div class="sk-site"><p class="container">Service page not found.</p></div>';
	return;
}

$cta = isset( $data['cta'] ) && is_array( $data['cta'] ) ? $data['cta'] : array();
?>
<div class="sk-site sk-service-depth">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => isset( $data['eyebrow'] ) ? $data['eyebrow'] : '',
      'title'           => isset( $data['title'] ) ? $data['title'] : '',
      'lead'            => isset( $data['lead'] ) ? $data['lead'] : '',
      'secondary_label' => 'All solutions',
      'secondary_url'   => '/services/',
    )
  );
  ?>
  <nav class="sk-depth-toc section" aria-label="On this page">
    <div class="container">
      <p class="sk-depth-toc-label">On this page</p>
      <ol class="sk-depth-toc-list">
        <?php foreach ( $data['sections'] as $section ) : ?>
          <?php if ( empty( $section['id'] ) || empty( $section['title'] ) ) { continue; } ?>
          <li><a href="#<?php echo esc_attr( $section['id'] ); ?>"><?php echo esc_html( $section['title'] ); ?></a></li>
        <?php endforeach; ?>
      </ol>
    </div>
  </nav>
  <?php
  softkom_v3_section_e(
    'service-depth',
    array(
      'service' => $slug,
    )
  );
  ?>
  <p class="container sk-depth-back">
    <a class="link-more" href="/services/">← Back to Solutions</a>
    · <a class="link-more" href="/contact/#strategy-call">Book a strategy call →</a>
  </p>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title' => isset( $cta['title'] ) ? $cta['title'] : '',
      'body'  => isset( $cta['body'] ) ? $cta['body'] : '',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
