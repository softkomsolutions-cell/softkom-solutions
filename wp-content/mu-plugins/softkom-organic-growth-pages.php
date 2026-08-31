<?php
/**
 * Plugin Name: Softkom Organic Growth Pages
 * Description: Creates and maintains high-intent organic acquisition pages that route visitors into the Softkom assessment.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_growth_pages_definition() {
    return array(
        'ai-automation-south-africa' => array(
            'title' => 'AI Automation South Africa',
            'eyebrow' => 'AI AUTOMATION · SOUTH AFRICA',
            'headline' => 'AI automation that removes repetitive work and creates measurable business value',
            'intro' => 'Softkom Solutions helps South African businesses identify and implement practical AI automation across sales, customer service, operations, reporting and administration. We focus on the workflows where automation can save time, improve response speed and help your team scale without adding unnecessary complexity.',
            'problems' => array('Leads are captured but follow-up is slow or inconsistent.','Staff repeatedly copy information between spreadsheets, email, CRM and other tools.','Customer questions consume time that could be handled automatically with human escalation when needed.','Reporting depends on manual consolidation and information is difficult to trust.','Your business has AI ideas but no clear way to prioritise them by value and feasibility.'),
            'solutions' => array('AI-assisted lead capture, qualification, routing and follow-up','Customer-service AI with controlled human escalation','Document, email and data-processing automation','Connected workflows between existing business systems','Management reporting and operational alerts','Custom AI-enabled business systems where off-the-shelf tools do not fit'),
            'faq' => array(
                array('What business processes can AI automate?','Common opportunities include lead follow-up, customer enquiries, document processing, data capture, reporting, CRM updates, internal approvals and repetitive administrative workflows.'),
                array('Do we need to replace our existing software?','Not necessarily. A good automation strategy often connects and improves the systems you already use before recommending replacement.'),
                array('How do we know where to start with AI?','Start with processes that are repetitive, high-volume, slow, error-prone or directly affect revenue and customer response times. Softkom’s free assessment helps prioritise these opportunities.'),
            ),
        ),
        'business-process-automation-south-africa' => array(
            'title' => 'Business Process Automation South Africa',
            'eyebrow' => 'BUSINESS PROCESS AUTOMATION · SOUTH AFRICA',
            'headline' => 'Replace manual business processes with connected, scalable workflows',
            'intro' => 'When teams rely on spreadsheets, inboxes, WhatsApp messages and repeated data capture, growth creates more administration instead of more capacity. Softkom designs practical business process automation for South African organisations that need cleaner workflows, faster hand-offs and better visibility.',
            'problems' => array('The same information is captured more than once.','Approvals and hand-offs depend on email or WhatsApp reminders.','Teams cannot easily see the current status of work.','Manual processes create errors, delays and reporting gaps.','Existing software does not match the way the business actually operates.'),
            'solutions' => array('Workflow mapping and automation','System and API integrations','Automated notifications, routing and approvals','CRM and sales-process automation','Operational dashboards and reporting','Purpose-built workflow applications'),
            'faq' => array(
                array('What is business process automation?','Business process automation uses software, integrations and rules to perform repetitive tasks, move information between systems and guide work through consistent processes.'),
                array('Can you automate processes that currently run in spreadsheets?','Yes. Spreadsheet-driven processes are often strong candidates for automation when they involve repeated capture, formulas, approvals, status tracking or reporting.'),
                array('Should we automate or build a custom system?','That depends on the process. Softkom can connect existing tools where that is efficient, or design a custom business system when the workflow requires more control, scale or differentiation.'),
            ),
        ),
        'custom-business-systems-south-africa' => array(
            'title' => 'Custom Business Systems South Africa',
            'eyebrow' => 'CUSTOM BUSINESS SYSTEMS · SOUTH AFRICA',
            'headline' => 'Business software designed around the way your organisation actually works',
            'intro' => 'Softkom Solutions designs custom business systems for organisations that have outgrown spreadsheets, disconnected SaaS tools or manual workarounds. We turn operational processes into secure, maintainable systems that improve control, visibility and scalability.',
            'problems' => array('Your team has built a critical process around spreadsheets and manual workarounds.','Multiple systems hold different versions of the same information.','Off-the-shelf software forces your team into inefficient processes.','Management lacks a reliable operational view.','Growth requires adding people simply to keep administration under control.'),
            'solutions' => array('Custom workflow and operations platforms','Client, supplier and partner portals','Internal management systems','CRM and pipeline solutions','Data integration and reporting platforms','AI-enabled operational systems'),
            'faq' => array(
                array('When does a business need a custom system?','A custom system becomes worth considering when a core process is difficult to manage with spreadsheets or standard software, creates repeated manual work, or is important enough to justify a system designed around the business.'),
                array('Can a custom system integrate with software we already use?','Yes. Where APIs or suitable integration methods are available, a custom system can connect existing tools rather than replacing everything.'),
                array('How much does a custom business system cost?','Cost depends on scope, integrations, users and complexity. Softkom’s custom business-system engagements typically begin around R75,000, with larger platforms scoped separately.'),
            ),
        ),
    );
}

function softkom_growth_page_html( $page ) {
    $assessment = esc_url( home_url( '/assessment/' ) );
    $problem_html = ''; foreach ( $page['problems'] as $item ) { $problem_html .= '<li>' . esc_html( $item ) . '</li>'; }
    $solution_html = ''; foreach ( $page['solutions'] as $item ) { $solution_html .= '<li>' . esc_html( $item ) . '</li>'; }
    $faq_html = ''; foreach ( $page['faq'] as $item ) { $faq_html .= '<details><summary>' . esc_html( $item[0] ) . '</summary><p>' . esc_html( $item[1] ) . '</p></details>'; }
    return '<div class="softkom-growth-page"><section class="skg-hero"><div class="skg-inner"><p class="skg-eyebrow">'.esc_html($page['eyebrow']).'</p><h1>'.esc_html($page['headline']).'</h1><p class="skg-lead">'.esc_html($page['intro']).'</p><div class="skg-actions"><a class="skg-primary" href="'.$assessment.'">Start the Free AI &amp; Systems Assessment</a><span>3 minutes · personalised score · practical recommendations</span></div></div></section><section><div class="skg-inner"><div class="skg-grid"><div><p class="skg-eyebrow">COMMON SIGNALS</p><h2>Where businesses lose time and capacity</h2><ul>'.$problem_html.'</ul></div><div class="skg-card"><p class="skg-eyebrow">WHAT SOFTKOM CAN AUTOMATE</p><h2>Practical solutions, not AI for its own sake</h2><ul>'.$solution_html.'</ul></div></div></div></section><section class="skg-band"><div class="skg-inner"><p class="skg-eyebrow">START WITH THE BUSINESS CASE</p><h2>Find the highest-value opportunities before you invest</h2><p>Our free Business Systems &amp; AI Readiness Assessment scores your current maturity, identifies automation opportunities and recommends the most practical next step.</p><a class="skg-primary" href="'.$assessment.'">Get My Readiness Score</a></div></section><section><div class="skg-inner"><p class="skg-eyebrow">FREQUENTLY ASKED QUESTIONS</p><h2>Questions businesses ask before automating</h2><div class="skg-faq">'.$faq_html.'</div></div></section></div>';
}

function softkom_growth_pages_sync() {
    if ( get_option( 'softkom_growth_pages_version' ) === '1.0.0' ) { return; }
    foreach ( softkom_growth_pages_definition() as $slug => $page ) {
        $existing = get_page_by_path( $slug, OBJECT, 'page' );
        $postarr = array('post_title'=>$page['title'],'post_name'=>$slug,'post_type'=>'page','post_status'=>'publish','post_content'=>softkom_growth_page_html($page));
        if ( $existing ) { $postarr['ID'] = $existing->ID; wp_update_post( wp_slash( $postarr ) ); }
        else { wp_insert_post( wp_slash( $postarr ) ); }
    }
    update_option( 'softkom_growth_pages_version', '1.0.0', false );
}
add_action( 'init', 'softkom_growth_pages_sync', 20 );

function softkom_growth_is_page() {
    if ( ! is_page() ) { return false; }
    return array_key_exists( get_post_field( 'post_name', get_queried_object_id() ), softkom_growth_pages_definition() );
}

function softkom_growth_assets() {
    if ( ! softkom_growth_is_page() ) { return; }
    wp_register_style( 'softkom-growth-pages', false, array(), '1.0.0' ); wp_enqueue_style( 'softkom-growth-pages' );
    wp_add_inline_style( 'softkom-growth-pages', '.softkom-growth-page{font-family:Inter,system-ui,sans-serif;color:#1E293B}.softkom-growth-page section{padding:72px 24px}.skg-inner{max-width:1120px;margin:auto}.skg-hero{background:linear-gradient(135deg,#F8FAFC,#fff)}.skg-eyebrow{color:#2563EB;font-weight:800;font-size:13px;letter-spacing:.07em}.softkom-growth-page h1{max-width:900px;color:#0F172A;font-size:clamp(40px,6vw,68px);line-height:1.03;margin:14px 0 24px}.softkom-growth-page h2{color:#0F172A;font-size:clamp(28px,4vw,40px);line-height:1.15}.skg-lead{max-width:850px;font-size:20px;line-height:1.7}.skg-actions{display:flex;align-items:center;gap:18px;flex-wrap:wrap;margin-top:30px}.skg-primary{display:inline-block;background:#0F172A;color:#fff!important;text-decoration:none;padding:15px 22px;border-radius:9px;font-weight:700}.skg-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px}.skg-grid li{margin:12px 0;line-height:1.55}.skg-card{background:#F8FAFC;border:1px solid #E2E8F0;border-radius:18px;padding:32px}.skg-band{background:#0F172A;color:#fff}.skg-band h2{color:#fff;max-width:800px}.skg-band p:not(.skg-eyebrow){max-width:800px;font-size:18px;line-height:1.7}.skg-band .skg-primary{background:#2563EB}.skg-faq{max-width:900px}.skg-faq details{border-bottom:1px solid #E2E8F0;padding:18px 0}.skg-faq summary{font-weight:700;color:#0F172A;cursor:pointer;font-size:18px}.skg-faq p{line-height:1.7}@media(max-width:760px){.skg-grid{grid-template-columns:1fr}.softkom-growth-page section{padding:52px 20px}}' );
}
add_action( 'wp_enqueue_scripts', 'softkom_growth_assets', 30 );

function softkom_growth_schema() {
    if ( ! softkom_growth_is_page() ) { return; }
    $slug = get_post_field( 'post_name', get_queried_object_id() ); $page = softkom_growth_pages_definition()[$slug];
    $entities = array(); foreach ( $page['faq'] as $item ) { $entities[] = array('@type'=>'Question','name'=>$item[0],'acceptedAnswer'=>array('@type'=>'Answer','text'=>$item[1])); }
    $schema = array('@context'=>'https://schema.org','@graph'=>array(array('@type'=>'Service','name'=>$page['title'],'provider'=>array('@type'=>'Organization','name'=>'Softkom Solutions','url'=>home_url('/')),'areaServed'=>array('@type'=>'Country','name'=>'South Africa'),'url'=>get_permalink()),array('@type'=>'FAQPage','mainEntity'=>$entities)));
    echo '<script type="application/ld+json">'.wp_json_encode($schema,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';
}
add_action( 'wp_head', 'softkom_growth_schema', 35 );
