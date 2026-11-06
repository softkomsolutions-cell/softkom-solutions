<?php
/**
 * Clean Softkom homepage document.
 *
 * Bypasses the legacy Astra/page wrapper so the Softkom homepage owns the
 * complete visible document chrome: one header, one homepage body, one footer.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php echo softkom_v3_render_partial( 'home-v3.php' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php
ob_start();
wp_footer();
$softkom_footer_output = ob_get_clean();
$softkom_footer_output = preg_replace(
	'~<section class="sk-authority-bridge"[^>]*>.*?</section>~is',
	'',
	$softkom_footer_output,
	1
);
echo $softkom_footer_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
?>
</body>
</html>
