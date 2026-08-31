<?php
/**
 * Softkom Organic + AI Search Discovery Layer.
 *
 * Adds crawlable discovery content, structured data and source attribution
 * around the public assessment without replacing the active theme.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_organic_is_assessment() {
    if ( is_admin() ) { return false; }
    $path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
    return is_string( $path ) && '/assessment/' === trailingslashit( $path );
}

function softkom_organic_source() {
    $source = isset( $_GET['utm_source'] ) ? sanitize_key( wp_unslash( $_GET['utm_source'] ) ) : '';
    $medium = isset( $_GET['utm_medium'] ) ? sanitize_key( wp_unslash( $_GET['utm_medium'] ) ) : '';
    if ( $source ) { return array( $source, $medium ?: 'unknown' ); }

    $ref = isset( $_SERVER['HTTP_REFERER'] ) ? strtolower( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) ) : '';
    $map = array(
        'google.' => array( 'google', 'organic' ),
        'bing.' => array( 'bing', 'organic' ),
        'chatgpt.com' => array( 'chatgpt', 'ai-search' ),
        'perplexity.ai' => array( 'perplexity', 'ai-search' ),
        'copilot.microsoft.com' => array( 'copilot', 'ai-search' ),
        'gemini.google.com' => array( 'gemini', 'ai-search' ),
        'claude.ai' => array( 'claude', 'ai-search' ),
    );
    foreach ( $map as $needle => $value ) {
        if ( $ref && false !== strpos( $ref, $needle ) ) { return $value; }
    }
    return array( 'direct', 'unknown' );
}

function softkom_organic_assessment_schema() {
    if ( ! softkom_organic_is_assessment() ) { return; }
    $url = home_url( '/assessment/' );
    $graph = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'Organization',
                '@id' => home_url( '/#organization' ),
                'name' => 'Softkom Solutions',
                'url' => home_url( '/' ),
                'description' => 'South African business systems, AI automation and digital transformation consultancy.',
                'areaServed' => array( '@type' => 'Country', 'name' => 'South Africa' ),
            ),
            array(
                '@type' => 'WebPage',
                '@id' => $url . '#webpage',
                'url' => $url,
                'name' => 'Free Business Systems & AI Readiness Assessment | South Africa',
                'description' => 'A free 3-minute assessment for South African businesses to identify AI automation, workflow, sales and business-system opportunities.',
                'about' => array( 'AI automation', 'business process automation', 'custom business systems', 'sales automation', 'South Africa' ),
                'isPartOf' => array( '@id' => home_url( '/#organization' ) ),
            ),
            array(
                '@type' => 'Service',
                '@id' => $url . '#service',
                'name' => 'Business Systems & AI Readiness Assessment',
                'provider' => array( '@id' => home_url( '/#organization' ) ),
                'areaServed' => array( '@type' => 'Country', 'name' => 'South Africa' ),
                'serviceType' => 'AI automation and business systems assessment',
                'url' => $url,
            ),
            array(
                '@type' => 'FAQPage',
                'mainEntity' => array(
                    array( '@type' => 'Question', 'name' => 'What does the AI readiness assessment identify?', 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => 'It identifies high-value opportunities across AI automation, sales, customer service, workflows and business systems, then prioritises practical next steps.' ) ),
                    array( '@type' => 'Question', 'name' => 'Who is the assessment for?', 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => 'It is designed for South African SMEs and growing organisations that want to reduce manual work, improve systems and identify where AI or automation can create measurable value.' ) ),
                    array( '@type' => 'Question', 'name' => 'How long does the assessment take?', 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => 'About three minutes. Results include a maturity score and prioritised recommendations.' ) ),
                ),
            ),
        ),
    );
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}
add_action( 'wp_head', 'softkom_organic_assessment_schema', 30 );

function softkom_organic_assessment_markup() {
    list( $source, $medium ) = softkom_organic_source();
    ob_start();
    ?>
    <section id="softkom-organic-discovery" aria-labelledby="softkom-organic-title" style="max-width:1120px;margin:48px auto 72px;padding:0 24px;color:#1E293B;font-family:Inter,system-ui,sans-serif;box-sizing:border-box;">
      <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:20px;padding:32px;">
        <p style="margin:0 0 8px;font-weight:700;color:#2563EB;letter-spacing:.04em;text-transform:uppercase;font-size:13px;">AI Automation &amp; Business Systems · South Africa</p>
        <h2 id="softkom-organic-title" style="margin:0 0 14px;color:#0F172A;font-size:clamp(28px,4vw,40px);line-height:1.15;">Find where AI and automation can create the most value in your business</h2>
        <p style="font-size:18px;line-height:1.65;max-width:850px;">Softkom Solutions helps South African businesses replace repetitive manual work, disconnected spreadsheets and slow follow-up with practical automation, AI workflows and custom business systems. The free assessment above identifies the opportunities worth prioritising before you invest in technology.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin:28px 0;">
          <div><strong>Sales &amp; lead automation</strong><br><span>Capture, qualify, route and follow up opportunities faster.</span></div>
          <div><strong>Operations &amp; workflows</strong><br><span>Connect systems and remove repetitive administration.</span></div>
          <div><strong>AI customer service</strong><br><span>Improve response times while keeping human escalation.</span></div>
          <div><strong>Custom business systems</strong><br><span>Replace fragmented tools with systems built around your processes.</span></div>
        </div>
        <h3 style="color:#0F172A;">What you receive</h3>
        <p>A business-systems maturity score, an AI and automation opportunity score, prioritised recommendations and a practical next step based on your answers. The assessment takes about three minutes.</p>
        <h3 style="color:#0F172A;">Built for South African businesses</h3>
        <p>Recommendations consider the realities of growing SMEs and operational teams in South Africa. Softkom focuses on measurable business outcomes: time saved, faster sales response, better customer experience, cleaner data and scalable processes.</p>
      </div>
      <span hidden data-softkom-acquisition-source="<?php echo esc_attr( $source ); ?>" data-softkom-acquisition-medium="<?php echo esc_attr( $medium ); ?>"></span>
    </section>
    <?php
    return ob_get_clean();
}

/**
 * Append to the final WordPress page content after shortcodes have rendered.
 * This is deliberately independent of the assessment shortcode implementation.
 */
function softkom_organic_append_to_assessment_content( $content ) {
    if ( ! softkom_organic_is_assessment() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }
    if ( false !== strpos( $content, 'id="softkom-organic-discovery"' ) ) {
        return $content;
    }
    return $content . softkom_organic_assessment_markup();
}
add_filter( 'the_content', 'softkom_organic_append_to_assessment_content', 99 );

function softkom_organic_robots( $robots ) {
    if ( softkom_organic_is_assessment() ) {
        $robots['index'] = true;
        $robots['follow'] = true;
        unset( $robots['noindex'], $robots['nofollow'] );
    }
    return $robots;
}
add_filter( 'wp_robots', 'softkom_organic_robots', 20 );
