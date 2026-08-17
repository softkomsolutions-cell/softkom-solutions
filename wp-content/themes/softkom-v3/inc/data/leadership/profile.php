<?php
/**
 * Leadership profile registry — verified entries only.
 *
 * Source of truth for public About leadership: docs/phase-4/leadership/evidence-log.md
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Softkom leadership profile (About evidence source).
 *
 * @return array<string, mixed>
 */
function softkom_v3_leadership_profile() {
	$profile = softkom_v3_leadership_profile_blank();

	$profile['name'] = 'Darren Enfield';
	$profile['role'] = 'Founder';
	$profile['summary'] = 'Darren leads Softkom’s client work directly — discovery, solution direction and commercial decisions — with a practical focus on business systems, automation, software products and operational problem-solving.';
	$profile['philosophy'] = 'Softkom was established to help organisations make practical systems decisions without being pushed prematurely toward a particular product or large transformation programme.';
	$profile['involvement'] = array(
		'Direct involvement in discovery conversations.',
		'Solution direction and commercial decisions on Softkom engagements.',
		'Specialist support engaged where the work requires it.',
	);
	$profile['focus'] = array(
		'Business systems and operational workflows.',
		'Integrations and controlled automation.',
		'Software products Softkom is building alongside client work.',
	);
	$profile['notes'] = 'RC2.4: identity fields verified for About. Awards, certifications, years-of-experience and major-client claims remain empty until logged.';

	return $profile;
}
