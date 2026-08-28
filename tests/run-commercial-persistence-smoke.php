<?php
/**
 * Softkom commercial persistence smoke test.
 *
 * Run with:
 * wp eval-file tests/run-commercial-persistence-smoke.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo "[ERROR] Run this test through WP-CLI.\n";
	exit( 1 );
}

$created_ids = array();
$passed = 0;
$failed = 0;

function softkom_cp_assert( $label, $condition ) {
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
echo "Softkom Commercial Persistence Smoke Test\n";
echo "=========================================================\n\n";

try {
	if ( function_exists( 'softkom_v3_load_data' ) ) {
		softkom_v3_load_data();
	}

	softkom_cp_assert( 'Commercial persistence handler exists', function_exists( 'softkom_v3_persist_commercial_recommendation' ) );
	softkom_cp_assert( 'Commercial recommendation calculator exists', function_exists( 'softkom_v3_calculate_recurring_recommendation' ) );

	$lead_id = wp_insert_post(
		array(
			'post_type'   => 'softkom_lead',
			'post_status' => 'private',
			'post_title'  => '__SOFTKOM_COMMERCIAL_TEST__',
		)
	);

	if ( $lead_id && ! is_wp_error( $lead_id ) ) {
		$created_ids[] = (int) $lead_id;
	}

	softkom_cp_assert( 'Temporary commercial lead created', $lead_id > 0 && ! is_wp_error( $lead_id ) );

	update_post_meta( $lead_id, '_softkom_email', 'commercial-test@example.com' );
	update_post_meta( $lead_id, '_softkom_score_ai_opportunity', 85 );
	update_post_meta( $lead_id, '_softkom_score_commercial_fit', 80 );
	update_post_meta( $lead_id, '_softkom_score_purchase_intent', 85 );
	update_post_meta( $lead_id, '_softkom_score_overall_lead', 82 );
	update_post_meta( $lead_id, '_softkom_lead_temperature', 'HOT' );
	update_post_meta( $lead_id, '_softkom_security_risk_level', 'LOW RISK' );
	update_post_meta( $lead_id, '_softkom_recommendations', wp_json_encode( array( array( 'id' => 'managed_automation', 'title' => 'Managed Automation' ) ) ) );
	update_post_meta( $lead_id, '_softkom_priority_opportunities', wp_json_encode( array( array( 'title' => 'AI Automation', 'score' => 85 ) ) ) );

	$recommendation = softkom_v3_calculate_recurring_recommendation( $lead_id );
	softkom_cp_assert( 'Recommendation contains service key', ! empty( $recommendation['service_key'] ) );
	softkom_cp_assert( 'Recommendation contains commercial plan key', ! empty( $recommendation['commercial_plan_key'] ) );
	softkom_cp_assert( 'Recommendation contains commercial plan name', ! empty( $recommendation['commercial_plan_name'] ) );
	softkom_cp_assert( 'Recommendation contains exact monthly price', ! empty( $recommendation['commercial_monthly'] ) && (float) $recommendation['commercial_monthly'] > 0 );
	softkom_cp_assert( 'Recommendation contains implementation price', isset( $recommendation['implementation_price_from'] ) );

	softkom_v3_persist_commercial_recommendation( $lead_id, $recommendation );

	$service_key = get_post_meta( $lead_id, '_softkom_service_key', true );
	$service_name = get_post_meta( $lead_id, '_softkom_service_name', true );
	$plan_key = get_post_meta( $lead_id, '_softkom_commercial_plan_key', true );
	$plan_name = get_post_meta( $lead_id, '_softkom_commercial_plan_name', true );
	$implementation_offer = get_post_meta( $lead_id, '_softkom_implementation_offer', true );
	$implementation_price = get_post_meta( $lead_id, '_softkom_implementation_price_from', true );
	$monthly = get_post_meta( $lead_id, '_softkom_commercial_monthly', true );
	$category = get_post_meta( $lead_id, '_softkom_commercial_category', true );
	$estimated_mrr = get_post_meta( $lead_id, '_softkom_estimated_mrr', true );
	$persisted_at = get_post_meta( $lead_id, '_softkom_commercial_persisted_at_gmt', true );

	softkom_cp_assert( 'Service key persisted exactly', $service_key === sanitize_key( $recommendation['service_key'] ) );
	softkom_cp_assert( 'Service name persisted', ! empty( $service_name ) );
	softkom_cp_assert( 'Commercial plan key persisted exactly', $plan_key === sanitize_key( $recommendation['commercial_plan_key'] ) );
	softkom_cp_assert( 'Commercial plan name persisted exactly', $plan_name === $recommendation['commercial_plan_name'] );
	softkom_cp_assert( 'Implementation offer persisted', ! empty( $implementation_offer ) );
	softkom_cp_assert( 'Implementation price persisted', (float) $implementation_price === (float) $recommendation['implementation_price_from'] );
	softkom_cp_assert( 'Exact monthly price persisted', (float) $monthly === (float) $recommendation['commercial_monthly'] );
	softkom_cp_assert( 'Commercial category persisted', $category === $recommendation['commercial_category'] );
	softkom_cp_assert( 'Estimated MRR uses exact catalogue monthly price', (float) $estimated_mrr === (float) $recommendation['commercial_monthly'] );
	softkom_cp_assert( 'Commercial persistence timestamp set', ! empty( $persisted_at ) );

} catch ( Throwable $e ) {
	echo '[EXCEPTION] ' . $e->getMessage() . "\n";
	$failed++;
} finally {
	foreach ( $created_ids as $id ) {
		wp_delete_post( $id, true );
	}
}

echo "\n=========================================================\n";
echo sprintf( "Commercial Persistence Results: %d Passed, %d Failed\n", $passed, $failed );
echo "=========================================================\n";

if ( $failed > 0 ) {
	exit( 1 );
}
