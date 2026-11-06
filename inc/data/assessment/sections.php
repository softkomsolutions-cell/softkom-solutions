<?php
/**
 * Business Systems Assessment — section definitions.
 *
 * Future lead-generation engine. Not a contact form.
 * Do not publish a public assessment UI until Softkom can honour the offer.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Assessment sections (canonical order).
 *
 * @return array<string, array<string, mixed>>
 */
function softkom_v3_assessment_sections() {
	return array(
		'business-visibility' => array(
			'id'          => 'business-visibility',
			'label'       => 'Business visibility',
			'purpose'     => 'Can leadership see how work is moving today without assembling conflicting exports?',
			'frameworks'  => array( 'systems-maturity', 'connected-business' ),
			'services'    => array( 'business-systems', 'business-intelligence' ),
			'insights'    => array( 'why-most-businesses-are-still-running-on-spreadsheets' ),
		),
		'reporting'           => array(
			'id'          => 'reporting',
			'label'       => 'Reporting',
			'purpose'     => 'How weekly and month-end pictures are produced, trusted and delayed.',
			'frameworks'  => array( 'systems-maturity', 'connected-business' ),
			'services'    => array( 'business-intelligence', 'business-systems' ),
			'insights'    => array(),
		),
		'process-maturity'    => array(
			'id'          => 'process-maturity',
			'label'       => 'Process maturity',
			'purpose'     => 'Whether recurring work has ownership, sequence and control — or lives in tribal knowledge.',
			'frameworks'  => array( 'systems-maturity', 'transformation-journey' ),
			'services'    => array( 'business-systems', 'process-automation' ),
			'insights'    => array(),
		),
		'systems-integration' => array(
			'id'          => 'systems-integration',
			'label'       => 'Systems integration',
			'purpose'     => 'Where systems should talk and currently force people to copy data.',
			'frameworks'  => array( 'connected-business', 'delivery-lifecycle' ),
			'services'    => array( 'process-automation', 'business-systems', 'marketplace-solutions' ),
			'insights'    => array(),
		),
		'automation'          => array(
			'id'          => 'automation',
			'label'       => 'Automation',
			'purpose'     => 'Where volume work has clear rules — and where Softkom would refuse to automate a broken process.',
			'frameworks'  => array( 'ai-opportunity-map', 'systems-maturity' ),
			'services'    => array( 'process-automation', 'ai-automation' ),
			'insights'    => array( 'how-to-identify-automation-opportunities' ),
		),
		'ai-readiness'        => array(
			'id'          => 'ai-readiness',
			'label'       => 'AI readiness',
			'purpose'     => 'Whether foundations, data and accountability make controlled AI safe to introduce.',
			'frameworks'  => array( 'ai-opportunity-map', 'systems-maturity' ),
			'services'    => array( 'ai-automation' ),
			'insights'    => array( 'how-to-identify-automation-opportunities' ),
		),
		'data-quality'        => array(
			'id'          => 'data-quality',
			'label'       => 'Data quality',
			'purpose'     => 'Master data ownership, re-entry and conflicting numbers across systems.',
			'frameworks'  => array( 'connected-business', 'systems-maturity' ),
			'services'    => array( 'business-systems', 'process-automation' ),
			'insights'    => array(),
		),
		'governance'          => array(
			'id'          => 'governance',
			'label'       => 'Governance',
			'purpose'     => 'Decision rights, change control and who owns process after go-live.',
			'frameworks'  => array( 'delivery-lifecycle', 'transformation-framework' ),
			'services'    => array( 'business-systems', 'compliance-platforms' ),
			'insights'    => array(),
		),
		'compliance'          => array(
			'id'          => 'compliance',
			'label'       => 'Compliance',
			'purpose'     => 'Whether evidence for audits and privacy obligations is designed into work or assembled ad hoc.',
			'frameworks'  => array( 'delivery-lifecycle', 'systems-maturity' ),
			'services'    => array( 'compliance-platforms' ),
			'insights'    => array( 'automating-compliance-soc2-iso-popia' ),
		),
		'operational-risk'    => array(
			'id'          => 'operational-risk',
			'label'       => 'Operational risk',
			'purpose'     => 'Single points of failure, fragile handoffs and exposure Softkom would flag before recommending tools.',
			'frameworks'  => array( 'systems-maturity', 'transformation-journey' ),
			'services'    => array( 'business-systems', 'process-automation' ),
			'insights'    => array(),
		),
	);
}

/**
 * Section ids in display order.
 *
 * @return string[]
 */
function softkom_v3_assessment_section_ids() {
	return array_keys( softkom_v3_assessment_sections() );
}
