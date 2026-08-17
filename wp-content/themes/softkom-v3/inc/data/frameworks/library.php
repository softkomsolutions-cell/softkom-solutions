<?php
/**
 * Softkom Framework Library — canonical source of truth.
 *
 * Diagram HTML remains in graphics.php. Definitions, relationships,
 * and associations live here so Insights, Cases, Assessment, and
 * Leadership can reference frameworks without duplicating copy.
 *
 * Rule: Frameworks clarify Softkom’s method. They are not delivery proof.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Full framework library.
 *
 * @return array<string, array<string, mixed>>
 */
function softkom_v3_frameworks_library() {
	return array(
		'transformation-framework' => array(
			'id'            => 'transformation-framework',
			'name'          => 'Softkom Transformation Framework™',
			'short_name'    => 'Transformation Framework',
			'trademark'     => true,
			'role'          => 'methodology-umbrella',
			'overview'      => 'The complete methodology Softkom uses to diagnose, design and deliver systems work — from how work moves today to a next step leadership can approve.',
			'purpose'       => 'Give Softkom and the client a shared method for sequencing diagnosis, design, delivery and controlled intelligence — without treating trademarks as proof of delivery.',
			'when_applicable'     => array(
				'A mid-market business needs a sequenced path from operational friction to scoped change.',
				'Leadership wants diagnosis before tool or automation prescriptions.',
				'Proposals and workshops need a stable method Softkom can reuse.',
			),
			'when_not_applicable' => array(
				'A single tactical fix with no organisational change (use Delivery Lifecycle alone).',
				'As a substitute for client-permitted case evidence.',
				'As marketing theatre without a real engagement sequence.',
			),
			'diagram'       => 'softkom_v3_graphic_connected_business', // umbrella visual often paired with journey
			'diagram_note'  => 'Buyer-facing journey diagram uses Transformation Journey™ stages; architecture visuals use Connected Business™.',
			'terminology'   => array(
				array( 'term' => 'Softkom Transformation Framework™', 'meaning' => 'Methodology umbrella Softkom uses across diagnosis and delivery.' ),
				array( 'term' => 'Transformation Journey™', 'meaning' => 'Eight-stage client sequence inside the Framework.' ),
				array( 'term' => 'Delivery Lifecycle', 'meaning' => 'How Softkom executes a scoped engagement once the next step is agreed.' ),
			),
			'relationships' => array(
				'contains'     => array( 'transformation-journey', 'delivery-lifecycle' ),
				'related'      => array( 'connected-business', 'systems-maturity', 'ai-opportunity-map' ),
			),
			'services'      => array( 'business-systems', 'process-automation', 'ai-automation', 'compliance-platforms' ),
			'insights'      => array(),
			'case_studies'  => array(),
			'assessment_sections' => array( 'process-maturity', 'systems-integration', 'automation', 'ai-readiness', 'governance' ),
			'buyer_facing_title'  => 'How Softkom sequences the work',
		),

		'transformation-journey' => array(
			'id'            => 'transformation-journey',
			'name'          => 'Transformation Journey™',
			'short_name'    => 'Transformation Journey',
			'trademark'     => true,
			'role'          => 'client-sequence',
			'overview'      => 'The eight-stage client journey Softkom uses to move from today’s operations to a practical next step leadership can approve.',
			'purpose'       => 'Sequence the client conversation: map work, name friction, assess, design change, integrate, apply AI with control, define results, and agree the next conversation.',
			'when_applicable'     => array(
				'Discovery workshops and strategy calls.',
				'Homepage and About sequencing explanations.',
				'Proposal narratives that need stage language without inventing proof.',
			),
			'when_not_applicable' => array(
				'Day-to-day project execution once scope is agreed (use Delivery Lifecycle).',
				'As a maturity score (use Systems Maturity™).',
			),
			'diagram'       => null,
			'diagram_note'  => 'Rendered by sections/journey.php from softkom_v3_framework_journey_stages().',
			'stages'        => softkom_v3_framework_journey_stages_raw(),
			'terminology'   => array(
				array( 'term' => 'Today’s Business', 'meaning' => 'How work moves today — teams, tools, handoffs, approvals, spreadsheet gaps.' ),
				array( 'term' => 'Assessment', 'meaning' => 'Diagnose bottlenecks and ownership before prescribing tools.' ),
				array( 'term' => 'Let’s Talk', 'meaning' => 'Turn the map into a scoped next step Softkom can defend.' ),
			),
			'relationships' => array(
				'parent'  => 'transformation-framework',
				'feeds'   => array( 'delivery-lifecycle' ),
				'related' => array( 'systems-maturity', 'connected-business', 'ai-opportunity-map' ),
			),
			'services'      => array( 'business-systems', 'process-automation', 'ai-automation' ),
			'insights'      => array(),
			'case_studies'  => array(),
			'assessment_sections' => array( 'business-visibility', 'process-maturity', 'systems-integration' ),
			'buyer_facing_title'  => 'How Softkom sequences the work',
		),

		'delivery-lifecycle' => array(
			'id'            => 'delivery-lifecycle',
			'name'          => 'Delivery Lifecycle',
			'short_name'    => 'Delivery Lifecycle',
			'trademark'     => false,
			'role'          => 'project-execution',
			'overview'      => 'How Softkom executes a scoped engagement once Softkom and the client agree what to do.',
			'purpose'       => 'Run discovery through support with clear participation expectations — without confusing project execution with the eight-stage client Journey.',
			'when_applicable'     => array(
				'Scoped build and integration engagements.',
				'About engage path and delivery diagrams.',
				'Statements of work and build plans.',
			),
			'when_not_applicable' => array(
				'Early strategy conversations that still need Journey placement.',
				'As a substitute for Systems Maturity™ diagnosis.',
			),
			'diagram'       => 'softkom_v3_graphic_delivery',
			'diagram_note'  => 'Four-step buyer summary and six-step diagram are the same Lifecycle at different resolutions.',
			'stages_compact' => array(
				array( 'id' => 'discovery', 'title' => 'Discovery', 'meaning' => 'Map friction, ownership and priorities.' ),
				array( 'id' => 'design', 'title' => 'Design', 'meaning' => 'Target workflows and data before code.' ),
				array( 'id' => 'build', 'title' => 'Build', 'meaning' => 'Ship in controlled, demonstrable increments.' ),
				array( 'id' => 'support', 'title' => 'Support', 'meaning' => 'Adopt, fix and iterate after go-live.' ),
			),
			'stages_full'   => array(
				array( 'id' => 'discover', 'title' => 'Discover' ),
				array( 'id' => 'design', 'title' => 'Design' ),
				array( 'id' => 'build', 'title' => 'Build' ),
				array( 'id' => 'integrate', 'title' => 'Integrate' ),
				array( 'id' => 'adopt', 'title' => 'Adopt' ),
				array( 'id' => 'support', 'title' => 'Support' ),
			),
			'terminology'   => array(
				array( 'term' => 'Discovery', 'meaning' => 'Shared picture of friction — not a feature wishlist.' ),
				array( 'term' => 'Design', 'meaning' => 'Workflows, data ownership and integrations before code.' ),
				array( 'term' => 'Support', 'meaning' => 'Adoption, fixes and iteration after go-live.' ),
			),
			'relationships' => array(
				'parent'  => 'transformation-framework',
				'follows' => array( 'transformation-journey' ),
				'related' => array( 'connected-business' ),
			),
			'services'      => array( 'business-systems', 'custom-software', 'process-automation', 'ai-automation', 'compliance-platforms' ),
			'insights'      => array(),
			'case_studies'  => array(),
			'assessment_sections' => array( 'process-maturity', 'systems-integration', 'governance' ),
			'buyer_facing_title'  => 'How Softkom delivers',
		),

		'systems-maturity' => array(
			'id'            => 'systems-maturity',
			'name'          => 'Systems Maturity™',
			'short_name'    => 'Systems Maturity',
			'site_name'     => 'Softkom Systems Maturity Model',
			'trademark'     => true,
			'role'          => 'diagnostic',
			'overview'      => 'A calm diagnostic Softkom uses in assessment conversations — spreadsheet dependent → fragmented tools → connected operations → intelligent operations.',
			'purpose'       => 'Place a business honestly on an operating maturity scale Softkom can discuss without shame scoring or invented ROI.',
			'when_applicable'     => array(
				'Business Systems Assessment conversations.',
				'Strategy calls that need a shared diagnostic language.',
				'Prioritising integrate vs replace vs automate.',
			),
			'when_not_applicable' => array(
				'As a public league table or vanity score.',
				'As proof that Softkom has transformed dozens of enterprises.',
			),
			'diagram'       => 'softkom_v3_graphic_maturity',
			'levels'        => array(
				array( 'num' => '01', 'title' => 'Spreadsheet dependent', 'meaning' => 'Critical work in files and tribal knowledge.' ),
				array( 'num' => '02', 'title' => 'Fragmented tools', 'meaning' => 'Systems exist; people still copy between them.' ),
				array( 'num' => '03', 'title' => 'Connected operations', 'meaning' => 'Data and workflows move with control.' ),
				array( 'num' => '04', 'title' => 'Intelligent operations', 'meaning' => 'Automation/AI on stable foundations; people accountable.' ),
			),
			'terminology'   => array(
				array( 'term' => 'Spreadsheet dependent', 'meaning' => 'Operational truth lives in files Softkom cannot govern as a system of record.' ),
				array( 'term' => 'Connected operations', 'meaning' => 'One operating picture with controlled handoffs.' ),
			),
			'relationships' => array(
				'parent'  => 'transformation-framework',
				'related' => array( 'transformation-journey', 'connected-business', 'ai-opportunity-map' ),
			),
			'services'      => array( 'business-systems', 'process-automation', 'ai-automation', 'business-intelligence' ),
			'insights'      => array( 'why-most-businesses-are-still-running-on-spreadsheets' ),
			'case_studies'  => array(),
			'assessment_sections' => array(
				'business-visibility',
				'reporting',
				'process-maturity',
				'systems-integration',
				'automation',
				'ai-readiness',
				'data-quality',
				'governance',
				'compliance',
				'operational-risk',
			),
			'buyer_facing_title'  => 'Where the business sits today',
		),

		'connected-business' => array(
			'id'            => 'connected-business',
			'name'          => 'Connected Business™',
			'short_name'    => 'Connected Business',
			'trademark'     => true,
			'role'          => 'architecture',
			'overview'      => 'Architecture view Softkom uses to explain how operations, integrations, automation and leadership visibility relate.',
			'purpose'       => 'Show directors how layers stack so they can see why spreadsheet glue and disconnected tools create blind spots.',
			'when_applicable'     => array(
				'Discovery mapping of client stacks.',
				'Homepage and services architecture explanations.',
				'Integration vs replacement conversations.',
			),
			'when_not_applicable' => array(
				'As a claim that every Softkom client is already at layer 04.',
				'As a product catalogue.',
			),
			'diagram'       => 'softkom_v3_graphic_connected_business',
			'layers'        => array(
				array( 'num' => '01', 'title' => 'Operations layer', 'meaning' => 'Orders, inventory, service workflows.' ),
				array( 'num' => '02', 'title' => 'Integrations', 'meaning' => 'ERP, CRM, finance, marketplace.' ),
				array( 'num' => '03', 'title' => 'Automation & AI assist', 'meaning' => 'Volume work removed; people in control.' ),
				array( 'num' => '04', 'title' => 'Leadership visibility', 'meaning' => 'Current operational picture for directors.' ),
			),
			'terminology'   => array(
				array( 'term' => 'Operations layer', 'meaning' => 'Where day-to-day work executes.' ),
				array( 'term' => 'Leadership visibility', 'meaning' => 'Trusted current picture — not competing exports.' ),
			),
			'relationships' => array(
				'parent'  => 'transformation-framework',
				'related' => array( 'systems-maturity', 'delivery-lifecycle', 'ai-opportunity-map' ),
			),
			'services'      => array( 'business-systems', 'process-automation', 'marketplace-solutions', 'business-intelligence' ),
			'insights'      => array(),
			'case_studies'  => array( 'lekr', 'psi-stationery' ),
			'assessment_sections' => array( 'business-visibility', 'systems-integration', 'reporting', 'automation' ),
			'buyer_facing_title'  => 'How operations connect',
		),

		'ai-opportunity-map' => array(
			'id'            => 'ai-opportunity-map',
			'name'          => 'AI Opportunity Map™',
			'short_name'    => 'AI Opportunity Map',
			'trademark'     => true,
			'role'          => 'controlled-intelligence',
			'overview'      => 'Softkom’s map for keeping AI controlled — classify → assist → automate → escalate — so volume work can move without removing human accountability.',
			'purpose'       => 'Help leaders decide where AI belongs and where judgment must stay with people.',
			'when_applicable'     => array(
				'AI readiness conversations after foundations are stable enough.',
				'Governance workshops for SMEs.',
				'Scoping assistive vs automated vs escalation paths.',
			),
			'when_not_applicable' => array(
				'When data quality and process ownership are still broken.',
				'As a promise of autonomous decision-making Softkom will not defend.',
			),
			'diagram'       => 'softkom_v3_graphic_ai_map',
			'bands'         => array(
				array( 'num' => '01', 'title' => 'Classify', 'meaning' => 'Sort, label, route at volume.' ),
				array( 'num' => '02', 'title' => 'Assist', 'meaning' => 'Draft/summarise for human approval.' ),
				array( 'num' => '03', 'title' => 'Automate', 'meaning' => 'Remove repetitive execution where outcomes are clear.' ),
				array( 'num' => '04', 'title' => 'Escalate', 'meaning' => 'Hand exceptions to people with accountability.' ),
			),
			'governance_rule' => 'AI may remove volume work; people keep accountability for customer commitments, pricing exceptions and regulated decisions.',
			'terminology'   => array(
				array( 'term' => 'Assist', 'meaning' => 'AI drafts; humans approve.' ),
				array( 'term' => 'Escalate', 'meaning' => 'Exceptions return to accountable people.' ),
			),
			'relationships' => array(
				'parent'  => 'transformation-framework',
				'related' => array( 'systems-maturity', 'connected-business', 'transformation-journey' ),
			),
			'services'      => array( 'ai-automation', 'process-automation', 'compliance-platforms' ),
			'insights'      => array( 'how-to-identify-automation-opportunities' ),
			'case_studies'  => array(),
			'assessment_sections' => array( 'automation', 'ai-readiness', 'data-quality', 'governance' ),
			'buyer_facing_title'  => 'Where AI belongs — and where people stay accountable',
		),
	);
}

