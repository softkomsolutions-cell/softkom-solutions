<?php
/**
 * Plugin Name: Softkom Revenue Lab Alpha
 * Description: AI-style customer simulation acquisition experience for the Softkom sales funnel.
 * Version: 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SOFTKOM_REVENUE_LAB_VERSION', '0.1.0' );

function softkom_revenue_lab_page_id() {
    $page = get_page_by_path( 'revenue-lab', OBJECT, 'page' );
    return $page ? (int) $page->ID : 0;
}

function softkom_revenue_lab_ensure_page() {
    if ( softkom_revenue_lab_page_id() || wp_installing() ) { return; }
    wp_insert_post(
        array(
            'post_title'   => 'Revenue Lab',
            'post_name'    => 'revenue-lab',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '[softkom_revenue_lab]',
            'meta_input'   => array( '_softkom_managed_page' => 'revenue-lab' ),
        )
    );
}
add_action( 'init', 'softkom_revenue_lab_ensure_page', 40 );

function softkom_revenue_lab_is_page() {
    return is_page( 'revenue-lab' );
}

function softkom_revenue_lab_enqueue() {
    if ( ! softkom_revenue_lab_is_page() ) { return; }

    $js_path  = WPMU_PLUGIN_DIR . '/softkom-revenue-lab.js';
    $css_path = WPMU_PLUGIN_DIR . '/softkom-revenue-lab.css';

    wp_enqueue_style(
        'softkom-revenue-lab',
        content_url( '/mu-plugins/softkom-revenue-lab.css' ),
        array(),
        is_readable( $css_path ) ? (string) filemtime( $css_path ) : SOFTKOM_REVENUE_LAB_VERSION
    );
    wp_enqueue_script(
        'softkom-revenue-lab',
        content_url( '/mu-plugins/softkom-revenue-lab.js' ),
        array(),
        is_readable( $js_path ) ? (string) filemtime( $js_path ) : SOFTKOM_REVENUE_LAB_VERSION,
        true
    );

    $wow_js_path  = WPMU_PLUGIN_DIR . '/softkom-revenue-lab-wow.js';
    $wow_css_path = WPMU_PLUGIN_DIR . '/softkom-revenue-lab-wow.css';
    wp_enqueue_style(
        'softkom-revenue-lab-wow',
        content_url( '/mu-plugins/softkom-revenue-lab-wow.css' ),
        array( 'softkom-revenue-lab' ),
        is_readable( $wow_css_path ) ? (string) filemtime( $wow_css_path ) : SOFTKOM_REVENUE_LAB_VERSION
    );
    wp_enqueue_script(
        'softkom-revenue-lab-wow',
        content_url( '/mu-plugins/softkom-revenue-lab-wow.js' ),
        array( 'softkom-revenue-lab' ),
        is_readable( $wow_js_path ) ? (string) filemtime( $wow_js_path ) : SOFTKOM_REVENUE_LAB_VERSION,
        true
    );
    wp_localize_script(
        'softkom-revenue-lab',
        'softkomRevenueLab',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'softkom_revenue_lab' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'softkom_revenue_lab_enqueue', 70 );

function softkom_revenue_lab_shortcode() {
    ob_start();
    ?>
    <main class="sk-rl-shell">
        <section class="sk-rl-hero">
            <div class="sk-rl-kicker">SOFTKOM REVENUE LAB · ALPHA</div>
            <h1>Send AI customers through your website before your real customers get there.</h1>
            <p>See who understands your offer, who gets stuck, who leaves, and what to fix first. Revenue Lab simulates buyer journeys against the public experience of your website.</p>
            <form class="sk-rl-form" id="sk-rl-scan-form">
                <label for="sk-rl-url">Website URL</label>
                <div class="sk-rl-url-row">
                    <input id="sk-rl-url" name="url" type="url" placeholder="https://yourcompany.co.za" required>
                    <button type="submit">Send in the AI customers</button>
                </div>
                <div class="sk-rl-competitor-inputs">
                    <label for="sk-rl-competitor">Optional competitor URL</label>
                    <input id="sk-rl-competitor" name="competitor_url" type="url" placeholder="https://competitor.co.za">
                </div>
                <p class="sk-rl-note">No login required. We inspect public pages only. Results are simulations, not guaranteed conversion forecasts.</p>
            </form>
        </section>

        <section class="sk-rl-running" id="sk-rl-running" hidden aria-live="polite">
            <div class="sk-rl-orbit"></div>
            <p class="sk-rl-running-title">Building your customer panel...</p>
            <div class="sk-rl-running-steps">
                <span data-step="1">Reading your homepage</span>
                <span data-step="2">Creating buyer personas</span>
                <span data-step="3">Testing buying signals</span>
                <span data-step="4">Finding friction and trust gaps</span>
            </div>
        </section>

        <section class="sk-rl-results" id="sk-rl-results" hidden>
            <div class="sk-rl-score-card">
                <div>
                    <div class="sk-rl-kicker">BUYER CONFIDENCE</div>
                    <div class="sk-rl-score"><span id="sk-rl-score">--</span><small>/100</small></div>
                </div>
                <div class="sk-rl-score-copy">
                    <h2 id="sk-rl-result-title">Your simulated customer test</h2>
                    <p id="sk-rl-summary"></p>
                </div>
            </div>

            <div class="sk-rl-wow-grid">
                <section class="sk-rl-panel sk-rl-panel--battle" id="sk-rl-battle-panel" hidden>
                    <div class="sk-rl-kicker">WEBSITE BATTLE</div>
                    <h3>Which website wins the buyer?</h3>
                    <div id="sk-rl-battle"></div>
                </section>
                <section class="sk-rl-panel sk-rl-panel--simulation" id="sk-rl-simulation-panel" hidden>
                    <div class="sk-rl-kicker">SIMULATE THE FIX</div>
                    <h3>What if the biggest gaps were repaired?</h3>
                    <div id="sk-rl-simulation"></div>
                </section>
            </div>

            <div class="sk-rl-grid">
                <section>
                    <div class="sk-rl-section-head">
                        <div>
                            <div class="sk-rl-kicker">CUSTOMER REPLAY</div>
                            <h2>Who stayed, who left, and why</h2>
                        </div>
                    </div>
                    <div id="sk-rl-personas" class="sk-rl-personas"></div>
                </section>

                <aside class="sk-rl-side">
                    <div class="sk-rl-panel">
                        <div class="sk-rl-kicker">WEBSITE X-RAY</div>
                        <h3>What the customers noticed</h3>
                        <div id="sk-rl-findings"></div>
                    </div>
                    <div class="sk-rl-panel sk-rl-panel--dark">
                        <div class="sk-rl-kicker">UNLOCK THE FULL TEST</div>
                        <h3>Get the complete customer panel and priority repair plan.</h3>
                        <form id="sk-rl-unlock-form">
                            <input name="name" type="text" placeholder="Your name" required>
                            <input name="company" type="text" placeholder="Company">
                            <input name="email" type="email" placeholder="Work email" required>
                            <button type="submit">Unlock full results</button>
                        </form>
                        <p id="sk-rl-unlock-status" class="sk-rl-note"></p>
                    </div>
                </aside>
            </div>
        </section>

        <section class="sk-rl-error" id="sk-rl-error" hidden></section>
    </main>
    <?php
    return ob_get_clean();
}
add_shortcode( 'softkom_revenue_lab', 'softkom_revenue_lab_shortcode' );

function softkom_revenue_lab_normalize_url( $raw ) {
    $raw = trim( (string) $raw );
    if ( '' === $raw ) { return new WP_Error( 'empty_url', 'Enter a website URL.' ); }
    if ( ! preg_match( '#^https?://#i', $raw ) ) { $raw = 'https://' . $raw; }

    $url = esc_url_raw( $raw, array( 'http', 'https' ) );
    if ( ! $url || ! wp_http_validate_url( $url ) ) {
        return new WP_Error( 'invalid_url', 'Enter a valid public website URL.' );
    }

    $host = wp_parse_url( $url, PHP_URL_HOST );
    if ( ! $host || in_array( strtolower( $host ), array( 'localhost', '127.0.0.1', '::1' ), true ) ) {
        return new WP_Error( 'private_url', 'Private or local URLs cannot be tested.' );
    }
    return $url;
}

function softkom_revenue_lab_text( DOMXPath $xpath, $query ) {
    $nodes = $xpath->query( $query );
    $parts = array();
    if ( $nodes ) {
        foreach ( $nodes as $node ) {
            $text = trim( preg_replace( '/\s+/', ' ', $node->textContent ) );
            if ( '' !== $text ) { $parts[] = $text; }
        }
    }
    return $parts;
}

function softkom_revenue_lab_scan_url( $url ) {
    $response = wp_safe_remote_get(
        $url,
        array(
            'timeout'     => 15,
            'redirection' => 3,
            'user-agent'  => 'SoftkomRevenueLab/0.1 (+https://softkomsolutions.com/revenue-lab/)',
            'headers'     => array( 'Accept' => 'text/html,application/xhtml+xml' ),
        )
    );
    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'fetch_failed', 'We could not reach that public website.' );
    }
    $code = (int) wp_remote_retrieve_response_code( $response );
    if ( $code < 200 || $code >= 400 ) {
        return new WP_Error( 'fetch_status', 'The website returned HTTP ' . $code . '.' );
    }

    $body = (string) wp_remote_retrieve_body( $response );
    if ( '' === trim( $body ) ) {
        return new WP_Error( 'empty_body', 'The website returned no readable page content.' );
    }
    if ( strlen( $body ) > 1500000 ) {
        $body = substr( $body, 0, 1500000 );
    }

    libxml_use_internal_errors( true );
    $dom = new DOMDocument();
    $loaded = $dom->loadHTML( '<?xml encoding="utf-8" ?>' . $body );
    libxml_clear_errors();
    if ( ! $loaded ) {
        return new WP_Error( 'parse_failed', 'The website could not be analysed.' );
    }
    $xpath = new DOMXPath( $dom );

    $titles   = softkom_revenue_lab_text( $xpath, '//title' );
    $h1s      = softkom_revenue_lab_text( $xpath, '//h1' );
    $headings = softkom_revenue_lab_text( $xpath, '//h1|//h2|//h3' );
    $buttons  = softkom_revenue_lab_text( $xpath, '//button|//a' );
    $paras    = softkom_revenue_lab_text( $xpath, '//p' );

    $visible = strtolower( implode( ' ', array_slice( array_merge( $headings, $paras ), 0, 160 ) ) );
    $cta_text = strtolower( implode( ' | ', array_slice( $buttons, 0, 140 ) ) );

    $signals = array(
        'has_h1'          => ! empty( $h1s ),
        'has_contact'     => (bool) preg_match( '/contact|enquir|quote|book|call|whatsapp|get started|schedule|request/', $cta_text ),
        'has_phone'       => (bool) $xpath->query( '//a[starts-with(@href,"tel:")]' )->length,
        'has_email'       => (bool) $xpath->query( '//a[starts-with(@href,"mailto:")]' )->length,
        'has_form'        => (bool) $xpath->query( '//form' )->length,
        'has_pricing'     => (bool) preg_match( '/pricing|price|packages|plans|from r[0-9]|zar|cost/', $visible ),
        'has_testimonial' => (bool) preg_match( '/testimonial|what our clients|reviews|customer stories|clients say/', $visible ),
        'has_proof'       => (bool) preg_match( '/case stud|projects|portfolio|our work|trusted by|years of experience|certified|accredit/', $visible ),
        'has_location'    => (bool) preg_match( '/johannesburg|gauteng|pretoria|cape town|durban|south africa|service area|areas we serve/', $visible ),
        'has_faq'         => (bool) preg_match( '/frequently asked|faq|questions/', $visible ),
        'has_about'       => (bool) preg_match( '/about us|who we are|our team|our story/', $cta_text . ' ' . $visible ),
        'word_count'      => str_word_count( wp_strip_all_tags( $visible ) ),
        'link_count'      => (int) $xpath->query( '//a[@href]' )->length,
        'form_count'      => (int) $xpath->query( '//form' )->length,
    );

    $industry = 'business services';
    $industry_map = array(
        'accounting' => array( 'accountant', 'accounting', 'bookkeeping', 'tax', 'payroll' ),
        'dental'     => array( 'dentist', 'dental', 'orthodont', 'tooth', 'teeth' ),
        'legal'      => array( 'law firm', 'attorney', 'lawyer', 'legal services' ),
        'construction'=> array( 'construction', 'builder', 'building contractor', 'renovation' ),
        'technology' => array( 'software', 'technology', 'automation', 'digital transformation', 'saas' ),
        'retail'     => array( 'shop', 'store', 'products', 'cart', 'checkout' ),
        'medical'    => array( 'clinic', 'doctor', 'medical', 'healthcare', 'practice' ),
        'property'   => array( 'property', 'real estate', 'estate agent', 'homes for sale' ),
    );
    foreach ( $industry_map as $candidate => $keywords ) {
        foreach ( $keywords as $keyword ) {
            if ( false !== strpos( $visible, $keyword ) ) { $industry = $candidate; break 2; }
        }
    }

    $score = 28;
    $score += $signals['has_h1'] ? 8 : 0;
    $score += $signals['has_contact'] ? 14 : 0;
    $score += ( $signals['has_phone'] || $signals['has_email'] ) ? 8 : 0;
    $score += $signals['has_form'] ? 10 : 0;
    $score += $signals['has_pricing'] ? 8 : 0;
    $score += $signals['has_testimonial'] ? 8 : 0;
    $score += $signals['has_proof'] ? 8 : 0;
    $score += $signals['has_location'] ? 4 : 0;
    $score += $signals['has_faq'] ? 4 : 0;
    $score = max( 10, min( 96, $score ) );

    $persona_templates = array(
        array( 'name' => 'The urgent buyer', 'goal' => 'Find the fastest way to contact the business and understand the next step.', 'needs' => array( 'has_contact', 'has_phone' ) ),
        array( 'name' => 'The comparison shopper', 'goal' => 'Decide why this business is a safer choice than a competitor.', 'needs' => array( 'has_proof', 'has_testimonial' ) ),
        array( 'name' => 'The price-conscious buyer', 'goal' => 'Understand likely cost or value before committing to an enquiry.', 'needs' => array( 'has_pricing', 'has_faq' ) ),
        array( 'name' => 'The cautious first-time buyer', 'goal' => 'Build enough trust to make contact.', 'needs' => array( 'has_about', 'has_proof' ) ),
        array( 'name' => 'The mobile local buyer', 'goal' => 'Confirm location or service area and contact the business quickly.', 'needs' => array( 'has_location', 'has_contact' ) ),
    );

    $personas = array();
    $successes = 0;
    foreach ( $persona_templates as $index => $persona ) {
        $met = 0;
        foreach ( $persona['needs'] as $need ) { if ( ! empty( $signals[ $need ] ) ) { $met++; } }
        $success = 2 === $met;
        $partial = 1 === $met;
        if ( $success ) { $successes++; }

        $status = $success ? 'converted' : ( $partial ? 'stuck' : 'left' );
        $reason = '';
        if ( 'converted' === $status ) {
            $reason = 'The site supplied the main information this buyer needed to continue confidently.';
        } elseif ( 'stuck' === $status ) {
            $reason = 'One important buying signal was present, but the journey still left a key question unanswered.';
        } else {
            $reason = 'The buyer could not find enough of the information needed to justify the next step.';
        }

        $personas[] = array(
            'id'     => $index + 1,
            'name'   => $persona['name'],
            'goal'   => $persona['goal'],
            'status' => $status,
            'reason' => $reason,
            'journey'=> array(
                'Homepage',
                $signals['has_proof'] ? 'Proof / credibility signal found' : 'Looked for proof',
                $signals['has_contact'] ? 'Conversion route found' : 'Looked for a clear next step',
            ),
        );
    }

    $findings = array();
    $finding_map = array(
        'has_contact'     => array( 'critical', 'Conversion path', 'Make the primary enquiry or booking action unmistakable.' ),
        'has_proof'       => array( 'high', 'Proof gap', 'Add case studies, project evidence, credentials or measurable outcomes close to buying decisions.' ),
        'has_testimonial' => array( 'medium', 'Social proof', 'Place credible customer proof where prospects are deciding whether to trust you.' ),
        'has_pricing'     => array( 'medium', 'Price confidence', 'Give buyers a pricing anchor, starting point, package range or clear explanation of how pricing works.' ),
        'has_location'    => array( 'medium', 'Local confidence', 'Make service area, physical location or geographic coverage easy to confirm.' ),
        'has_faq'         => array( 'low', 'Objection handling', 'Answer common buyer questions before they become reasons to abandon.' ),
    );
    foreach ( $finding_map as $key => $config ) {
        if ( empty( $signals[ $key ] ) ) {
            $findings[] = array( 'severity' => $config[0], 'title' => $config[1], 'recommendation' => $config[2] );
        }
    }
    if ( empty( $findings ) ) {
        $findings[] = array( 'severity' => 'positive', 'title' => 'Strong buying foundation', 'recommendation' => 'The public homepage exposes the core trust and action signals. The next test should focus on deeper journeys and competitor preference.' );
    }

    return array(
        'url'        => $url,
        'title'      => $titles ? $titles[0] : wp_parse_url( $url, PHP_URL_HOST ),
        'h1'         => $h1s ? $h1s[0] : '',
        'industry'   => $industry,
        'score'      => $score,
        'successes'  => $successes,
        'personas'   => $personas,
        'findings'   => array_slice( $findings, 0, 5 ),
        'signals'    => $signals,
        'summary'    => sprintf(
            '%d of 5 simulated buyers found enough buying signals to complete their intended journey. This is an early directional test of the public homepage, not a real conversion-rate forecast.',
            $successes
        ),
    );
}


function softkom_revenue_lab_simulate_fix( $result ) {
    $signals = isset( $result['signals'] ) && is_array( $result['signals'] ) ? $result['signals'] : array();
    $priority = array( 'has_contact', 'has_proof', 'has_testimonial', 'has_pricing', 'has_location', 'has_faq' );
    $fixed = $signals;
    $repairs = array();
    foreach ( $priority as $key ) {
        if ( empty( $fixed[ $key ] ) ) {
            $fixed[ $key ] = true;
            $repairs[] = $key;
            if ( count( $repairs ) >= 3 ) { break; }
        }
    }

    $before = (int) $result['successes'];
    $after = 0;
    $needs = array(
        array( 'has_contact', 'has_phone' ),
        array( 'has_proof', 'has_testimonial' ),
        array( 'has_pricing', 'has_faq' ),
        array( 'has_about', 'has_proof' ),
        array( 'has_location', 'has_contact' ),
    );
    foreach ( $needs as $pair ) {
        $met = 0;
        foreach ( $pair as $need ) {
            if ( ! empty( $fixed[ $need ] ) ) { $met++; }
        }
        if ( 2 === $met ) { $after++; }
    }

    $labels = array(
        'has_contact' => 'clearer primary conversion action',
        'has_proof' => 'stronger proof and case-study evidence',
        'has_testimonial' => 'credible social proof',
        'has_pricing' => 'pricing/value guidance',
        'has_location' => 'clear service-area information',
        'has_faq' => 'buyer objection handling',
    );

    return array(
        'before' => $before,
        'after'  => $after,
        'repairs'=> array_values( array_map( function( $key ) use ( $labels ) { return $labels[ $key ]; }, $repairs ) ),
        'note'   => 'Directional simulation only: this models whether missing buying signals are repaired. It is not a forecast of real conversion uplift.',
    );
}

function softkom_revenue_lab_battle( $primary, $competitor ) {
    $winner = 'tie';
    if ( (int) $primary['score'] > (int) $competitor['score'] ) { $winner = 'primary'; }
    if ( (int) $primary['score'] < (int) $competitor['score'] ) { $winner = 'competitor'; }

    $reasons = array();
    $signal_labels = array(
        'has_contact' => 'clear conversion path',
        'has_proof' => 'proof and credibility',
        'has_testimonial' => 'social proof',
        'has_pricing' => 'pricing/value clarity',
        'has_location' => 'local/service-area confidence',
        'has_faq' => 'objection handling',
    );
    foreach ( $signal_labels as $key => $label ) {
        $a = ! empty( $primary['signals'][ $key ] );
        $b = ! empty( $competitor['signals'][ $key ] );
        if ( $a !== $b ) {
            $reasons[] = array(
                'advantage' => $a ? 'primary' : 'competitor',
                'label' => $label,
            );
        }
    }

    return array(
        'winner' => $winner,
        'primary' => array(
            'title' => $primary['title'],
            'url' => $primary['url'],
            'score' => $primary['score'],
            'successes' => $primary['successes'],
        ),
        'competitor' => array(
            'title' => $competitor['title'],
            'url' => $competitor['url'],
            'score' => $competitor['score'],
            'successes' => $competitor['successes'],
        ),
        'reasons' => array_slice( $reasons, 0, 4 ),
    );
}

function softkom_revenue_lab_ajax_scan() {
    check_ajax_referer( 'softkom_revenue_lab', 'nonce' );
    $url = isset( $_POST['url'] ) ? softkom_revenue_lab_normalize_url( wp_unslash( $_POST['url'] ) ) : new WP_Error( 'missing_url', 'Enter a website URL.' );
    if ( is_wp_error( $url ) ) { wp_send_json_error( array( 'message' => $url->get_error_message() ), 400 ); }

    $result = softkom_revenue_lab_scan_url( $url );
    if ( is_wp_error( $result ) ) { wp_send_json_error( array( 'message' => $result->get_error_message() ), 422 ); }

    $result['simulation'] = softkom_revenue_lab_simulate_fix( $result );

    $competitor_raw = isset( $_POST['competitor_url'] ) ? trim( (string) wp_unslash( $_POST['competitor_url'] ) ) : '';
    if ( '' !== $competitor_raw ) {
        $competitor_url = softkom_revenue_lab_normalize_url( $competitor_raw );
        if ( ! is_wp_error( $competitor_url ) ) {
            $competitor = softkom_revenue_lab_scan_url( $competitor_url );
            if ( ! is_wp_error( $competitor ) ) {
                $result['battle'] = softkom_revenue_lab_battle( $result, $competitor );
            }
        }
    }

    $token = wp_generate_password( 24, false, false );
    set_transient( 'softkom_rl_' . $token, $result, 30 * MINUTE_IN_SECONDS );

    $preview = $result;
    $preview['personas'] = array_slice( $result['personas'], 0, 3 );
    $preview['findings'] = array_slice( $result['findings'], 0, 3 );
    $preview['token'] = $token;
    unset( $preview['signals'] );

    wp_send_json_success( $preview );
}
add_action( 'wp_ajax_softkom_revenue_lab_scan', 'softkom_revenue_lab_ajax_scan' );
add_action( 'wp_ajax_nopriv_softkom_revenue_lab_scan', 'softkom_revenue_lab_ajax_scan' );

function softkom_revenue_lab_store_lead( $result, $name, $company, $email ) {
    if ( ! post_type_exists( 'softkom_lead' ) ) { return 0; }

    $lead_id = wp_insert_post(
        array(
            'post_type'   => 'softkom_lead',
            'post_status' => 'private',
            'post_title'  => trim( $company ? $company . ' - Revenue Lab' : $name . ' - Revenue Lab' ),
        ),
        true
    );
    if ( is_wp_error( $lead_id ) ) { return 0; }

    $parts = preg_split( '/\s+/', trim( $name ), 2 );
    update_post_meta( $lead_id, '_softkom_first_name', isset( $parts[0] ) ? $parts[0] : '' );
    update_post_meta( $lead_id, '_softkom_last_name', isset( $parts[1] ) ? $parts[1] : '' );
    update_post_meta( $lead_id, '_softkom_email', $email );
    update_post_meta( $lead_id, '_softkom_company', $company );
    update_post_meta( $lead_id, '_softkom_traffic_source', 'revenue-lab' );
    update_post_meta( $lead_id, '_softkom_acquisition_mode', 'revenue-lab' );
    update_post_meta( $lead_id, '_softkom_revenue_lab_url', $result['url'] );
    update_post_meta( $lead_id, '_softkom_revenue_lab_score', (int) $result['score'] );
    update_post_meta( $lead_id, '_softkom_revenue_lab_successes', (int) $result['successes'] );
    update_post_meta( $lead_id, '_softkom_revenue_lab_industry', $result['industry'] );
    update_post_meta( $lead_id, '_softkom_revenue_lab_result', wp_json_encode( $result ) );
    update_post_meta( $lead_id, '_softkom_pipeline_stage', 'new' );

    $temperature = $result['score'] < 60 ? 'HOT' : ( $result['score'] < 78 ? 'WARM' : 'NURTURE' );
    update_post_meta( $lead_id, '_softkom_lead_temperature', $temperature );
    update_post_meta( $lead_id, '_softkom_score_purchase_intent', 72 );
    update_post_meta( $lead_id, '_softkom_score_commercial_fit', 70 );

    if ( function_exists( 'softkom_public_acquisition_activity' ) ) {
        softkom_public_acquisition_activity( $lead_id, 'revenue_lab_unlocked', '', $temperature );
    }

    return (int) $lead_id;
}

function softkom_revenue_lab_ajax_unlock() {
    check_ajax_referer( 'softkom_revenue_lab', 'nonce' );

    $token   = isset( $_POST['token'] ) ? sanitize_text_field( wp_unslash( $_POST['token'] ) ) : '';
    $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
    $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if ( ! $token || ! $name || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Enter your name and a valid work email.' ), 400 );
    }

    $result = get_transient( 'softkom_rl_' . $token );
    if ( ! is_array( $result ) ) {
        wp_send_json_error( array( 'message' => 'This test expired. Run the website test again.' ), 410 );
    }

    $lead_id = softkom_revenue_lab_store_lead( $result, $name, $company, $email );

    $admin_email = sanitize_email( get_option( 'admin_email' ) );
    if ( is_email( $admin_email ) ) {
        wp_mail(
            $admin_email,
            '[Softkom Revenue Lab] ' . ( $company ? $company : $name ) . ' unlocked results',
            implode( "\n", array(
                'Revenue Lab lead unlocked full results.',
                'Name: ' . $name,
                'Company: ' . ( $company ? $company : '-' ),
                'Email: ' . $email,
                'Website: ' . $result['url'],
                'Buyer Confidence: ' . $result['score'] . '/100',
                'Simulated successful journeys: ' . $result['successes'] . '/5',
                'Lead ID: ' . ( $lead_id ? $lead_id : 'not stored' ),
            ) )
        );
    }

    unset( $result['signals'] );
    $result['lead_id'] = $lead_id;
    $result['next_step_url'] = add_query_arg(
        array( 'source' => 'revenue-lab', 'email' => rawurlencode( $email ) ),
        home_url( '/contact/' )
    );

    wp_send_json_success( $result );
}
add_action( 'wp_ajax_softkom_revenue_lab_unlock', 'softkom_revenue_lab_ajax_unlock' );
add_action( 'wp_ajax_nopriv_softkom_revenue_lab_unlock', 'softkom_revenue_lab_ajax_unlock' );
