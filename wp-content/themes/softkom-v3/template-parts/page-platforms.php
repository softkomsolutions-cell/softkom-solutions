<?php
/**
 * Softkom V3 Platforms hub — product ecosystem centrepiece.
 *
 * Extensible IA: add platforms via softkom_v3_data_products() / roadmap data.
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
      'title'           => 'An ecosystem of specialised software platforms',
      'lead'            => 'Softkom builds products for industries and operators where generic software is not enough — each one introduced with the care of a product launch.',
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
      'title' => 'Product Roadmap',
      'body'  => 'MarketplaceOS and Brick Alpha today. Product Studio, Future ERP and future platforms next.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'ecosystem',
    array(
      'muted' => true,
      'id'    => 'ecosystem',
      'title' => 'The Softkom product ecosystem',
      'body'  => 'MarketplaceOS, Product Studio, Brick Alpha — then Future ERP, AI Copilot and industry platforms. New products slot in without redesigning the site.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'platform-showcase',
    array(
      'id'    => 'products',
      'title' => 'Meet the platforms',
      'body'  => 'Click into each product for the full story — challenge, solution, modules and how to get involved.',
    )
  );
  ?>

  <section class="section section-muted" id="product-studio">
    <div class="container sk-reveal">
      <div class="sk-future-band">
        <p class="eyebrow">Third Platform</p>
        <h2 class="sk-display">Product Studio</h2>
        <p class="lead sk-lead-narrow">As Product Studio matures, it becomes Softkom’s specialised product-creation layer — sitting alongside MarketplaceOS and Brick Alpha in the same ecosystem.</p>
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'start-discovery' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
      </div>
    </div>
  </section>

  <section class="section" id="future">
    <div class="container sk-reveal">
      <div class="sk-future-band">
        <p class="eyebrow">Future Innovation</p>
        <h2 class="sk-display">More specialised platforms are coming</h2>
        <p class="lead sk-lead-narrow">Future ERP, AI Copilot and industry platforms extend the Softkom stack — purpose-built where generic tools force permanent workarounds.</p>
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'start-discovery' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
      </div>
    </div>
  </section>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Ready to build something better?',
      'body'          => 'Start a conversation about MarketplaceOS, Brick Alpha, Product Studio, or a specialised platform for your industry.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-platforms',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
