<?php
/**
 * Softkom V3 WP-CLI Runtime Smoke Test Suite
 *
 * Runs via WP-CLI:
 * wp eval-file tests/run-wp-runtime-smoke.php
 *
 * Requirements:
 * - Uses isolated temporary __SOFTKOM_RUNTIME_TEST__ records.
 * - Always cleans up created test records in a finally block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo "[ERROR] ABSPATH not defined. This script must be executed via WP-CLI:\n";
	echo "wp eval-file tests/run-wp-runtime-smoke.php\n";
	exit( 1 );
}

$created_post_ids = array();
$passed = 0;
$failed = 0;

function smoke_assert( $label, $condition ) {
	global $passed, $failed;
	if ( $condition ) {
		$passed++;
		echo "[PASS] {$label}\n";
	} else {
		$failed++;
		echo "[FAIL] {$label}\n";
	}
}

echo "=========================================================\n";
echo "Softkom V3 WP-CLI Runtime Smoke Test Suite\n";
echo "=========================================================\n\n";

try {
	// -------------------------------------------------------------------------
	// 1. WP Runtime & Theme Bootstrap Check
	// -------------------------------------------------------------------------
	smoke_assert( "WordPress bootstrap is active", function_exists( 'add_action' ) && function_exists( 'get_option' ) );
	smoke_assert( "Softkom Assessment Data Loader is defined", function_exists( 'softkom_v3_load_data' ) );

	if ( function_exists( 'softkom_v3_load_data' ) ) {
		softkom_v3_load_data();
	}

	smoke_assert( "Lead CPT function exists", function_exists( 'softkom_v3_register_lead_post_type' ) );
	smoke_assert( "Store assessment lead function exists", function_exists( 'softkom_v3_store_assessment_lead' ) );

	// -------------------------------------------------------------------------
	// 2. Create Isolated Runtime Test Lead
	// -------------------------------------------------------------------------
	$test_email = 'runtime_test_' . time() . '@__SOFTKOM_RUNTIME_TEST__.local';
	$test_title = '__SOFTKOM_RUNTIME_TEST__ - Smoke Lead ' . time();

	$payload = array(
		'first_name'   => '__SOFTKOM_RUNTIME_TEST__',
		'last_name'    => 'SmokeUser',
		'email'        => $test_email,
		'company'      => '__SOFTKOM_RUNTIME_TEST__ Corp',
		'phone'        => '+27-70-000-0000',
		'answers'      => array(
			'visibility-01' => 1,
			'reporting-03'  => 1,
			'process-01'    => 1,
			'automation-01' => 1,
		),
		'qualification'=> array(
			'company_size'  => '51-200',
			'decision_role' => 'owner-executive',
			'urgency'       => 'critical',
			'sales_process' => 'mostly-manual',
		),
		'started_at'   => time() - 60,
		'completed_at' => time(),
	);

	if ( function_exists( 'softkom_v3_store_assessment_lead' ) ) {
		$lead_id = softkom_v3_store_assessment_lead( $payload );
		if ( $lead_id && ! is_wp_error( $lead_id ) ) {
			$created_post_ids[] = (int) $lead_id;
			update_post_meta( $lead_id, '_softkom_runtime_test_marker', '__SOFTKOM_RUNTIME_TEST__' );
			smoke_assert( "Assessment lead post created successfully (ID: {$lead_id})", $lead_id > 0 );

			$retrieved_email = get_post_meta( $lead_id, '_softkom_lead_email', true );
			smoke_assert( "Lead metadata correctly assigned", $retrieved_email === $test_email );
		} else {
			smoke_assert( "Assessment lead post created successfully", false );
		}
	} else {
		// Fallback manual post creation for testing harness if helper missing
		$lead_id = wp_insert_post(
			array(
				'post_title'  => $test_title,
				'post_type'   => 'softkom_lead',
				'post_status' => 'publish',
				'meta_input'  => array(
					'_softkom_lead_email'        => $test_email,
					'_softkom_runtime_test_marker' => '__SOFTKOM_RUNTIME_TEST__',
				),
			)
		);
		if ( $lead_id && ! is_wp_error( $lead_id ) ) {
			$created_post_ids[] = (int) $lead_id;
			smoke_assert( "Fallback lead post created successfully (ID: {$lead_id})", $lead_id > 0 );
		}
	}

	// -------------------------------------------------------------------------
	// 3. Create Isolated Runtime Test Campaign
	// -------------------------------------------------------------------------
	$campaign_title = '__SOFTKOM_RUNTIME_TEST__ - Smoke Campaign ' . time();
	$campaign_id    = wp_insert_post(
		array(
			'post_title'  => $campaign_title,
			'post_type'   => 'softkom_campaign',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_softkom_campaign_utm_source'   => 'smoke-source',
				'_softkom_campaign_utm_campaign' => '__softkom_runtime_test_campaign__',
				'_softkom_runtime_test_marker'   => '__SOFTKOM_RUNTIME_TEST__',
			),
		)
	);

	if ( $campaign_id && ! is_wp_error( $campaign_id ) ) {
		$created_post_ids[] = (int) $campaign_id;
		smoke_assert( "Campaign post created successfully (ID: {$campaign_id})", $campaign_id > 0 );

		if ( function_exists( 'softkom_v3_campaign_tracked_url' ) ) {
			$tracked_url = softkom_v3_campaign_tracked_url( $campaign_id );
			smoke_assert( "Campaign tracked URL generated", is_string( $tracked_url ) && strlen( $tracked_url ) > 0 );
		}
	}

} catch ( Exception $e ) {
	echo "[EXCEPTION] " . $e->getMessage() . "\n";
	$failed++;
} finally {
	// -------------------------------------------------------------------------
	// Cleanup Block - Guarantee All Temporary Records Are Deleted
	// -------------------------------------------------------------------------
	echo "\n---------------------------------------------------------\n";
	echo "Cleanup: Deleting isolated __SOFTKOM_RUNTIME_TEST__ records...\n";
	echo "---------------------------------------------------------\n";

	$cleaned_count = 0;

	// 1. Clean up explicitly tracked IDs created in this run
	foreach ( array_unique( $created_post_ids ) as $id ) {
		if ( $id > 0 ) {
			$deleted = wp_delete_post( $id, true );
			if ( $deleted ) {
				$cleaned_count++;
				echo "Deleted explicit test post ID: {$id}\n";
			}
		}
	}

	// 2. Safety sweep for any orphaned __SOFTKOM_RUNTIME_TEST__ posts
	if ( function_exists( 'get_posts' ) ) {
		$orphaned = get_posts(
			array(
				'post_type'      => array( 'softkom_lead', 'softkom_campaign', 'post' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'meta_query'     => array(
					array(
						'key'     => '_softkom_runtime_test_marker',
						'value'   => '__SOFTKOM_RUNTIME_TEST__',
						'compare' => '=',
					),
				),
			)
		);

		foreach ( $orphaned as $orphaned_post ) {
			$oid = is_object( $orphaned_post ) ? $orphaned_post->ID : (int) $orphaned_post;
			if ( $oid > 0 && ! in_array( $oid, $created_post_ids, true ) ) {
				wp_delete_post( $oid, true );
				$cleaned_count++;
				echo "Cleaned up orphaned test post ID: {$oid}\n";
			}
		}
	}

	echo "Cleanup completed ({$cleaned_count} test records removed).\n";
}

// -----------------------------------------------------------------------------
// Summary
// -----------------------------------------------------------------------------
echo "\n=========================================================\n";
echo sprintf( "Runtime Smoke Results: %d Passed, %d Failed\n", $passed, $failed );
echo "=========================================================\n";

if ( $failed > 0 ) {
	exit( 1 );
}
exit( 0 );
