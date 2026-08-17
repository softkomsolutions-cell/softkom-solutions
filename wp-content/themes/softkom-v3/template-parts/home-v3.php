<?php
/**
 * Softkom V3 homepage — product-led RC1 launch polish.
 *
 * Sequence: hero → philosophy → platforms → ecosystem → why → security → roadmap → vision → CTA.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site sk-home sk-home--product">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'variant'         => 'split',
      'eyebrow'         => 'Specialised Software Platforms',
      'title'           => 'Softkom builds specialised software platforms.',
      'lead'            => 'Purpose-built products for industries where generic tools force permanent workarounds.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-platforms' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-platforms' ),
      'media'           => softkom_v3_graphic_platforms_hero(),
    )
  );
  ?>

  <?php softkom_v3_section_e( 'philosophy' ); ?>

  <?php
  softkom_v3_section_e(
    'platform-showcase',
    array(
      'muted' => true,
      'id'    => 'platforms',
      'title' => 'Our Platforms',
      'body'  => 'Each product is built for a specialised market — launched with the care of a product company.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'ecosystem',
    array(
      'id'    => 'ecosystem',
      'title' => 'An Expanding Portfolio',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha lead. Future ERP, AI Copilot and industry platforms extend the stack — without redesigning the architecture.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'why-pillars',
    array(
      'muted' => true,
      'id'    => 'why-softkom',
      'title' => 'Why organisations choose Softkom',
      'body'  => 'Four principles that shape every platform Softkom builds.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'trust',
    array(
      'id'    => 'trust',
      'title' => 'Security & Reliability',
      'body'  => 'Enterprise-minded engineering Softkom can stand behind — without inventing certifications Softkom has not earned.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'roadmap',
    array(
      'muted' => true,
      'id'    => 'roadmap',
      'title' => 'Product Roadmap',
      'body'  => 'MarketplaceOS and Brick Alpha today. Product Studio, Future ERP and future platforms next.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'vision',
    array(
      'id'    => 'vision',
      'title' => 'Building the next generation of specialised software platforms.',
      'body'  => 'A growing portfolio — each platform purpose-built for a market where generic software reaches its limits.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Ready to build something better?',
      'body'          => 'Evaluating a Softkom platform or exploring a specialised product for your industry — start with a focused conversation.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-platforms',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
