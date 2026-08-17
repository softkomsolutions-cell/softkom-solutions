<?php
/**
 * Softkom V3 Ã¢â‚¬â€ Astra child theme (component library)
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SOFTKOM_V3_VERSION', '3.3.7' );


/*
 * Load persistent Funnel lead registration during normal theme startup.
 * This must run before WordPress init so the admin post type is available.
 */
$softkom_leads_file = get_stylesheet_directory()
    . '/inc/data/assessment/funnel-leads.php';

if ( is_readable( $softkom_leads_file ) ) {
    require_once $softkom_leads_file;
}

$softkom_commercial_catalogue_file = get_stylesheet_directory()
    . '/inc/data/assessment/commercial-catalogue.php';

if ( is_readable( $softkom_commercial_catalogue_file ) ) {
    require_once $softkom_commercial_catalogue_file;
}

$softkom_commercial_admin_file = get_stylesheet_directory()
    . '/inc/data/assessment/commercial-catalogue-admin.php';

if (
    is_admin() &&
    is_readable( $softkom_commercial_admin_file )
) {
    require_once $softkom_commercial_admin_file;
}
/*
 * Load Campaign Manager.
 */
$softkom_campaign_manager_file = get_stylesheet_directory()
    . '/inc/data/assessment/campaign-manager.php';

if ( is_readable( $softkom_campaign_manager_file ) ) {
    require_once $softkom_campaign_manager_file;
}





$softkom_recurring_file = get_stylesheet_directory()
    . '/inc/data/assessment/funnel-recurring-revenue.php';

if ( is_readable( $softkom_recurring_file ) ) {
    require_once $softkom_recurring_file;
}
/**
 * Theme supports required by Softkom V3 chrome (Custom Logo / Site Identity).
 */
function softkom_v3_setup() {
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 72,
			'width'       => 220,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		)
	);
}
add_action( 'after_setup_theme', 'softkom_v3_setup', 20 );

/**
 * Softkom header Custom Logo: full-size source for crisp retina scaling + homepage link.
 *
 * @param string $html          Custom logo HTML.
 * @param int    $blog_id       Blog ID.
 * @param int    $custom_logo_id Attachment ID (WP 5.5+).
 * @return string
 */
function softkom_v3_get_custom_logo_filter( $html, $blog_id = 0 ) {
	unset( $blog_id );
	if ( ! softkom_v3_is_system_page() || ! has_custom_logo() ) {
		return $html;
	}

	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( ! $custom_logo_id ) {
		return $html;
	}

	$alt = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true );
	if ( '' === $alt ) {
		$alt = get_bloginfo( 'name', 'display' );
	}

	// Prefer theme header lockup (smaller) over full-size media library PNG.
	// First call (header) is eager; subsequent (footer) is lazy.
	static $logo_render_count = 0;
	$logo_render_count++;
	$loading = ( 1 === $logo_render_count ) ? 'eager' : 'lazy';

	$header_webp = get_stylesheet_directory() . '/assets/images/softkom-logo-header.webp';
	$header_125  = get_stylesheet_directory() . '/assets/images/softkom-logo-header-125.png';
	$header_png  = get_stylesheet_directory() . '/assets/images/softkom-logo-header.png';
	if ( is_readable( $header_webp ) ) {
		$img = sprintf(
			'<img class="custom-logo" src="%1$s" alt="%2$s" width="125" height="36" decoding="async" loading="%3$s" />',
			esc_url( get_stylesheet_directory_uri() . '/assets/images/softkom-logo-header.webp' ),
			esc_attr( $alt ),
			esc_attr( $loading )
		);
	} elseif ( is_readable( $header_125 ) ) {
		$img = sprintf(
			'<img class="custom-logo" src="%1$s" alt="%2$s" width="125" height="36" decoding="async" loading="%3$s" />',
			esc_url( get_stylesheet_directory_uri() . '/assets/images/softkom-logo-header-125.png' ),
			esc_attr( $alt ),
			esc_attr( $loading )
		);
	} elseif ( is_readable( $header_png ) ) {
		$img = sprintf(
			'<img class="custom-logo" src="%1$s" alt="%2$s" width="125" height="36" decoding="async" loading="%3$s" />',
			esc_url( get_stylesheet_directory_uri() . '/assets/images/softkom-logo-header.png' ),
			esc_attr( $alt ),
			esc_attr( $loading )
		);
	} else {
		$img = wp_get_attachment_image(
			$custom_logo_id,
			'medium',
			false,
			array(
				'class'    => 'custom-logo',
				'alt'      => $alt,
				'loading'  => $loading,
				'decoding' => 'async',
				'sizes'    => '220px',
			)
		);
	}

	if ( ! $img ) {
		return $html;
	}

	// Strip accidental fetchpriority=high from footer instances (WP may inject it).
	$img = preg_replace( '/\sfetchpriority=["\']high["\']/i', '', $img );

	return sprintf(
		'<a href="%1$s" class="custom-logo-link" rel="home" aria-label="%2$s">%3$s</a>',
		esc_url( home_url( '/' ) ),
		esc_attr( $alt ),
		$img
	);
}
add_filter( 'get_custom_logo', 'softkom_v3_get_custom_logo_filter', 10, 2 );

