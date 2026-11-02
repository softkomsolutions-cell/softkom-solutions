<?php
/**
 * Softkom V3 shared footer — simplified commercial navigation.
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
        <p>We help growing businesses improve the processes behind day-to-day operations — then implement the practical systems, integrations and automation needed to make the work easier to run.</p>
        <div class="footer-socials">
          <a href="https://www.linkedin.com/company/softkomsolutions" rel="noopener noreferrer" target="_blank">LinkedIn</a>
          <a href="https://wa.me/27749933805" rel="noopener noreferrer" target="_blank">WhatsApp</a>
        </div>
      </div>

      <div>
        <strong>Explore</strong>
        <a href="<?php echo esc_url( $home_url . '#problems-we-solve' ); ?>">How We Help</a>
        <a href="<?php echo esc_url( $home_url . '#proof' ); ?>">Case Studies</a>
        <a href="<?php echo esc_url( $home_url . '#how-we-work' ); ?>">How We Work</a>
        <a href="/insights/">Insights</a>
      </div>

      <div>
        <strong>Start a Conversation</strong>
        <a href="<?php echo esc_url( $home_url . '#contact' ); ?>">Tell Us What Is Not Working</a>
        <a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
        <a href="tel:+27749933805">+27 74 993 3805</a>
        <span class="footer-address">Johannesburg, South Africa</span>
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
