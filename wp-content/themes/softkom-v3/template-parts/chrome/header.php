<?php
/**
 * Softkom V3 shared header â€” sticky, transparent â†’ solid, slide-over mobile.
 *
 * Product-led nav: platforms first, simple labels, no consultancy dilution.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_url = home_url( '/' );
?>
<a class="sk-skip-link" href="#sk-main">Skip to content</a>
<header class="sk-header sk-header--transparent">
  <div class="container sk-header-inner">
    <?php if ( has_custom_logo() ) : ?>
      <div class="sk-brand">
        <?php the_custom_logo(); ?>
      </div>
    <?php else : ?>
      <a class="sk-brand sk-brand-text" href="<?php echo esc_url( $home_url ); ?>">
        <?php bloginfo( 'name' ); ?>
      </a>
    <?php endif; ?>

    <nav id="sk-primary-nav" class="sk-nav sk-nav--desktop" aria-label="Primary">
      <a href="/platforms/">Platforms</a>
      <a href="/platforms/marketplaceos/">MarketplaceOS</a>
      <a href="/platforms/brick-alpha/">Brick Alpha</a>
      <a href="/company/">Company</a>
      <a href="/insights/">Insights</a>
      <a href="/contact/">Contact</a>
    </nav>

    <div class="sk-header-actions">
      <a class="sk-btn sk-btn-primary sk-header-cta" href="<?php echo esc_url( softkom_v3_cta_url( 'book-strategy-call' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'book-strategy-call' ) ); ?></a>
      <button class="sk-nav-toggle" type="button" aria-expanded="false" aria-controls="sk-mobile-nav" data-sk-nav-open>
        <span class="sk-nav-toggle-bars" aria-hidden="true"></span>
        <span class="sk-nav-toggle-label">Menu</span>
      </button>
    </div>
  </div>
</header>

<div class="sk-nav-overlay" data-sk-nav-overlay hidden></div>
<aside id="sk-mobile-nav" class="sk-nav-drawer" aria-label="Mobile navigation" aria-hidden="true" data-sk-nav-drawer>
  <div class="sk-nav-drawer-head">
    <span class="sk-nav-drawer-title">Menu</span>
    <button class="sk-nav-close" type="button" aria-label="Close menu" data-sk-nav-close>
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <nav class="sk-nav-drawer-links" aria-label="Mobile primary">
    <a href="<?php echo esc_url( $home_url ); ?>">Home</a>
    <a href="/platforms/">Platforms</a>
    <a href="/platforms/marketplaceos/">MarketplaceOS</a>
    <a href="/platforms/brick-alpha/">Brick Alpha</a>
    <a href="/company/">Company</a>
    <a href="/insights/">Insights</a>
    <a href="/contact/">Contact</a>
  </nav>
  <div class="sk-nav-drawer-cta">
    <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( softkom_v3_cta_url( 'book-strategy-call' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'book-strategy-call' ) ); ?></a>
  </div>
</aside>


