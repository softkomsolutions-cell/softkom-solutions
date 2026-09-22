<?php
/**
 * Plugin Name: Softkom Acquisition Reinforcement
 * Description: Adds buyer-intent context and mapped internal links to the six Sprint 2 acquisition pages without changing their URLs.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function softkom_acquisition_reinforcement_pages() {
	return array(
		'automate-data-entry-south-africa' => array(
			'questions' => array(
				array( 'How much is manual data entry costing the business?', 'The real cost is the staff time spent retyping information plus the downstream cost of errors, reconciliation and delays. Softkom starts by identifying the repeated capture points and estimating the operational impact before recommending automation.' ),
				array( 'Can Softkom automate information from PDFs, emails and forms?', 'Yes. Where appropriate, document and email workflows can extract structured information, validate it and route it into the right system. AI can assist with less structured inputs, with human review retained for exceptions.' ),
				array( 'Do we need new software?', 'Usually not. Softkom first looks at whether your existing CRM, accounting, ecommerce and operational systems can be connected or automated. Custom software is considered only when the workflow genuinely needs it.' ),
			),
			'related' => array( 'connect-business-software-south-africa', 'automate-business-reporting-south-africa', 'operations-management-system-south-africa' ),
		),
		'automate-business-reporting-south-africa' => array(
			'questions' => array(
				array( 'Can reporting be automated without replacing Excel?', 'Yes. Excel can remain an analysis tool while the collection, consolidation and refresh of source data are automated so reports no longer depend on manual copy-and-paste.' ),
				array( 'Can reports combine accounting, CRM and operational data?', 'Yes. Softkom can connect the source systems and create a consistent reporting flow so management is working from the same underlying data rather than separate departmental copies.' ),
				array( 'What should we automate first?', 'Start with the recurring report that consumes the most staff time, creates the most reconciliation work or delays an important management decision.' ),
			),
			'related' => array( 'automate-data-entry-south-africa', 'connect-business-software-south-africa', 'operations-management-system-south-africa' ),
		),
		'automate-approvals-workflows-south-africa' => array(
			'questions' => array(
				array( 'Can we keep using email or WhatsApp for approvals?', 'Yes. The communication channel can stay familiar while the workflow underneath tracks ownership, status, reminders and a reliable approval history.' ),
				array( 'Which approvals are usually worth automating first?', 'Purchase approvals, quote sign-off, leave requests, document approvals and other repeatable sequences are strong starting points because the rules and delays are easy to identify.' ),
				array( 'Do we need a custom workflow system?', 'Not necessarily. Softkom first checks whether your existing tools can be configured or connected. A tailored workflow system is appropriate when approvals are complex, compliance-sensitive or central to operations.' ),
			),
			'related' => array( 'automate-lead-follow-up-south-africa', 'connect-business-software-south-africa', 'operations-management-system-south-africa' ),
		),
		'connect-business-software-south-africa' => array(
			'questions' => array(
				array( 'Can Softkom connect the software we already pay for?', 'Yes. That is usually the first option. Softkom assesses the APIs, exports and workflow capabilities of the systems you already use before recommending replacement.' ),
				array( 'What if one system has no API?', 'Options can include controlled import/export workflows, middleware or a lightweight sync layer. The aim is to remove re-entry and create one dependable flow without replacing good software unnecessarily.' ),
				array( 'Is integration cheaper than custom software?', 'In many cases, yes. If the existing applications already perform their core jobs well, integration can remove most of the manual work at far lower cost and risk than a full replacement.' ),
			),
			'related' => array( 'automate-data-entry-south-africa', 'automate-business-reporting-south-africa', 'operations-management-system-south-africa' ),
		),
		'automate-lead-follow-up-south-africa' => array(
			'questions' => array(
				array( 'Can follow-up work across web forms, email and WhatsApp?', 'Yes. Enquiries can be captured into one tracked flow, acknowledged quickly, assigned to the right person and followed through with reminders and next actions.' ),
				array( 'Will the replies sound robotic?', 'They do not have to. Automation should handle speed, routing and routine follow-up while salespeople retain control of the relationship, judgement and closing conversation.' ),
				array( 'Do we need a new CRM?', 'Not automatically. Softkom first checks whether the CRM or tools you already use can support the required workflow before recommending another platform.' ),
			),
			'related' => array( 'automate-approvals-workflows-south-africa', 'connect-business-software-south-africa', 'automate-data-entry-south-africa' ),
		),
		'operations-management-system-south-africa' => array(
			'questions' => array(
				array( 'How do we know if we need an operations management system?', 'If managers cannot see current jobs, orders, stock or team status from one reliable place, and staff compensate with spreadsheets, chat and manual updates, the operation is a strong candidate for a structured system.' ),
				array( 'Can we improve operations without replacing everything?', 'Yes. Softkom first considers automation, integration and configuration of existing tools. A custom operations system is recommended only where those options cannot provide enough control or visibility.' ),
				array( 'What does a custom business system typically cost?', 'Softkom custom business-system engagements typically start around R75,000, with larger or more complex platforms scoped separately. The assessment is used to confirm whether a custom build is justified before that investment is proposed.' ),
			),
			'related' => array( 'connect-business-software-south-africa', 'automate-business-reporting-south-africa', 'automate-approvals-workflows-south-africa' ),
		),
	);
}

function softkom_acquisition_reinforcement_is_page() {
	if ( ! is_page() ) { return false; }
	$slug = get_post_field( 'post_name', get_queried_object_id() );
	return isset( softkom_acquisition_reinforcement_pages()[ $slug ] );
}

add_filter( 'the_content', function ( $content ) {
	if ( is_admin() || ! is_main_query() || ! in_the_loop() || ! softkom_acquisition_reinforcement_is_page() ) {
		return $content;
	}

	$slug  = get_post_field( 'post_name', get_queried_object_id() );
	$data  = softkom_acquisition_reinforcement_pages()[ $slug ];
	$all   = function_exists( 'softkom_traffic_sprint_pages' ) ? softkom_traffic_sprint_pages() : array();
	$items = '';
	foreach ( $data['questions'] as $q ) {
		$items .= '<div class="ska-answer"><h3>' . esc_html( $q[0] ) . '</h3><p>' . esc_html( $q[1] ) . '</p></div>';
	}

	$links = '';
	foreach ( $data['related'] as $target ) {
		$label = isset( $all[ $target ]['title'] ) ? $all[ $target ]['title'] : ucwords( str_replace( '-', ' ', $target ) );
		$links .= '<a href="' . esc_url( home_url( '/' . $target . '/' ) ) . '">' . esc_html( $label ) . '<span>→</span></a>';
	}

	$assessment = esc_url( add_query_arg( array( 'utm_source' => 'softkom-organic', 'utm_medium' => 'website', 'utm_campaign' => 'buyer-intent', 'utm_content' => $slug ), home_url( '/assessment/' ) ) );
	$strategy   = esc_url( add_query_arg( array( 'source' => 'organic-buyer-page', 'utm_source' => 'softkom-organic', 'utm_medium' => 'website', 'utm_campaign' => 'strategy-call', 'utm_content' => $slug ), home_url( '/contact/' ) ) );
	$projects   = esc_url( home_url( '/projects/' ) );
	$block  = '<section class="sks-white ska-buyer" aria-label="Buyer questions"><div class="sks-inner">';
	$block .= '<p class="sks-label">BEFORE YOU INVEST</p><h2>Questions buyers usually ask before automating this process</h2>';
	$block .= '<div class="sks-grid">' . $items . '</div>';
	$block .= '<div class="ska-proof"><p><strong>Softkom focuses on practical business outcomes first.</strong> Delivery examples include ecommerce, mobile ordering and business-system work for South African clients. Review the project portfolio or run the free assessment to identify the highest-value next step.</p><div class="sks-actions"><a class="sks-secondary" href="' . $projects . '">View Softkom Projects</a><a class="sks-primary" href="' . $assessment . '">Start My Free Assessment →</a><a class="sks-secondary" href="' . $strategy . '">Book a Strategy Call</a></div></div>';
	$block .= '<div class="ska-related"><p class="sks-label">RELATED OPERATIONAL PROBLEMS</p><nav class="sks-proof">' . $links . '</nav></div>';
	$block .= '</div></section>';

	return $content . $block;
}, 84 );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! softkom_acquisition_reinforcement_is_page() ) { return; }
	wp_register_style( 'softkom-acquisition-reinforcement', false, array( 'softkom-traffic-sprint' ), '1.0.0' );
	wp_enqueue_style( 'softkom-acquisition-reinforcement' );
	wp_add_inline_style( 'softkom-acquisition-reinforcement', '.ska-buyer{border-top:1px solid #e2e8f0}.ska-answer{background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:20px}.ska-answer h3{margin:0 0 8px;color:#0f172a;font-size:18px;line-height:1.35}.ska-answer p{margin:0;color:#475569;line-height:1.65}.ska-proof{margin-top:32px;padding:24px;border:1px solid #bfdbfe;background:#eff6ff;border-radius:16px}.ska-proof p{max-width:850px;margin:0;color:#334155;line-height:1.65}.ska-proof .sks-actions{margin-bottom:0}.ska-related{margin-top:36px}' );
}, 31 );
