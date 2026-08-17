<?php
/**
 * Section: security & reliability principles + honest evidence placeholders.
 *
 * Principles Softkom can defend as engineering posture.
 * Named proof (stories, logos, certifications) remains Phase 4 — not invented.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'trust';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Security & Reliability';
$body  = isset( $body ) ? $body : 'How Softkom builds platforms organisations can run with confidence.';

$evidence = array(
	array(
		'label'  => 'Customer stories',
		'detail' => 'Named delivery narratives with permission.',
		'status' => 'Coming soon',
	),
	array(
		'label'  => 'Partner logos',
		'detail' => 'Technology and channel partners Softkom works with.',
		'status' => 'Coming soon',
	),
	array(
		'label'  => 'Testimonials',
		'detail' => 'Operator and investor quotes once cleared for publication.',
		'status' => 'Coming soon',
	),
);
?>
<section class="section<?php echo $muted ? ' section-muted' : ''; ?>" id="<?php echo esc_attr( $id ); ?>">
  <div class="container">
    <?php
    echo softkom_v3_component( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      'section-head',
      array(
        'title' => $title,
        'body'  => $body,
      )
    );
    ?>
    <div class="sk-security-grid">
      <?php foreach ( softkom_v3_data_security() as $i => $item ) : ?>
        <article class="sk-security-card sk-reveal">
          <div class="sk-security-mark" aria-hidden="true">
            <?php
            if ( ! empty( $item['icon'] ) && function_exists( 'softkom_v3_icon' ) ) {
              echo softkom_v3_icon( $item['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
              echo '<span>' . esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span>';
            }
            ?>
          </div>
          <h3><?php echo esc_html( $item['title'] ); ?></h3>
          <p><?php echo esc_html( $item['body'] ); ?></p>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="sk-evidence-band sk-reveal">
      <div class="sk-evidence-band-head">
        <h3>Evidence as Softkom earns it</h3>
        <p>Customer stories, partners and testimonials appear here when cleared — never invented for launch.</p>
      </div>
      <ul class="sk-trust-grid sk-trust-grid--compact">
        <?php foreach ( $evidence as $item ) : ?>
          <li class="sk-trust-card">
            <div class="sk-trust-card-top">
              <strong><?php echo esc_html( $item['label'] ); ?></strong>
              <span class="sk-status sk-status--coming"><?php echo esc_html( $item['status'] ); ?></span>
            </div>
            <p><?php echo esc_html( $item['detail'] ); ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
