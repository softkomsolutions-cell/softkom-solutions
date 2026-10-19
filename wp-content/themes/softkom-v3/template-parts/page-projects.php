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
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Selected Work',
      'title'           => 'A selection of work we have actually delivered',
      'lead'            => 'Client projects and Softkom products are shown separately so it is clear what was built for a customer and what we built ourselves.'
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
      'show_links'  => false,
      'exclude_ids' => array( 'future-platforms' ),
      'title'       => 'Products we have built',
      'body'        => "Softkom-owned product work including MarketplaceOS, Brick Alpha and Product Studio. These are shown separately from client delivery so there is no confusion between internal product development and commissioned client work.",
    )
  );
  ?>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'See something here that feels familiar?'
      'body'          => 'Discuss the process that is stuck. Softkom will compare it to verified delivery experience and outline a practical next step once scope is understood.',
      'primary_cta'   => 'discuss-problem',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
