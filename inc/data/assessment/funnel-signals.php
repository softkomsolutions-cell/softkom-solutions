<?php
/**
 * Softkom Funnel V2 assessment signal mapping.
 *
 * Converts diagnostic assessment answers into commercial
 * opportunity signals used by the scoring and recommendation engines.
 *
 * Assessment answers are expected on the existing 1-5 maturity scale.
 *
 * Lower maturity = higher opportunity signal.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Convert a 1-5 maturity answer into a 0-100 opportunity value.
 *
 * 1 = 100 opportunity
 * 2 = 75
 * 3 = 50
 * 4 = 25
 * 5 = 0
 */
function softkom_v3_answer_to_opportunity( $answer ) {

    $answer = max( 1, min( 5, (int) $answer ) );

    return ( 5 - $answer ) * 25;
}


/**
 * Map diagnostic questions to Funnel V2 opportunity signals.
 */
function softkom_v3_funnel_question_signal_map() {

    return array(

        /*
         * Process & automation.
         */
        'process-01' => array(
            'manual_processes',
            'tribal_knowledge',
            'documentation_gap',
        ),

        'process-02' => array(
            'workflow_delays',
            'manual_handoffs',
        ),

        'process-03' => array(
            'tribal_knowledge',
            'knowledge_access_opportunity',
            'operational_risk',
        ),

        'automation-01' => array(
            'manual_processes',
            'repetitive_admin',
        ),

        'automation-02' => array(
            'manual_processes',
            'documentation_gap',
        ),

        'automation-03' => array(
            'manual_handoffs',
            'workflow_delays',
            'repetitive_admin',
        ),


        /*
         * Systems integration.
         */
        'integration-01' => array(
            'disconnected_data',
            'integration_gap',
            'duplicate_entry',
        ),

        'integration-02' => array(
            'disconnected_data',
            'integration_gap',
        ),

        'integration-03' => array(
            'solution_gap',
            'integration_gap',
        ),


        /*
         * Reporting & visibility.
         */
        'visibility-01' => array(
            'visibility_gap',
            'reporting_gap',
        ),

        'visibility-02' => array(
            'disconnected_data',
            'visibility_gap',
        ),

        'visibility-03' => array(
            'customer_service_opportunity',
            'visibility_gap',
        ),

        'reporting-01' => array(
            'reporting_gap',
            'repetitive_admin',
        ),

        'reporting-02' => array(
            'reporting_gap',
            'disconnected_data',
        ),

        'reporting-03' => array(
            'reporting_gap',
        ),


        /*
         * Data.
         */
        'data-01' => array(
            'duplicate_entry',
            'disconnected_data',
            'repetitive_admin',
        ),

        'data-02' => array(
            'governance_gap',
            'disconnected_data',
        ),

        'data-03' => array(
            'disconnected_data',
            'reporting_gap',
        ),


        /*
         * AI readiness.
         */
        'ai-01' => array(
            'disconnected_data',
            'ai_foundation_gap',
        ),

        'ai-02' => array(
            'ai_governance_gap',
            'governance_gap',
        ),

        'ai-03' => array(
            'ai_governance_gap',
            'compliance_gap',
        ),


        /*
         * Governance & compliance.
         */
        'governance-01' => array(
            'governance_gap',
        ),

        'governance-02' => array(
            'governance_gap',
            'operational_risk',
        ),

        'governance-03' => array(
            'governance_gap',
            'solution_gap',
        ),

        'compliance-01' => array(
            'compliance_gap',
        ),

        'compliance-02' => array(
            'audit_evidence_gap',
            'compliance_gap',
            'repetitive_admin',
        ),

        'compliance-03' => array(
            'compliance_gap',
            'governance_gap',
        ),


        /*
         * Operational risk / knowledge.
         */
        'risk-01' => array(
            'operational_risk',
            'tribal_knowledge',
            'knowledge_access_opportunity',
        ),

        'risk-02' => array(
            'operational_risk',
            'manual_processes',
            'disconnected_data',
        ),

        'risk-03' => array(
            'operational_risk',
            'documentation_gap',
            'customer_service_opportunity',
        ),
    );
}


