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
<div class="sk-site">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Industries',
      'title'           => 'Operating environments Softkom works across',
      'lead'            => 'Softkom applies business-systems, integration and automation methods across operational environments. Direct experience varies by sector, so verified delivery environments and adjacent applications are shown separately.',
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
      'grouped' => true,
      'title'   => 'Evidence-backed and adjacent environments',
      'body'    => "Each card names a common operational pressure, a relevant Softkom capability, and the evidence relationship.\n\nMatch the operating problem — not a sector label alone.",
      'note'    => 'Deeper industry pages remain on Softkom’s roadmap. Until then, judge fit from verified project records and a discovery conversation.',
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
      'body'              => 'Verified Softkom delivery across infrastructure-facing, retail/distribution, e-commerce and professional-services environments — labelled so client work and Softkom products stay distinct.',
    )
  );
  ?>
  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Recognise the operating pressure?',
      'body'          => 'Discuss the process that is stuck. Softkom will confirm fit against verified delivery experience — and will not claim specialist depth Softkom cannot defend.',
      'primary_cta'   => 'discuss-problem',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
