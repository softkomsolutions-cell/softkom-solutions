<?php
/**
 * Softkom V3 Contact — minimal product-led conversation page.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$discovery = softkom_v3_cta( 'start-discovery' );
?>
<div class="sk-site sk-contact">
  <?php softkom_v3_component_e( 'header' ); ?>

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Contact',
      'title'           => 'Let\'s Start the Conversation',
      'lead'            => 'Whether you want to explore a Softkom platform or discuss a specialised product for your industry — Softkom replies with a clear next step.',
      'primary_label'   => $discovery ? $discovery['label'] : 'Book a Discovery Call',
      'primary_url'     => $discovery ? $discovery['url'] : '/contact/#discovery',
      'secondary_label' => 'Email Softkom',
      'secondary_url'   => 'mailto:info@softkomsolutions.com',
    )
  );
  ?>

  <section class="section" id="discovery">
    <div class="container">
      <div class="sk-grid sk-grid--2 sk-contact-layout">
        <div class="sk-card sk-form sk-form--minimal" id="enquiry">
          <p class="eyebrow eyebrow--tight">Message</p>
          <h3>Send Softkom a note</h3>
          <p>Share what you are exploring — a platform demo, an industry challenge, or a product conversation.</p>
          <?php echo do_shortcode( '[sureforms id="2722" show_title="false"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          <p class="sk-form-privacy-note">
            Softkom handles enquiry data under South African privacy requirements, including POPIA.
            By submitting this form you consent to Softkom using the information provided to respond to this enquiry, in accordance with the
            <a href="/privacy-policy/">Privacy Policy</a>.
          </p>
        </div>
        <div class="sk-stack sk-contact-aside">
          <div class="sk-contact-item">
            <p class="eyebrow eyebrow--tight">Discovery Call</p>
            <h3>Book a focused call</h3>
            <p>A short conversation to explore fit — platforms, product direction, or a specialised build.</p>
            <a class="sk-btn sk-btn-primary" href="#enquiry"><?php echo esc_html( softkom_v3_cta_label( 'start-discovery' ) ); ?></a>
          </div>
          <div class="sk-contact-item">
            <p class="eyebrow eyebrow--tight">LinkedIn</p>
            <a class="sk-contact-link" href="https://www.linkedin.com/company/softkomsolutions" rel="noopener noreferrer" target="_blank">Softkom Solutions on LinkedIn →</a>
          </div>
          <div class="sk-contact-item">
            <p class="eyebrow eyebrow--tight">Email</p>
            <a class="sk-contact-link" href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
          </div>
          <div class="sk-contact-item">
            <p class="eyebrow eyebrow--tight">Phone</p>
            <a class="sk-contact-link" href="tel:+27749933805">+27 74 993 3805</a>
            <p class="sk-contact-meta">Voice or WhatsApp</p>
          </div>
          <div class="sk-contact-item">
            <p class="eyebrow eyebrow--tight">Location</p>
            <p>Glensan, Johannesburg, South Africa</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
