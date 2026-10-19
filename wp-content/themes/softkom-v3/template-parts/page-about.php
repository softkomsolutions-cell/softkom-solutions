<?php
/**
 * Softkom V3 Company — service-led company story.
 *
 * Story: why we exist → vision → philosophy → how we build → the future.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sk-site sk-company">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Company',
      'title'           => 'Softkom is a small technology company that likes difficult business problems.',
      'lead'            => 'We are based in Johannesburg and work with clients in South Africa and remotely. Most of our work starts with a process that is too manual, a system that no longer fits, or a project that needs experienced delivery ownership.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-solutions' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-solutions' ),
    )
  );
  ?>

  <section class="section" id="why-we-exist">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">Why Softkom exists</p>
      <h2 class="sk-display">We started Softkom because too much technology work stops at “good enough”.</h2>
      <p class="lead">A website can look finished and still not help the business. An app can be deployed and still be awkward to use. A system can be technically correct and still create more admin than it removes. We focus on the part after the demo: does it work, do people use it, and does it make the business easier to run?</p>
    </div>
  </section>

  <section class="section section-muted" id="our-vision">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">How we are set up</p>
      <h2 class="sk-display">Small enough to stay involved. Flexible enough to bring in the right specialists.</h2>
      <p class="lead">Darren stays close to discovery, scope, delivery and client communication. Where a project needs specialist development, security, infrastructure or integration skills, we bring those people into the work instead of pretending one person can do everything.</p>
    </div>
  </section>

  <?php
  softkom_v3_section_e(
    'philosophy',
    array(
      'id'    => 'our-philosophy',
      'title' => 'What matters to us',
      'body'  => 'Understand the business first. Keep the solution as simple as it can be. Build properly. Stay accountable after launch.',
    )
  );
  ?>

  <section class="section section-muted" id="how-we-build">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">How we work with clients</p>
        <h2 class="sk-display">Less ceremony. More visibility.</h2>
        <p class="lead sk-lead-narrow">We spend enough time understanding the problem before building, but we do not disappear for months behind documentation. Clients see progress, make decisions early and test the system before launch.</p>
      </div>
      <div class="sk-build-steps">
        <?php
        $steps = array(
          array( 'Understand', 'What is happening now, who is involved, and where the frustration or cost sits.' ),
          array( 'Agree the shape', 'What we are building, what it connects to, and what is deliberately out of scope.' ),
          array( 'Build', 'Working software in stages, with regular client decisions instead of a big reveal at the end.' ),
          array( 'Improve', 'Fix what needs fixing, support the team and extend the system when there is a good reason to.' ),
        );
        foreach ( $steps as $i => $step ) :
          ?>
          <article class="sk-build-step sk-reveal">
            <span class="sk-build-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
            <h3><?php echo esc_html( $step[0] ); ?></h3>
            <p><?php echo esc_html( $step[1] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php
  softkom_v3_section_e(
    'vision',
    array(
      'id'    => 'the-future',
      'title' => 'We build our own products too',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha give us first-hand experience of the same product decisions, release pressure and integration problems our clients face.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'leadership',
    array(
      'muted' => true,
      'id'    => 'leadership',
      'title' => 'Leadership',
      'body'  => 'Darren Enfield has spent more than 25 years around technology — in sales, project delivery and technical support. That mix matters because a good solution has to make commercial sense, work technically and survive contact with the people who use it every day.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Tell us what you are trying to fix.',
      'body'          => 'You do not need a formal brief. Explain the problem, what you have tried and what a better outcome would look like. We can take it from there.',
      'primary_cta'   => 'start-conversation',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
