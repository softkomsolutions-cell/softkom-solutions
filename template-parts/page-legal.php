<?php
/**
 * Softkom V3 legal / policy page composer.
 *
 * Renders WP post content inside library chrome. Legacy inline styles are
 * stripped; semantic structure is preserved.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_the_title();
$slug  = get_post_field( 'post_name', get_the_ID() );
$leads = array(
	'privacy-policy'   => 'How Softkom collects, uses and protects personal information — including POPIA-minded handling for South African clients and website visitors.',
	'cookie'           => 'How Softkom uses cookies and similar technologies on this website.',
	'terms-of-service' => 'The terms that govern use of Softkom websites and related services.',
);
$lead = isset( $leads[ $slug ] ) ? $leads[ $slug ] : 'Softkom policy information for clients and website visitors.';

$raw = get_post_field( 'post_content', get_the_ID() );
$body_html = '';

if ( is_string( $raw ) && $raw && false === stripos( $raw, '[softkom_' ) ) {
	$allowed = array(
		'p'      => array(),
		'h2'     => array(),
		'h3'     => array(),
		'h4'     => array(),
		'ul'     => array(),
		'ol'     => array(),
		'li'     => array(),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
		'a'      => array(
			'href'   => true,
			'rel'    => true,
			'target' => true,
		),
	);
	$clean = preg_replace( '#<h1[^>]*>.*?</h1>#is', '', $raw );
	$clean = wp_kses( $clean, $allowed );
	$clean = preg_replace(
		'#<p>\s*(LEGAL DISCLOSURE|DATA COLLECTION|USAGE AGREEMENT|PRIVACY POLICY|COOKIE POLICY|TERMS OF SERVICE)\s*</p>#i',
		'',
		$clean
	);
	if ( $title ) {
		$clean = preg_replace( '#^\s*' . preg_quote( $title, '#' ) . '\s*#i', '', $clean );
	}
	$clean = preg_replace( '#<p>\s*</p>#', '', $clean );
	$plain = trim( wp_strip_all_tags( $clean ) );
	if ( strlen( $plain ) > 80 && false === stripos( $plain, 'elementor' ) ) {
		$body_html = $clean;
	}
}

if ( ! $body_html ) {
	$body_html  = '<p>Softkom Solutions treats personal information carefully and uses this website to explain services, capture genuine enquiries and support client relationships.</p>';
	$body_html .= '<p>For privacy, cookie, POPIA or terms questions, email <a class="link-more" href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a> or call <a class="link-more" href="tel:+27749933805">+27 74 993 3805</a>.</p>';
	$body_html .= '<p>Softkom will provide the current policy detail relevant to your enquiry.</p>';
}
?>
<div class="sk-site">
  <?php softkom_v3_component_e( 'header' ); ?>
  <?php
  softkom_v3_component_e(
    'masthead',
    array(
      'eyebrow'         => 'Legal',
      'title'           => $title ? $title : 'Legal',
      'lead'            => $lead,
      'secondary_label' => 'Contact Softkom',
      'secondary_url'   => '/contact/',
    )
  );
  ?>
  <section class="section">
    <div class="container">
      <div class="sk-stack sk-stack--measure sk-prose">
        <?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd or fixed HTML ?>
      </div>
    </div>
  </section>
  <?php softkom_v3_component_e( 'cta-band' ); ?>
  <?php softkom_v3_component_e( 'footer' ); ?>
</div>
