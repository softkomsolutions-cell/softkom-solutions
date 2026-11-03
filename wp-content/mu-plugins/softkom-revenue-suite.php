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



function softkom_revenue_suite_efficiency_review() {
    return array(
        'slug' => 'business-efficiency-review',
        'title' => 'Business Efficiency Review',
        'eyebrow' => 'START WITH HOW THE BUSINESS ACTUALLY WORKS',
        'headline' => 'Show us how your business works. We will show you where it can work better.',
        'intro' => 'You do not need to know which software, automation or agent you need. Softkom reviews the systems, subscriptions and everyday processes you already use, then identifies where money is being wasted, staff time is being lost and opportunities are being missed.',
        'pain' => array(
            'Software costs keep growing but nobody has the full picture.',
            'Staff retype the same information into more than one system.',
            'Quotes, leads or invoices depend on someone remembering to follow up.',
            'Reports are still assembled manually.',
            'Important processes live in spreadsheets, inboxes or WhatsApp.',
            'Different tools overlap or do not talk to each other.',
            'AI tools are being used without a clear owner or policy.',
            'Management knows the business is inefficient but does not know where to start.'
        ),
        'review' => array(
            'Current software and subscriptions',
            'Core workflows and hand-offs',
            'Manual data capture and duplicate work',
            'Sales, quote and lead follow-up',
            'Invoice and collections processes',
            'Reporting and management visibility',
            'AI usage, ownership and risk',
            'Integration gaps between systems',
            'Customer-facing journeys where relevant'
        ),
        'outputs' => array(
            'Business Efficiency Map',
            'Software waste and overlap findings',
            'Manual-hours and repetitive-work estimate',
            'Revenue leakage opportunities',
            'Risk and control gaps',
            'Prioritised quick wins',
            'Recommended automations, integrations or managed assistants',
            'Clear items that should not be changed'
        ),
        'price' => 'From R7,500',
        'monthly' => 'Optional ongoing optimisation from R2,500/month'
    );
}


