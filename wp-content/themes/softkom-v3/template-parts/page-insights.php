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
<div class="sk-site sk-insights">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Insights',
      'title'           => 'Useful thinking for people trying to improve how the business runs',
      'lead'            => 'Short, practical articles on spreadsheets, automation, systems choices, compliance and delivery risk — written for people who need to make a decision, not for search engines.'
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
      'title' => 'What we write about',
      'body'  => "Most articles come from problems we see in real projects: too much manual work, disconnected systems, weak reporting, unclear automation opportunities and compliance work that depends on last-minute evidence gathering.",
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
      'body'  => 'A few practical pieces to help you think through the problem before deciding whether you need software, integration, automation or simply a better process.'
    )
  );
  ?>

  <section class="section" id="compliance-boundary">
    <div class="container sk-stack--measure">
      <?php
      echo softkom_v3_component(
        'section-head',
        array(
          'title' => 'A note on compliance content',
          'body'  => "Softkom can help design systems, workflows and evidence processes that support compliance work. We do not present ourselves as a certification body or law firm.\n\nWhere articles discuss SOC 2, ISO 27001 or POPIA, the focus is on operational controls, evidence and system design.",
        )
      );
      ?>
    </div>
  </section>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Need help applying this to your business?'
      'body'          => 'Tell us what you are dealing with. We can help work out whether the next step is a system change, an integration, automation or something simpler.'
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
