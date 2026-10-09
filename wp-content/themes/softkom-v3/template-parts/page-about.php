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

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Company',
      'title'           => 'Business systems built around how companies really operate',
      'lead'            => 'Softkom helps growing companies solve operational problems with custom business systems, automation, integrations and practical digital infrastructure.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-solutions' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-solutions' ),
    )
  );
  ?>

  <section class="section" id="why-we-exist">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">Why We Exist</p>
      <h2 class="sk-display">Generic software forces permanent workarounds</h2>
      <p class="lead">When the process is the competitive edge, off-the-shelf tools become a tax on every exception, handoff and report. Softkom designs the missing systems, integrations and workflows so teams spend less time fighting software and more time running the business.</p>
    </div>
  </section>

  <section class="section section-muted" id="our-vision">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">Our Vision</p>
      <h2 class="sk-display">Senior-led delivery with specialist technical capability</h2>
      <p class="lead">Client discovery, solution direction and commercial decisions stay close to Softkom leadership. Specialist technical capability is brought into each engagement according to what the work actually requires.</p>
    </div>
  </section>

  <?php
  softkom_v3_section_e(
    'philosophy',
    array(
      'id'    => 'our-philosophy',
      'title' => 'Our Philosophy',
      'body'  => 'Markets first. Specialised where generic fails. Products that compound.',
    )
  );
  ?>

  <section class="section section-muted" id="how-we-build">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">How We Deliver Client Systems</p>
        <h2 class="sk-display">From operational problem to working system</h2>
        <p class="lead sk-lead-narrow">Softkom maps how work moves, designs workflows and data ownership before code ships, then delivers in controlled increments with testing, handover and support after go-live.</p>
      </div>
      <div class="sk-build-steps">
        <?php
        $steps = array(
          array( 'Understand', 'Business pressure, user workflows and where the current process breaks.' ),
          array( 'Design', 'Workflows, integrations, data ownership and delivery boundaries before features.' ),
          array( 'Build', 'Ship a reliable working system in controlled stages — then layer automation and intelligence.' ),
          array( 'Evolve', 'Adoption, support and iteration as the operation grows.' ),
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
      'title' => 'Platforms & Product Engineering',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha demonstrate the same engineering capability Softkom applies to client systems, integrations and automation.',
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
      'body'  => 'Darren Enfield brings 25+ years across ICT, technology sales, project delivery and technical support. Discovery, solution direction and commercial planning stay senior-led, with specialist delivery support where the work requires it.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Ready to Explore What\'s Possible?',
      'body'          => 'Tell us what is slowing the business down. We will help determine whether the right next step is integration, automation, a custom system or no build at all.',
      'primary_cta'   => 'start-conversation',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
