<?php
/**
 * Softkom V3 — 404 template (library chrome only).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

status_header( 404 );
nocache_headers();

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo esc_html( wp_get_document_title() ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class( 'softkom-v3' ); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
}
?>
<div class="sk-site">
<?php
if ( function_exists( 'softkom_v3_component_e' ) ) {
	softkom_v3_component_e( 'header' );
	softkom_v3_component_e(
		'masthead',
		array(
			'eyebrow'         => '404',
			'title'           => 'This page is not available',
			'lead'            => 'The link may be outdated, or the page has moved. Return home for Softkom’s systems overview, or go straight to Solutions if you already know the operating problem you need to discuss.',
			'primary_label'   => 'Return home',
			'primary_url'     => home_url( '/' ),
			'secondary_label' => 'View Solutions',
			'secondary_url'   => home_url( '/services/' ),
		)
	);
	softkom_v3_component_e( 'cta-band' );
	softkom_v3_component_e( 'footer' );
} else {
	echo '<main class="container section"><h1>This page is not available</h1><p><a href="' . esc_url( home_url( '/' ) ) . '">Return home</a></p></main>';
}
?>
</div>
<?php wp_footer(); ?>
</body>
</html>
