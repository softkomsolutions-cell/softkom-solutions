<?php
/**
 * Softkom V3 shared header — one-page commercial navigation.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_url        = home_url( '/' );
$header_cta_url  = $home_url . '#contact';
$header_cta_label = 'Tell Us What Is Not Working';
?>
<a class="sk-skip-link" href="#sk-main">Skip to content</a>
<header class="sk-header sk-header--transparent">
  <div class="container sk-header-inner">
    <a class="sk-brand sk-brand-lockup" href="<?php echo esc_url( $home_url ); ?>" aria-label="Softkom Solutions home">
      <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/softkom-logo-light.png' ); ?>" alt="Softkom Solutions">
    </a>

    <nav id="sk-primary-nav" class="sk-nav sk-nav--desktop" aria-label="Primary">
      <a href="<?php echo esc_url( $home_url . '#problems-we-solve' ); ?>">How We Help</a>
      <a href="<?php echo esc_url( $home_url . '#proof' ); ?>">Case Studies</a>
      <a href="<?php echo esc_url( $home_url . '#how-we-work' ); ?>">How We Work</a>
      <a href="/insights/">Insights</a>
      <a href="<?php echo esc_url( $home_url . '#contact' ); ?>">Contact</a>
    </nav>

    <div class="sk-header-actions">
      <a class="sk-btn sk-btn-primary sk-header-cta" href="<?php echo esc_url( $header_cta_url ); ?>"><?php echo esc_html( $header_cta_label ); ?></a>
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
    <a href="<?php echo esc_url( $home_url . '#problems-we-solve' ); ?>">How We Help</a>
    <a href="<?php echo esc_url( $home_url . '#proof' ); ?>">Case Studies</a>
    <a href="<?php echo esc_url( $home_url . '#how-we-work' ); ?>">How We Work</a>
    <a href="/insights/">Insights</a>
    <a href="<?php echo esc_url( $home_url . '#contact' ); ?>">Contact</a>
  </nav>
  <div class="sk-nav-drawer-cta">
    <a class="sk-btn sk-btn-primary" href="<?php echo esc_url( $header_cta_url ); ?>"><?php echo esc_html( $header_cta_label ); ?></a>
  </div>
</aside>
