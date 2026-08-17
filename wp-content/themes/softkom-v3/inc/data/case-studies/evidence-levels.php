<?php
/**
 * Case study evidence & permission levels.
 *
 * Never require metrics. Never invent metrics.
 * Do not display unavailable information.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Evidence levels Softkom may assign to a case.
 *
 * @return array<string, array{id:string,label:string,public_ok:bool,description:string}>
 */
function softkom_v3_case_evidence_levels() {
	return array(
		'anonymous' => array(
			'id'          => 'anonymous',
			'label'       => 'Anonymous',
			'public_ok'   => true,
			'description' => 'Publishable without naming the client. Use industry and problem language Softkom can defend.',
		),
		'named'     => array(
			'id'          => 'named',
			'label'       => 'Named',
			'public_ok'   => true,
			'description' => 'Client naming permission logged. Still omit metrics/quotes unless separately approved.',
		),
		'internal'  => array(
			'id'          => 'internal',
			'label'       => 'Internal',
			'public_ok'   => false,
			'description' => 'Sales and delivery use only. Not for public pages.',
		),
		'permission-pending' => array(
			'id'          => 'permission-pending',
			'label'       => 'Permission pending',
			'public_ok'   => false,
			'description' => 'Capture exists; waiting on client permission before public use beyond already-approved cards.',
		),
	);
}

/**
 * Permission flags on a case (metrics optional; default false).
 *
 * @return array<string, bool>
 */
function softkom_v3_case_permission_defaults() {
	return array(
		'public_name'  => false,
		'metrics'      => false,
		'quotes'       => false,
		'screenshots'  => false,
		'logo'         => false,
	);
}
