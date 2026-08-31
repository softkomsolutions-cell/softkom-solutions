<?php
/**
 * Softkom standalone assessment runtime.
 *
 * Allows the proven V3 assessment engine to run even when the active site
 * theme is not softkom-v3. Public assessment rendering and its crawlable
 * organic discovery content are self-contained here for production safety.
 *
 * @package Softkom
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_assessment_runtime_base_dir() { return WP_CONTENT_DIR . '/softkom-assessment-runtime'; }
function softkom_assessment_runtime_base_url() { return content_url( '/softkom-assessment-runtime' ); }

function softkom_assessment_runtime_load_data() {
    static $loaded = false;
    if ( $loaded ) { return; }
    $dir = softkom_assessment_runtime_base_dir() . '/data';
    if ( ! is_dir( $dir ) ) { return; }
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
        if ( is_readable( $file ) ) { require_once $file; $seen[$file] = true; }
    }
    foreach ( glob( $dir . '/*.php' ) as $file ) {
        if ( empty( $seen[$file] ) ) { require_once $file; }
    }
    $loaded = true;
}

function softkom_assessment_runtime_admin_boot() {
    if ( is_admin() && ! wp_doing_ajax() ) { softkom_assessment_runtime_load_data(); }
}
add_action( 'init', 'softkom_assessment_runtime_admin_boot', 1 );

function softkom_assessment_runtime_ajax_boot() {
    if ( ! wp_doing_ajax() ) { return; }
    $action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
    if ( 'softkom_assessment_submit' === $action ) { softkom_assessment_runtime_load_data(); }
}
add_action( 'init', 'softkom_assessment_runtime_ajax_boot', 1 );

/** Crawlable discovery copy rendered directly after the assessment. */
function softkom_assessment_runtime_discovery_html() {
    ob_start(); ?>
    <section id="softkom-organic-discovery" aria-labelledby="softkom-organic-title" style="max-width:1120px;margin:48px auto 72px;padding:0 24px;color:#1E293B;font-family:Inter,system-ui,sans-serif;box-sizing:border-box;">
      <div style="background:#fff;border:1px solid #E2E8F0;border-radius:20px;padding:clamp(24px,4vw,40px);box-shadow:0 18px 45px rgba(15,23,42,.06);">
        <p style="margin:0 0 10px;font-weight:700;color:#2563EB;letter-spacing:.06em;text-transform:uppercase;font-size:13px;">AI Automation &amp; Business Systems · South Africa</p>
        <h2 id="softkom-organic-title" style="margin:0 0 16px;color:#0F172A;font-size:clamp(28px,4vw,40px);line-height:1.15;">Find where AI and automation can create the most value in your business</h2>
        <p style="font-size:18px;line-height:1.7;max-width:880px;margin:0;">Softkom Solutions helps South African businesses replace repetitive manual work, disconnected spreadsheets and slow follow-up with practical automation, AI workflows and custom business systems. The free assessment above identifies the opportunities worth prioritising before you invest in technology.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin:30px 0;">
          <div style="padding:18px;border:1px solid #E2E8F0;border-radius:14px;"><strong style="color:#0F172A;">Sales &amp; lead automation</strong><br><span>Capture, qualify, route and follow up opportunities faster.</span></div>
          <div style="padding:18px;border:1px solid #E2E8F0;border-radius:14px;"><strong style="color:#0F172A;">Operations &amp; workflows</strong><br><span>Connect systems and remove repetitive administration.</span></div>
          <div style="padding:18px;border:1px solid #E2E8F0;border-radius:14px;"><strong style="color:#0F172A;">AI customer service</strong><br><span>Improve response times while keeping human escalation.</span></div>
          <div style="padding:18px;border:1px solid #E2E8F0;border-radius:14px;"><strong style="color:#0F172A;">Custom business systems</strong><br><span>Replace fragmented tools with systems built around your processes.</span></div>
        </div>
        <h3 style="margin:28px 0 8px;color:#0F172A;font-size:22px;">What you receive</h3>
        <p style="margin:0;line-height:1.7;">A business-systems maturity score, an AI and automation opportunity score, prioritised recommendations and a practical next step based on your answers. The assessment takes about three minutes.</p>
        <h3 style="margin:28px 0 8px;color:#0F172A;font-size:22px;">Built for South African businesses</h3>
        <p style="margin:0;line-height:1.7;">Recommendations focus on measurable outcomes such as time saved, faster sales response, better customer experience, cleaner data and scalable processes.</p>
      </div>
    </section>
    <?php return ob_get_clean();
}

