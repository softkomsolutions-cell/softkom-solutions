<?php
/**
 * Plugin Name: Softkom Search Discovery
 * Description: Technical SEO, internal linking and AI/search discovery support for Softkom acquisition pages.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_search_money_slugs() {
    return array(
        'ai-automation-south-africa',
        'business-process-automation-south-africa',
        'custom-business-systems-south-africa',
        'assessment',
    );
}
function softkom_search_is_money_page() {
    if ( ! is_page() ) return false;
    return in_array( get_post_field( 'post_name', get_queried_object_id() ), softkom_search_money_slugs(), true );
}

/* Explicit index/follow for the acquisition cluster. */
add_filter( 'wp_robots', function( $robots ) {
    if ( softkom_search_is_money_page() ) {
        unset( $robots['noindex'], $robots['nofollow'] );
        $robots['index'] = true;
        $robots['follow'] = true;
        $robots['max-image-preview'] = 'large';
        $robots['max-snippet'] = -1;
        $robots['max-video-preview'] = -1;
    }
    return $robots;
}, 100 );

/* Canonical safety net. SEO plugins can still output their own canonical. */
add_action( 'wp_head', function() {
    if ( ! softkom_search_is_money_page() ) return;
    if ( defined( 'AIOSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) return;
    echo '<link rel="canonical" href="' . esc_url( get_permalink() ) . '">' . "\n";
}, 2 );

/* Search-engine sitemap discovery. */
add_filter( 'robots_txt', function( $output, $public ) {
    if ( ! $public ) return $output;
    $line = 'Sitemap: ' . home_url( '/wp-sitemap.xml' );
    if ( strpos( $output, $line ) === false ) $output .= "\n" . $line . "\n";
    return $output;
}, 50, 2 );

/* Add crawlable related-solution links to each commercial acquisition page. */
add_filter( 'the_content', function( $content ) {
    if ( is_admin() || ! is_main_query() || ! in_the_loop() || ! is_page() ) return $content;
    $slug = get_post_field( 'post_name', get_queried_object_id() );
    $commercial = array(
        'ai-automation-south-africa' => 'AI Automation',
        'business-process-automation-south-africa' => 'Business Process Automation',
        'custom-business-systems-south-africa' => 'Custom Business Systems',
    );
    if ( ! isset( $commercial[$slug] ) ) return $content;
    $links = '';
    foreach ( $commercial as $target => $label ) {
        if ( $target === $slug ) continue;
        $links .= '<a href="' . esc_url( home_url( '/' . $target . '/' ) ) . '">' . esc_html( $label ) . '<span>→</span></a>';
    }
    $links .= '<a href="' . esc_url( home_url( '/assessment/' ) ) . '">Free AI &amp; Systems Assessment<span>→</span></a>';
    return $content . '<section class="sk-search-links" aria-label="Related Softkom solutions"><div><p>EXPLORE RELATED SOLUTIONS</p><h2>Continue exploring what fits your business</h2><nav>' . $links . '</nav></div></section>';
}, 80 );

add_action( 'wp_enqueue_scripts', function() {
    if ( ! softkom_search_is_money_page() ) return;
    wp_register_style( 'softkom-search-discovery', false, array(), '1.0.0' );
    wp_enqueue_style( 'softkom-search-discovery' );
    wp_add_inline_style( 'softkom-search-discovery', '.sk-search-links{font-family:Inter,system-ui,sans-serif;padding:70px 24px;background:#f8fafc;border-top:1px solid #e2e8f0}.sk-search-links>div{max-width:1180px;margin:auto}.sk-search-links p{margin:0 0 10px;color:#2563eb;font-size:12px;font-weight:800;letter-spacing:.09em}.sk-search-links h2{max-width:700px;margin:0 0 28px;color:#0f172a;font-size:clamp(28px,4vw,42px);line-height:1.1;letter-spacing:-.03em}.sk-search-links nav{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}.sk-search-links a{display:flex;justify-content:space-between;align-items:center;gap:15px;padding:18px 20px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;color:#0f172a!important;text-decoration:none!important;font-weight:700}.sk-search-links a:hover{border-color:#93c5fd;box-shadow:0 8px 24px rgba(15,23,42,.06)}.sk-search-links a span{color:#2563eb}@media(max-width:760px){.sk-search-links nav{grid-template-columns:1fr}}' );
}, 40 );

/* Entity graph shared across the acquisition cluster for AI/search understanding. */
add_action( 'wp_head', function() {
    if ( ! softkom_search_is_money_page() ) return;
    $graph = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'Organization',
                '@id' => home_url( '/#organization' ),
                'name' => 'Softkom Solutions',
                'url' => home_url( '/' ),
                'description' => 'South African business systems, AI automation and digital infrastructure company helping growing organisations replace manual processes and disconnected tools with scalable systems.',
                'areaServed' => array( '@type' => 'Country', 'name' => 'South Africa' ),
                'knowsAbout' => array( 'AI automation', 'business process automation', 'custom business systems', 'workflow automation', 'systems integration', 'business systems' ),
            ),
            array(
                '@type' => 'WebPage',
                '@id' => get_permalink() . '#webpage',
                'url' => get_permalink(),
                'name' => wp_get_document_title(),
                'isPartOf' => array( '@type' => 'WebSite', '@id' => home_url( '/#website' ), 'url' => home_url( '/' ), 'name' => 'Softkom Solutions' ),
                'about' => array( '@id' => home_url( '/#organization' ) ),
                'inLanguage' => 'en-ZA',
            ),
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 45 );
