<?php
/**
 * Softkom V3 Industries — RC2.4 evidence separation.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site sk-industries">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Industries',
      'title'           => 'The kinds of businesses and operating problems we know well',
      'lead'            => 'Our strongest experience is in retail and distribution, e-commerce, professional services and infrastructure suppliers. We also take on adjacent work where the operating problem and delivery model are a good fit.',
      'primary_label'   => softkom_v3_cta_label( 'discuss-problem' ),
      'primary_url'     => softkom_v3_cta_url( 'discuss-problem' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-solutions' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-solutions' ),
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'industries',
    array(
      'muted'   => false,
      'grouped' => false,
      'group'   => 'experience',
      'title'   => 'Where we already have useful context',
      'body'    => "Industry labels matter less than the problem itself. If your team is dealing with manual ordering, disconnected systems, reporting gaps, approvals or operational admin, that is usually the more useful starting point.",
      'note'    => 'We also work outside these sectors when the operating problem is a strong fit — especially around ordering, integrations, workflow automation, reporting and internal systems.',
      'foot_label' => softkom_v3_cta_label( 'discuss-problem' ) . ' →',
      'foot_url'   => softkom_v3_cta_url( 'discuss-problem' ),
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'projects',
    array(
      'limit'             => 3,
      'show_technologies' => false,
      'show_links'        => true,
      'muted'             => true,
      'id'                => 'selected-work',
      'title'             => 'Selected work',
      'body'              => 'A few examples of work across retail, e-commerce, professional services and infrastructure-facing businesses.'
    )
  );
  ?>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Does one of these problems sound familiar?',
      'body'          => 'Tell us what is happening in your operation. We can usually tell quickly whether it is close to work we know how to handle.',
      'primary_cta'   => 'discuss-problem',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