function softkom_efficiency_review_form_html() {
    $industries = array( 'Professional services','Retail / ecommerce','Construction / trades','Manufacturing','Healthcare','Property','Education','Technology','Hospitality','Other' );
    $systems = array( 'Microsoft 365','Google Workspace','Xero','Sage','QuickBooks','CRM','Ecommerce platform','Project management tool','WhatsApp Business','AI tools','Other' );

    $industry_options = '<option value="">Select industry</option>';
    foreach ( $industries as $item ) { $industry_options .= '<option value="' . esc_attr( $item ) . '">' . esc_html( $item ) . '</option>'; }

    $system_checks = '';
    foreach ( $systems as $item ) {
        $system_checks .= '<label class="skber-check"><input type="checkbox" name="systems[]" value="' . esc_attr( $item ) . '"><span>' . esc_html( $item ) . '</span></label>';
    }

    return '<form id="business-efficiency-review-intake" class="skber-form" data-skber-form>
        <div class="skber-head">
            <div>
                <span class="skber-brand">SOFTKOM SOLUTIONS</span>
                <h2>Business Efficiency Review</h2>
                <p>Tell us how the business currently works. No technical preparation needed.</p>
            </div>
            <div class="skber-progress-wrap">
                <span data-skber-progress-label>Step 1 of 5</span>
                <div class="skber-progress-track"><i data-skber-progress></i></div>
            </div>
        </div>

        <div data-skber-form-body>
            <section data-skber-step>
                <p class="skber-step-label">1 · About the business</p>
                <div class="skber-grid">
                    <label>Company name<input required name="company" autocomplete="organization"></label>
                    <label>Your name<input required name="name" autocomplete="name"></label>
                    <label>Work email<input required type="email" name="email" autocomplete="email"></label>
                    <label>Phone<input name="phone" autocomplete="tel"></label>
                    <label>Industry<select required name="industry">' . $industry_options . '</select></label>
                    <label>Team size<select required name="team_size"><option value="">Select team size</option><option>1–5</option><option>6–15</option><option>16–30</option><option>31–75</option><option>76–150</option><option>150+</option></select></label>
                </div>
            </section>

            <section data-skber-step hidden>
                <p class="skber-step-label">2 · Where does the business feel inefficient?</p>
                <div class="skber-choice-grid">
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="software-cost"><span>Software costs keep growing</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="manual-capture"><span>Staff retype the same information</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="lead-followup"><span>Leads or quotes are not followed up consistently</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="collections"><span>Too much time goes into chasing invoices</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="reporting"><span>Reports are assembled manually</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="systems"><span>Systems do not talk to each other</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="ai"><span>AI use is growing without clear control</span></label>
                    <label class="skber-check"><input type="checkbox" name="pain[]" value="key-person"><span>Too much depends on one key person</span></label>
                </div>
                <label class="skber-full">Anything else slowing the business down?<textarea name="pain_notes" rows="4" placeholder="A short description is enough."></textarea></label>
            </section>

            <section data-skber-step hidden>
                <p class="skber-step-label">3 · What do you currently use?</p>
                <div class="skber-choice-grid">' . $system_checks . '</div>
                <label class="skber-full">Other important systems or spreadsheets<textarea name="systems_notes" rows="4" placeholder="Examples: stock spreadsheet, job card system, custom CRM, supplier portal..."></textarea></label>
            </section>

            <section data-skber-step hidden>
                <p class="skber-step-label">4 · Where is the time or money going?</p>
                <div class="skber-grid">
                    <label>Approx. monthly software spend<select name="software_spend"><option value="">Not sure</option><option>Under R2,500</option><option>R2,500–R7,500</option><option>R7,500–R15,000</option><option>R15,000–R30,000</option><option>R30,000+</option></select></label>
                    <label>Manual admin each week<select name="manual_hours"><option value="">Not sure</option><option>Under 5 hours</option><option>5–10 hours</option><option>10–20 hours</option><option>20–40 hours</option><option>40+ hours</option></select></label>
                    <label>Quotes / leads per month<select name="lead_volume"><option value="">Not sure</option><option>Under 20</option><option>20–50</option><option>51–100</option><option>101–250</option><option>250+</option></select></label>
                    <label>Biggest priority<select required name="priority"><option value="">Choose one</option><option>Reduce costs</option><option>Save staff time</option><option>Increase sales / follow-up</option><option>Improve control</option><option>Reduce risk</option><option>Not sure yet</option></select></label>
                </div>
            </section>

            <section data-skber-step hidden>
                <p class="skber-step-label">5 · Final context</p>
                <label class="skber-full">If Softkom could improve one thing in the next 90 days, what would make the biggest difference?<textarea required name="ninety_day_goal" rows="5" placeholder="Write it in your own words."></textarea></label>
                <label class="skber-consent"><input required type="checkbox" name="consent" value="yes"><span>I agree that Softkom may use this information to assess the business and contact me about the review.</span></label>
            </section>

            <div class="skber-nav">
                <button type="button" class="skber-secondary" data-skber-prev hidden>Back</button>
                <div class="skber-nav-spacer"></div>
                <button type="button" class="skber-primary" data-skber-next>Continue</button>
                <button type="submit" class="skber-primary" data-skber-submit hidden>Send My Review Request</button>
            </div>
            <p class="skber-message" data-skber-message></p>
        </div>

        <div class="skber-success" data-skber-success hidden>
            <span class="skber-success-mark">✓</span>
            <h3>Thank you <span data-skber-success-name></span>.</h3>
            <p>Your Business Efficiency Review request has been captured. Softkom now has enough context to prepare the next step without making you repeat everything on a call.</p>
        </div>
    </form>';
}

