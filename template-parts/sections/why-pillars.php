<?php
/**
 * Section: why Softkom — four visual pillars.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'why-softkom';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Why Softkom';
$body  = isset( $body ) ? $body : '';
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
    <div class="sk-pillars">
      <?php foreach ( softkom_v3_data_why() as $i => $pillar ) : ?>
        <article class="sk-pillar sk-reveal">
          <div class="sk-pillar-mark" aria-hidden="true">
            <?php
            if ( ! empty( $pillar['icon'] ) && function_exists( 'softkom_v3_icon' ) ) {
              echo softkom_v3_icon( $pillar['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
              echo '<span>' . esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span>';
            }
            ?>
          </div>
          <h3><?php echo esc_html( $pillar['title'] ); ?></h3>
          <p><?php echo esc_html( $pillar['body'] ); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
