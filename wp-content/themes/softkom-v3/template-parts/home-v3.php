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
?>
<div class="sk-site sk-home sk-home--product">
  <?php softkom_v3_component_e( 'header' ); ?>

  <main id="sk-main">
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'variant'         => 'split',
      'eyebrow'         => 'Business Systems · Automation · Integration',
      'title'           => 'Build the systems your business needs to scale.',
      'lead'            => 'Softkom helps growing companies replace manual work, connect disconnected systems and gain better operational control with custom business systems, AI automation and practical integrations.',
      'primary_label'   => 'Book a Strategy Call',
      'primary_url'     => $start_discovery_url,
      'secondary_label' => 'Take the 3-Minute Assessment',
      'secondary_url'   => home_url( '/assessment/' ),
      'media'           => function_exists( 'softkom_v3_graphic_platforms_hero' ) ? softkom_v3_graphic_platforms_hero() : '',
    )
  );
  ?>

  <section class="section" id="problems-we-solve">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">What We Solve</p>
        <h2 class="sk-display">When the business has outgrown spreadsheets, workarounds and disconnected software</h2>
        <p class="lead sk-lead-narrow">Softkom focuses on the operational gaps that create delay, rework and poor visibility as a company grows.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <?php
        $problems = array(
          array( 'Manual processes', 'Replace repetitive spreadsheet, email and WhatsApp workflows with controlled business systems.' ),
          array( 'Disconnected systems', 'Connect CRM, accounting, ecommerce, inventory and operational tools so information moves once.' ),
          array( 'Poor operational visibility', 'Create dashboards, alerts and reporting that show what is happening without manual consolidation.' ),
          array( 'Processes that no longer scale', 'Turn fragile hand-offs, approvals and exception handling into trackable workflows.' ),
          array( 'Off-the-shelf software gaps', 'Build the missing layer around the software you already use instead of forcing a costly full replacement.' ),
          array( 'AI without a business case', 'Identify and implement AI where it reduces real workload, improves service or increases control.' ),
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
        <p class="eyebrow">Solutions</p>
        <h2 class="sk-display">What you can hire Softkom to deliver</h2>
        <p class="lead sk-lead-narrow">Focused, project-based delivery for companies that need better systems, automation, integrations and digital operations.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <?php
        $solutions = array(
          array( 'Custom Business Systems', 'Purpose-built operational software for workflows, approvals, portals, reporting and internal control.' ),
          array( 'AI & Automation', 'Automate repetitive work, customer interactions, document handling and decision support where the ROI is clear.' ),
          array( 'Systems Integration', 'Connect the platforms your business already relies on and eliminate duplicate capture and brittle manual hand-offs.' ),
          array( 'Operational Workflow Automation', 'Digitise recurring processes across operations, sales, service, stock, approvals and reporting.' ),
          array( 'Compliance & Cybersecurity', 'Practical POPIA-aware controls, security, backup and compliance infrastructure for growing businesses.' ),
          array( 'E-commerce & Digital Platforms', 'Build and improve commercial platforms where catalogue, ordering, workflows and integrations matter.' ),
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

  <section class="section" id="assessment">
    <div class="container">
      <div class="sk-card sk-reveal">
        <p class="eyebrow">Not Sure Where to Start?</p>
        <h2 class="sk-display">Find your best systems or automation opportunity in about 3 minutes</h2>
        <p class="lead">Use the Business Systems Assessment to identify manual-process risk, spreadsheet dependence, integration gaps, reporting weaknesses and practical automation opportunities.</p>
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
        <p class="eyebrow">Delivery Experience</p>
        <h2 class="sk-display">Built around real operational requirements</h2>
        <p class="lead sk-lead-narrow">Softkom combines client delivery with its own product engineering. The examples below show the kinds of systems, workflows and integrations the team works with.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <article class="sk-card sk-reveal">
          <h3>PS&amp;I Stationery</h3>
          <p>Multi-portal commerce and operational platform work spanning parent, school and business journeys, catalogue, authentication, ordering workflows and backend integration.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>TSA</h3>
          <p>Corporate and infrastructure digital platform work covering product architecture, project presentation, manufacturing capability and industry-facing lead generation.</p>
        </article>
        <article class="sk-card sk-reveal">
          <h3>MarketplaceOS Engineering</h3>
          <p>Multi-channel operational software spanning catalogue control, pricing intelligence, inventory, fulfilment, integrations and management visibility.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="how-we-work">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">How We Work</p>
        <h2 class="sk-display">A clear path from operational problem to working system</h2>
      </div>
      <div class="sk-build-steps">
        <?php
        $steps = array(
          array( 'Discover', 'Understand the operational problem, users, commercial priority and business case.' ),
          array( 'Design', 'Map workflows, integrations, data ownership, risks and the smallest useful delivery scope.' ),
          array( 'Build', 'Deliver in controlled stages with visible milestones, testing and decision points.' ),
          array( 'Launch', 'Deploy, validate, train and hand over with the operational team involved.' ),
          array( 'Support', 'Maintain, improve and automate further as the business grows.' ),
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
      'title' => 'Built for Business-Critical Work',
      'body'  => 'Practical engineering, security-minded delivery and senior involvement throughout the engagement.',
    )
  );
  ?>

  <?php
  softkom_v3_section_e(
    'platform-showcase',
    array(
      'id'    => 'platforms',
      'title' => 'Platforms We Are Building',
      'body'  => 'MarketplaceOS, Product Studio and Brick Alpha demonstrate the same engineering capabilities Softkom applies to custom client systems.',
    )
  );
  ?>

  <?php
  softkom_v3_component_e(
    'cta-band',
    array(
      'title'         => 'Have an operational problem that software should solve?',
      'body'          => 'Tell us what is slowing the business down. Softkom will help determine whether the right next step is integration, automation, a custom system or no build at all.',
      'primary_cta'   => 'start-discovery',
      'secondary_cta' => 'explore-services',
    )
  );
  ?>
  </main>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
