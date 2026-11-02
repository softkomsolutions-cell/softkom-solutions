<?php
/**
 * Softkom V3 shared footer — premium, product-led.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The footer can render on error/404 paths before the canonical data loader
 * has registered CTA helpers. Load the CTA registry defensively and provide
 * safe fallbacks so shared chrome cannot fatal.
 */
if ( ! function_exists( 'softkom_v3_cta_url' ) || ! function_exists( 'softkom_v3_cta_label' ) ) {
	$softkom_cta_file = get_stylesheet_directory() . '/inc/data/cta.php';
	if ( is_readable( $softkom_cta_file ) ) {
		require_once $softkom_cta_file;
	}
}

$home_url = home_url( '/' );
$read_insights_url = function_exists( 'softkom_v3_cta_url' )
	? softkom_v3_cta_url( 'read-insights' )
	: home_url( '/insights/' );
$read_insights_label = function_exists( 'softkom_v3_cta_label' )
	? softkom_v3_cta_label( 'read-insights' )
	: 'Read Insights';
?>
<footer class="sk-footer">
  <div class="container">
    <div class="footer-grid footer-grid--simple">
      <div class="footer-brand-col">
        <?php if ( has_custom_logo() ) : ?>
          <div class="footer-logo sk-brand"><?php the_custom_logo(); ?></div>
        <?php else : ?>
          <div class="footer-brand">Softkom</div>
        <?php endif; ?>
        <p>Practical business systems that reduce waste, recover revenue and remove repetitive work.</p>
      </div>
      <div>
        <strong>Explore</strong>
        <a href="<?php echo esc_url( $home_url ); ?>#solutions">How We Help</a>
        <a href="<?php echo esc_url( $home_url ); ?>#proof">Case Studies</a>
        <a href="<?php echo esc_url( $home_url ); ?>#how-we-work">How We Work</a>
        <a href="/insights/">Insights</a>
      </div>
      <div>
        <strong>Start</strong>
        <a href="/business-efficiency-review/">Business Efficiency Review</a>
        <a href="/revenue-lab/">Revenue Lab</a>
        <a href="<?php echo esc_url( $home_url ); ?>#contact">Contact Softkom</a>
      </div>
      <div>
        <strong>Contact</strong>
        <a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
        <a href="tel:+27749933805">+27 74 993 3805</a>
        <span class="footer-address">Johannesburg, South Africa</span>
      </div>
    </div>
    <div class="footer-bottom">
      <div>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Softkom Solutions. All rights reserved.</div>
      <div class="footer-legal">
        <a href="/privacy-policy/">Privacy</a>
        <a href="/cookie/">Cookies</a>
        <a href="/terms-of-service/">Terms</a>
      </div>
    </div>
  </div>
</footer>
