<?php
/**
 * Softkom Funnel V2 qualification configuration.
 *
 * Captures commercial intent and additional AI opportunity
 * signals without affecting the core maturity score.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


function softkom_v3_funnel_qualification_config() {

    return array(

        'company_size' => array(
            'label' => 'How many people work in your organisation?',
            'options' => array(
                '1-5'     => 30,
                '6-20'    => 55,
                '21-50'   => 75,
                '51-200'  => 90,
                '201-plus'=> 100,
            ),
        ),

        'decision_role' => array(
            'label' => 'What role do you play in technology or operational decisions?',
            'options' => array(
                'researcher'       => 25,
                'influencer'       => 55,
                'decision-maker'   => 85,
                'owner-executive'  => 100,
            ),
        ),

        'urgency' => array(
            'label' => 'How important is solving these challenges?',
            'options' => array(
                'exploring' => 25,
                'useful'    => 50,
                'important' => 75,
                'critical'  => 100,
            ),
        ),

        'timeframe' => array(
            'label' => 'When would you ideally like to start?',
            'options' => array(
                '12-plus-months' => 20,
                '6-12-months'    => 45,
                '3-6-months'     => 70,
                '1-3-months'     => 90,
                'immediately'    => 100,
            ),
        ),

        'budget_readiness' => array(
            'label' => 'How ready are you to invest in the right solution?',
            'options' => array(
                'research-only'    => 20,
                'budget-unknown'   => 45,
                'budget-planning'  => 70,
                'budget-available' => 100,
            ),
        ),

        'change_readiness' => array(
            'label' => 'How open is your organisation to changing existing processes?',
            'options' => array(
                'resistant' => 20,
                'cautious'   => 45,
                'open'       => 75,
                'ready'      => 100,
            ),
        ),

        'consultation_intent' => array(
            'label' => 'Would you like Softkom to discuss your results with you?',
            'options' => array(
                'roadmap-only' => 20,
                'maybe-later'  => 45,
                'yes'          => 85,
                'book-now'     => 100,
            ),
        ),

        'sales_process' => array(
            'label' => 'How much of your prospecting and sales follow-up is currently manual?',
            'options' => array(
                'automated'        => 0,
                'mostly-automated' => 25,
                'mixed'            => 50,
                'mostly-manual'    => 75,
                'fully-manual'     => 100,
            ),
        ),

        'customer_enquiries' => array(
            'label' => 'How much staff time is spent answering repetitive customer enquiries?',
            'options' => array(
                'very-little' => 10,
                'some'        => 40,
                'significant' => 75,
                'very-high'   => 100,
            ),
        ),

        'knowledge_access' => array(
            'label' => 'How easy is it for employees to find accurate internal information, policies and SOPs?',
            'options' => array(
                'very-easy' => 0,
                'mostly-easy'=> 25,
                'mixed'      => 50,
                'difficult'  => 75,
                'very-hard'  => 100,
            ),
        ),

        'partner_type' => array(
            'label' => 'Do you deliver technology, consulting or digital services to your own clients?',
            'options' => array(
                'no'         => 0,
                'sometimes'  => 40,
                'consultant' => 70,
                'agency'     => 90,
                'technology-partner' => 100,
            ),
        ),
    );
}


/**
 * Return the numeric value for a qualification response.
 */
function softkom_v3_qualification_value(
    $question_id,
    $answer
) {

    $config = softkom_v3_funnel_qualification_config();

    if (
        ! isset( $config[ $question_id ]['options'][ $answer ] )
    ) {
        return 0;
    }

    return softkom_v3_funnel_clamp_score(
        $config[ $question_id ]['options'][ $answer ]
    );
}


/**
 * Convert qualification responses into Funnel V2 signals.
 */
function softkom_v3_build_qualification_signals( $answers ) {

    $signals = array();

    $map = array(

        'sales_process' => array(
            'sales_automation_opportunity',
            'manual_lead_research',
            'manual_follow_up',
            'lead_generation_gap',
        ),

        'customer_enquiries' => array(
            'customer_service_opportunity',
            'repetitive_enquiries',
            'manual_customer_support',
        ),

        'knowledge_access' => array(
            'knowledge_access_opportunity',
            'knowledge_search',
            'documentation_gap',
        ),

        'partner_type' => array(
            'partner_delivery',
            'agency_ai_delivery',
            'white_label_opportunity',
        ),
    );

    foreach ( $map as $question_id => $signal_names ) {

        if ( ! isset( $answers[ $question_id ] ) ) {
            continue;
        }

        $value = softkom_v3_qualification_value(
            $question_id,
            $answers[ $question_id ]
        );

        foreach ( $signal_names as $signal ) {
            $signals[ $signal ] = $value;
        }
    }

    return $signals;
}


/**
 * Build Purchase Intent signals.
 */
function softkom_v3_build_purchase_intent_signals( $answers ) {

    $keys = array(
        'urgency',
        'timeframe',
        'budget_readiness',
        'decision_role',
        'consultation_intent',
    );

    $signals = array();

    foreach ( $keys as $key ) {

        if ( ! isset( $answers[ $key ] ) ) {
            continue;
        }

        $value = softkom_v3_qualification_value(
            $key,
            $answers[ $key ]
        );

        if ( 'decision_role' === $key ) {
            $signals['decision_authority'] = $value;
        } else {
            $signals[ $key ] = $value;
        }
    }

    return $signals;
}


/**
 * Build Commercial Fit signals.
 */
function softkom_v3_build_commercial_fit_signals( $answers ) {

    $company_size = isset( $answers['company_size'] )
        ? softkom_v3_qualification_value(
            'company_size',
            $answers['company_size']
        )
        : 0;

    $change_readiness = isset( $answers['change_readiness'] )
        ? softkom_v3_qualification_value(
            'change_readiness',
            $answers['change_readiness']
        )
        : 0;

    return array(
        'company_fit'      => $company_size,
        'problem_fit'      => $change_readiness,
        'solution_fit'     => $change_readiness,
        'technology_fit'   => $change_readiness,
        'change_readiness' => $change_readiness,
    );
}
