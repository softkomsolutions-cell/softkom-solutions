<?php
/**
 * Component: card — single card family.
 *
 * Args: variant (default|link|project|faq|stacked), href, icon_svg, mark, eyebrow,
 *       title, outcome, body, tags (array), link_label, link_url, pills (array),
 *       facts (array of label=>value), visual (array title, widths, nodes), id.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant    = isset( $variant ) ? $variant : 'default';
$title      = isset( $title ) ? $title : '';
$body       = isset( $body ) ? $body : '';
$icon_svg   = isset( $icon_svg ) ? $icon_svg : '';
$mark       = isset( $mark ) ? $mark : '';
$eyebrow    = isset( $eyebrow ) ? $eyebrow : '';
$outcome    = isset( $outcome ) ? $outcome : '';
$tags       = isset( $tags ) && is_array( $tags ) ? $tags : array();
$link_label = isset( $link_label ) ? $link_label : '';
$link_url   = isset( $link_url ) ? $link_url : '';
$href       = isset( $href ) ? $href : '';
$pills      = isset( $pills ) && is_array( $pills ) ? $pills : array();
$facts      = isset( $facts ) && is_array( $facts ) ? $facts : array();
$visual     = isset( $visual ) && is_array( $visual ) ? $visual : null;
$id_attr    = ! empty( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

$classes = array( 'sk-card' );
if ( 'link' === $variant ) {
	$classes[] = 'sk-card--link';
}
if ( 'project' === $variant ) {
	$classes[] = 'sk-card--project';
}
if ( 'stacked' === $variant || ( 'project' === $variant && empty( $visual ) ) ) {
	$classes[] = 'sk-card--stacked';
}
if ( 'faq' === $variant ) {
	$classes[] = 'sk-card--faq';
}
$class_attr = esc_attr( implode( ' ', $classes ) );

$inner_start = '';
$inner_end   = '';
if ( 'project' === $variant ) {
	$inner_start = '<div>';
	$inner_end   = '</div>';
}

$tag = ( 'link' === $variant && $href ) ? 'a' : 'article';
$extra = ( 'link' === $variant && $href ) ? ' href="' . esc_url( $href ) . '"' : '';
?>
<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="<?php echo $class_attr; ?>"<?php echo $extra . $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
  <?php echo $inner_start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  <?php if ( $pills ) : ?>
    <div class="project-meta">
      <?php foreach ( $pills as $pill ) : ?>
        <span class="pill <?php echo esc_attr( $pill['class'] ); ?>"><?php echo esc_html( $pill['label'] ); ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if ( $icon_svg ) : ?>
    <div class="icon-well" aria-hidden="true"><?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
  <?php elseif ( $mark ) : ?>
    <div class="tech-mark"><?php echo esc_html( $mark ); ?></div>
  <?php endif; ?>
  <?php if ( $eyebrow ) : ?>
    <p class="eyebrow eyebrow--tight"><?php echo esc_html( $eyebrow ); ?></p>
  <?php endif; ?>
  <?php if ( $title ) : ?>
    <h3><?php echo esc_html( $title ); ?></h3>
  <?php endif; ?>
  <?php if ( $outcome ) : ?>
    <p class="outcome"><?php echo esc_html( $outcome ); ?></p>
  <?php endif; ?>
  <?php if ( $body ) : ?>
    <p><?php echo esc_html( $body ); ?></p>
  <?php endif; ?>
  <?php if ( $facts ) : ?>
    <dl class="project-facts">
      <?php foreach ( $facts as $fact ) : ?>
        <div>
          <dt><?php echo esc_html( $fact['label'] ); ?></dt>
          <dd><?php echo esc_html( $fact['value'] ); ?></dd>
        </div>
      <?php endforeach; ?>
    </dl>
  <?php endif; ?>
  <?php if ( $tags ) : ?>
    <div class="tags">
      <?php foreach ( $tags as $tag_label ) : ?>
        <span class="tag"><?php echo esc_html( $tag_label ); ?></span>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if ( $link_label && $link_url ) : ?>
    <a class="link-more" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
  <?php endif; ?>
  <?php echo $inner_end; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  <?php if ( 'project' === $variant && $visual ) : ?>
    <?php echo softkom_v3_component( 'project-visual', $visual ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  <?php endif; ?>
</<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
