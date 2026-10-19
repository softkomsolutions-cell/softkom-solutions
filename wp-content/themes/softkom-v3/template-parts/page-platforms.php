<?php
/**
 * Softkom V3 Products hub.
 *
 * Product content is driven by the shared product data registry.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site sk-platforms">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Platforms',
      'title'           => 'Products we have built for specific business problems',
      'lead'            => 'MarketplaceOS, Product Studio and Brick Alpha started from real operating problems that general-purpose software did not handle well.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'view-marketplaceos' ),
      'secondary_url'   => softkom_v3_cta_url( 'view-marketplaceos' ),
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'roadmap',
    array(
      'id'    => 'roadmap',
      'title' => 'Current products',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha are active Softkom products, each built around a different practical use case.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'ecosystem',
    array(
      'muted' => true,
      'id'    => 'ecosystem',
      'title' => 'Three different products, one engineering base',
      'body'  => 'They share the same strengths we use in client work: systems design, integrations, workflow automation, QA and release management.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'platform-showcase',
    array(
      'id'    => 'products',
      'title' => 'Explore the products',
      'body'  => 'Each product page explains what it does, the problem it was built to solve and the main capabilities available today.',
    )
  );
  ?>

  <section class="section section-muted" id="product-studio">
    <div class="container sk-reveal">
      <div class="sk-future-band">
        <p class="eyebrow">Product Studio</p>
        <h2 class="sk-display">Product Studio</h2>
        <p class="lead sk-lead-narrow">Product Studio helps marketplace sellers research products, improve listing content and imagery, check compliance requirements and prepare marketplace-ready exports.</p>
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'start-discovery' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
      </div>
    </div>
  </section>

  <section class="section" id="portfolio-extension">
    <div class="container sk-reveal">
      <div class="sk-future-band">
        <p class="eyebrow">Product Engineering</p>
        <h2 class="sk-display">We keep building where the gap is real</h2>
        <p class="lead sk-lead-narrow">New product work only makes sense when there is a clear operating problem worth solving. The current focus is marketplace operations, portfolio intelligence, automation and connected business systems.</p>
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'start-discovery' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
      </div>
    </div>
  </section>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Need something similar for your business?',
      'body'          => 'Tell us what you are trying to do. It may fit one of our existing products, or it may need a separate system built around your operation.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-platforms',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
