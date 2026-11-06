<?php
/**
 * Softkom V3 Insights — RC2.4 editorial clarity.
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
      'eyebrow'         => 'Insights',
      'title'           => 'Practical guidance for better systems and operational decisions',
      'lead'            => 'Softkom Insights help operators decide when to replace spreadsheet-based processes, which workflows are suitable for automation, when to buy, configure or build, how to reduce implementation risk, and how to support governance and audit evidence. Not every topic is fully covered yet — Softkom publishes only what Softkom can defend.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-solutions' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-solutions' ),
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'topics',
    array(
      'title' => 'Decisions these insights support',
      'body'  => "Editorial pillars Softkom writes about — aligned to Softkom’s delivery work.\n\nIf you are deciding where to start, begin with spreadsheet dependence or automation opportunity identification; both map directly to how Softkom opens a discovery conversation.",
    )
  );
  ?>
  <?php
  softkom_v3_section_e(
    'insights',
    array(
      'muted' => true,
      'id'    => 'articles',
      'title' => 'Featured articles',
      'body'  => 'Selected pieces on operations, automation and compliance. Use them to brief an internal discussion before a discovery conversation — or to test whether Softkom’s framing matches how your team sees the problem.',
    )
  );
  ?>

  <section class="section" id="compliance-boundary">
    <div class="container sk-stack--measure">
      <?php
      echo softkom_v3_component(
        'section-head',
        array(
          'title' => 'Compliance writing boundary',
          'body'  => "Softkom may design systems and workflows that support operational controls and evidence collection. Softkom does not certify organisations or provide legal advice unless a separately qualified professional is engaged.\n\nArticles that mention SOC 2, ISO 27001 or POPIA discuss operational controls, evidence workflows and audit preparation support — not certification outcomes or legal advice.",
        )
      );
      ?>
    </div>
  </section>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Ready to turn insight into a systems plan?',
      'body'          => 'Start a discovery conversation. Softkom will map bottlenecks and outline a practical path once the operating problem is clear.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
