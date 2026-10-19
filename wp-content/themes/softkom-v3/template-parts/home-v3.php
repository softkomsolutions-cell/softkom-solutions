<?php
/**
 * Softkom V3 homepage — service-led commercial positioning.
 *
 * Sequence: hero → buyer problems → solutions → assessment → proof → delivery → trust → platforms → CTA.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'softkom_v3_cta_url' ) || ! function_exists( 'softkom_v3_cta_label' ) ) {
	$softkom_cta_file = get_stylesheet_directory() . '/inc/data/cta.php';
	if ( is_readable( $softkom_cta_file ) ) {
		require_once $softkom_cta_file;
	}
}

$start_discovery_label = function_exists( 'softkom_v3_cta_label' )
	? softkom_v3_cta_label( 'start-discovery' )
	: 'Book a Strategy Call';
$start_discovery_url = function_exists( 'softkom_v3_cta_url' )
	? softkom_v3_cta_url( 'start-discovery' )
	: home_url( '/contact/#discovery' );
$hero_proof = '
<div class="sk-hero-proof-card">
  <p class="eyebrow">Why clients talk to us</p>
  <h3>Senior-led delivery, without the big-agency layers.</h3>
  <p>Softkom is Johannesburg-based and works with businesses in South Africa and remotely. Current work includes multi-portal ordering, marketplace operations, integrations, automation and business systems.</p>
  <ul class="sk-hero-proof-list">
    <li>25+ years of ICT experience behind client discovery and delivery</li>
    <li>Project, subcontract and white-label delivery models</li>
    <li>Custom systems typically scoped from about R75k upwards</li>
  </ul>
</div>
';
?>
<div class="sk-site sk-home sk-home--product">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'variant'         => 'split',
      'eyebrow'         => 'Softkom Solutions · Johannesburg',
      'title'           => 'Software built around the way your business actually works.',
      'lead'            => 'We design and build business software, integrations and automation for companies that have outgrown spreadsheets, disconnected tools or awkward manual processes.',
      'primary_label'   => 'Book a Strategy Call',
      'primary_url'     => $start_discovery_url,
      'secondary_label' => 'View Selected Work',
      'secondary_url'   => home_url( '/case-studies/' ),
      'media'           => $hero_proof,
    )
  );
  ?>

  <section class="section" id="problems-we-solve">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">Where we usually help</p>
        <h2 class="sk-display">The point where the current way of working stops being good enough</h2>
        <p class="lead sk-lead-narrow">Most clients come to us because a process has become too manual, two systems do not talk to each other, or an off-the-shelf product no longer fits.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <?php
        $problems = array(
          array( 'Too much work happens by hand', 'If staff are copying information between spreadsheets, email, WhatsApp and business systems, we can usually simplify the process.' ),
          array( 'Your systems do not talk to each other', 'We connect CRM, finance, e-commerce, inventory and operational systems so the same information is not captured three times.' ),
          array( 'Management cannot see what is happening', 'We build practical reporting and dashboards around the information people actually need to run the business.' ),
          array( 'A process worked when the company was smaller', 'We turn informal hand-offs and approvals into workflows that are easier to follow, measure and improve.' ),
          array( 'The software almost fits, but not quite', 'Sometimes the answer is not replacing everything. We can build the missing piece around the tools you already use.' ),
          array( 'You want to use AI, but need a sensible use case', 'We use AI where it can save time or improve a real workflow. We do not add it just because it is fashionable.' ),
        );
        foreach ( $problems as $problem ) :
          ?>
          <article class="sk-card sk-reveal">
            <h3><?php echo esc_html( $problem[0] ); ?></h3>
            <p><?php echo esc_html( $problem[1] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="solutions">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">What we build</p>
        <h2 class="sk-display">A small team for serious systems work</h2>
        <p class="lead sk-lead-narrow">Softkom works project by project. We can take on a complete build, a defined technical workstream, or work behind another company as a delivery partner.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <?php
        $solutions = array(
          array( 'Custom business systems', 'Internal tools, portals, workflows and reporting built for the way your team actually works.' ),
          array( 'Automation and AI', 'Automation for repetitive work, document handling, customer service and internal processes where it genuinely saves time.' ),
          array( 'Systems integration', 'APIs and integrations that connect the software you already depend on.' ),
          array( 'Workflow software', 'Digital workflows for sales, service, stock, approvals, operations and recurring admin.' ),
          array( 'Compliance and security', 'Practical controls, backup, security and compliance support without turning every project into a consulting exercise.' ),
          array( 'E-commerce and digital platforms', 'Commercial websites and platforms where ordering, catalogue, payments and integrations need to work properly.' ),
        );
        foreach ( $solutions as $solution ) :
          ?>
          <article class="sk-card sk-reveal">
            <h3><?php echo esc_html( $solution[0] ); ?></h3>
            <p><?php echo esc_html( $solution[1] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <p><a class="sk-btn sk-btn-secondary" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Explore Solutions</a></p>
    </div>
  </section>

  <section class="section" id="engagement-fit">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">What working with us looks like</p>
        <h2 class="sk-display">Clear scope, senior involvement, and no pressure to overbuild</h2>
        <p class="lead sk-lead-narrow">Most Softkom engagements are project-based. Custom business systems typically start around R75k, while larger integrations and platform work are priced after scope, complexity and delivery risk are understood.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <article class="sk-card sk-reveal">
          <h3>You speak to senior people</h3>
          <p>Discovery, scope and delivery decisions stay close to Softkom leadership instead of disappearing through layers of account management.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>You see progress early</h3>
          <p>We prefer working software, visible milestones and regular decisions over long periods where the client cannot see what is happening.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>You are not forced into a bigger project</h3>
          <p>If the right answer is a smaller integration, a phased build or improving what you already have, we will say so.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="assessment">
    <div class="container">
      <div class="sk-card sk-reveal">
        <p class="eyebrow">Not sure what the right project is yet?</p>
        <h2 class="sk-display">Start with a quick assessment</h2>
        <p class="lead">The assessment gives you a simple view of where manual work, disconnected systems or weak reporting may be costing time. It takes about three minutes.</p>
        <p>
          <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( '/assessment/' ) ); ?>">Take the Free Assessment</a>
          <a class="sk-btn sk-btn-secondary" href="<?php echo esc_url( $start_discovery_url ); ?>">Book a Strategy Call</a>
        </p>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="proof">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">Recent work</p>
        <h2 class="sk-display">Work we can show you</h2>
        <p class="lead sk-lead-narrow">A few examples of delivered client work and working Softkom products. Where a client name is not cleared for public use, we keep it anonymous.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <article class="sk-card sk-reveal">
          <h3>Retail ordering platform</h3>
          <p>A multi-portal ordering platform for parents, schools and business customers, covering catalogue, checkout, delivery options, authentication and backend order workflows.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>Traffic Signals &amp; Accessories</h3>
          <p>A clearer commercial website for a specialist infrastructure supplier, with product information, project capability and stronger enquiry paths.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>MarketplaceOS</h3>
          <p>Marketplace operations software covering catalogue, repricing, inventory, fulfilment, integrations and seller reporting.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="how-we-work">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">How projects run</p>
        <h2 class="sk-display">Straightforward delivery, with fewer surprises</h2>
      </div>
      <div class="sk-build-steps">
        <?php
        $steps = array(
          array( 'Understand', 'We start with the problem, the people doing the work and what a useful result would look like.' ),
          array( 'Scope', 'We agree what needs to be built, what needs to connect, and what can wait.' ),
          array( 'Build', 'You see working software as the project moves forward, not only at the end.' ),
          array( 'Launch', 'We test it with the people who will use it, then deploy and hand over properly.' ),
          array( 'Support', 'We stay involved where ongoing support, fixes or further improvement make sense.' ),
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
    'trust',
    array(
      'muted' => true,
      'id'    => 'trust',
      'title' => 'Built for real businesses, not demos',
      'body'  => 'You deal directly with senior people, and the work is judged by whether it solves the business problem and works reliably after launch.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'platform-showcase',
    array(
      'id'    => 'platforms',
      'title' => 'Products we have built',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha are working examples of the product engineering, integration and QA capability we bring to client projects.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Have something in the business that is harder than it should be?',
      'body'          => 'Tell us what is happening now and what you want to improve. We will tell you whether it looks like a software, integration or automation project — and if it does not, we will say so.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-solutions',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
