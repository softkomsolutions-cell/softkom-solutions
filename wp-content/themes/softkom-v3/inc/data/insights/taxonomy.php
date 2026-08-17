<?php
/**
 * Insights platform — taxonomy (categories & topics).
 *
 * Recommended categories are fixed for Phase 4 editorial discipline.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Insight categories Softkom may assign.
 *
 * @return array<string, array{id:string,label:string,description:string}>
 */
function softkom_v3_insight_categories() {
	$defs = array(
		'business-systems'      => 'ERP, platforms and how operational systems support the business.',
		'erp'                   => 'Selection, implementation, ownership and failure patterns around ERP.',
		'integration'           => 'Connecting systems so data moves once with clear ownership.',
		'operations'            => 'How work moves day to day — handoffs, queues, exceptions.',
		'ai'                    => 'Controlled intelligence: where AI helps and where people stay accountable.',
		'governance'            => 'Decision rights, change control and accountability in systems work.',
		'compliance'            => 'Evidence, control and regulatory readiness designed into workflows.',
		'leadership'            => 'What directors need to see and decide about systems investment.',
		'marketplace'           => 'Multi-channel selling, catalogue and fulfilment realities.',
		'digital-transformation'=> 'Sequenced change — not tool theatre — for mid-market businesses.',
	);

	$out = array();
	foreach ( $defs as $id => $description ) {
		$out[ $id ] = array(
			'id'          => $id,
			'label'       => softkom_v3_insight_category_label( $id ),
			'description' => $description,
		);
	}
	return $out;
}

/**
 * Human label for a category id.
 *
 * @param string $id Category id.
 * @return string
 */
function softkom_v3_insight_category_label( $id ) {
	$map = array(
		'business-systems'       => 'Business Systems',
		'erp'                    => 'ERP',
		'integration'            => 'Integration',
		'operations'             => 'Operations',
		'ai'                     => 'AI',
		'governance'             => 'Governance',
		'compliance'             => 'Compliance',
		'leadership'             => 'Leadership',
		'marketplace'            => 'Marketplace',
		'digital-transformation' => 'Digital Transformation',
	);
	return isset( $map[ $id ] ) ? $map[ $id ] : $id;
}

/**
 * Recommended category ids (editorial default set).
 *
 * @return string[]
 */
function softkom_v3_insight_recommended_categories() {
	return array_keys( softkom_v3_insight_categories() );
}

/**
 * Topics Softkom may tag (finer than category).
 *
 * @return string[]
 */
function softkom_v3_insight_topics() {
	return array(
		'spreadsheet-dependence',
		'erp-failure-patterns',
		'when-not-to-automate',
		'integrate-vs-replace',
		'master-data-ownership',
		'month-end-reporting',
		'ai-governance-sme',
		'marketplace-integrations',
		'change-after-go-live',
		'build-vs-buy',
		'popia-in-workflows',
		'process-handoffs',
		'audit-evidence-design',
	);
}
