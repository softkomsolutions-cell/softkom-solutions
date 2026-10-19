<?php
/**
 * Softkom V3 Selected Work — RC2.4 project integrity.
 *
 * Public label: Selected Work. Route slug remains /case-studies/ for stability.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site sk-projects">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Selected Work',
      'title'           => 'A selection of real client and product work',
      'lead'            => 'Client projects and Softkom products are shown separately so it is clear what we have delivered for customers, what is currently in launch preparation, and what we have built ourselves.',
      'primary_label'   => softkom_v3_cta_label( 'discuss-problem' ),
      'primary_url'     => softkom_v3_cta_url( 'discuss-problem' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-solutions' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-solutions' ),
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'projects',
    array(
      'muted'             => false,
      'id'                => 'client-projects',
      'show_links'        => false,
      'show_technologies' => true,
      'title'             => 'Client work',
      'body'              => "A few client projects that show the kind of work Softkom takes on. Each one gives the business context, the problem, what was delivered and the technology involved.\n\nWhere a client name is not cleared for public use, the project stays anonymous.",
      'note'              => 'If one of these feels close to your situation, mention it when you contact us.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'products',
    array(
      'muted'      => true,
      'show_links'  => false,
      'title'       => 'Products we have built',
      'body'        => "MarketplaceOS, Brick Alpha and Product Studio are Softkom products. They show the same product engineering, integration and QA capability we bring to client projects.",
    )
  );
  ?>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'See something here that feels familiar?',
      'body'          => 'Tell us what is happening in your business. We can usually tell quickly whether the problem is close to work we know how to deliver.',
      'primary_cta'   => 'discuss-problem',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