/**
 * One-time local/staging bootstrap: ensure approved Softkom light lockup is Custom Logo.
 *
 * Uses the web PHP runtime (mysqli available). Skips production hostnames.
 */
function softkom_v3_ensure_custom_logo() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$local = in_array( $host, array( '127.0.0.1', 'localhost' ), true )
		|| ( is_string( $host ) && false !== strpos( $host, '.local' ) );
	if ( ! $local ) {
		return;
	}

	$flag = (string) get_option( 'softkom_v3_logo_lockup_version', '' );
	if ( 'light-1' === $flag && has_custom_logo() ) {
		$current_id = (int) get_theme_mod( 'custom_logo' );
		$file       = $current_id ? get_attached_file( $current_id ) : '';
		if ( $file && false !== strpos( basename( $file ), 'softkom-logo-light' ) ) {
			return;
		}
	}

	if ( ! function_exists( 'media_handle_sideload' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$candidates = array(
		WP_CONTENT_DIR . '/uploads/2026/07/softkom-logo-light.png',
		WP_CONTENT_DIR . '/uploads/2026/01/softkom-logo-light.png',
		get_stylesheet_directory() . '/assets/images/softkom-logo-light.png',
	);

	$source = '';
	foreach ( $candidates as $path ) {
		if ( is_readable( $path ) ) {
			$source = $path;
			break;
		}
	}
	if ( '' === $source ) {
		return;
	}

	// Reuse existing media attachment when present.
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_wp_attached_file',
					'value'   => 'softkom-logo-light.png',
					'compare' => 'LIKE',
				),
			),
		)
	);

	$attachment_id = $existing ? (int) $existing[0] : 0;

	if ( ! $attachment_id ) {
		$tmp = wp_tempnam( 'softkom-logo-light.png' );
		if ( ! $tmp || ! copy( $source, $tmp ) ) {
			return;
		}
		$file_array = array(
			'name'     => 'softkom-logo-light.png',
			'tmp_name' => $tmp,
		);
		$attachment_id = media_handle_sideload( $file_array, 0, 'Softkom logo' );
		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return;
		}
	}

	update_post_meta( $attachment_id, '_wp_attachment_image_alt', 'Softkom Solutions' );
	wp_update_post(
		array(
			'ID'         => $attachment_id,
			'post_title' => 'Softkom Solutions',
		)
	);
	set_theme_mod( 'custom_logo', (int) $attachment_id );
	update_option( 'softkom_v3_logo_lockup_version', 'light-1', false );
}
add_action( 'init', 'softkom_v3_ensure_custom_logo', 30 );

/**
 * Whether the current request is a Softkom V3 redesigned page.
 *
 * @return bool
 */
