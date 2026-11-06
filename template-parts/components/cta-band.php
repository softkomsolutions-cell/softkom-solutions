<?php
/**
 * Component: cta-band — one closing CTA for every page.
 *
 * Args: title, body, id, primary_cta, secondary_cta,
 *       primary_label, primary_url, secondary_label, secondary_url (overrides).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $title ) ? $title : 'Ready to discuss the process that is holding the business back?';
$body  = isset( $body ) ? $body : 'Book a strategy call when you want a focused conversation on bottlenecks, fit and a practical next step — or start a discovery conversation if you prefer to write first.';
$id    = isset( $id ) ? $id : 'assessment';

$primary_id   = isset( $primary_cta ) ? (string) $primary_cta : 'book-strategy-call';
$secondary_id = isset( $secondary_cta ) ? (string) $secondary_cta : 'start-discovery';

$primary = function_exists( 'softkom_v3_cta' ) ? softkom_v3_cta( $primary_id ) : null;
$secondary = function_exists( 'softkom_v3_cta' ) ? softkom_v3_cta( $secondary_id ) : null;

$primary_label = isset( $primary_label ) ? $primary_label : ( $primary ? $primary['label'] : 'Book a Strategy Call' );
$primary_url   = isset( $primary_url ) ? $primary_url : ( $primary ? $primary['url'] : '/contact/#strategy-call' );
$secondary_label = isset( $secondary_label ) ? $secondary_label : ( $secondary ? $secondary['label'] : 'Start a Discovery Conversation' );
$secondary_url   = isset( $secondary_url ) ? $secondary_url : ( $secondary ? $secondary['url'] : '/contact/#strategy-call' );
?>
<section class="cta-band sk-reveal" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <h2><?php echo esc_html( $title ); ?></h2>
    <p><?php echo esc_html( $body ); ?></p>
    <?php
    echo softkom_v3_component(
      'cta-row',
      array(
        'primary_label'   => $primary_label,
        'primary_url'     => $primary_url,
        'primary_class'   => 'sk-btn-on-dark',
        'secondary_label' => $secondary_label,
        'secondary_url'   => $secondary_url,
        'secondary_class' => 'sk-btn-on-dark-outline',
      )
    );
    ?>
    <p class="cta-meta">
      <a href="mailto:info@softkomsolutions.com">info@softkomsolutions.com</a>
      · <a href="tel:+27749933805">+27 74 993 3805</a>
      · Glensan, Johannesburg
    </p>
  </div>
</section>
