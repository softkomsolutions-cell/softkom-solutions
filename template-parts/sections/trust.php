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
        <h3>What clients can expect from Softkom</h3>
        <p>Senior involvement, practical scoping, security-minded delivery, visible milestones and support after go-live. Softkom does not invent certifications, client claims or performance numbers it cannot substantiate.</p>
      </div>
    </div>
  </div>
</section>
