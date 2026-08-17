<?php
/**
 * Softkom V3 — Brick Alpha product page (luxury / investment aesthetic).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$p = softkom_v3_product_brick_alpha();
?>
<div class="sk-site sk-product-page sk-product-page--brick-alpha">
  <?php softkom_v3_component_e( 'header' ); ?>

  <section class="sk-luxury-hero">
    <div class="container sk-luxury-hero-grid">
      <div class="sk-reveal">
        <p class="eyebrow eyebrow--luxury"><?php echo esc_html( $p['eyebrow'] ); ?></p>
        <h1 class="sk-display-luxury"><?php echo esc_html( $p['title'] ); ?></h1>
        <p class="lead sk-lead-luxury"><?php echo esc_html( $p['hero_lead'] ); ?></p>
        <?php
        echo softkom_v3_component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          'cta-row',
          array(
            'primary_label'   => softkom_v3_cta_label( 'book-demo' ),
            'primary_url'     => softkom_v3_cta_url( 'book-demo' ),
            'secondary_label' => softkom_v3_cta_label( 'explore-platforms' ),
            'secondary_url'   => softkom_v3_cta_url( 'explore-platforms' ),
          )
        );
        ?>
      </div>
      <div class="sk-luxury-media sk-reveal" aria-hidden="true">
        <?php echo softkom_v3_graphic_product_ui( 'brick-alpha' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section sk-luxury-section" id="challenge">
    <div class="container sk-product-prose sk-reveal">
      <p class="eyebrow eyebrow--luxury">The Challenge</p>
      <h2 class="sk-display-luxury"><?php echo esc_html( $p['challenge']['title'] ); ?></h2>
      <?php foreach ( preg_split( '/\n\n+/', $p['challenge']['body'] ) as $para ) : ?>
        <p class="lead sk-lead-luxury"><?php echo esc_html( $para ); ?></p>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section sk-luxury-section sk-luxury-section--alt" id="solution">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow eyebrow--luxury">Solution</p>
        <h2 class="sk-display-luxury"><?php echo esc_html( $p['solution']['title'] ); ?></h2>
        <p class="lead sk-lead-luxury"><?php echo esc_html( $p['solution']['body'] ); ?></p>
      </div>
      <div class="sk-product-shot sk-product-shot--dark">
        <?php echo softkom_v3_graphic_product_ui( 'brick-alpha' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section sk-luxury-section" id="modules">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow eyebrow--luxury">Capabilities</p>
        <h2 class="sk-display-luxury">Precision over playfulness</h2>
      </div>
      <div class="sk-grid sk-grid--2 sk-modules sk-modules--luxury">
        <?php foreach ( $p['modules'] as $module ) : ?>
          <article class="sk-module-card sk-module-card--luxury sk-reveal">
            <h3><?php echo esc_html( $module['title'] ); ?></h3>
            <p><?php echo esc_html( $module['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section sk-luxury-section sk-luxury-section--alt" id="intelligence">
    <div class="container sk-product-split sk-reveal">
      <div>
        <p class="eyebrow eyebrow--luxury">Intelligence</p>
        <h2 class="sk-display-luxury"><?php echo esc_html( $p['ai']['title'] ); ?></h2>
        <p class="lead sk-lead-luxury"><?php echo esc_html( $p['ai']['body'] ); ?></p>
        <ul class="sk-feature-list sk-feature-list--luxury">
          <?php foreach ( $p['ai']['items'] as $item ) : ?>
            <li><?php echo esc_html( $item ); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="sk-product-shot sk-product-shot--dark">
        <?php echo softkom_v3_graphic_product_ui( 'compound' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </div>
  </section>

  <section class="section sk-luxury-section" id="benefits">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow eyebrow--luxury">Benefits</p>
        <h2 class="sk-display-luxury">Built for serious collectors</h2>
      </div>
      <div class="sk-pillars sk-pillars--luxury">
        <?php foreach ( $p['benefits'] as $i => $benefit ) : ?>
          <article class="sk-pillar sk-pillar--luxury sk-reveal">
            <div class="sk-pillar-mark" aria-hidden="true"><span><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span></div>
            <h3><?php echo esc_html( $benefit['title'] ); ?></h3>
            <p><?php echo esc_html( $benefit['body'] ); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="section sk-luxury-section sk-luxury-section--alt" id="testimonials">
    <div class="container sk-reveal">
      <p class="eyebrow eyebrow--luxury">Testimonials</p>
      <h2 class="sk-display-luxury">Private preview</h2>
      <?php foreach ( $p['testimonials'] as $t ) : ?>
        <blockquote class="sk-quote sk-quote--luxury">
          <p>“<?php echo esc_html( $t['quote'] ); ?>”</p>
          <footer>
            <strong><?php echo esc_html( $t['author'] ); ?></strong>
            <span><?php echo esc_html( $t['role'] ); ?></span>
          </footer>
        </blockquote>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="sk-luxury-cta">
    <div class="container sk-reveal">
      <h2 class="sk-display-luxury">Request a private briefing</h2>
      <p class="lead sk-lead-luxury">Brick Alpha is in development. Start a conversation if you want early access or a walkthrough of the investment thesis.</p>
      <div class="sk-cta-row">
        <a class="sk-btn sk-btn-luxury" href="<?php echo esc_url( softkom_v3_cta_url( 'book-demo' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'book-demo' ) ); ?></a>
        <a class="sk-btn sk-btn-luxury-outline" href="<?php echo esc_url( softkom_v3_cta_url( 'start-conversation' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'start-conversation' ) ); ?></a>
      </div>
    </div>
  </section>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