function softkom_assessment_runtime_render() {
    softkom_assessment_runtime_load_data();
    $template = softkom_assessment_runtime_base_dir() . '/page-assessment.php';
    if ( ! is_readable( $template ) ) { return '<p>Assessment runtime is not available.</p>'; }
    ob_start();
    include $template;
    $assessment = ob_get_clean();
    return $assessment . softkom_assessment_runtime_discovery_html();
}

function softkom_assessment_runtime_register_shortcode() {
    if ( shortcode_exists( 'softkom_assessment_v3' ) ) { remove_shortcode( 'softkom_assessment_v3' ); }
    add_shortcode( 'softkom_assessment_v3', 'softkom_assessment_runtime_render' );
}
add_action( 'init', 'softkom_assessment_runtime_register_shortcode', 999 );

function softkom_assessment_runtime_assets() {
    if ( ! is_page( 'assessment' ) ) { return; }
    $base_dir = softkom_assessment_runtime_base_dir();
    $base_url = softkom_assessment_runtime_base_url();
    $css = $base_dir . '/softkom-assessment.css';
    $js  = $base_dir . '/softkom-assessment.js';
    wp_enqueue_style( 'softkom-assessment-runtime-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );
    if ( is_readable( $css ) ) { wp_enqueue_style( 'softkom-assessment-runtime', $base_url . '/softkom-assessment.css', array( 'softkom-assessment-runtime-fonts' ), (string) filemtime( $css ) ); }
    if ( is_readable( $js ) ) {
        wp_enqueue_script( 'softkom-assessment-runtime', $base_url . '/softkom-assessment.js', array(), (string) filemtime( $js ), true );
        wp_localize_script( 'softkom-assessment-runtime', 'softkomAssessment', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'softkom_assessment_submit' ), 'action' => 'softkom_assessment_submit',
        ) );
    }
    $isolation = '
        body.softkom-assessment-live { margin:0; background:#f8fafc; font-family:Inter,system-ui,sans-serif; }
        body.softkom-assessment-live #masthead, body.softkom-assessment-live .site-header, body.softkom-assessment-live header.site-header,
        body.softkom-assessment-live #colophon, body.softkom-assessment-live .site-footer, body.softkom-assessment-live footer.site-footer,
        body.softkom-assessment-live .entry-header, body.softkom-assessment-live .page-header { display:none !important; }
        body.softkom-assessment-live .site-content, body.softkom-assessment-live #content, body.softkom-assessment-live .content-area,
        body.softkom-assessment-live .site-main, body.softkom-assessment-live article, body.softkom-assessment-live .entry-content {
            width:100% !important; max-width:none !important; margin:0 !important; padding:0 !important;
        }
        body.softkom-assessment-live .sk-assessment { min-height:100vh; }
        body.softkom-assessment-live #softkom-organic-discovery { display:block !important; visibility:visible !important; opacity:1 !important; }
    ';
    wp_add_inline_style( 'softkom-assessment-runtime', $isolation );
}
add_action( 'wp_enqueue_scripts', 'softkom_assessment_runtime_assets', 25 );

function softkom_assessment_runtime_body_class( $classes ) {
    if ( is_page( 'assessment' ) ) { $classes[] = 'softkom-assessment-live'; }
    return $classes;
}
add_filter( 'body_class', 'softkom_assessment_runtime_body_class' );
