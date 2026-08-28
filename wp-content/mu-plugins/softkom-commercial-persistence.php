<?php
/**
 * Softkom commercial recommendation persistence.
 *
 * Persists the concrete catalogue selection used by the recurring revenue
 * engine so proposals, forecasting, reporting and white-label resale can use
 * exact service/plan/pricing fields rather than re-deriving them later.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persist normalized commercial recommendation metadata for a Softkom lead.
 *
 * @param int        $lead_id        Lead post ID.
 * @param array|null $recommendation Optional pre-calculated recommendation.
 * @return array Persisted recommendation, or an empty array on failure.
 */
function softkom_v3_persist_commercial_recommendation( $lead_id, $recommendation = null ) {
	$lead_id = absint( $lead_id );

	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return array();
	}

	if ( ! is_array( $recommendation ) ) {
		if ( ! function_exists( 'softkom_v3_calculate_recurring_recommendation' ) ) {
			return array();
		}

		$recommendation = softkom_v3_calculate_recurring_recommendation( $lead_id );
	}

	if ( empty( $recommendation ) ) {
		return array();
	}

	$service_key = isset( $recommendation['service_key'] )
		? sanitize_key( $recommendation['service_key'] )
		: '';

	$service_name = isset( $recommendation['managed_service'] )
		? sanitize_text_field( $recommendation['managed_service'] )
		: '';

	$plan_key = isset( $recommendation['commercial_plan_key'] )
		? sanitize_key( $recommendation['commercial_plan_key'] )
		: '';

	$plan_name = isset( $recommendation['commercial_plan_name'] )
		? sanitize_text_field( $recommendation['commercial_plan_name'] )
		: '';

	$implementation_name = isset( $recommendation['implementation_offer'] )
		? sanitize_text_field( $recommendation['implementation_offer'] )
		: '';

	$implementation_price = isset( $recommendation['implementation_price_from'] )
		? max( 0, (float) $recommendation['implementation_price_from'] )
		: 0;

	$monthly_price = isset( $recommendation['commercial_monthly'] )
		? max( 0, (float) $recommendation['commercial_monthly'] )
		: 0;

	$category = isset( $recommendation['commercial_category'] )
		? sanitize_text_field( $recommendation['commercial_category'] )
		: '';

	$meta = array(
		'_softkom_service_key'                   => $service_key,
		'_softkom_service_name'                  => $service_name,
		'_softkom_commercial_plan_key'           => $plan_key,
		'_softkom_commercial_plan_name'          => $plan_name,
		'_softkom_implementation_offer'          => $implementation_name,
		'_softkom_implementation_price_from'     => $implementation_price,
		'_softkom_commercial_monthly'            => $monthly_price,
		'_softkom_commercial_category'           => $category,
		'_softkom_commercial_persisted_at_gmt'   => current_time( 'mysql', true ),
	);

	foreach ( $meta as $key => $value ) {
		update_post_meta( $lead_id, $key, $value );
	}

	/*
	 * When the central commercial catalogue provides an exact monthly price,
	 * use that exact value for forecasting instead of the legacy MRR midpoint.
	 */
	if ( $monthly_price > 0 ) {
		update_post_meta( $lead_id, '_softkom_estimated_mrr', $monthly_price );
	}

	update_post_meta(
		$lead_id,
		'_softkom_recurring_recommendation',
		wp_json_encode( $recommendation )
	);

	return $recommendation;
}

/**
 * Persist catalogue selection after a newly-created HOT lead has been routed.
 * Priority 30 runs after the existing auto-pipeline handler (priority 20).
 */
function softkom_v3_persist_commercial_on_lead_stored( $lead_id, $result, $security ) {
	unset( $result, $security );
	softkom_v3_persist_commercial_recommendation( $lead_id );
}
add_action( 'softkom_v3_assessment_lead_stored', 'softkom_v3_persist_commercial_on_lead_stored', 30, 3 );

/**
 * Persist the same catalogue fields before the existing manual Apply
 * Recommendation handler runs. The existing handler exits after redirecting,
 * so this hook intentionally runs first.
 */
function softkom_v3_persist_commercial_before_manual_apply() {
	$lead_id = isset( $_GET['post_id'] ) ? absint( $_GET['post_id'] ) : 0;

	if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $lead_id ) ) {
		return;
	}

	if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'softkom_apply_recurring_' . $lead_id ) ) {
		return;
	}

	softkom_v3_persist_commercial_recommendation( $lead_id );
}
add_action( 'admin_post_softkom_apply_recurring_recommendation', 'softkom_v3_persist_commercial_before_manual_apply', 5 );
