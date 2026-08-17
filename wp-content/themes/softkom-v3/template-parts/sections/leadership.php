<?php
/**
 * Section: leadership identity (About).
 *
 * Renders only verified public fields from leadership profile.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'leadership';
$muted = isset( $muted ) ? (bool) $muted : false;
$title = isset( $title ) ? $title : 'Leadership and client involvement';
$body  = isset( $body ) ? $body : 'Softkom keeps client work close to the people making delivery and commercial decisions.';
$note  = isset( $note ) ? $note : '';

$profile = function_exists( 'softkom_v3_leadership_profile' ) ? softkom_v3_leadership_profile() : array();
$view    = function_exists( 'softkom_v3_leadership_public_view' ) ? softkom_v3_leadership_public_view( $profile ) : array();

if ( empty( $view['name'] ) && empty( $view['summary'] ) && empty( $view['philosophy'] ) ) {
	return;
}

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
?>
<div class="sk-stack sk-stack--measure sk-leadership">
  <?php if ( ! empty( $view['name'] ) ) : ?>
    <div class="sk-leadership__identity">
      <h3><?php echo esc_html( $view['name'] ); ?></h3>
      <?php if ( ! empty( $view['role'] ) ) : ?>
        <p class="outcome"><?php echo esc_html( $view['role'] ); ?></p>
      <?php endif; ?>
      <?php if ( ! empty( $view['summary'] ) ) : ?>
        <p><?php echo esc_html( $view['summary'] ); ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if ( ! empty( $view['philosophy'] ) ) : ?>
    <div class="sk-leadership__philosophy">
      <p class="eyebrow eyebrow--tight">Why Softkom exists</p>
      <p><?php echo esc_html( $view['philosophy'] ); ?></p>
    </div>
  <?php endif; ?>

  <?php if ( ! empty( $view['involvement'] ) || ! empty( $view['focus'] ) ) : ?>
    <div class="sk-grid sk-grid--2">
      <?php if ( ! empty( $view['involvement'] ) ) : ?>
        <?php
        echo softkom_v3_component(
          'card',
          array(
            'title' => 'Client involvement',
            'body'  => implode( ' ', $view['involvement'] ),
          )
        );
        ?>
      <?php endif; ?>
      <?php if ( ! empty( $view['focus'] ) ) : ?>
        <?php
        echo softkom_v3_component(
          'card',
          array(
            'title' => 'Focus',
            'body'  => implode( ' ', $view['focus'] ),
          )
        );
        ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => $muted, 'content' => $content ) );
