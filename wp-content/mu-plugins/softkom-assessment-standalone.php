<?php
/**
 * Softkom standalone assessment runtime.
 *
 * Allows the proven V3 assessment engine to run even when the active site
 * theme is not softkom-v3. This is intentionally scoped to /assessment/ so
 * production can launch the funnel without switching the entire live theme.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function softkom_assessment_runtime_base_dir() {
    return WP_CONTENT_DIR . '/softkom-assessment-runtime';
}

function softkom_assessment_runtime_base_url() {
    return content_url( '/softkom-assessment-runtime' );
}

function softkom_assessment_runtime_load_data() {
    static $loaded = false;
    if ( $loaded ) {
        return;
    }

    $dir = softkom_assessment_runtime_base_dir() . '/data';
    if ( ! is_dir( $dir ) ) {
        return;
    }

    $priority = array(
        'schema.php','taxonomy.php','evidence-levels.php','library.php','sections.php','scoring.php',
        'funnel-scoring.php','recommendations.php','question-bank.php','funnel-questions.php',
        'funnel-solutions.php','funnel-signals.php','funnel-qualification.php','funnel-security.php',
        'funnel-leads.php','commercial-catalogue.php','commercial-catalogue-admin.php',
        'funnel-recurring-revenue.php','funnel-ajax.php','profile.php','registry.php',
    );

    $seen = array();
    foreach ( $priority as $name ) {
        $file = $dir . '/' . $name;
        if ( is_readable( $file ) ) {
            require_once $file;
            $seen[ $file ] = true;
        }
    }

    foreach ( glob( $dir . '/*.php' ) as $file ) {
        if ( empty( $seen[ $file ] ) ) {
            require_once $file;
        }
    }

    $loaded = true;
}

function softkom_assessment_runtime_ajax_boot() {
    if ( ! wp_doing_ajax() ) {
        return;
    }

    $action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
    if ( 'softkom_assessment_submit' === $action ) {
        softkom_assessment_runtime_load_data();
    }
}
add_action( 'init', 'softkom_assessment_runtime_ajax_boot', 1 );

/**
 * Render the standalone assessment template.
 */
function softkom_assessment_runtime_render() {
    softkom_assessment_runtime_load_data();

    $template = softkom_assessment_runtime_base_dir() . '/page-assessment.php';
    if ( ! is_readable( $template ) ) {
        return '<p>Assessment runtime is not available.</p>';
    }

    ob_start();
    include $template;
    return ob_get_clean();
}

/**
 * Take ownership of the shortcode after themes/plugins have registered theirs.
 *
 * The live site still contains an older shortcode implementation. Returning
 * early when that shortcode existed caused production to keep rendering the
 * legacy assessment. We intentionally replace only this shortcode and only
 * with the packaged, tested V3 assessment runtime.
 */
function softkom_assessment_runtime_register_shortcode() {
    if ( shortcode_exists( 'softkom_assessment_v3' ) ) {
        remove_shortcode( 'softkom_assessment_v3' );
    }

    add_shortcode( 'softkom_assessment_v3', 'softkom_assessment_runtime_render' );
}
add_action( 'init', 'softkom_assessment_runtime_register_shortcode', 999 );

function softkom_assessment_runtime_assets() {
    if ( ! is_page( 'assessment' ) ) {
        return;
    }

    $base_dir = softkom_assessment_runtime_base_dir();
    $base_url = softkom_assessment_runtime_base_url();
    $css = $base_dir . '/softkom-assessment.css';
    $js  = $base_dir . '/softkom-assessment.js';

    wp_enqueue_style(
        'softkom-assessment-runtime-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    if ( is_readable( $css ) ) {
        wp_enqueue_style(
            'softkom-assessment-runtime',
            $base_url . '/softkom-assessment.css',
            array( 'softkom-assessment-runtime-fonts' ),
            (string) filemtime( $css )
        );
    }

    if ( is_readable( $js ) ) {
        wp_enqueue_script(
            'softkom-assessment-runtime',
            $base_url . '/softkom-assessment.js',
            array(),
            (string) filemtime( $js ),
            true
        );

        wp_localize_script(
            'softkom-assessment-runtime',
            'softkomAssessment',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'softkom_assessment_submit' ),
                'action'  => 'softkom_assessment_submit',
            )
        );
    }

    $isolation = '
        body.softkom-assessment-live { margin:0; background:#f8fafc; font-family:Inter,system-ui,sans-serif; }
        body.softkom-assessment-live #masthead,
        body.softkom-assessment-live .site-header,
        body.softkom-assessment-live header.site-header,
        body.softkom-assessment-live #colophon,
        body.softkom-assessment-live .site-footer,
        body.softkom-assessment-live footer.site-footer,
        body.softkom-assessment-live .entry-header,
        body.softkom-assessment-live .page-header { display:none !important; }
        body.softkom-assessment-live .site-content,
        body.softkom-assessment-live #content,
        body.softkom-assessment-live .content-area,
        body.softkom-assessment-live .site-main,
        body.softkom-assessment-live article,
        body.softkom-assessment-live .entry-content { width:100% !important; max-width:none !important; margin:0 !important; padding:0 !important; }
        body.softkom-assessment-live .sk-assessment { min-height:100vh; }
    ';
    wp_add_inline_style( 'softkom-assessment-runtime', $isolation );
}
add_action( 'wp_enqueue_scripts', 'softkom_assessment_runtime_assets', 25 );

function softkom_assessment_runtime_body_class( $classes ) {
    if ( is_page( 'assessment' ) ) {
        $classes[] = 'softkom-assessment-live';
    }
    return $classes;
}
add_filter( 'body_class', 'softkom_assessment_runtime_body_class' );
