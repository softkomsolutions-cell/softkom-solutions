<?php
/**
 * Softkom Industry Funnel Profiles.
 *
 * Reusable profile layer on top of the proven Business Systems Assessment.
 * The scoring engine and question IDs stay stable while customer-facing copy,
 * questions and conversion positioning can be customised per industry.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_industry_funnel_profiles() {
	return array(
		'softkom' => array(
			'name' => 'Softkom Solutions',
			'version' => 1,
			'copy' => array(
				'eyebrow' => 'Softkom Pilot Assessment',
				'title' => 'How scalable are Softkom\'s business systems?',
				'lead' => 'Identify where lead generation, project delivery, support, reporting and automation need to improve before Softkom scales further.',
				'start_label' => 'Start the Softkom Assessment',
				'note' => 'Internal pilot. The same engine will become the reusable foundation for industry-specific assessments.',
				'complete_eyebrow' => 'Softkom Pilot Complete',
				'complete_title' => 'Softkom\'s systems score is ready.',
				'complete_body' => 'Enter the test details to create a complete lead, commercial recommendation and pipeline record.',
				'results_eyebrow' => 'Softkom Pilot Results',
				'next_step_title' => 'Turn the findings into Softkom\'s practical systems roadmap',
				'next_step_body' => 'Use this pilot to validate the complete journey before creating industry editions.',
				'next_step_label' => 'Review Softkom Solutions',
				'next_step_url' => home_url( '/services/' ),
			),
			'questions' => array(
				array('id'=>'visibility-01','section'=>'Strategy & Planning','question'=>'Can Softkom leadership see the current sales pipeline, project delivery, support workload and recurring revenue without assembling reports manually?','help'=>'Consider whether decisions can be made from live information instead of separate spreadsheets and delayed updates.'),
				array('id'=>'reporting-03','section'=>'Strategy & Planning','question'=>'Are Softkom\'s KPIs clearly defined around lead conversion, project margin, delivery quality, support and monthly recurring revenue?','help'=>'Focus on measures that directly guide commercial and operational decisions.'),
				array('id'=>'process-01','section'=>'Process & Automation','question'=>'How well are Softkom\'s recurring sales, proposal, delivery, support and follow-up processes documented?','help'=>'Consider whether each process has clear ownership, steps and expected outcomes.'),
				array('id'=>'automation-01','section'=>'Process & Automation','question'=>'How much repetitive work in lead capture, follow-up, onboarding, project updates and support is already automated?','help'=>'Think about notifications, approvals, reminders, data movement and recurring administration.'),
				array('id'=>'integration-01','section'=>'Technology','question'=>'How automatically do Softkom\'s website, lead system, proposals, project delivery, support and finance tools exchange data?','help'=>'Look for duplicate entry, manual exports and information that becomes disconnected between tools.'),
				array('id'=>'ai-01','section'=>'Technology','question'=>'How reliable and well-managed is the Softkom data foundation needed for safe AI automation?','help'=>'Consider data quality, ownership, permissions and whether AI output can be checked against trusted sources.'),
				array('id'=>'process-03','section'=>'People & Culture','question'=>'How resilient would Softkom\'s sales, delivery and support processes be if a key person were unavailable?','help'=>'Consider shared knowledge, documented procedures and cross-training.'),
				array('id'=>'risk-01','section'=>'People & Culture','question'=>'How comfortably could Softkom absorb a sudden increase in qualified leads, projects or support demand?','help'=>'Imagine growth without relying on overtime, emergency workarounds or one key person.'),
				array('id'=>'data-01','section'=>'Data & Reporting','question'=>'How consistently is lead, client, project and revenue information captured once and reused across Softkom\'s systems?','help'=>'Look for repeated entry and inconsistent records between sales, delivery, support and finance.'),
				array('id'=>'reporting-02','section'=>'Data & Reporting','question'=>'How confidently can Softkom act on pipeline, delivery and recurring-revenue reports without rechecking the figures offline?','help'=>'Consider trust in definitions, dashboards and sources of truth.'),
				array('id'=>'visibility-03','section'=>'Customer Experience','question'=>'How reliably are project or support exceptions identified before a Softkom client needs to escalate them?','help'=>'Consider proactive alerts, ownership, delivery risks and support visibility.'),
				array('id'=>'automation-03','section'=>'Customer Experience','question'=>'How automatically are Softkom client onboarding, progress updates, internal handoffs and follow-ups triggered?','help'=>'Think about the full journey from signed proposal through delivery and ongoing support.'),
				array('id'=>'governance-01','section'=>'Governance & Compliance','question'=>'How clear are approval rights for Softkom pricing, proposals, deployments, system changes and access?','help'=>'Consider decision ownership, change control and who can approve commercial or technical risk.'),
				array('id'=>'compliance-02','section'=>'Governance & Compliance','question'=>'How consistently is POPIA, security, backup, deployment and support evidence captured during normal Softkom work?','help'=>'Consider whether evidence is built into normal workflows or assembled manually when needed.'),
			),
		),
		'property-repairs' => array(
			'name' => 'Property Repairs & Maintenance',
			'version' => 1,
			'copy' => array(
				'eyebrow' => 'Free Property Services Growth Assessment',
				'title' => 'How many repair and maintenance leads are slipping through the cracks?',
				'lead' => 'Find gaps in enquiry handling, quoting, scheduling, job updates, follow-up and repeat business — and see where automation can help you win more profitable jobs.',
				'start_label' => 'Check My Lead & Job System',
				'note' => 'Takes a few minutes. You will receive a practical score and recommended next steps.',
				'complete_eyebrow' => 'Assessment Complete',
				'complete_title' => 'Your property services growth score is ready.',
				'complete_body' => 'Enter your details to see where enquiries, quotes and jobs can be converted more reliably.',
				'results_eyebrow' => 'Your Growth Assessment',
				'next_step_title' => 'Turn more enquiries into booked, profitable jobs',
				'next_step_body' => 'Use your results to prioritise lead response, quote follow-up, scheduling and customer communication improvements.',
				'next_step_label' => 'Request a Growth Review',
				'next_step_url' => home_url( '/contact/' ),
			),
			'questions' => array(
				array('id'=>'visibility-01','section'=>'Lead & Job Visibility','question'=>'Can you see every new enquiry, quote, booked job and follow-up in one place?','help'=>'Think about website enquiries, WhatsApp, calls, referrals and jobs waiting for action.'),
				array('id'=>'reporting-03','section'=>'Lead & Job Visibility','question'=>'Do you know your enquiry-to-quote and quote-to-job conversion rates?','help'=>'Consider whether you can identify which lead sources and services actually produce profitable work.'),
				array('id'=>'process-01','section'=>'Quoting & Follow-up','question'=>'How consistently does every new enquiry move through qualification, site visit, quote and follow-up?','help'=>'Consider whether staff follow a repeatable process or rely on memory and individual habits.'),
				array('id'=>'automation-01','section'=>'Quoting & Follow-up','question'=>'How much of your enquiry acknowledgement, quote follow-up, reminders and customer updates happens automatically?','help'=>'Look for repetitive admin that delays response times or gets forgotten when the team is busy.'),
				array('id'=>'integration-01','section'=>'Scheduling & Operations','question'=>'How well do your enquiry, quoting, scheduling, job and invoicing tools share information?','help'=>'Look for duplicate capturing, WhatsApp-only information and jobs that require manual handoffs.'),
				array('id'=>'ai-01','section'=>'Scheduling & Operations','question'=>'How ready is your business to use AI for enquiry triage, quote assistance, customer communication or job administration?','help'=>'Consider whether your service, customer and job information is accurate and accessible enough for automation.'),
				array('id'=>'process-03','section'=>'Team & Delivery','question'=>'Could another team member take over a customer or job without relying on one person\'s memory or WhatsApp history?','help'=>'Consider job notes, photos, scope, customer promises and next actions.'),
				array('id'=>'risk-01','section'=>'Team & Delivery','question'=>'Could your current operation handle a sudden increase in qualified repair or maintenance enquiries?','help'=>'Think about quoting capacity, scheduling, technicians, customer updates and administration.'),
				array('id'=>'data-01','section'=>'Customer & Job Data','question'=>'Is customer, property, quote and job information captured once and reused throughout the job lifecycle?','help'=>'Look for repeated entry and important information scattered across phones, spreadsheets and systems.'),
				array('id'=>'reporting-02','section'=>'Customer & Job Data','question'=>'Can you quickly see which services, areas and lead sources generate your best margins?','help'=>'Consider whether you can make marketing and staffing decisions from trusted figures.'),
				array('id'=>'visibility-03','section'=>'Customer Experience','question'=>'Are delays, missed appointments, overdue quotes and unresolved customer issues flagged before the customer has to chase you?','help'=>'Consider proactive alerts and clear ownership of exceptions.'),
				array('id'=>'automation-03','section'=>'Customer Experience','question'=>'How automatically do customers receive booking confirmations, reminders, progress updates and completion follow-ups?','help'=>'Think about communication from first enquiry through completed job and review request.'),
				array('id'=>'governance-01','section'=>'Controls & Growth','question'=>'Are pricing, discounts, job approvals, purchasing and technician responsibilities clearly controlled?','help'=>'Consider who can approve changes that affect margin, customer promises and delivery.'),
				array('id'=>'compliance-02','section'=>'Controls & Growth','question'=>'How consistently do you retain job evidence such as photos, approvals, invoices, warranties and customer communication?','help'=>'Consider whether records are easy to retrieve when a customer, insurer, landlord or property manager asks for them.'),
			),
		),
	);
}

function softkom_industry_funnel_active_profile() {
	$profiles = softkom_industry_funnel_profiles();
	$key = isset( $_GET['industry'] ) ? sanitize_key( wp_unslash( $_GET['industry'] ) ) : 'softkom';
	if ( ! isset( $profiles[ $key ] ) ) {
		$key = 'softkom';
	}
	$profile = $profiles[ $key ];
	$profile['key'] = $key;
	return $profile;
}

function softkom_industry_funnel_enqueue() {
	if ( ! is_page( 'assessment' ) ) {
		return;
	}
	$profile = softkom_industry_funnel_active_profile();
	$handle = 'softkom-industry-funnel';
	$src = content_url( '/mu-plugins/softkom-industry-funnel.js' );
	$path = WPMU_PLUGIN_DIR . '/softkom-industry-funnel.js';
	$version = is_readable( $path ) ? (string) filemtime( $path ) : '1';
	wp_enqueue_script( $handle, $src, array(), $version, true );
	wp_localize_script($handle,'softkomIndustryFunnel',array('key'=>$profile['key'],'name'=>$profile['name'],'version'=>(int)$profile['version'],'copy'=>$profile['copy'],'questions'=>$profile['questions']));
}
add_action( 'wp_enqueue_scripts', 'softkom_industry_funnel_enqueue', 40 );

function softkom_industry_funnel_store_profile( $lead_id, $result, $security ) {
	unset( $result, $security );
	$profiles = softkom_industry_funnel_profiles();
	$key = isset( $_POST['industry_key'] ) && is_scalar( $_POST['industry_key'] ) ? sanitize_key( wp_unslash( (string) $_POST['industry_key'] ) ) : 'softkom';
	if ( ! isset( $profiles[ $key ] ) ) {
		$key = 'softkom';
	}
	update_post_meta( $lead_id, '_softkom_industry_key', $key );
	update_post_meta( $lead_id, '_softkom_industry_name', $profiles[ $key ]['name'] );
	update_post_meta( $lead_id, '_softkom_industry_profile_version', (int) $profiles[ $key ]['version'] );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_industry_funnel_store_profile', 35, 3 );

function softkom_industry_funnel_lead_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'softkom_company' === $key ) {
			$new['softkom_industry'] = 'Industry Profile';
		}
	}
	return $new;
}
add_filter( 'manage_softkom_lead_posts_columns', 'softkom_industry_funnel_lead_columns', 30 );

function softkom_industry_funnel_render_lead_column( $column, $post_id ) {
	if ( 'softkom_industry' !== $column ) {
		return;
	}
	$name = get_post_meta( $post_id, '_softkom_industry_name', true );
	echo '' !== $name ? esc_html( $name ) : '&mdash;';
}
add_action( 'manage_softkom_lead_posts_custom_column', 'softkom_industry_funnel_render_lead_column', 30, 2 );
