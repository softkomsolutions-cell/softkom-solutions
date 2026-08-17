<?php
/**
 * Softkom V3 shared footer â€” premium, product-led.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_url = home_url( '/' );
?>
<footer class="sk-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand-col">
        <?php if ( has_custom_logo() ) : ?>
          <div class="footer-logo sk-brand">
            <?php the_custom_logo(); ?>
          </div>
        <?php else : ?>
          <div class="footer-brand">Softkom</div>
        <?php endif; ?>
        <p>Specialised software platforms for markets where generic tools fall short.</p>
        <div class="footer-socials">
          <a href="https://www.linkedin.com/company/softkomsolutions" rel="noopener noreferrer" target="_blank">LinkedIn</a>
          <a href="https://wa.me/27749933805" rel="noopener noreferrer" target="_blank">WhatsApp</a>
        </div>
      </div>
      <div>
        <strong>Platforms</strong>
        <a href="/platforms/">All Platforms</a>
        <a href="/platforms/marketplaceos/">MarketplaceOS</a>
        <a href="/platforms/brick-alpha/">Brick Alpha</a>
        <a href="/platforms/#product-studio">Product Studio</a>
        <a href="<?php echo esc_url( $home_url ); ?>#roadmap">Roadmap</a>
      </div>
      <div>
        <strong>Company</strong>
        <a href="/company/">About Softkom</a>
        <a href="<?php echo esc_url( $home_url ); ?>#why-softkom">Why Softkom</a>
        <a href="/contact/">Contact</a>
      </div>
      <div>
        <strong>Insights</strong>
        <a href="/insights/">All Insights</a>
        <a href="<?php echo esc_url( softkom_v3_cta_url( 'read-insights' ) ); ?>"><?php echo esc_html( softkom_v3_cta_label( 'read-insights' ) ); ?></a>
      </div>
      <div>
        <strong>Trust</strong>
        <a href="<?php echo esc_url( $home_url ); ?>#trust">Security &amp; Reliability</a>
        <a href="<?php echo esc_url( $home_url ); ?>#trust">Evidence <span class="footer-soon">Soon</span></a>
        <a href="<?php echo esc_url( $home_url ); ?>#roadmap">Product roadmap</a>
      </div>
      <div>
        <strong>Contact</strong>
        <a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
        <a href="tel:+27749933805">+27 74 993 3805</a>
        <span class="footer-address">Glensan, Johannesburg, South Africa</span>
      </div>
    </div>
    <div class="footer-bottom">
      <div>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Softkom Solutions. All rights reserved.</div>
      <div class="footer-legal">
        <a href="/privacy-policy/">Privacy</a>
        <a href="/privacy-policy/">POPIA</a>
        <a href="/cookie/">Cookie Policy</a>
        <a href="/terms-of-service/">Terms</a>
      </div>
    </div>
  </div>
</footer>


