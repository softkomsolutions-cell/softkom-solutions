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
      'title' => 'Product Portfolio',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha are active Softkom products, each addressing a different operational problem with specialised software.',
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
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha form Softkom’s current product ecosystem, supported by the same systems engineering, automation and integration capability used in client delivery.',
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
        <p class="eyebrow">Product Studio</p>
        <h2 class="sk-display">Product Studio</h2>
        <p class="lead sk-lead-narrow">Product Studio is Softkom’s specialised product-creation layer for marketplace sellers — built to improve product research, content, imagery, compliance and listing workflows.</p>
        <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'start-discovery' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
      </div>
    </div>
  </section>

  <section class="section" id="portfolio-extension">
    <div class="container sk-reveal">
      <div class="sk-future-band">
        <p class="eyebrow">Product Engineering</p>
        <h2 class="sk-display">Specialised software built for real operating gaps</h2>
        <p class="lead sk-lead-narrow">Softkom continues to extend its product engineering capability around marketplace operations, portfolio intelligence, automation and connected business systems — always where generic tools leave an operational gap.</p>
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
