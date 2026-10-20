<?php
/**
 * Softkom V3 Contact — service-led conversation page.
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

  <main id="sk-main">

  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Contact',
      'title'           => 'Tell us what you are trying to fix or build',
      'lead'            => 'You do not need a polished brief. Tell us what is happening now, what is frustrating you and what a better outcome would look like. We will take it from there.',
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
          <p>A few sentences is enough. Tell us the problem, what you are using today and what you would like to change.</p>
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
            <p>A short call to understand the situation, see whether we are a good fit and decide what the next step should be.</p>
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
            <p>Johannesburg, South Africa</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section section-muted" id="what-happens-next">
    <div class="container">
      <div class="sk-reveal">
        <p class="eyebrow">What happens next</p>
        <h2 class="sk-display">You will know quickly whether there is a fit</h2>
        <p class="lead sk-lead-narrow">We review the problem first. If it looks like work Softkom can genuinely help with, we arrange a short conversation and agree the next step. That may be a scoped proposal, a technical review, a small first phase or simply a recommendation on what to do next.</p>
      </div>
      <div class="sk-grid sk-grid--3">
        <article class="sk-card">
          <h3>1. We read the detail</h3>
          <p>Your enquiry is reviewed in context, not treated as a generic sales lead.</p>
        </article>
        <article class="sk-card">
          <h3>2. We check fit</h3>
          <p>If we need more detail, we ask. If it is not a sensible Softkom project, we tell you rather than forcing a proposal.</p>
        </article>
        <article class="sk-card">
          <h3>3. We agree the next step</h3>
          <p>Where there is a fit, the next step is clearly defined before anyone commits to a larger piece of work.</p>
        </article>
      </div>
    </div>
  </section>

  </main>

  <script id="softkom-contact-default-za">
  (function () {
    function setSouthAfricaDefault() {
      var tel = document.querySelector('#enquiry input[type="tel"]');
      if (!tel) return false;

      var iti = tel.closest('.iti');
      var selected = iti && iti.querySelector('.iti__selected-country, .iti__selected-flag');
      var current = selected && (selected.getAttribute('aria-label') || selected.textContent || '');
      if (current && /south africa/i.test(current)) return true;

      if (selected) {
        selected.click();
        var za = document.querySelector('.iti__country[data-country-code="za"], [data-country-code="za"]');
        if (za) {
          za.click();
          return true;
        }
      }

      if (!tel.value || /^\+1\s*$/.test(tel.value)) {
        tel.value = '+27 ';
        tel.dispatchEvent(new Event('input', { bubbles: true }));
        tel.dispatchEvent(new Event('change', { bubbles: true }));
      }
      return true;
    }

    if (!setSouthAfricaDefault()) {
      var tries = 0;
      var timer = window.setInterval(function () {
        tries += 1;
        if (setSouthAfricaDefault() || tries > 20) {
          window.clearInterval(timer);
        }
      }, 250);
    }
  }());
  </script>

  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