function softkom_efficiency_review_store_lead( $data ) {
    if ( ! post_type_exists( 'softkom_lead' ) ) { return 0; }

    $lead_id = wp_insert_post(
        array(
            'post_type' => 'softkom_lead',
            'post_status' => 'private',
            'post_title' => trim( $data['company'] . ' - Business Efficiency Review' ),
        ),
        true
    );
    if ( is_wp_error( $lead_id ) ) { return 0; }

    $parts = preg_split( '/\s+/', trim( $data['name'] ), 2 );
    update_post_meta( $lead_id, '_softkom_first_name', isset( $parts[0] ) ? $parts[0] : '' );
    update_post_meta( $lead_id, '_softkom_last_name', isset( $parts[1] ) ? $parts[1] : '' );
    update_post_meta( $lead_id, '_softkom_email', $data['email'] );
    update_post_meta( $lead_id, '_softkom_phone', $data['phone'] );
    update_post_meta( $lead_id, '_softkom_company', $data['company'] );
    update_post_meta( $lead_id, '_softkom_traffic_source', 'business-efficiency-review' );
    update_post_meta( $lead_id, '_softkom_acquisition_mode', 'business-efficiency-review' );
    update_post_meta( $lead_id, '_softkom_pipeline_stage', 'new' );
    update_post_meta( $lead_id, '_softkom_lead_temperature', 'WARM' );
    update_post_meta( $lead_id, '_softkom_score_purchase_intent', 76 );
    update_post_meta( $lead_id, '_softkom_score_commercial_fit', 78 );
    update_post_meta( $lead_id, '_softkom_efficiency_review_payload', wp_json_encode( $data ) );

    if ( function_exists( 'softkom_public_acquisition_activity' ) ) {
        softkom_public_acquisition_activity( $lead_id, 'business_efficiency_review_submitted', '', 'WARM' );
    }

    return (int) $lead_id;
}

