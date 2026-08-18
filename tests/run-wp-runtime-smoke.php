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
$GLOBALS['softkom_smoke_passed'] = 0;
$GLOBALS['softkom_smoke_failed'] = 0;

function smoke_assert( $label, $condition ) {
	if ( $condition ) {
		$GLOBALS['softkom_smoke_passed']++;
		echo "[PASS] {$label}\n";
	} else {
		$GLOBALS['softkom_smoke_failed']++;
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

	smoke_assert( "Lead storage handler exists", function_exists( 'softkom_v3_store_assessment_lead' ) );
	smoke_assert( "HOT lead auto-pipeline handler exists", function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) );

	// -------------------------------------------------------------------------
	// 2. Create Isolated Runtime Test Campaign
	// -------------------------------------------------------------------------
	$campaign_key   = '__softkom_runtime_test_campaign_' . time() . '__';
	$campaign_title = '__SOFTKOM_RUNTIME_TEST__ - Smoke Campaign ' . time();
	$campaign_id    = wp_insert_post(
		array(
			'post_title'  => $campaign_title,
			'post_type'   => 'softkom_campaign',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_softkom_campaign_utm_source'   => 'smoke-source',
				'_softkom_campaign_utm_medium'   => 'cpc',
				'_softkom_campaign_utm_campaign' => $campaign_key,
				'_softkom_campaign_budget'       => '1000',
				'_softkom_runtime_test_marker'   => '__SOFTKOM_RUNTIME_TEST__',
			),
		)
	);

	if ( $campaign_id && ! is_wp_error( $campaign_id ) ) {
		$created_post_ids[] = (int) $campaign_id;
		smoke_assert( "Campaign post created successfully (ID: {$campaign_id})", $campaign_id > 0 );

		if ( function_exists( 'softkom_v3_campaign_tracked_url' ) ) {
			$tracked_url = softkom_v3_campaign_tracked_url( $campaign_id );
			smoke_assert( "Campaign tracked URL generated", is_string( $tracked_url ) && strpos( $tracked_url, 'utm_source=smoke-source' ) !== false );
		}
	}

	// -------------------------------------------------------------------------
	// 3. Create & Verify HOT Sales-Eligible Lead with Auto-Pipeline & Attribution
	// -------------------------------------------------------------------------
	$test_email = 'runtime-test-' . time() . '@example.com';
	$test_title = '__SOFTKOM_RUNTIME_TEST__ Corp - Smoke User';

	$mock_result = array(
		'lead' => array(
			'first_name' => 'Smoke',
			'last_name'  => 'User',
			'email'      => $test_email,
			'company'    => '__SOFTKOM_RUNTIME_TEST__ Corp',
		),
		'scores' => array(
			'maturity'        => 30,
			'ai_opportunity'  => 85,
			'commercial_fit'  => 80,
			'purchase_intent' => 85,
			'overall_lead'    => 82,
		),
		'lead_temperature' => 'HOT',
		'maturity_level'   => array(
			'key'   => 'spreadsheet-dependent',
			'title' => 'Spreadsheet Dependent',
		),
		'priority_opportunities' => array(
			array( 'title' => 'AI Automation', 'score' => 85 ),
		),
		'recommendations' => array(
			array( 'id' => 'managed_automation', 'title' => 'Managed Automation' ),
		),
		'lead_routing' => array(
			'sales_eligible' => true,
		),
		'attribution' => array(
			'utm_source'   => 'smoke-source',
			'utm_medium'   => 'cpc',
			'utm_campaign' => $campaign_key,
		),
	);

	$assessment_answers    = array( 'visibility-01' => 1, 'process-01' => 1 );
	$qualification_answers = array( 'company_size' => '51-200', 'urgency' => 'critical' );
	$security              = array( 'risk_score' => 10, 'risk_level' => 'LOW RISK' );

	softkom_v3_store_assessment_lead(
		$mock_result,
		$assessment_answers,
		$qualification_answers,
		$security
	);

	// Find the created lead
	$created_leads = get_posts(
		array(
			'post_type'      => 'softkom_lead',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'     => '_softkom_email',
					'value'   => $test_email,
					'compare' => '=',
				),
			),
		)
	);

	$lead_id = ( $created_leads && ! empty( $created_leads[0] ) ) ? $created_leads[0]->ID : 0;

	if ( $lead_id > 0 ) {
		$created_post_ids[] = (int) $lead_id;
		update_post_meta( $lead_id, '_softkom_runtime_test_marker', '__SOFTKOM_RUNTIME_TEST__' );

		smoke_assert( "HOT lead stored successfully (ID: {$lead_id})", $lead_id > 0 );

		// Verify stored metadata
		$stored_email       = get_post_meta( $lead_id, '_softkom_email', true );
		$stored_temp        = get_post_meta( $lead_id, '_softkom_lead_temperature', true );
		$stored_routing_raw = get_post_meta( $lead_id, '_softkom_lead_routing', true );
		$stored_routing     = is_string( $stored_routing_raw ) ? json_decode( $stored_routing_raw, true ) : $stored_routing_raw;
		$stored_source      = get_post_meta( $lead_id, '_softkom_utm_source', true );
		$stored_medium      = get_post_meta( $lead_id, '_softkom_utm_medium', true );
		$stored_campaign    = get_post_meta( $lead_id, '_softkom_utm_campaign', true );

		smoke_assert( "Lead _softkom_email meta is correct", $stored_email === $test_email );
		smoke_assert( "Lead _softkom_lead_temperature is HOT", $stored_temp === 'HOT' );
		smoke_assert( "Lead _softkom_lead_routing indicates sales_eligible true", is_array( $stored_routing ) && ! empty( $stored_routing['sales_eligible'] ) );
		smoke_assert( "Lead attribution _softkom_utm_source is correct", $stored_source === 'smoke-source' );
		smoke_assert( "Lead attribution _softkom_utm_medium is correct", $stored_medium === 'cpc' );
		smoke_assert( "Lead attribution _softkom_utm_campaign is correct", $stored_campaign === $campaign_key );

		// Execute HOT Lead Auto-Pipeline
		if ( function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) ) {
			softkom_v3_auto_pipeline_hot_lead( $lead_id, $mock_result, $security );
		}

		$stage      = get_post_meta( $lead_id, '_softkom_pipeline_stage', true );
		$offer      = get_post_meta( $lead_id, '_softkom_assigned_offer', true );
		$mrr        = get_post_meta( $lead_id, '_softkom_estimated_mrr', true );
		$auto_flag  = get_post_meta( $lead_id, '_softkom_recurring_auto_applied', true );
		$follow_up  = get_post_meta( $lead_id, '_softkom_follow_up_date', true );
		$history    = get_post_meta( $lead_id, '_softkom_pipeline_history', true );

		smoke_assert( "HOT lead pipeline stage is qualified", $stage === 'qualified' );
		smoke_assert( "HOT lead assigned offer is non-empty", ! empty( $offer ) );
		smoke_assert( "HOT lead estimated MRR is > 0", ! empty( $mrr ) && (float) $mrr > 0 );
		smoke_assert( "HOT lead recurring auto-applied flag is set", ! empty( $auto_flag ) );
		smoke_assert( "HOT lead follow-up date is set", ! empty( $follow_up ) );

		$auto_applied_events = 0;
		if ( is_array( $history ) ) {
			foreach ( $history as $h ) {
				if ( isset( $h['event'] ) && 'recommendation_auto_applied' === $h['event'] ) {
					$auto_applied_events++;
				}
			}
		}
		smoke_assert( "Pipeline history contains exactly one recommendation_auto_applied event", 1 === $auto_applied_events );

		// Test Idempotency
		if ( function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) ) {
			softkom_v3_auto_pipeline_hot_lead( $lead_id, $mock_result, $security );

			$mrr_after     = get_post_meta( $lead_id, '_softkom_estimated_mrr', true );
			$offer_after   = get_post_meta( $lead_id, '_softkom_assigned_offer', true );
			$history_after = get_post_meta( $lead_id, '_softkom_pipeline_history', true );

			$events_after = 0;
			if ( is_array( $history_after ) ) {
				foreach ( $history_after as $h ) {
					if ( isset( $h['event'] ) && 'recommendation_auto_applied' === $h['event'] ) {
						$events_after++;
					}
				}
			}

			smoke_assert( "Auto-pipeline execution is idempotent (MRR unchanged)", $mrr === $mrr_after );
			smoke_assert( "Auto-pipeline execution is idempotent (offer unchanged)", $offer === $offer_after );
			smoke_assert( "Auto-pipeline execution is idempotent (history event count unchanged)", 1 === $events_after );
		}
	} else {
		smoke_assert( "HOT lead stored successfully", false );
	}

	// -------------------------------------------------------------------------
	// 4. Verify Campaign Performance / Reporting Integration
	// -------------------------------------------------------------------------
	if ( $campaign_id > 0 && function_exists( 'softkom_v3_campaign_performance' ) ) {
		$perf = softkom_v3_campaign_performance( $campaign_id );
		smoke_assert( "Campaign performance metrics returned as array", is_array( $perf ) );
		smoke_assert( "Campaign performance reflects leads >= 1", isset( $perf['leads'] ) && (int) $perf['leads'] >= 1 );
		smoke_assert( "Campaign performance reflects warm_hot >= 1", isset( $perf['warm_hot'] ) && (int) $perf['warm_hot'] >= 1 );
		smoke_assert( "Campaign performance reflects pipeline_leads >= 1", isset( $perf['pipeline_leads'] ) && (int) $perf['pipeline_leads'] >= 1 );
		smoke_assert( "Campaign performance reflects estimated_mrr > 0", isset( $perf['estimated_mrr'] ) && (float) $perf['estimated_mrr'] > 0 );
	}

} catch ( Throwable $e ) {
	echo "[EXCEPTION] " . $e->getMessage() . "\nTrace:\n" . $e->getTraceAsString() . "\n";
	$GLOBALS['softkom_smoke_failed']++;
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
echo sprintf( "Runtime Smoke Results: %d Passed, %d Failed\n", $GLOBALS['softkom_smoke_passed'], $GLOBALS['softkom_smoke_failed'] );
echo "=========================================================\n";

if ( $GLOBALS['softkom_smoke_failed'] > 0 ) {
	exit( 1 );
}
exit( 0 );