function softkom_v3_is_system_page() {
	if ( is_front_page() || is_404() ) {
		return true;
	}
	if ( ! is_page() ) {
		return false;
	}
	$slugs = array(
		'services',
		'services-4',
		'case-studies',
		'company',
		'assessment',
              'insights-3',
		'insights',
		'contact',
		'industries',
		'platforms',
		'marketplaceos',
		'brick-alpha',
		'privacy-policy',
		'cookie',
		'terms-of-service',
		// RC2.3 P0 service decision pages (child pages under /services/).
		'business-systems',
		'process-automation',
		'process-integrations',
		'ai-automation',
		'compliance-platforms',
	);
	if ( is_page( $slugs ) ) {
		return true;
	}
	// Children of Solutions or Platforms hubs use Softkom V3 chrome.
	if ( is_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post && (int) $post->post_parent > 0 ) {
			$parent = get_post( (int) $post->post_parent );
			if ( $parent instanceof WP_Post && in_array( $parent->post_name, array( 'services', 'platforms' ), true ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Legacy Elementor slug redirects (keep bookmarks working after clean permalinks).
 */
function softkom_v3_legacy_slug_redirects() {
	global $wp;

	$candidates = array();
	if ( isset( $wp->request ) && is_string( $wp->request ) && '' !== $wp->request ) {
		$candidates[] = trim( $wp->request, '/' );
	}
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$uri_path = (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH );
		$candidates[] = trim( $uri_path, '/' );
	}

	$map = array(
		'services-4' => '/services/',
	);

	foreach ( array_unique( array_filter( $candidates ) ) as $path ) {
		if ( isset( $map[ $path ] ) ) {
			wp_safe_redirect( home_url( $map[ $path ] ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'softkom_v3_legacy_slug_redirects', 0 );

/**
 * Resource hints for fonts (preconnect).
 */
function softkom_v3_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'softkom_v3_resource_hints', 10, 2 );

/**
 * Build / refresh a single Softkom CSS bundle to cut HTTP/1.1 round-trips.
 *
 * Source files remain authoritative; the bundle regenerates when any source is newer.
 *
 * @return string Absolute filesystem path to the bundle, or empty string on failure.
 */
function softkom_v3_ensure_css_bundle() {
	$dir     = get_stylesheet_directory() . '/assets/css';
	$bundle  = $dir . '/softkom-bundle.css';
	$sources = array(
		'softkom-tokens.css',
		'softkom-components.css',
		'softkom-chrome.css',
		'softkom-diagrams.css',
		'softkom-home.css',
		'softkom-product.css',
	);

	$newest = 0;
	foreach ( $sources as $file ) {
		$path = $dir . '/' . $file;
		if ( ! is_readable( $path ) ) {
			return '';
		}
		$newest = max( $newest, (int) filemtime( $path ) );
	}

	if ( is_readable( $bundle ) && (int) filemtime( $bundle ) >= $newest ) {
		return $bundle;
	}

	$out = "/* Softkom V3 CSS bundle Ã¢â‚¬â€ generated; edit source files, not this file. */\n";
	foreach ( $sources as $file ) {
		$out .= "\n/* --- {$file} --- */\n";
		$out .= file_get_contents( $dir . '/' . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	if ( false === file_put_contents( $bundle, $out ) ) {
		return '';
	}

	return $bundle;
}

/**
 * Enqueue Softkom V3 assets.
 */
function softkom_v3_enqueue_assets() {
	wp_enqueue_style(
		'softkom-v3-style',
		get_stylesheet_uri(),
		array(),
		SOFTKOM_V3_VERSION
	);

	// Single Softkom type family stack Ã¢â‚¬â€ Inter for UI/body, Plus Jakarta for display.
	wp_enqueue_style(
		'softkom-v3-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap',
		array(),
		null
	);

	if ( ! softkom_v3_is_system_page() ) {
		// Non-system pages still need Astra parent chrome.
		wp_enqueue_style(
			'astra-theme-css',
			get_template_directory_uri() . '/style.css',
			array(),
			wp_get_theme( 'astra' )->get( 'Version' )
		);
		return;
	}

	$base   = get_stylesheet_directory_uri() . '/assets';
	$bundle = softkom_v3_ensure_css_bundle();

	if ( $bundle ) {
		wp_enqueue_style(
			'softkom-v3-bundle',
			$base . '/css/softkom-bundle.css',
			array( 'softkom-v3-fonts' ),
			(string) filemtime( $bundle )
		);
	} else {
		// Fallback: individual files if bundle write fails (read-only FS).
		$prev = '';
		foreach ( array( 'tokens', 'components', 'chrome', 'diagrams', 'home', 'product' ) as $slug ) {
			$deps = $prev ? array( $prev ) : array( 'softkom-v3-fonts' );
			$handle = 'softkom-v3-' . $slug;
			wp_enqueue_style(
				$handle,
				$base . '/css/softkom-' . $slug . '.css',
				$deps,
				SOFTKOM_V3_VERSION
			);
			$prev = $handle;
		}
	}

	wp_enqueue_script(
		'softkom-v3-site',
		$base . '/js/softkom-site.js',
		array(),
		SOFTKOM_V3_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'softkom_v3_enqueue_assets', 15 );

/**
 * Dequeue Astra / Elementor / HFE chrome Softkom V3 does not use on system pages.
 * Keeps shortcode/Elementor page shell; removes unused widget CSS that blocks paint.
 */
function softkom_v3_dequeue_bloat() {
	if ( ! softkom_v3_is_system_page() ) {
		return;
	}

	$styles = array(
		// Elementor FA / icons (legacy handles).
		'elementor-icons',
		'elementor-icons-shared-0',
		'elementor-icons-fa-solid',
		'elementor-icons-fa-brands',
		'elementor-icons-fa-regular',
		'font-awesome-5-all',
		'font-awesome-4-shim',
		'widget-icon-list',
		'widget-social-icons',
		'e-animation-fadeIn',
		'e-animation-fadeInUp',
		// SureForms Ã¢â‚¬â€ payment CSS unused on Softkom pages.
		'sureforms-payment-history',
		'srfm-payment-history',
		// Header Footer Elementor Ã¢â‚¬â€ Softkom owns chrome; HFE CSS is pure dead weight.
		'hfe-style',
		'hfe-widgets-style',
		'hfe-elementor-icons',
		'hfe-icons-list',
		'hfe-social-icons',
		'hfe-social-share-icons-brands',
		'hfe-social-share-icons-fontawesome',
		'hfe-nav-menu-icons',
		// Astra duplicate Google fonts (Softkom loads Inter + Plus Jakarta only).
		'astra-google-fonts',
		'astra-google-fonts-css',
	);
	foreach ( $styles as $handle ) {
		wp_dequeue_style( $handle );
		wp_deregister_style( $handle );
	}

	$scripts = array(
		'elementor-webpack-runtime',
		'elementor-frontend-modules',
		'elementor-waypoints',
		'swiper',
		'elementor-frontend',
	);
	// Only strip Elementor JS when the Softkom shortcode owns the page body
	// (homepage / platforms / product pages) Ã¢â‚¬â€ avoid breaking non-Softkom Elementor layouts.
	$softkom_owned = is_front_page()
		|| is_page( array( 'platforms', 'marketplaceos', 'brick-alpha', 'company', 'contact', 'insights-3', 'insights', 'services', 'industries', 'case-studies' ) );
	if ( $softkom_owned ) {
		foreach ( $scripts as $handle ) {
			wp_dequeue_script( $handle );
			wp_deregister_script( $handle );
		}
		// Softkom site JS has no jQuery dependency; drop migrate on owned pages.
		wp_dequeue_script( 'jquery-migrate' );
		wp_deregister_script( 'jquery-migrate' );
		wp_dequeue_script( 'astra-theme-js' );
		wp_deregister_script( 'astra-theme-js' );

		// Softkom composers own layout Ã¢â‚¬â€ Elementor kit/frontend CSS is unused weight.
		wp_dequeue_style( 'elementor-frontend' );
		wp_deregister_style( 'elementor-frontend' );
		wp_dequeue_style( 'elementor-icons' );

		// Softkom tokens/components replace Astra chrome; keep parent only if bundle missing.
		wp_dequeue_style( 'astra-theme-css' );
		wp_deregister_style( 'astra-theme-css' );

		global $wp_styles;
		if ( isset( $wp_styles->registered ) && is_array( $wp_styles->queue ) ) {
			foreach ( $wp_styles->queue as $handle ) {
				if ( 0 === strpos( $handle, 'elementor-post-' ) || 0 === strpos( $handle, 'elementor-gf-' ) ) {
					wp_dequeue_style( $handle );
				}
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'softkom_v3_dequeue_bloat', 100 );

/**
 * Stop Elementor from printing Roboto (and other) Google Fonts on Softkom pages.
 *
 * @param bool $print Whether Elementor should print Google Fonts.
 * @return bool
 */
function softkom_v3_disable_elementor_google_fonts( $print ) {
	if ( softkom_v3_is_system_page() ) {
		return false;
	}
	return $print;
}
add_filter( 'elementor/frontend/print_google_fonts', 'softkom_v3_disable_elementor_google_fonts' );

/**
 * Softkom V3 body classes.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function softkom_v3_body_class( $classes ) {
	if ( softkom_v3_is_system_page() ) {
		$classes[] = 'softkom-v3';
	}
	if ( is_front_page() ) {
		$classes[] = 'softkom-v3-home';
	}
	return $classes;
}
add_filter( 'body_class', 'softkom_v3_body_class' );

/**
 * Remove Astra + HFE header/footer from the DOM on Softkom V3 pages.
 * CSS-hiding alone still downloads footer widget images (perf regression).
 */
function softkom_v3_disable_astra_chrome() {
	if ( ! softkom_v3_is_system_page() ) {
		return;
	}
	add_filter( 'astra_get_option_header-main-rt-section', '__return_empty_string' );
	remove_action( 'astra_header', 'astra_header_markup' );
	remove_action( 'astra_footer', 'astra_footer_markup' );
	remove_action( 'astra_footer_content', 'astra_advanced_footer_markup', 1 );

	// Astra 4 builder footer/header (still renders #colophon when only astra_footer_markup is removed).
	if ( class_exists( 'Astra_Builder_Footer' ) ) {
		remove_action( 'astra_footer', array( Astra_Builder_Footer::get_instance(), 'footer_markup' ) );
	}
	if ( class_exists( 'Astra_Builder_Header' ) ) {
		remove_action( 'astra_header', array( Astra_Builder_Header::get_instance(), 'header_markup' ) );
	}
	remove_all_actions( 'astra_header' );
	remove_all_actions( 'astra_footer' );

	// Header Footer Elementor Ã¢â‚¬â€ Softkom chrome replaces these.
	add_filter( 'hfe_header_enabled', '__return_false' );
	add_filter( 'hfe_footer_enabled', '__return_false' );
	add_filter( 'enable_hfe_render', '__return_false' );
}
add_action( 'wp', 'softkom_v3_disable_astra_chrome', 20 );

/**
 * Load shared data arrays once.
 *
 * Top-level files load first (catalog, graphics, icons, services-depth).
 * Phase 4 modules then load in dependency order from subdirectories.
 */
function softkom_v3_load_data() {
	static $loaded = false;
	if ( $loaded ) {
		return;
	}
	$dir = get_stylesheet_directory() . '/inc/data';
	foreach ( glob( $dir . '/*.php' ) as $file ) {
		require_once $file;
	}

	$modules = array(
		'frameworks',
		'insights',
		'case-studies',
		'assessment',
		'leadership',
	);
	$priority_names = array(
		'schema.php',
		'taxonomy.php',
		'evidence-levels.php',
		'library.php',
		'sections.php',
		'scoring.php',
                'funnel-scoring.php',
                'recommendations.php',
		'question-bank.php',
                'funnel-questions.php',
                'funnel-solutions.php',
                'funnel-signals.php',
                'funnel-qualification.php',
                'funnel-security.php',
                'funnel-leads.php',
                'commercial-catalogue.php',
              'commercial-catalogue-admin.php',
              'funnel-recurring-revenue.php',
              'funnel-ajax.php',
		'profile.php',
		'registry.php',
	);

	foreach ( $modules as $module ) {
		$path = $dir . '/' . $module;
		if ( ! is_dir( $path ) ) {
			continue;
		}
		$seen = array();
		foreach ( $priority_names as $name ) {
			$file = $path . '/' . $name;
			if ( is_readable( $file ) ) {
				require_once $file;
				$seen[ $file ] = true;
			}
		}
		foreach ( glob( $path . '/*.php' ) as $file ) {
			if ( empty( $seen[ $file ] ) ) {
				require_once $file;
			}
		}
	}

	$loaded = true;
}

/**
 * Load the Funnel V2 handler before WordPress dispatches its AJAX action.
 *
 * The normal data loader is otherwise reached while rendering a shortcode or
 * component, which never happens during a standalone admin-ajax.php request.
 */
function softkom_v3_load_assessment_ajax_data() {
	if ( ! wp_doing_ajax() ) {
		return;
	}

	$action = isset( $_REQUEST['action'] )
		? sanitize_key( wp_unslash( $_REQUEST['action'] ) )
		: '';

	if ( 'softkom_assessment_submit' === $action ) {
		softkom_v3_load_data();
	}
}
add_action( 'init', 'softkom_v3_load_assessment_ajax_data', 1 );

/**
 * Render a library component (args only Ã¢â‚¬â€ no page-local markup).
 *
 * @param string               $slug Component slug under template-parts/components/.
 * @param array<string,mixed>  $args Component props.
 * @return string
 */
function softkom_v3_component( $slug, $args = array() ) {
	// Ensure registries (including CTA helpers) load for chrome used outside shortcodes Ã¢â‚¬â€ e.g. 404.
	softkom_v3_load_data();
	$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( (string) $slug ) );
	$path = get_stylesheet_directory() . '/template-parts/components/' . $slug . '.php';
	if ( ! file_exists( $path ) ) {
		return '';
	}
	ob_start();
	// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- controlled component API
	extract( $args, EXTR_SKIP );
	include $path;
	return ob_get_clean();
}

/**
 * Echo a library component.
 *
 * @param string              $slug Component slug.
 * @param array<string,mixed> $args Props.
 */
function softkom_v3_component_e( $slug, $args = array() ) {
	echo softkom_v3_component( $slug, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render a library section.
 *
 * @param string              $slug Section slug under template-parts/sections/.
 * @param array<string,mixed> $args Section props / filters.
 * @return string
 */
function softkom_v3_section( $slug, $args = array() ) {
	softkom_v3_load_data();
	$slug = preg_replace( '/[^a-z0-9\-]/', '', strtolower( (string) $slug ) );
	$path = get_stylesheet_directory() . '/template-parts/sections/' . $slug . '.php';
	if ( ! file_exists( $path ) ) {
		return '';
	}
	ob_start();
	// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	extract( $args, EXTR_SKIP );
	include $path;
	return ob_get_clean();
}

/**
 * Echo a library section.
 *
 * @param string              $slug Section slug.
 * @param array<string,mixed> $args Props.
 */
function softkom_v3_section_e( $slug, $args = array() ) {
	echo softkom_v3_section( $slug, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render a page composer partial.
 *
 * @param string $relative Relative path under template-parts/.
 * @return string
 */
function softkom_v3_render_partial( $relative ) {
	softkom_v3_load_data();
	ob_start();
	$partial = get_stylesheet_directory() . '/template-parts/' . ltrim( $relative, '/' );
	if ( file_exists( $partial ) ) {
		include $partial;
	}
	return ob_get_clean();
}

add_shortcode( 'softkom_home_v3', function () {
	return softkom_v3_render_partial( 'home-v3.php' );
} );
add_shortcode( 'softkom_services_v3', function () {
	return softkom_v3_render_partial( 'page-services.php' );
} );
add_shortcode( 'softkom_projects_v3', function () {
	return softkom_v3_render_partial( 'page-projects.php' );
} );
add_shortcode( 'softkom_about_v3', function () {
	return softkom_v3_render_partial( 'page-about.php' );
} );
add_shortcode( 'softkom_insights_v3', function () {
	return softkom_v3_render_partial( 'page-insights.php' );
} );
add_shortcode( 'softkom_contact_v3', function () {
	return softkom_v3_render_partial( 'page-contact.php' );
} );
add_shortcode( 'softkom_industries_v3', function () {
	return softkom_v3_render_partial( 'page-industries.php' );
} );
add_shortcode( 'softkom_legal_v3', function () {
	return softkom_v3_render_partial( 'page-legal.php' );
} );
add_shortcode( 'softkom_platforms_v3', function () {
	return softkom_v3_render_partial( 'page-platforms.php' );
} );
add_shortcode( 'softkom_product_marketplaceos_v3', function () {
	return softkom_v3_render_partial( 'page-product-marketplaceos.php' );
} );
add_shortcode( 'softkom_product_brick_alpha_v3', function () {
	return softkom_v3_render_partial( 'page-product-brick-alpha.php' );
} );

/**
 * Render a RC2.3 service decision page by catalog slug.
 *
 * @param string $slug Service id (e.g. business-systems).
 * @return string
 */
function softkom_v3_render_service_depth( $slug ) {
	softkom_v3_load_data();
	$softkom_service_slug = sanitize_title( (string) $slug );
	ob_start();
	$partial = get_stylesheet_directory() . '/template-parts/page-service-depth.php';
	if ( file_exists( $partial ) ) {
		include $partial;
	}
	return ob_get_clean();
}

add_shortcode(
	'softkom_service_depth_v3',
	function ( $atts ) {
		$atts = shortcode_atts(
			array(
				'service' => '',
			),
			$atts,
			'softkom_service_depth_v3'
		);
		return softkom_v3_render_service_depth( $atts['service'] );
	}
);
add_shortcode( 'softkom_service_business_systems_v3', function () {
	return softkom_v3_render_service_depth( 'business-systems' );
} );
add_shortcode( 'softkom_service_process_integrations_v3', function () {
	return softkom_v3_render_service_depth( 'process-automation' );
} );
add_shortcode( 'softkom_service_ai_automation_v3', function () {
	return softkom_v3_render_service_depth( 'ai-automation' );
} );
add_shortcode( 'softkom_service_compliance_platforms_v3', function () {
	return softkom_v3_render_service_depth( 'compliance-platforms' );
} );

/**
 * Document title for homepage / 404 (AIOSEO may be absent on local clone).
 *
 * @param array $parts Title parts.
 * @return array
 */
function softkom_v3_document_title_parts( $parts ) {
	if ( is_front_page() ) {
		$parts['title']   = 'Specialised Software Platforms';
		$parts['site']    = 'Softkom';
		$parts['tagline'] = '';
	}
	if ( is_404() ) {
		$parts['title'] = 'Page not found';
		$parts['site']  = 'Softkom Solutions';
	}
	return $parts;
}
add_filter( 'document_title_parts', 'softkom_v3_document_title_parts' );

/**
 * Resolve Softkom OG image URL (logo fallback until dedicated OG art ships).
 *
 * @return string
 */
function softkom_v3_og_image_url() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$src = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $src ) {
			return $src;
		}
	}
	$path = get_stylesheet_directory() . '/assets/images/softkom-logo-light.png';
	if ( is_readable( $path ) ) {
		return get_stylesheet_directory_uri() . '/assets/images/softkom-logo-light.png';
	}
	return get_stylesheet_directory_uri() . '/assets/images/softkom-logo.svg';
}

/**
 * Core meta + Open Graph + Twitter for Softkom system pages when no SEO plugin owns them.
 */
function softkom_v3_head_meta() {
	if ( ! function_exists( 'softkom_v3_is_system_page' ) || ! softkom_v3_is_system_page() ) {
		return;
	}

	$desc  = 'Softkom builds specialised software platforms for businesses and industries where generic software isn\'t enough Ã¢â‚¬â€ MarketplaceOS, Brick Alpha, Product Studio, and a growing product portfolio.';
	$title = 'Specialised Software Platforms | Softkom';
	$url   = home_url( '/' );

	if ( is_front_page() ) {
		// defaults above
	} elseif ( is_page( 'platforms' ) ) {
		$title = 'Platforms | Softkom';
		$desc  = 'Explore Softkom specialised software platforms Ã¢â‚¬â€ MarketplaceOS, Brick Alpha, Product Studio, and the roadmap ahead.';
		$url   = home_url( '/platforms/' );
	} elseif ( is_page( 'marketplaceos' ) || is_page( 'platforms/marketplaceos' ) ) {
		$title = 'MarketplaceOS | Softkom';
		$desc  = 'MarketplaceOS Ã¢â‚¬â€ specialised multi-channel operations software from Softkom.';
		$url   = home_url( '/platforms/marketplaceos/' );
	} elseif ( is_page( 'brick-alpha' ) || is_page( 'platforms/brick-alpha' ) ) {
		$title = 'Brick Alpha | Softkom';
		$desc  = 'Brick Alpha Ã¢â‚¬â€ investment intelligence for LEGO and collectibles from Softkom.';
		$url   = home_url( '/platforms/brick-alpha/' );
	} elseif ( is_page( 'company' ) ) {
		$title = 'Company | Softkom';
		$desc  = 'Softkom builds specialised software platforms Ã¢â‚¬â€ purpose-built products, long-term investment, human-centred AI.';
		$url   = home_url( '/company/' );
	} elseif ( is_page( 'contact' ) ) {
		$title = 'Book a Discovery Call | Softkom';
		$desc  = 'Start a focused conversation about Softkom platforms or a specialised product for your industry.';
		$url   = home_url( '/contact/' );
	} else {
		$queried = get_queried_object();
		if ( $queried && ! empty( $queried->post_title ) ) {
			$title = $queried->post_title . ' | Softkom';
			$url   = get_permalink( $queried );
		}
	}

	$image = softkom_v3_og_image_url();
	?>
	<meta name="description" content="<?php echo esc_attr( $desc ); ?>" />
	<link rel="canonical" href="<?php echo esc_url( $url ); ?>" />
	<meta property="og:type" content="website" />
	<meta property="og:site_name" content="Softkom" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $desc ); ?>" />
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
	<meta property="og:locale" content="en_ZA" />
	<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>" />
	<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
	<?php
}
add_action( 'wp_head', 'softkom_v3_head_meta', 5 );

/**
 * JSON-LD Organization + WebSite + platforms ItemList on homepage.
 * BreadcrumbList on product and platforms pages.
 */
function softkom_v3_schema_jsonld() {
	if ( ! function_exists( 'softkom_v3_is_system_page' ) || ! softkom_v3_is_system_page() ) {
		return;
	}

	if ( is_front_page() ) {
		$org = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Organization',
			'name'        => 'Softkom Solutions',
			'url'         => home_url( '/' ),
			'email'       => 'info@softkomsolutions.com',
			'telephone'   => '+27-74-993-3805',
			'logo'        => softkom_v3_og_image_url(),
			'address'     => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => 'Glensan, Johannesburg',
				'addressCountry'  => 'ZA',
			),
			'sameAs'      => array(
				'https://www.linkedin.com/company/softkomsolutions',
			),
			'description' => 'Softkom builds specialised software platforms for businesses and industries where generic software is not enough.',
		);

		$website = array(
			'@context'  => 'https://schema.org',
			'@type'     => 'WebSite',
			'name'      => 'Softkom Solutions',
			'url'       => home_url( '/' ),
			'publisher' => array(
				'@type' => 'Organization',
				'name'  => 'Softkom Solutions',
			),
		);

		$software = array(
			'@context'        => 'https://schema.org',
			'@type'           => 'ItemList',
			'name'            => 'Softkom Platforms',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => 'MarketplaceOS',
					'url'      => home_url( '/platforms/marketplaceos/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => 'Brick Alpha',
					'url'      => home_url( '/platforms/brick-alpha/' ),
				),
				array(
					'@type'    => 'ListItem',
					'position' => 3,
					'name'     => 'Product Studio',
					'url'      => home_url( '/platforms/#product-studio' ),
				),
			),
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		echo '<script type="application/ld+json">' . wp_json_encode( $software, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
		return;
	}

	$crumbs = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url( '/' ),
		),
	);

	if ( is_page( 'platforms' ) ) {
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => 'Platforms',
			'item'     => home_url( '/platforms/' ),
		);
	} elseif ( is_page( 'marketplaceos' ) || is_page( 'platforms/marketplaceos' ) ) {
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => 'Platforms',
			'item'     => home_url( '/platforms/' ),
		);
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 3,
			'name'     => 'MarketplaceOS',
			'item'     => home_url( '/platforms/marketplaceos/' ),
		);
	} elseif ( is_page( 'brick-alpha' ) || is_page( 'platforms/brick-alpha' ) ) {
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => 'Platforms',
			'item'     => home_url( '/platforms/' ),
		);
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 3,
			'name'     => 'Brick Alpha',
			'item'     => home_url( '/platforms/brick-alpha/' ),
		);
	} else {
		$queried = get_queried_object();
		if ( ! $queried || empty( $queried->post_title ) ) {
			return;
		}
		$crumbs[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => $queried->post_title,
			'item'     => get_permalink( $queried ),
		);
	}

	$breadcrumb = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $crumbs,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'softkom_v3_schema_jsonld', 20 );

/**
 * Discourage indexing on local/staging environments.
 *
 * @param array $robots Robots directives.
 * @return array
 */
function softkom_v3_robots( $robots ) {
	if ( 'local' === wp_get_environment_type() || false !== strpos( home_url(), '127.0.0.1' ) || false !== strpos( home_url(), '.local' ) ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'softkom_v3_robots' );



/**
 * Softkom V3 page provisioning.
 *
 * Safe to run repeatedly:
 * - updates existing pages
 * - creates missing pages
 * - assigns platform child hierarchy
 * - removes legacy Elementor page ownership
 */
function softkom_v3_install_pages() {

    /**
     * Create or update one Softkom-owned page.
     */
    $upsert_page = function( $slug, $title, $content, $parent_id = 0 ) {

        $path = $slug;

        if ( $parent_id > 0 ) {
            $parent = get_post( $parent_id );

            if ( $parent instanceof WP_Post ) {
                $path = $parent->post_name . '/' . $slug;
            }
        }

        /*
         * First look for the desired final path.
         */
        $existing = get_page_by_path( $path, OBJECT, 'page' );

        /*
         * If this should become a child page, reuse an existing
         * top-level version rather than creating a duplicate.
         */
        if ( ! $existing && $parent_id > 0 ) {
            $existing = get_page_by_path( $slug, OBJECT, 'page' );
        }

        $post_data = array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_parent'  => (int) $parent_id,
        );

        if ( $existing instanceof WP_Post ) {

            $post_data['ID'] = $existing->ID;

            $page_id = wp_update_post( $post_data, true );

        } else {

            $page_id = wp_insert_post( $post_data, true );
        }

        if ( is_wp_error( $page_id ) ) {
            return 0;
        }

        /*
         * V3 shortcode/template output now owns these pages.
         * Remove old Elementor document data so Elementor does
         * not continue rendering the previous page layout.
         */
        delete_post_meta( $page_id, '_elementor_data' );
        delete_post_meta( $page_id, '_elementor_edit_mode' );
        delete_post_meta( $page_id, '_elementor_page_settings' );
        delete_post_meta( $page_id, '_elementor_template_type' );

        /*
         * Return page to the normal WordPress template.
         */
        update_post_meta( $page_id, '_wp_page_template', 'default' );

        clean_post_cache( $page_id );

        return (int) $page_id;
    };

    /*
     * Parent hub must exist first.
     */
    $platforms_id = $upsert_page(
        'platforms',
        'Platforms',
        '[softkom_platforms_v3]'
    );

    /*
     * Platform products belong underneath /platforms/.
     */
    if ( $platforms_id ) {

        $upsert_page(
            'marketplaceos',
            'MarketplaceOS',
            '[softkom_product_marketplaceos_v3]',
            $platforms_id
        );

        $upsert_page(
            'brick-alpha',
            'Brick Alpha',
            '[softkom_product_brick_alpha_v3]',
            $platforms_id
        );
    }

    /*
     * Main site pages.
     */
    $upsert_page(
        'company',
        'Company',
        '[softkom_about_v3]'
    );

    $upsert_page(
        'insights',
        'Insights',
        '[softkom_insights_v3]'
    );

    $upsert_page(
        'contact',
        'Contact',
        '[softkom_contact_v3]'
    );

    $upsert_page(
        'services',
        'Services',
        '[softkom_services_v3]'
    );

    $upsert_page(
        'projects',
        'Projects',
        '[softkom_projects_v3]'
    );

    $upsert_page(
        'industries',
        'Industries',
        '[softkom_industries_v3]'
    );

    $upsert_page(
        'assessment',
        'Business Systems Assessment',
        '[softkom_assessment_v3]'
    );

    /*
     * Rebuild WordPress routes after changing hierarchy.
     */
    flush_rewrite_rules( false );
}


/**
 * One-time V3 installer trigger.
 *
 * Run while logged in:
 * /wp-admin/?softkom_v3_install=1
 */
function softkom_v3_install_trigger() {

    if (
        is_admin() &&
        current_user_can( 'manage_options' ) &&
        isset( $_GET['softkom_v3_install'] ) &&
        '1' === $_GET['softkom_v3_install']
    ) {

        softkom_v3_install_pages();

        wp_safe_redirect(
            admin_url( '?softkom_v3_installed=1' )
        );

        exit;
    }
}
add_action( 'admin_init', 'softkom_v3_install_trigger' );


/**
 * Funnel V2 assessment shortcode.
 */
function softkom_v3_assessment_shortcode() {
    return softkom_v3_render_partial( 'page-assessment.php' );
}

add_shortcode(
    'softkom_assessment_v3',
    'softkom_v3_assessment_shortcode'
);


/**
 * Funnel V2 assessment frontend assets.
 */
function softkom_v3_enqueue_assessment_assets() {

    global $post;

    $contains_shortcode = $post instanceof WP_Post
        && has_shortcode( $post->post_content, 'softkom_assessment_v3' );

    if ( ! is_page( 'assessment' ) && ! $contains_shortcode ) {
        return;
    }

    $base = get_stylesheet_directory_uri() . '/assets';

    wp_enqueue_style(
        'softkom-v3-assessment',
        $base . '/css/softkom-assessment.css',
        array(),
        SOFTKOM_V3_VERSION
    );

    wp_enqueue_script(
        'softkom-v3-assessment',
        $base . '/js/softkom-assessment.js',
        array(),
        SOFTKOM_V3_VERSION,
        true
    );

    wp_localize_script(
        'softkom-v3-assessment',
        'softkomAssessment',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce(
                'softkom_assessment_submit'
            ),
            'action'  => 'softkom_assessment_submit',
        )
    );
}

add_action(
    'wp_enqueue_scripts',
    'softkom_v3_enqueue_assessment_assets',
    30
);





