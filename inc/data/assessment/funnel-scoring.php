<?php
/**
 * Softkom Funnel V2 scoring engine.
 *
 * Generates:
 * - Business Systems Maturity Score
 * - AI & Automation Opportunity Score
 * - Commercial Fit Score
 * - Purchase Intent Score
 * - Overall Lead Score
 * - Lead Temperature
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Clamp a score to 0-100.
 */
function softkom_v3_funnel_clamp_score( $score ) {
    return max( 0, min( 100, (int) round( $score ) ) );
}


/**
 * Convert overall lead score into sales temperature.
 */
function softkom_v3_lead_temperature( $score ) {

    $score = softkom_v3_funnel_clamp_score( $score );

    if ( $score >= 80 ) {
        return 'HOT';
    }

    if ( $score >= 60 ) {
        return 'WARM';
    }

    if ( $score >= 40 ) {
        return 'NURTURE';
    }

    return 'EDUCATE';
}


/**
 * Convert a 0-100 maturity score into the four assessment maturity levels.
 */
function softkom_v3_funnel_maturity_level( $score ) {

    $score = softkom_v3_funnel_clamp_score( $score );

    if ( $score >= 75 ) {
        return array(
            'key'         => 'intelligent-operations',
            'title'       => 'Intelligent Operations',
            'description' => 'Your foundations can support controlled automation and AI at scale.',
        );
    }

    if ( $score >= 55 ) {
        return array(
            'key'         => 'connected-operations',
            'title'       => 'Connected Operations',
            'description' => 'Core processes and systems are established, with targeted gaps to improve.',
        );
    }

    if ( $score >= 35 ) {
        return array(
            'key'         => 'fragmented-tools',
            'title'       => 'Fragmented Tools',
            'description' => 'Useful tools exist, but disconnected workflows and ownership gaps create friction.',
        );
    }

    return array(
        'key'         => 'spreadsheet-dependent',
        'title'       => 'Spreadsheet Dependent',
        'description' => 'Key work relies on manual coordination, individual knowledge and fragile handoffs.',
    );
}


/**
 * Calculate weighted overall lead score.
 *
 * Maturity:       25%
 * AI Opportunity: 25%
 * Commercial Fit: 20%
 * Purchase Intent:30%
 */
function softkom_v3_overall_lead_score(
    $maturity_score,
    $ai_opportunity_score,
    $commercial_fit_score,
    $purchase_intent_score
) {

    $score =
        ( softkom_v3_funnel_clamp_score( $maturity_score ) * 0.25 ) +
        ( softkom_v3_funnel_clamp_score( $ai_opportunity_score ) * 0.25 ) +
        ( softkom_v3_funnel_clamp_score( $commercial_fit_score ) * 0.20 ) +
        ( softkom_v3_funnel_clamp_score( $purchase_intent_score ) * 0.30 );

    return softkom_v3_funnel_clamp_score( $score );
}


/**
 * Calculate AI & Automation Opportunity Score.
 *
 * Higher score = greater opportunity for Softkom
 * AI & Automation Solutions.
 */
function softkom_v3_ai_opportunity_score( $signals ) {

    $manual_processes = isset( $signals['manual_processes'] )
        ? (int) $signals['manual_processes']
        : 0;

    $repetitive_admin = isset( $signals['repetitive_admin'] )
        ? (int) $signals['repetitive_admin']
        : 0;

    $disconnected_data = isset( $signals['disconnected_data'] )
        ? (int) $signals['disconnected_data']
        : 0;

    $customer_service = isset( $signals['customer_service_opportunity'] )
        ? (int) $signals['customer_service_opportunity']
        : 0;

    $knowledge_access = isset( $signals['knowledge_access_opportunity'] )
        ? (int) $signals['knowledge_access_opportunity']
        : 0;

    $sales_automation = isset( $signals['sales_automation_opportunity'] )
        ? (int) $signals['sales_automation_opportunity']
        : 0;

    $score =
        ( $manual_processes * 0.25 ) +
        ( $repetitive_admin * 0.20 ) +
        ( $disconnected_data * 0.15 ) +
        ( $customer_service * 0.15 ) +
        ( $knowledge_access * 0.10 ) +
        ( $sales_automation * 0.15 );

    return softkom_v3_funnel_clamp_score( $score );
}