function softkom_efficiency_review_ajax_submit() {
    check_ajax_referer( 'softkom_efficiency_review', 'nonce' );

    $data = array(
        'company' => isset($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '',
        'name' => isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '',
        'email' => isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '',
        'phone' => isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '',
        'industry' => isset($_POST['industry']) ? sanitize_text_field(wp_unslash($_POST['industry'])) : '',
        'team_size' => isset($_POST['team_size']) ? sanitize_text_field(wp_unslash($_POST['team_size'])) : '',
        'pain' => isset($_POST['pain']) ? array_values(array_map('sanitize_text_field',(array)wp_unslash($_POST['pain']))) : array(),
        'pain_notes' => isset($_POST['pain_notes']) ? sanitize_textarea_field(wp_unslash($_POST['pain_notes'])) : '',
        'systems' => isset($_POST['systems']) ? array_values(array_map('sanitize_text_field',(array)wp_unslash($_POST['systems']))) : array(),
        'systems_notes' => isset($_POST['systems_notes']) ? sanitize_textarea_field(wp_unslash($_POST['systems_notes'])) : '',
        'software_spend' => isset($_POST['software_spend']) ? sanitize_text_field(wp_unslash($_POST['software_spend'])) : '',
        'manual_hours' => isset($_POST['manual_hours']) ? sanitize_text_field(wp_unslash($_POST['manual_hours'])) : '',
        'lead_volume' => isset($_POST['lead_volume']) ? sanitize_text_field(wp_unslash($_POST['lead_volume'])) : '',
        'priority' => isset($_POST['priority']) ? sanitize_text_field(wp_unslash($_POST['priority'])) : '',
        'ninety_day_goal' => isset($_POST['ninety_day_goal']) ? sanitize_textarea_field(wp_unslash($_POST['ninety_day_goal'])) : '',
    );

    if ( ! $data['company'] || ! $data['name'] || ! is_email( $data['email'] ) || ! $data['industry'] || ! $data['team_size'] || ! $data['priority'] || ! $data['ninety_day_goal'] ) {
        wp_send_json_error( array( 'message' => 'Please complete the required fields.' ), 400 );
    }

    if ( empty( $_POST['consent'] ) ) {
        wp_send_json_error( array( 'message' => 'Please confirm that Softkom may contact you about the review.' ), 400 );
    }

    $lead_id = softkom_efficiency_review_store_lead( $data );
    $admin_email = sanitize_email( get_option( 'admin_email' ) );
    if ( is_email( $admin_email ) ) {
        wp_mail(
            $admin_email,
            '[Softkom] Business Efficiency Review - ' . $data['company'],
            implode( "\n", array(
                'New Business Efficiency Review request.',
                'Company: ' . $data['company'],
                'Name: ' . $data['name'],
                'Email: ' . $data['email'],
                'Phone: ' . ( $data['phone'] ? $data['phone'] : '-' ),
                'Industry: ' . $data['industry'],
                'Team size: ' . $data['team_size'],
                'Priority: ' . $data['priority'],
                '90-day goal: ' . $data['ninety_day_goal'],
                'Lead ID: ' . ( $lead_id ? $lead_id : 'not stored' ),
            ) )
        );
    }

    wp_send_json_success( array( 'lead_id' => $lead_id ) );
}
add_action( 'wp_ajax_softkom_efficiency_review_submit', 'softkom_efficiency_review_ajax_submit' );
add_action( 'wp_ajax_nopriv_softkom_efficiency_review_submit', 'softkom_efficiency_review_ajax_submit' );

function softkom_revenue_suite_efficiency_review_html() {
    $r = softkom_revenue_suite_efficiency_review();
    $pain=''; foreach($r['pain'] as $x){$pain.='<li><span>!</span>'.esc_html($x).'</li>';}
    $review=''; foreach($r['review'] as $x){$review.='<li><span>✓</span>'.esc_html($x).'</li>';}
    $outputs=''; foreach($r['outputs'] as $x){$outputs.='<li><span>✓</span>'.esc_html($x).'</li>';}
    $contact=esc_url( softkom_revenue_suite_contact_url( 'business-efficiency-review' ) );
    return '<div class="skrs-page">
        <section class="skrs-hero">
            <div class="skrs-inner">
                <div class="skrs-kicker">'.esc_html($r['eyebrow']).'</div>
                <h1>'.esc_html($r['headline']).'</h1>
                <p class="skrs-lead">'.esc_html($r['intro']).'</p>
                <div class="skrs-actions"><a class="skrs-primary" href="'.$contact.'">Review How Our Business Works →</a></div>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead"><p>COMMON SIGNS</p><h2>You should not need to diagnose the problem yourself.</h2></div>
                <ul class="skrs-pain">'.$pain.'</ul>
            </div>
        </section>
        <section class="skrs-section skrs-muted">
            <div class="skrs-inner skrs-two">
                <div>
                    <p class="skrs-label">WHAT WE REVIEW</p>
                    <h2>We look at the current business before recommending anything new.</h2>
                    <ul class="skrs-deliverables">'.$review.'</ul>
                </div>
                <div class="skrs-outcome">
                    <p>THE PRINCIPLE</p>
                    <strong>Do not buy another tool until you know what is actually broken.</strong>
                    <span>Sometimes the right answer is automation. Sometimes it is integration, cleanup, better follow-up or simply using the software you already pay for properly.</span>
                </div>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner skrs-two">
                <div>
                    <p class="skrs-label">WHAT YOU GET</p>
                    <h2>A prioritised Business Efficiency Map.</h2>
                    <ul class="skrs-deliverables">'.$outputs.'</ul>
                </div>
                <div class="skrs-outcome">
                    <p>PRICING</p>
                    <strong>'.esc_html($r['price']).'</strong>
                    <span>'.esc_html($r['monthly']).'</span>
                </div>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead"><p>WHAT HAPPENS NEXT</p><h2>Check it. Fix what matters. Manage only what needs ongoing attention.</h2></div>
                <div class="skrs-pricing">
                    <article><span>1 · REVIEW</span><h3>Understand the current business</h3><p>Map software, processes, hand-offs, costs, missed opportunities and control gaps.</p></article>
                    <article><span>2 · PRIORITISE</span><h3>Choose the highest-value fixes</h3><p>Quick wins first. No unnecessary rebuilds and no technology for its own sake.</p></article>
                    <article class="skrs-featured"><span>3 · IMPROVE</span><h3>Implement and manage</h3><p>Use Softkom products, integrations, automations, assistants or custom software only where the business case is clear.</p></article>
                </div>
            </div>
        </section>
        <section class="skrs-section skrs-intake-section"><div class="skrs-inner">' . softkom_efficiency_review_form_html() . '</div></section>
        <section class="skrs-final"><div class="skrs-inner"><p>START WITH THE BUSINESS</p><h2>Find where money, time and opportunities are leaking.</h2><a class="skrs-primary" href="#business-efficiency-review-intake">Start the Business Efficiency Review →</a></div></section>
    </div>';
}

function softkom_revenue_suite_agents() {
    return array(
        'payment-chaser' => array(
            'title' => 'Payment Chaser',
            'eyebrow' => 'GET PAID FASTER',
            'headline' => 'Stop spending staff time chasing unpaid invoices.',
            'intro' => 'The Payment Chaser follows up outstanding invoices, records promises to pay, stops when payment arrives and sends disputes or exceptions back to a person.',
            'jobs' => array(
                'Monitors outstanding invoices and due dates',
                'Sends polite reminders before and after due date',
                'Tracks promises to pay',
                'Stops follow-up automatically when payment is recorded',
                'Escalates disputes and exceptions to staff',
                'Produces an overdue-debt summary'
            ),
            'setup' => 'From R4,950',
            'monthly' => 'From R1,950/month',
            'cta' => 'Reduce Invoice Chasing'
        ),
        'quote-follow-up' => array(
            'title' => 'Quote Follow-Up Assistant',
            'eyebrow' => 'STOP LETTING QUOTES GO COLD',
            'headline' => 'How many quotes did you send last month that nobody followed up?',
            'intro' => 'This assistant follows up sent quotes, captures objections, identifies interested buyers and hands hot opportunities back to sales.',
            'jobs' => array(
                'Detects when a quote is sent',
                'Follows up at sensible intervals',
                'Captures timing, price and approval objections',
                'Identifies interested prospects',
                'Escalates hot opportunities to sales',
                'Tracks won, lost and no-response outcomes'
            ),
            'setup' => 'From R5,500',
            'monthly' => 'From R1,950/month',
            'cta' => 'Improve Quote Follow-Up'
        ),
        'supplier-quote-assistant' => array(
            'title' => 'Supplier Quote Assistant',
            'eyebrow' => 'REDUCE PROCUREMENT CHASING',
            'headline' => 'Stop spending hours chasing suppliers for prices.',
            'intro' => 'Give the assistant a requirement and it can prepare requests, chase suppliers, collect replies and turn the responses into a clean comparison for a buyer to review.',
            'jobs' => array(
                'Uses the approved supplier list',
                'Prepares and sends RFQs',
                'Follows up outstanding supplier responses',
                'Extracts price, lead time and terms',
                'Builds a side-by-side comparison',
                'Routes the final decision to a person'
            ),
            'setup' => 'From R7,500',
            'monthly' => 'From R1,950/month + usage',
            'cta' => 'Reduce Supplier Chasing'
        ),
        'management-briefing' => array(
            'title' => 'Management Briefing Assistant',
            'eyebrow' => 'STOP BUILDING REPORTS BY HAND',
            'headline' => 'Get the numbers that matter without spending Monday morning assembling them.',
            'intro' => 'The Management Briefing Assistant pulls together the operational information management needs and highlights what changed, what needs attention and what can wait.',
            'jobs' => array(
                'Combines sales, finance and operations inputs',
                'Summarises key changes and exceptions',
                'Highlights overdue debt and delayed work',
                'Flags unusual trends',
                'Produces a concise management briefing',
                'Keeps the final decision with management'
            ),
            'setup' => 'From R7,500',
            'monthly' => 'From R2,500/month',
            'cta' => 'Automate Management Reporting'
        ),
        'staff-helpdesk' => array(
            'title' => 'Staff Helpdesk',
            'eyebrow' => 'STOP ANSWERING THE SAME QUESTIONS',
            'headline' => 'Give staff one place to get answers about policies, forms and everyday processes.',
            'intro' => 'The Staff Helpdesk answers routine internal questions from approved company information and escalates sensitive or exceptional cases to the right person.',
            'jobs' => array(
                'Answers routine policy and process questions',
                'Points staff to forms and procedures',
                'Supports onboarding',
                'Explains approved internal processes',
                'Keeps answers grounded in company documents',
                'Escalates sensitive matters to people'
            ),
            'setup' => 'From R5,500',
            'monthly' => 'From R1,950/month',
            'cta' => 'Reduce Repetitive Staff Questions'
        ),
        'document-processing-assistant' => array(
            'title' => 'Document Processing Assistant',
            'eyebrow' => 'REMOVE REPETITIVE DATA CAPTURE',
            'headline' => 'Stop retyping information from invoices, forms and orders.',
            'intro' => 'The Document Processing Assistant reads incoming documents, extracts the information the business needs, validates it and routes exceptions for human review.',
            'jobs' => array(
                'Reads common incoming documents',
                'Extracts required fields',
                'Checks for missing or inconsistent information',
                'Routes clean records into the next workflow step',
                'Flags exceptions for review',
                'Creates an audit trail of what was processed'
            ),
            'setup' => 'From R7,500',
            'monthly' => 'From R2,500/month',
            'cta' => 'Reduce Manual Data Capture'
        )
    );
}

function softkom_revenue_suite_agent_html( $slug, $agent ) {
    $jobs = '';
    foreach ( $agent['jobs'] as $job ) {
        $jobs .= '<li><span>✓</span>' . esc_html( $job ) . '</li>';
    }
    $contact = esc_url( softkom_revenue_suite_contact_url( 'digital-team-' . $slug ) );
    $hub = esc_url( home_url( '/digital-team/' ) );

    return '<div class="skrs-page">
        <section class="skrs-hero">
            <div class="skrs-inner">
                <a class="skrs-back" href="' . $hub . '">← Digital Team</a>
                <div class="skrs-kicker">' . esc_html( $agent['eyebrow'] ) . '</div>
                <h1>' . esc_html( $agent['headline'] ) . '</h1>
                <p class="skrs-lead">' . esc_html( $agent['intro'] ) . '</p>
                <div class="skrs-actions"><a class="skrs-primary" href="' . $contact . '">' . esc_html( $agent['cta'] ) . ' →</a></div>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner skrs-two">
                <div>
                    <p class="skrs-label">WHAT IT TAKES OFF YOUR TEAM</p>
                    <h2>A narrow job, done consistently.</h2>
                    <ul class="skrs-deliverables">' . $jobs . '</ul>
                </div>
                <div class="skrs-outcome">
                    <p>COMMERCIAL MODEL</p>
                    <strong>Setup: ' . esc_html( $agent['setup'] ) . '</strong>
                    <strong style="font-size:26px">' . esc_html( $agent['monthly'] ) . '</strong>
                    <span>Final pricing depends on integrations, message volume, number of systems and the level of human review required.</span>
                </div>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead">
                    <p>HOW SOFTKOM SELLS IT</p>
                    <h2>Give repetitive work to the system. Keep decisions with your people.</h2>
                </div>
                <div class="skrs-pricing">
                    <article><span>1 · DEFINE</span><h3>Choose one repetitive job</h3><p>We narrow the scope so the assistant has a clear responsibility and measurable outcome.</p></article>
                    <article><span>2 · CONNECT</span><h3>Connect the systems it needs</h3><p>Email, accounting, CRM, documents, supplier lists or internal information as required.</p></article>
                    <article class="skrs-featured"><span>3 · MANAGE</span><h3>Softkom keeps it working</h3><p>Monitoring, prompt/workflow changes, exception handling and ongoing optimisation.</p></article>
                </div>
            </div>
        </section>
        <section class="skrs-final">
            <div class="skrs-inner">
                <p>START WITH ONE JOB</p>
                <h2>' . esc_html( $agent['cta'] ) . '</h2>
                <a class="skrs-primary" href="' . $contact . '">Talk to Softkom →</a>
            </div>
        </section>
    </div>';
}

function softkom_revenue_suite_digital_team_html() {
    $cards = '';
    foreach ( softkom_revenue_suite_agents() as $slug => $agent ) {
        $cards .= '<article class="skrs-hub-card">
            <p>' . esc_html( $agent['title'] ) . '</p>
            <h2>' . esc_html( $agent['headline'] ) . '</h2>
            <span>' . esc_html( $agent['intro'] ) . '</span>
            <a href="' . esc_url( home_url( '/digital-team-' . $slug . '/' ) ) . '">See How It Works →</a>
        </article>';
    }

    return '<div class="skrs-page skrs-hub">
        <section class="skrs-hero">
            <div class="skrs-inner">
                <div class="skrs-kicker">SOFTKOM DIGITAL TEAM</div>
                <h1>Give repetitive work to the system. Keep decisions with your people.</h1>
                <p class="skrs-lead">Softkom builds and manages focused assistants for the repetitive jobs that drain staff time: collections, quote follow-up, supplier pricing, reporting, staff questions and document processing.</p>
            </div>
        </section>
        <section class="skrs-section">
            <div class="skrs-inner">
                <div class="skrs-sectionhead">
                    <p>CHOOSE THE JOB</p>
                    <h2>Start with one repetitive task that has a clear business cost.</h2>
                </div>
                <div class="skrs-hub-grid">' . $cards . '</div>
            </div>
        </section>
        <section class="skrs-final">
            <div class="skrs-inner">
                <p>NEED MORE THAN ONE?</p>
                <h2>Combine several assistants into a managed Digital Team.</h2>
                <a class="skrs-primary" href="' . esc_url( softkom_revenue_suite_contact_url( 'digital-team' ) ) . '">Plan a Digital Team →</a>
            </div>
        </section>
    </div>';
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

    $cards = '<article class="skrs-hub-card skrs-featured">
        <p>START HERE</p>
        <h2>Not sure where the waste is? Start with a Business Efficiency Review.</h2>
        <span>We analyse the systems, subscriptions and processes you already use before recommending any new technology.</span>
        <a href="' . esc_url( home_url( '/business-efficiency-review/' ) ) . '">Review How Your Business Works →</a>
    </article><article class="skrs-hub-card skrs-hub-card--revenue-lab">
        <p>WEBSITE SALES</p>
        <h2>Is your website losing customers?</h2>
        <span>Find where buyers hesitate, leave or choose a competitor.</span>
        <a href="' . esc_url( home_url( '/revenue-lab/' ) ) . '">Run the Website Sales Check →</a>
    </article>';

    $cards .= '<article class="skrs-hub-card skrs-hub-card--digital-team">
        <p>DIGITAL TEAM</p>
        <h2>Which repetitive job should your team stop doing by hand?</h2>
        <span>Collections, quote follow-up, supplier pricing, reporting, staff questions and document processing.</span>
        <a href="' . esc_url( home_url( '/digital-team/' ) ) . '">Explore the Digital Team →</a>
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

    $efficiency = softkom_revenue_suite_efficiency_review();
    $efficiency_page = get_page_by_path( $efficiency['slug'], OBJECT, 'page' );
    $efficiency_post = array(
        'post_title' => $efficiency['title'],
        'post_name' => $efficiency['slug'],
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => softkom_revenue_suite_efficiency_review_html()
    );
    if ( $efficiency_page ) {
        $efficiency_post['ID'] = $efficiency_page->ID;
        wp_update_post( wp_slash( $efficiency_post ) );
    } else {
        wp_insert_post( wp_slash( $efficiency_post ) );
    }

    $digital_team = get_page_by_path( 'digital-team', OBJECT, 'page' );
    $digital_team_post = array(
        'post_title' => 'Softkom Digital Team',
        'post_name' => 'digital-team',
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_content' => softkom_revenue_suite_digital_team_html()
    );
    if ( $digital_team ) {
        $digital_team_post['ID'] = $digital_team->ID;
        wp_update_post( wp_slash( $digital_team_post ) );
    } else {
        wp_insert_post( wp_slash( $digital_team_post ) );
    }

    foreach ( softkom_revenue_suite_agents() as $agent_slug => $agent ) {
        $agent_page_slug = 'digital-team-' . $agent_slug;
        $agent_page = get_page_by_path( $agent_page_slug, OBJECT, 'page' );
        $agent_post = array(
            'post_title' => $agent['title'],
            'post_name' => $agent_page_slug,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => softkom_revenue_suite_agent_html( $agent_slug, $agent )
        );
        if ( $agent_page ) {
            $agent_post['ID'] = $agent_page->ID;
            wp_update_post( wp_slash( $agent_post ) );
        } else {
            wp_insert_post( wp_slash( $agent_post ) );
        }
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
    $slugs[] = 'business-efficiency-review';
    $slugs[] = 'digital-team';
    foreach ( array_keys( softkom_revenue_suite_agents() ) as $agent_slug ) {
        $slugs[] = 'digital-team-' . $agent_slug;
    }
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
    if ( is_page( 'business-efficiency-review' ) ) {
        $js_path = WPMU_PLUGIN_DIR . '/softkom-efficiency-review.js';
        wp_enqueue_script(
            'softkom-efficiency-review',
            content_url( '/mu-plugins/softkom-efficiency-review.js' ),
            array(),
            is_readable( $js_path ) ? (string) filemtime( $js_path ) : SOFTKOM_REVENUE_SUITE_VERSION,
            true
        );
        wp_localize_script(
            'softkom-efficiency-review',
            'SoftkomEfficiencyReview',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'softkom_efficiency_review' ),
            )
        );
    }
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
