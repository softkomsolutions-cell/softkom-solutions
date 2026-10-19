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
$title = isset( $title ) ? $title : 'How we work';
$body  = isset( $body ) ? $body : 'The practical things we pay attention to when software has to keep working after launch.';

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
        <h3>What working with Softkom should feel like</h3>
        <p>You should know what is being built, what has changed, what still needs a decision and who is responsible for the next step. We stay close to the work and keep claims about delivery, security and results factual.</p>
      </div>
    </div>
  </div>
</section>