/**
 * Journey stages as raw tuples for diagram/section renderers.
 *
 * @return array<int, array{0:string,1:string,2:string}>
 */
function softkom_v3_framework_journey_stages_raw() {
	return array(
		array( '01', 'Today’s Business', 'How work moves today — teams, tools, handoffs, approvals and the spreadsheets that fill the gaps.' ),
		array( '02', 'Problems', 'Where friction blocks scale: re-entry, stalled approvals, late visibility and revenue leaking at the edges.' ),
		array( '03', 'Assessment', 'Softkom diagnosis — map bottlenecks and ownership before prescribing tools or automation.' ),
		array( '04', 'Transformation', 'A designed change path with clear scope, sequence and investment drivers leadership can approve.' ),
		array( '05', 'Integrated Systems', 'Connected systems across the tools you keep — data moves once, with clear ownership at each handoff.' ),
		array( '06', 'AI', 'Controlled intelligence on solid foundations — volume work removed, people accountable for judgment.' ),
		array( '07', 'Results', 'Operating improvement leaders can manage against — throughput, control, accuracy and decision speed.' ),
		array( '08', 'Let’s Talk', 'Book a strategy call and turn the map into a practical next step Softkom can scope with you.' ),
	);
}

/**
 * Public accessor used by graphics.php / journey section.
 *
 * @return array<int, array{0:string,1:string,2:string}>
 */
function softkom_v3_framework_journey_stages() {
	return softkom_v3_framework_journey_stages_raw();
}

/**
 * Single framework by id.
 *
 * @param string $id Framework id.
 * @return array<string, mixed>|null
 */
function softkom_v3_framework( $id ) {
	$library = softkom_v3_frameworks_library();
	$id      = sanitize_title( (string) $id );
	return isset( $library[ $id ] ) ? $library[ $id ] : null;
}

/**
 * Framework ids only.
 *
 * @return string[]
 */
function softkom_v3_framework_ids() {
	return array_keys( softkom_v3_frameworks_library() );
}

/**
 * Resolve diagram markup if a callable is registered on the framework.
 *
 * @param string $id Framework id.
 * @return string
 */
function softkom_v3_framework_diagram_html( $id ) {
	$fw = softkom_v3_framework( $id );
	if ( ! $fw || empty( $fw['diagram'] ) || ! is_callable( $fw['diagram'] ) ) {
		return '';
	}
	return (string) call_user_func( $fw['diagram'] );
}