/**
 * Build opportunity signals from submitted assessment answers.
 *
 * Example:
 *
 * array(
 *     'automation-01'  => 2,
 *     'integration-01' => 1,
 *     'reporting-01'   => 3,
 * )
 */
function softkom_v3_build_funnel_signals( $answers ) {

    $map = softkom_v3_funnel_question_signal_map();

    $signal_totals = array();
    $signal_counts = array();

    foreach ( $answers as $question_id => $answer ) {

        if ( ! isset( $map[ $question_id ] ) ) {
            continue;
        }

        $opportunity = softkom_v3_answer_to_opportunity(
            $answer
        );

        foreach ( $map[ $question_id ] as $signal ) {

            if ( ! isset( $signal_totals[ $signal ] ) ) {
                $signal_totals[ $signal ] = 0;
                $signal_counts[ $signal ] = 0;
            }

            $signal_totals[ $signal ] += $opportunity;
            $signal_counts[ $signal ]++;
        }
    }

    $signals = array();

    foreach ( $signal_totals as $signal => $total ) {

        $signals[ $signal ] =
            softkom_v3_funnel_clamp_score(
                $total / $signal_counts[ $signal ]
            );
    }

    return $signals;
}


/**
 * Build recommendations directly from assessment answers.
 */
function softkom_v3_funnel_recommendations_from_answers(
    $answers,
    $limit = 3
) {

    $signals = softkom_v3_build_funnel_signals(
        $answers
    );

    return softkom_v3_funnel_top_recommendations(
        $signals,
        $limit
    );
}


/**
 * Return the strongest plain-language opportunity signals.
 */
function softkom_v3_funnel_top_opportunities(
    $signals,
    $limit = 3
) {

    $labels = array(
        'manual_processes'             => 'Manual process reduction',
        'repetitive_admin'             => 'Repetitive administration automation',
        'workflow_delays'              => 'Workflow and approval acceleration',
        'manual_handoffs'              => 'Automated team handoffs',
        'disconnected_data'            => 'Connected data and systems',
        'integration_gap'              => 'Systems integration',
        'duplicate_entry'              => 'Single-entry data flows',
        'visibility_gap'               => 'Real-time business visibility',
        'reporting_gap'                => 'Reliable management reporting',
        'customer_service_opportunity' => 'Customer service automation',
        'knowledge_access_opportunity' => 'Faster access to company knowledge',
        'sales_automation_opportunity' => 'AI-assisted sales automation',
        'governance_gap'               => 'Operational governance',
        'compliance_gap'               => 'Compliance workflow improvement',
        'audit_evidence_gap'            => 'Automated audit evidence',
        'operational_risk'             => 'Operational resilience',
        'tribal_knowledge'              => 'Documented institutional knowledge',
        'documentation_gap'             => 'Process and SOP documentation',
        'white_label_opportunity'       => 'White-label AI delivery',
    );

    $opportunities = array();

    foreach ( $signals as $signal => $score ) {
        $score = softkom_v3_funnel_clamp_score( $score );

        if ( $score <= 0 ) {
            continue;
        }

        $opportunities[] = array(
            'id'    => sanitize_key( $signal ),
            'title' => isset( $labels[ $signal ] )
                ? $labels[ $signal ]
                : ucwords( str_replace( '_', ' ', $signal ) ),
            'score' => $score,
        );
    }

    usort(
        $opportunities,
        function ( $a, $b ) {
            if ( $a['score'] === $b['score'] ) {
                return strcmp( $a['title'], $b['title'] );
            }

            return $b['score'] <=> $a['score'];
        }
    );

    return array_slice(
        $opportunities,
        0,
        max( 1, (int) $limit )
    );
}
