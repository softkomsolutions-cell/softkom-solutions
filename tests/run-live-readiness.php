<?php
/**
 * Softkom live readiness verification.
 *
 * Read-only production-safe checks. This script does not create, update or
 * delete leads, campaigns, options or other WordPress data.
 *
 * Run with:
 * wp eval-file wp-content/softkom-assessment-runtime/live-readiness.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo "[ERROR] Run this script through WP-CLI.\n";
	exit( 1 );
}

$GLOBALS['softkom_live_passed'] = 0;
$GLOBALS['softkom_live_failed'] = 0;

function softkom_live_assert( $label, $condition ) {
	if ( $condition ) {
		$GLOBALS['softkom_live_passed']++;
		echo "[PASS] {$label}\n";
	} else {
		$GLOBALS['softkom_live_failed']++;
		echo "[FAIL] {$label}\n";
	}
}

echo "=========================================================\n";
echo "Softkom Live Readiness Verification (Read Only)\n";
echo "=========================================================\n\n";

if ( function_exists( 'softkom_v3_load_data' ) ) {
	softkom_v3_load_data();
}

softkom_live_assert( 'WordPress bootstrap active', function_exists( 'get_option' ) && function_exists( 'get_posts' ) );
softkom_live_assert( 'Assessment data loader available', function_exists( 'softkom_v3_load_data' ) );
softkom_live_assert( 'Lead storage handler available', function_exists( 'softkom_v3_store_assessment_lead' ) );
softkom_live_assert( 'Recurring recommendation calculator available', function_exists( 'softkom_v3_calculate_recurring_recommendation' ) );
softkom_live_assert( 'HOT lead auto-pipeline handler available', function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) );
softkom_live_assert( 'Commercial persistence handler available', function_exists( 'softkom_v3_persist_commercial_recommendation' ) );
softkom_live_assert( 'Public acquisition conversion handler available', function_exists( 'softkom_public_acquisition_track_conversion' ) );
softkom_live_assert( 'Public acquisition notification handler available', function_exists( 'softkom_public_acquisition_notify' ) );
softkom_live_assert( 'Campaign performance function available', function_exists( 'softkom_v3_campaign_performance' ) );
softkom_live_assert( 'Campaign tracked URL function available', function_exists( 'softkom_v3_campaign_tracked_url' ) );

$assessment_page = get_page_by_path( 'assessment' );
softkom_live_assert( 'Assessment page exists', $assessment_page instanceof WP_Post && 'publish' === $assessment_page->post_status );

$contact_page = get_page_by_path( 'contact' );
softkom_live_assert( 'Contact page exists', $contact_page instanceof WP_Post && 'publish' === $contact_page->post_status );

$admin_email = sanitize_email( get_option( 'admin_email' ) );
softkom_live_assert( 'Valid WordPress admin email configured', is_email( $admin_email ) );

$catalogue = function_exists( 'softkom_v3_commercial_catalogue' ) ? softkom_v3_commercial_catalogue() : array();
softkom_live_assert( 'Commercial catalogue is available', is_array( $catalogue ) && ! empty( $catalogue ) );

$has_priced_plan = false;
foreach ( (array) $catalogue as $service ) {
	if ( empty( $service['plans'] ) || ! is_array( $service['plans'] ) ) {
		continue;
	}
	foreach ( $service['plans'] as $plan ) {
		if ( ! empty( $plan['monthly'] ) && (float) $plan['monthly'] > 0 ) {
			$has_priced_plan = true;
			break 2;
		}
	}
}
softkom_live_assert( 'Commercial catalogue contains priced recurring plans', $has_priced_plan );

$required_mu_files = array(
	'softkom-public-acquisition.php',
	'softkom-public-acquisition.js',
	'softkom-strategy-request.php',
	'softkom-sales-notifications.php',
	'softkom-industry-funnel.php',
	'softkom-industry-funnel.js',
	'softkom-assessment-standalone.php',
	'softkom-commercial-persistence.php',
);

foreach ( $required_mu_files as $file ) {
	softkom_live_assert(
		'MU file present: ' . $file,
		file_exists( WPMU_PLUGIN_DIR . '/' . $file )
	);
}

$runtime_dir = WP_CONTENT_DIR . '/softkom-assessment-runtime';
$required_runtime_files = array(
	'page-assessment.php',
	'softkom-assessment.js',
	'softkom-assessment.css',
);

foreach ( $required_runtime_files as $file ) {
	softkom_live_assert(
		'Assessment runtime file present: ' . $file,
		file_exists( $runtime_dir . '/' . $file )
	);
}

echo "\nSite: " . home_url( '/' ) . "\n";
echo "Admin email: " . ( $admin_email ? $admin_email : '(invalid)' ) . "\n";
echo "\n=========================================================\n";
echo sprintf(
	"Live Readiness Results: %d Passed, %d Failed\n",
	$GLOBALS['softkom_live_passed'],
	$GLOBALS['softkom_live_failed']
);
echo "=========================================================\n";

if ( $GLOBALS['softkom_live_failed'] > 0 ) {
	exit( 1 );
}
