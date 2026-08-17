<?php
/**
 * Softkom V3 Company — vision-forward About (product-led).
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
      'title'           => 'Building specialised software platforms with a clear vision',
      'lead'            => 'Softkom exists for businesses and industries where generic software is not enough — and where the next advantage comes from products designed around real operations.',
      'primary_label'   => softkom_v3_cta_label( 'start-discovery' ),
      'primary_url'     => softkom_v3_cta_url( 'start-discovery' ),
      'secondary_label' => softkom_v3_cta_label( 'explore-platforms' ),
      'secondary_url'   => softkom_v3_cta_url( 'explore-platforms' ),
    )
  );
  ?>

  <section class="section" id="why-we-exist">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">Why We Exist</p>
      <h2 class="sk-display">Generic software forces permanent workarounds</h2>
      <p class="lead">When the process is the competitive edge, off-the-shelf tools become a tax on every exception, handoff and report. Softkom builds specialised platforms so operators stop fighting the software — and start compounding capability.</p>
    </div>
  </section>

  <section class="section section-muted" id="our-vision">
    <div class="container sk-company-block sk-reveal">
      <p class="eyebrow">Our Vision</p>
      <h2 class="sk-display">A portfolio of world-class specialised platforms</h2>
      <p class="lead">Softkom is becoming a product-led software company — MarketplaceOS, Brick Alpha, and the platforms still ahead — each purpose-built for a market that needs more than generic tools.</p>
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
        <p class="eyebrow">How We Build Products</p>
        <h2 class="sk-display">From market reality to lasting platforms</h2>
        <p class="lead sk-lead-narrow">Softkom maps how work moves, designs ownership and data before code ships, then builds in controlled increments — with support after go-live so the product stays usable as the market changes.</p>
      </div>
      <div class="sk-build-steps">
        <?php
        $steps = array(
          array( 'Understand', 'Industry pressure, operator workflows and where generic tools break.' ),
          array( 'Design', 'Operating model, data ownership and product boundaries before features.' ),
          array( 'Build', 'Ship a platform foundation operators can trust — then layer intelligence.' ),
          array( 'Evolve', 'Adoption, support and iteration as volumes and channels grow.' ),
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
      'title' => 'The Future',
      'body'  => 'MarketplaceOS and Brick Alpha are the beginning. Softkom is building toward a growing portfolio of specialised platforms.',
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
      'body'  => 'Product direction and client conversations stay close to Softkom leadership — with specialist support where the work requires it.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Ready to Explore What\'s Possible?',
      'body'          => 'Start a conversation about Softkom platforms — or a specialised product for your industry.',
      'primary_cta'   => 'start-conversation',
      'secondary_cta' => 'explore-platforms',
    )
  );
  ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