/**
 * Calculate Commercial Fit Score.
 */
function softkom_v3_commercial_fit_score( $signals ) {

    $company_fit = isset( $signals['company_fit'] )
        ? (int) $signals['company_fit']
        : 0;

    $problem_fit = isset( $signals['problem_fit'] )
        ? (int) $signals['problem_fit']
        : 0;

    $solution_fit = isset( $signals['solution_fit'] )
        ? (int) $signals['solution_fit']
        : 0;

    $technology_fit = isset( $signals['technology_fit'] )
        ? (int) $signals['technology_fit']
        : 0;

    $change_readiness = isset( $signals['change_readiness'] )
        ? (int) $signals['change_readiness']
        : 0;

    $score =
        ( $company_fit * 0.20 ) +
        ( $problem_fit * 0.30 ) +
        ( $solution_fit * 0.25 ) +
        ( $technology_fit * 0.10 ) +
        ( $change_readiness * 0.15 );

    return softkom_v3_funnel_clamp_score( $score );
}


/**
 * Calculate Purchase Intent Score.
 */
function softkom_v3_purchase_intent_score( $signals ) {

    $urgency = isset( $signals['urgency'] )
        ? (int) $signals['urgency']
        : 0;

    $timeframe = isset( $signals['timeframe'] )
        ? (int) $signals['timeframe']
        : 0;

    $budget_readiness = isset( $signals['budget_readiness'] )
        ? (int) $signals['budget_readiness']
        : 0;

    $decision_authority = isset( $signals['decision_authority'] )
        ? (int) $signals['decision_authority']
        : 0;

    $consultation_intent = isset( $signals['consultation_intent'] )
        ? (int) $signals['consultation_intent']
        : 0;

    $score =
        ( $urgency * 0.25 ) +
        ( $timeframe * 0.20 ) +
        ( $budget_readiness * 0.20 ) +
        ( $decision_authority * 0.15 ) +
        ( $consultation_intent * 0.20 );

    return softkom_v3_funnel_clamp_score( $score );
}


/**
 * Build complete Funnel V2 scoring result.
 */
function softkom_v3_calculate_funnel_v2_scores(
    $maturity_score,
    $ai_signals,
    $commercial_signals,
    $intent_signals
) {

    $maturity_score = softkom_v3_funnel_clamp_score(
        $maturity_score
    );

    $ai_score = softkom_v3_ai_opportunity_score(
        $ai_signals
    );

    $commercial_score = softkom_v3_commercial_fit_score(
        $commercial_signals
    );

    $intent_score = softkom_v3_purchase_intent_score(
        $intent_signals
    );

    $overall_score = softkom_v3_overall_lead_score(
        $maturity_score,
        $ai_score,
        $commercial_score,
        $intent_score
    );

    return array(
        'maturity_score'        => $maturity_score,
        'ai_opportunity_score'  => $ai_score,
        'commercial_fit_score'  => $commercial_score,
        'purchase_intent_score' => $intent_score,
        'overall_lead_score'    => $overall_score,
        'lead_temperature'      => softkom_v3_lead_temperature(
            $overall_score
        ),
    );
}


/**
 * Compatibility wrapper for the original Funnel V2 API.
 */
function softkom_v3_funnel_scores( $values ) {

    $maturity = isset( $values['maturity'] )
        ? (int) $values['maturity']
        : 0;

    $ai = isset( $values['ai_opportunity'] )
        ? (int) $values['ai_opportunity']
        : 0;

    $fit = isset( $values['commercial_fit'] )
        ? (int) $values['commercial_fit']
        : 0;

    $intent = isset( $values['purchase_intent'] )
        ? (int) $values['purchase_intent']
        : 0;

    $overall = softkom_v3_overall_lead_score(
        $maturity,
        $ai,
        $fit,
        $intent
    );

    return array(
        'maturity_score'        => softkom_v3_funnel_clamp_score( $maturity ),
        'ai_opportunity_score'  => softkom_v3_funnel_clamp_score( $ai ),
        'commercial_fit_score'  => softkom_v3_funnel_clamp_score( $fit ),
        'purchase_intent_score' => softkom_v3_funnel_clamp_score( $intent ),
        'overall_lead_score'    => $overall,
        'lead_temperature'      => softkom_v3_lead_temperature( $overall ),
    );
}
