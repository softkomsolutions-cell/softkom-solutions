<?php
/**
 * Plugin Name: Softkom Revenue & Efficiency Suite
 * Description: Productised commercial offers that help SMEs find revenue leakage, software waste, AI risk and operational friction.
 * Version: 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'SOFTKOM_REVENUE_SUITE_VERSION', '0.1.0' );

function softkom_revenue_suite_offers() {
    return array(
        'software-cost-check' => array(
            'title'       => 'Software Cost Check',
            'eyebrow'     => 'CUT UNNECESSARY SOFTWARE COSTS',
            'headline'    => 'Are you paying every month for software your business no longer needs?',
            'intro'       => 'Software subscriptions quietly accumulate. Staff leave, licences stay active, teams buy overlapping tools and annual renewals happen without anyone checking whether the software is still worth paying for.',
            'promise'     => 'We identify what you are paying for, where the waste is, what can be consolidated and what should be reviewed before the next renewal.',
            'pain'        => array(
                'Your monthly software bill keeps increasing.',
                'You are not sure how many subscriptions the business actually has.',
                'Former employees may still have paid licences or access.',
                'Different teams are paying for tools that do the same job.',
                'AI tools and SaaS renew automatically before anyone reviews them.',
                'Nobody owns the full picture of software spend.'
            ),
            'deliverables'=> array(
                'Software and licence inventory',
                'Monthly and annual spend map',
                'Unused and duplicated licence review',
                'Renewal calendar',
                'Overlapping product identification',
                'Priority savings plan'
            ),
            'metric'      => 'Potential annual software saving',
            'check_name'  => 'Software Cost Check',
            'check_price' => 'R3,500',
            'check_copy'  => 'A focused review of software subscriptions, licences, renewals and obvious duplication.',
            'fix_name'    => 'Savings Cleanup',
            'fix_price'   => 'From R7,500',
            'fix_copy'    => 'Softkom helps remove waste, consolidate tools and clean up licences and access.',
            'manage_name' => 'Software Cost Management',
            'manage_price'=> 'From R1,750/month',
            'manage_copy' => 'Ongoing licence, renewal, leaver and software-spend oversight.',
            'cta'         => 'Check My Software Spend'
        ),
        'ai-usage-risk-review' => array(
            'title'       => 'AI Usage & Risk Review',
            'eyebrow'     => 'KEEP AI USE UNDER CONTROL',
            'headline'    => 'Do you know how AI is already being used inside your business?',
            'intro'       => 'Your team may already be using ChatGPT, Copilot, Claude, Gemini, AI browser extensions and AI features inside other software. The problem is not using AI. The problem is not knowing what company information it can see, what it costs and who is responsible for it.',
            'promise'     => 'We show you which AI tools are in use, what they can access, where the practical risks are and what should be approved, restricted or reviewed.',
            'pain'        => array(
                'Employees use personal AI accounts for company work.',
                'Customer or company information may be pasted into unknown tools.',
                'Different teams are buying overlapping AI products.',
                'Nobody knows which AI tools are officially approved.',
                'AI-generated work may be reaching customers without review.',
                'Staff can leave while retaining access to company AI tools.'
            ),
            'deliverables'=> array(
                'AI tool and usage inventory',
                'Approved / unapproved tool register',
                'Data and access risk review',
                'AI software cost overview',
                'Practical employee usage policy',
                'Priority control plan'
            ),
            'metric'      => 'AI tools, access and cost under management',
            'check_name'  => 'AI Usage Check',
            'check_price' => 'R4,950',
            'check_copy'  => 'A practical review for smaller teams that need visibility over current AI use.',
            'fix_name'    => 'AI Control Setup',
            'fix_price'   => 'From R12,500',
            'fix_copy'    => 'Register, approval rules, practical policy, owners, permissions and vendor controls.',
            'manage_name' => 'AI Management',
            'manage_price'=> 'From R2,950/month',
            'manage_copy' => 'Ongoing reviews of new tools, access, cost, vendor changes and usage policy.',
            'cta'         => 'Review Our AI Usage'
        ),
        'lost-lead-recovery' => array(
            'title'       => 'Lost Lead Recovery',
            'eyebrow'     => 'RECOVER OPPORTUNITIES YOU ALREADY PAID FOR',
            'headline'    => 'You already paid to get the lead. Why pay to find them again?',
            'intro'       => 'Old enquiries, quotes, WhatsApps and previous customers often sit untouched while businesses keep spending money on new leads. We identify which opportunities are still worth pursuing and put a structured recovery process around them.',
            'promise'     => 'We find the recoverable opportunities already sitting in your CRM, inboxes, spreadsheets and quote history, then help you follow them up properly.',
            'pain'        => array(
                'Quotes are sent and never followed up.',
                'Website enquiries disappear into email inboxes.',
                'Old leads sit in spreadsheets or a CRM untouched.',
                'Previous customers are never asked to return.',
                'Sales teams focus only on new leads.',
                'Nobody knows why opportunities went quiet.'
            ),
            'deliverables'=> array(
                'Historic lead and quote review',
                'Recoverable opportunity segmentation',
                'Dormant-customer identification',
                'Follow-up workflow and messaging',
                'Recovery pipeline tracking',
                'Monthly recovery reporting'
            ),
            'metric'      => 'Recoverable opportunities and quoted value',
            'check_name'  => 'Recovery Opportunity Review',
            'check_price' => 'R5,500',
            'check_copy'  => 'Find the best opportunities already sitting in your existing sales data.',
            'fix_name'    => 'Recovery Setup',
            'fix_price'   => 'From R9,500',
            'fix_copy'    => 'Clean the data, build the follow-up workflow and set up tracking.',
            'manage_name' => 'Managed Recovery',
            'manage_price'=> 'From R2,950/month',
            'manage_copy' => 'Ongoing recovery campaigns, pipeline review and monthly reporting.',
            'cta'         => 'Find Our Lost Opportunities'
        ),
        'business-website-protection' => array(
            'title'       => 'Business Website Protection',
            'eyebrow'     => 'KNOW WHEN YOUR DIGITAL SALES PATH BREAKS',
            'headline'    => 'Your website can be online and still be losing business.',
            'intro'       => 'A site can load while its enquiry form is broken, checkout fails, SSL expires or a key customer journey stops working. Basic uptime monitoring will not tell you that.',
            'promise'     => 'We monitor the business-critical parts of your website so problems are found before customers have to tell you.',
            'pain'        => array(
                'Contact forms fail silently.',
                'Payment or checkout journeys stop working.',
                'SSL, domains or DNS changes create outages.',
                'Backups are assumed to work but never checked.',
                'Plugin or platform updates break key journeys.',
                'Nobody tests the website from a customer point of view.'
            ),
            'deliverables'=> array(
                'Website availability monitoring',
                'Critical form and journey checks',
                'SSL and domain expiry monitoring',
                'Backup and update health checks',
                'Critical-error review',
                'Monthly website health summary'
            ),
            'metric'      => 'Critical customer journeys monitored',
            'check_name'  => 'Website Health Check',
            'check_price' => 'R2,500',
            'check_copy'  => 'A once-off review of the customer-facing functions that matter to the business.',
            'fix_name'    => 'Website Repair',
            'fix_price'   => 'Quoted to scope',
            'fix_copy'    => 'Softkom fixes the issues found before they become recurring failures.',
            'manage_name' => 'Business Website Protection',
            'manage_price'=> 'From R1,250/month',
            'manage_copy' => 'Ongoing monitoring of business-critical website functions and technical health.',
            'cta'         => 'Check Our Website'
        ),
        'business-process-review' => array(
            'title'       => 'Business Process Review',
            'eyebrow'     => 'REMOVE MANUAL WORK THAT SHOULD NOT BE MANUAL',
            'headline'    => 'How many hours does your team lose every month to spreadsheets, retyping and chasing?',
            'intro'       => 'Many SMEs do not need a giant new software platform. They need the repetitive work between their existing systems to stop consuming staff time.',
            'promise'     => 'We map the manual work, identify the highest-value fixes and show what should be automated, integrated or left alone.',
            'pain'        => array(
                'The same information is captured more than once.',
                'Approvals are chased through email or WhatsApp.',
                'Reports are assembled manually every week or month.',
                'Staff move information between systems by hand.',
                'Important work depends on one person remembering the process.',
                'Off-the-shelf software does not cover the whole workflow.'
            ),
            'deliverables'=> array(
                'Process and hand-off review',
                'Manual-hours estimate',
                'Automation opportunity map',
                'Integration opportunities',
                'Priority business case',
                'Implementation roadmap'
            ),
            'metric'      => 'Manual hours that can be removed or controlled',
            'check_name'  => 'Business Process Review',
            'check_price' => 'From R4,950',
            'check_copy'  => 'Find the operational friction costing time, control and staff capacity.',
            'fix_name'    => 'Automation Sprint',
            'fix_price'   => 'From R12,500',
            'fix_copy'    => 'Implement the highest-value workflow, automation or integration first.',
            'manage_name' => 'Automation Support',
            'manage_price'=> 'From R2,500/month',
            'manage_copy' => 'Ongoing workflow improvement, support and optimisation.',
            'cta'         => 'Find Our Manual Waste'
        )
    );
}

function softkom_revenue_suite_hub_definition() {
    return array(
        'slug'     => 'business-revenue-efficiency',
        'title'    => 'Find Where Your Business Is Losing Money',
        'headline' => 'Where is your business losing money, time or control?',
        'intro'    => 'Softkom starts with the business problem, not the technology. Choose the area that feels familiar and we will help you find the leakage, quantify it and decide what is worth fixing.'
    );
}

function softkom_revenue_suite_contact_url( $slug ) {
    return add_query_arg(
        array(
            'source'      => 'revenue-efficiency-suite',
            'offer'       => $slug,
            'utm_source'  => 'softkom-website',
            'utm_medium'  => 'commercial-diagnostic',
            'utm_campaign'=> 'check-fix-manage'
        ),
        home_url( '/contact/' )
    );
}

function softkom_revenue_suite_offer_html( $slug, $offer ) {
    $pain = '';
    foreach ( $offer['pain'] as $item ) {
        $pain .= '<li><span>!</span>' . esc_html( $item ) . '</li>';
    }

    $deliverables = '';
    foreach ( $offer['deliverables'] as $item ) {
        $deliverables .= '<li><span>✓</span>' . esc_html( $item ) . '</li>';
    }

    $contact = esc_url( softkom_revenue_suite_contact_url( $slug ) );
    $hub = esc_url( home_url( '/business-revenue-efficiency/' ) );

    return '<div class="skrs-page">
        <section class="skrs-hero">
            <div class="skrs-inner">
                <a class="skrs-back" href="' . $hub . '">← Revenue & Efficiency</a>
                <div class="skrs-kicker">' . esc_html( $offer['eyebrow'] ) . '</div>
                <h1>' . esc_html( $offer['headline'] ) . '</h1>
                <p class="skrs-lead">' . esc_html( $offer['intro'] ) . '</p>
                <div class="skrs-actions">
                    <a class="skrs-primary" href="' . $contact . '">' . esc_html( $offer['cta'] ) . ' →</a>
                </div>
                <p class="skrs-promise">' . esc_html( $offer['promise'] ) . '</p>
            </div>
        </section>

        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead">
                    <p>DOES THIS SOUND FAMILIAR?</p>
                    <h2>Common warning signs</h2>
                </div>
                <ul class="skrs-pain">' . $pain . '</ul>
            </div>
        </section>

        <section class="skrs-section skrs-muted">
            <div class="skrs-inner skrs-two">
                <div>
                    <p class="skrs-label">WHAT SOFTKOM CHECKS</p>
                    <h2>Turn the problem into something measurable</h2>
                    <ul class="skrs-deliverables">' . $deliverables . '</ul>
                </div>
                <div class="skrs-outcome">
                    <p>THE NUMBER THAT MATTERS</p>
                    <strong>' . esc_html( $offer['metric'] ) . '</strong>
                    <span>We focus the review on a business outcome the owner or management team can understand and act on.</span>
                </div>
            </div>
        </section>

        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead">
                    <p>SIMPLE COMMERCIAL MODEL</p>
                    <h2>Check it. Fix it. Keep it under control.</h2>
                </div>
                <div class="skrs-pricing">
                    <article>
                        <span>1 · CHECK</span>
                        <h3>' . esc_html( $offer['check_name'] ) . '</h3>
                        <strong>' . esc_html( $offer['check_price'] ) . '</strong>
                        <p>' . esc_html( $offer['check_copy'] ) . '</p>
                    </article>
                    <article>
                        <span>2 · FIX</span>
                        <h3>' . esc_html( $offer['fix_name'] ) . '</h3>
                        <strong>' . esc_html( $offer['fix_price'] ) . '</strong>
                        <p>' . esc_html( $offer['fix_copy'] ) . '</p>
                    </article>
                    <article class="skrs-featured">
                        <span>3 · MANAGE</span>
                        <h3>' . esc_html( $offer['manage_name'] ) . '</h3>
                        <strong>' . esc_html( $offer['manage_price'] ) . '</strong>
                        <p>' . esc_html( $offer['manage_copy'] ) . '</p>
                    </article>
                </div>
                <p class="skrs-fine">Final scope depends on business size, data quality, number of systems/users and the work required. Third-party software or licence costs are excluded unless stated otherwise.</p>
            </div>
        </section>

        <section class="skrs-final">
            <div class="skrs-inner">
                <p>START WITH THE PROBLEM, NOT A SOFTWARE PURCHASE</p>
                <h2>' . esc_html( $offer['cta'] ) . '</h2>
                <a class="skrs-primary" href="' . $contact . '">Talk to Softkom →</a>
            </div>
        </section>
    </div>';
}

function softkom_revenue_suite_hub_html() {
    $hub = softkom_revenue_suite_hub_definition();
    $offers = softkom_revenue_suite_offers();

    $cards = '<article class="skrs-hub-card skrs-hub-card--revenue-lab">
        <p>WEBSITE SALES</p>
        <h2>Is your website losing customers?</h2>
        <span>Find where buyers hesitate, leave or choose a competitor.</span>
        <a href="' . esc_url( home_url( '/revenue-lab/' ) ) . '">Run the Website Sales Check →</a>
    </article>';

    foreach ( $offers as $slug => $offer ) {
        $cards .= '<article class="skrs-hub-card">
            <p>' . esc_html( $offer['title'] ) . '</p>
            <h2>' . esc_html( $offer['headline'] ) . '</h2>
            <span>' . esc_html( $offer['promise'] ) . '</span>
            <a href="' . esc_url( home_url( '/' . $slug . '/' ) ) . '">See How It Works →</a>
        </article>';
    }

    return '<div class="skrs-page skrs-hub">
        <section class="skrs-hero">
            <div class="skrs-inner">
                <div class="skrs-kicker">SOFTKOM REVENUE & EFFICIENCY</div>
                <h1>' . esc_html( $hub['headline'] ) . '</h1>
                <p class="skrs-lead">' . esc_html( $hub['intro'] ) . '</p>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead">
                    <p>CHOOSE THE PROBLEM YOU RECOGNISE</p>
                    <h2>We find the leakage before recommending the fix.</h2>
                </div>
                <div class="skrs-hub-grid">' . $cards . '</div>
            </div>
        </section>
        <section class="skrs-final">
            <div class="skrs-inner">
                <p>NOT SURE WHICH ONE APPLIES?</p>
                <h2>Tell us where the business feels inefficient and we will help identify the best place to start.</h2>
                <a class="skrs-primary" href="' . esc_url( add_query_arg( 'source', 'revenue-efficiency-hub', home_url( '/contact/' ) ) ) . '">Talk to Softkom →</a>
            </div>
        </section>
    </div>';
}

function softkom_revenue_suite_sync_pages() {
    $version = '0.1.0';
    if ( get_option( 'softkom_revenue_suite_pages_version' ) === $version ) { return; }

    $hub = softkom_revenue_suite_hub_definition();
    $hub_page = get_page_by_path( $hub['slug'], OBJECT, 'page' );
    $hub_post = array(
        'post_title'   => $hub['title'],
        'post_name'    => $hub['slug'],
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_content' => softkom_revenue_suite_hub_html()
    );
    if ( $hub_page ) {
        $hub_post['ID'] = $hub_page->ID;
        wp_update_post( wp_slash( $hub_post ) );
    } else {
        wp_insert_post( wp_slash( $hub_post ) );
    }

    foreach ( softkom_revenue_suite_offers() as $slug => $offer ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        $post = array(
            'post_title'   => $offer['title'],
            'post_name'    => $slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_content' => softkom_revenue_suite_offer_html( $slug, $offer )
        );
        if ( $page ) {
            $post['ID'] = $page->ID;
            wp_update_post( wp_slash( $post ) );
        } else {
            wp_insert_post( wp_slash( $post ) );
        }
    }

    update_option( 'softkom_revenue_suite_pages_version', $version, false );
}
add_action( 'init', 'softkom_revenue_suite_sync_pages', 28 );

function softkom_revenue_suite_slugs() {
    $slugs = array_keys( softkom_revenue_suite_offers() );
    $hub = softkom_revenue_suite_hub_definition();
    array_unshift( $slugs, $hub['slug'] );
    return $slugs;
}

function softkom_revenue_suite_is_page() {
    return is_page() && in_array( get_post_field( 'post_name', get_queried_object_id() ), softkom_revenue_suite_slugs(), true );
}

function softkom_revenue_suite_assets() {
    if ( ! softkom_revenue_suite_is_page() ) { return; }
    $path = WPMU_PLUGIN_DIR . '/softkom-revenue-suite.css';
    wp_enqueue_style(
        'softkom-revenue-suite',
        content_url( '/mu-plugins/softkom-revenue-suite.css' ),
        array(),
        is_readable( $path ) ? (string) filemtime( $path ) : SOFTKOM_REVENUE_SUITE_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'softkom_revenue_suite_assets', 70 );

add_filter( 'body_class', function( $classes ) {
    if ( softkom_revenue_suite_is_page() ) { $classes[] = 'softkom-revenue-suite'; }
    return $classes;
} );

add_filter( 'wp_robots', function( $robots ) {
    if ( softkom_revenue_suite_is_page() ) {
        unset( $robots['noindex'], $robots['nofollow'] );
        $robots['index'] = true;
        $robots['follow'] = true;
        $robots['max-image-preview'] = 'large';
    }
    return $robots;
}, 100 );
