<?php
/**
 * Softkom V3 — AutoSEO product page.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$p = softkom_v3_product_autoseo();
?>
<div class="sk-site sk-product-page sk-product-page--autoseo">
  <?php softkom_v3_component_e( 'header' ); ?>

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'variant'         => 'split',
      'eyebrow'         => $p['eyebrow'],
      'title'           => $p['title'] . ' — search growth that keeps working after the audit',
      'lead'            => $p['hero_lead'],
      'primary_label'   => 'Join AutoSEO Early Access',
      'primary_url'     => home_url( '/#contact' ),
      'secondary_label' => 'See How It Works',
      'secondary_url'   => home_url( '/platforms/autoseo/#solution' ),
    )
  );
  ?>

  <section class="section" id="challenge">
    <div class="container sk-product-prose sk-reveal">
      <p class="eyebrow">The Problem</p>
      <h2 class="sk-display"><?php echo esc_html( $p['challenge']['title'] ); ?></h2>
      <?php foreach ( preg_split( '/\n\n+/', $p['challenge']['body'] ) as $para ) : ?>
        <p class="lead"><?php echo esc_html( $para ); ?></p>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section section-muted" id="solution">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow">The AutoSEO Approach</p>
        <h2 class="sk-display"><?php echo esc_html( $p['solution']['title'] ); ?></h2>
        <p class="lead"><?php echo esc_html( $p['solution']['body'] ); ?></p>
      </div>
      <div class="sk-card">
        <p class="eyebrow">Operating Principle</p>
        <h3>Find → prioritise → prepare → approve → publish → measure</h3>
        <p>AutoSEO is designed to keep a human approval point before material website changes while automating the repetitive research and monitoring around them.</p>
      </div>
    </div>
  </section>

  <section class="section" id="modules">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">Core Modules</p>
        <h2 class="sk-display">One system from visibility problem to measurable improvement</h2>
      </div>
      <div class="sk-grid sk-grid--2 sk-modules">
        <?php foreach ( $p['modules'] as $module ) : ?>
          <article class="sk-module-card sk-reveal">
            <h3><?php echo esc_html( $module['title'] ); ?></h3>
            <p><?php echo esc_html( $module['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="intelligence">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow">AI & Automation</p>
        <h2 class="sk-display"><?php echo esc_html( $p['ai']['title'] ); ?></h2>
        <p class="lead"><?php echo esc_html( $p['ai']['body'] ); ?></p>
        <ul class="sk-feature-list">
          <?php foreach ( $p['ai']['items'] as $item ) : ?>
            <li><?php echo esc_html( $item ); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="sk-card">
        <p class="eyebrow">Not a content mill</p>
        <h3>Automation should improve a business website, not flood it.</h3>
        <p>AutoSEO is intentionally built around relevance, evidence, conversion paths and approval gates rather than publishing large volumes of low-value pages.</p>
      </div>
    </div>
  </section>

  <section class="section" id="ai-visibility">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow">AI Visibility Tracker</p>
        <h2 class="sk-display"><?php echo esc_html( $p['ai_visibility']['title'] ); ?></h2>
        <p class="lead"><?php echo esc_html( $p['ai_visibility']['body'] ); ?></p>
      </div>
      <div class="sk-card">
        <p class="eyebrow">Tracked Signals</p>
        <ul class="sk-feature-list">
          <?php foreach ( $p['ai_visibility']['metrics'] as $metric ) : ?>
            <li><?php echo esc_html( $metric ); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="integrations">
    <div class="container sk-reveal">
      <p class="eyebrow">Integrations</p>
      <h2 class="sk-display"><?php echo esc_html( $p['integrations']['title'] ); ?></h2>
      <p class="lead sk-lead-narrow"><?php echo esc_html( $p['integrations']['body'] ); ?></p>
      <ul class="sk-integration-pills">
        <?php foreach ( $p['integrations']['items'] as $item ) : ?>
          <li><?php echo esc_html( $item ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="section section-muted" id="benefits">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">What Businesses Gain</p>
        <h2 class="sk-display">SEO activity tied back to business outcomes</h2>
      </div>
      <div class="sk-pillars">
        <?php foreach ( $p['benefits'] as $i => $benefit ) : ?>
          <article class="sk-pillar sk-reveal">
            <div class="sk-pillar-mark" aria-hidden="true"><span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span></div>
            <h3><?php echo esc_html( $benefit['title'] ); ?></h3>
            <p><?php echo esc_html( $benefit['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section" id="early-access">
    <div class="container">
      <div class="sk-card sk-reveal">
        <p class="eyebrow">Early Access</p>
        <h2 class="sk-display">AutoSEO is being built into the Softkom growth stack.</h2>
        <p class="lead">The first rollout is aimed at businesses that want stronger search visibility and more qualified website enquiries without managing a separate collection of SEO tools.</p>
        <p><a class="sk-btn sk-btn-primary" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Tell Us About Your Website</a></p>
      </div>
    </div>
  </section>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
