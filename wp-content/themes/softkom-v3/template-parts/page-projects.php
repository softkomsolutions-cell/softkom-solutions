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
<div class="sk-site">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Selected Work',
      'title'           => 'Selected systems, automation and digital delivery work',
      'lead'            => 'Client work and Softkom-owned products are labelled separately. Product development is not presented as client delivery.',
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
      'title'             => 'Client delivery',
      'body'              => "Work delivered for client organisations — labelled Client Project. Where naming permission is not yet confirmed, records are anonymised.\n\nRead each as a short delivery brief: industry context, the challenge, the work delivered, and the technology used. These are selected work records, not full case studies with metrics or testimonials.",
      'note'              => 'If a project resembles your bottleneck, bring that comparison to a discovery conversation. Softkom will say whether delivery experience is close enough to scope with confidence.',
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'products',
    array(
      'muted'      => true,
      'show_links' => false,
      'title'      => 'Softkom products (in development)',
      'body'       => "Internal product lines Softkom is building — MarketplaceOS AI for multi-channel sellers, and Brick Alpha for collectibles intelligence.\n\nBoth are marked Softkom Product / In Development. They are not client delivery, and Softkom does not claim market adoption or production readiness here.",
    )
  );
  ?>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Have an operating problem like one of these?',
      'body'          => 'Discuss the process that is stuck. Softkom will compare it to verified delivery experience and outline a practical next step once scope is understood.',
      'primary_cta'   => 'discuss-problem',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
