<?php
/**
 * Softkom V3 — MarketplaceOS product page.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$p = softkom_v3_product_marketplaceos();
?>
<div class="sk-site sk-product-page sk-product-page--marketplaceos">
  <?php softkom_v3_component_e( 'header' ); ?>

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'variant'         => 'split',
      'eyebrow'         => $p['eyebrow'],
      'title'           => $p['title'],
      'lead'            => $p['hero_lead'],
      'primary_label'   => softkom_v3_cta_label( 'book-demo' ),
      'primary_url'     => softkom_v3_cta_url( 'book-demo' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-platforms' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-platforms' ),
      'media'           => softkom_v3_graphic_product_ui( 'marketplaceos' ),
    )
  );
  ?>

  <section class="section" id="challenge">
    <div class="container sk-product-prose sk-reveal">
      <p class="eyebrow">Business Challenge</p>
      <h2 class="sk-display"><?php echo esc_html( $p['challenge']['title'] ); ?></h2>
      <?php foreach ( preg_split( '/\n\n+/', $p['challenge']['body'] ) as $para ) : ?>
        <p class="lead"><?php echo esc_html( $para ); ?></p>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section section-muted" id="solution">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow">Solution</p>
        <h2 class="sk-display"><?php echo esc_html( $p['solution']['title'] ); ?></h2>
        <p class="lead"><?php echo esc_html( $p['solution']['body'] ); ?></p>
      </div>
      <div class="sk-product-shot">
        <?php echo softkom_v3_graphic_platforms_hero(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section" id="modules">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">Platform Modules</p>
        <h2 class="sk-display">Built for how multi-channel ops actually run</h2>
      </div>
      <div class="sk-grid sk-grid--2 sk-modules">
        <?php foreach ( $p['modules'] as $module ) : ?>
          <article class="sk-module-card sk-reveal">
            <h3><?php echo esc_html( $module['title'] ); ?></h3>
            <p><?php echo esc_html( $module['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="ai">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow">AI Features</p>
        <h2 class="sk-display"><?php echo esc_html( $p['ai']['title'] ); ?></h2>
        <p class="lead"><?php echo esc_html( $p['ai']['body'] ); ?></p>
        <ul class="sk-feature-list">
          <?php foreach ( $p['ai']['items'] as $item ) : ?>
            <li><?php echo esc_html( $item ); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="sk-product-shot">
        <?php echo softkom_v3_graphic_product_ui( 'marketplaceos' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section" id="integrations">
    <div class="container sk-reveal">
      <p class="eyebrow">Integrations</p>
      <h2 class="sk-display"><?php echo esc_html( $p['integrations']['title'] ); ?></h2>
      <p class="lead sk-lead-narrow"><?php echo esc_html( $p['integrations']['body'] ); ?></p>
      <ul class="sk-integration-pills">
        <?php foreach ( $p['integrations']['items'] as $item ) : ?>
          <li><?php echo esc_html( $item ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="section section-muted" id="screenshots">
    <div class="container sk-reveal">
      <p class="eyebrow">Product</p>
      <h2 class="sk-display">A clearer operating picture</h2>
      <p class="lead sk-lead-narrow">Interface previews of MarketplaceOS — catalogue, channels and fulfilment control. Production screenshots replace these as the product hardens.</p>
      <div class="sk-shot-row">
        <?php echo softkom_v3_graphic_product_ui( 'marketplaceos' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        <?php echo softkom_v3_graphic_product_ui( 'specialised' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section" id="benefits">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">Benefits</p>
        <h2 class="sk-display">What operators gain</h2>
      </div>
      <div class="sk-pillars">
        <?php foreach ( $p['benefits'] as $i => $benefit ) : ?>
          <article class="sk-pillar sk-reveal">
            <div class="sk-pillar-mark" aria-hidden="true"><span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span></div>
            <h3><?php echo esc_html( $benefit['title'] ); ?></h3>
            <p><?php echo esc_html( $benefit['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="testimonials">
    <div class="container sk-reveal">
      <p class="eyebrow">Testimonials</p>
      <h2 class="sk-display">Early signal</h2>
      <?php foreach ( $p['testimonials'] as $t ) : ?>
        <blockquote class="sk-quote">
          <p>“<?php echo esc_html( $t['quote'] ); ?>”</p>
          <footer>
            <strong><?php echo esc_html( $t['author'] ); ?></strong>
            <span><?php echo esc_html( $t['role'] ); ?></span>
          </footer>
        </blockquote>
      <?php endforeach; ?>
    </div>
  </section>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Book a MarketplaceOS demo',
      'body'          => 'See how MarketplaceOS approaches catalogue, pricing and fulfilment control for multi-channel sellers.',
      'primary_cta'   => 'book-demo',
      'secondary_cta' => 'start-conversation',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
