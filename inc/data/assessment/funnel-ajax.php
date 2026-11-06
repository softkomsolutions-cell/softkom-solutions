<?php
/**
 * Softkom Funnel V2 AJAX submission handler.
 *
 * Receives assessment answers + lead information, calculates funnel scores
 * and returns personalized priorities and recommendations.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Process Funnel V2 assessment submission.
 */
function softkom_v3_process_assessment_submission() {

    check_ajax_referer(
        'softkom_assessment_submit',
        'nonce'
    );

    $first_name = isset( $_POST['first_name'] ) && is_scalar( $_POST['first_name'] )
        ? sanitize_text_field( wp_unslash( (string) $_POST['first_name'] ) )
        : '';

    $last_name = isset( $_POST['last_name'] ) && is_scalar( $_POST['last_name'] )
        ? sanitize_text_field( wp_unslash( (string) $_POST['last_name'] ) )
        : '';

    $email = isset( $_POST['email'] ) && is_scalar( $_POST['email'] )
        ? sanitize_email( wp_unslash( (string) $_POST['email'] ) )
        : '';

    $company = isset( $_POST['company'] ) && is_scalar( $_POST['company'] )
        ? sanitize_text_field( wp_unslash( (string) $_POST['company'] ) )
        : '';

    if (
        '' === $first_name ||
        '' === $last_name ||
        '' === $company ||
        ! is_email( $email )
    ) {
        wp_send_json_error(
            array(
                'message' => 'Please complete all required contact fields.',
            ),
            400
        );
    }

    $honeypot = isset( $_POST['website'] ) && is_scalar( $_POST['website'] )
        ? sanitize_text_field( wp_unslash( (string) $_POST['website'] ) )
        : '';

    $started_at = isset( $_POST['started_at'] )
        ? absint( $_POST['started_at'] )
        : 0;

    $completed_at = isset( $_POST['completed_at'] )
        ? absint( $_POST['completed_at'] )
        : time();


    /*
     * Traffic Attribution V1.
     *
     * Acquisition data is supplied by the frontend and sanitized
     * before entering the internal lead-processing workflow.
     */
    $attribution = array(

        'utm_source' => isset( $_POST['utm_source'] )
            && is_scalar( $_POST['utm_source'] )
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['utm_source']
                    )
                )
                : '',

        'utm_medium' => isset( $_POST['utm_medium'] )
            && is_scalar( $_POST['utm_medium'] )
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['utm_medium']
                    )
                )
                : '',

        'utm_campaign' => isset( $_POST['utm_campaign'] )
            && is_scalar( $_POST['utm_campaign'] )
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['utm_campaign']
                    )
                )
                : '',

        'utm_content' => isset( $_POST['utm_content'] )
            && is_scalar( $_POST['utm_content'] )
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['utm_content']
                    )
                )
                : '',

        'utm_term' => isset( $_POST['utm_term'] )
            && is_scalar( $_POST['utm_term'] )
                ? sanitize_text_field(
                    wp_unslash(
                        (string) $_POST['utm_term']
                    )
                )
                : '',

        'landing_page' => isset( $_POST['landing_page'] )
            && is_scalar( $_POST['landing_page'] )
                ? esc_url_raw(
                    wp_unslash(
                        (string) $_POST['landing_page']
                    )
                )
                : '',

        'referrer' => isset( $_POST['referrer'] )
            && is_scalar( $_POST['referrer'] )
                ? esc_url_raw(
                    wp_unslash(
                        (string) $_POST['referrer']
                    )
                )
                : '',
    );


    $attribution['source'] =
        '' !== $attribution['utm_source']
            ? $attribution['utm_source']
            : 'direct';

    $attribution['medium'] =
        '' !== $attribution['utm_medium']
            ? $attribution['utm_medium']
            : 'unknown';


    $security = softkom_v3_security_evaluate_submission(
        array(
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'email'        => $email,
            'company'      => $company,
            'honeypot'     => $honeypot,
            'started_at'   => $started_at,
            'completed_at' => $completed_at,
        )
    );

    if ( 'BLOCK' === $security['risk_level'] ) {
        wp_send_json_error(
            array(
                'message' => 'We could not process this submission.',
            ),
            429
        );
    }

    /*
     * Assessment answers are sent as a JSON object by the frontend.
     */
    $raw_answers = isset( $_POST['answers'] )
        ? wp_unslash( $_POST['answers'] )
        : '';

    $answers = is_string( $raw_answers )
        ? json_decode( $raw_answers, true )
        : null;

    if ( ! is_array( $answers ) ) {
        wp_send_json_error(
            array(
                'message' => 'Assessment answers were not received.',
            ),
            400
        );
    }

    $required_question_ids = softkom_v3_funnel_public_question_ids();
    $allowed_question_ids  = array_fill_keys( $required_question_ids, true );
    $clean_answers         = array();

    foreach ( $answers as $question_id => $answer ) {
        $question_id = sanitize_key( $question_id );

        if ( ! isset( $allowed_question_ids[ $question_id ] ) ) {
            continue;
        }

        $answer = absint( $answer );

        if ( $answer >= 1 && $answer <= 5 ) {
            $clean_answers[ $question_id ] = $answer;
        }
    }

    $missing_question_ids = array_diff(
        $required_question_ids,
        array_keys( $clean_answers )
    );

    if ( ! empty( $missing_question_ids ) ) {
        wp_send_json_error(
            array(
                'message' => 'Please complete every assessment question before requesting results.',
            ),
            400
        );
    }

    /*
     * Qualification fields arrive as qualification[field_name] form values.
     */
    $raw_qualification = isset( $_POST['qualification'] )
        && is_array( $_POST['qualification'] )
        ? wp_unslash( $_POST['qualification'] )
        : array();

    $qualification_config = softkom_v3_funnel_qualification_config();
    $clean_qualification  = array();

    foreach ( $qualification_config as $question_id => $config ) {
        $value = isset( $raw_qualification[ $question_id ] )
            && is_scalar( $raw_qualification[ $question_id ] )
            ? sanitize_key( (string) $raw_qualification[ $question_id ] )
            : '';

        if ( '' === $value || ! array_key_exists( $value, $config['options'] ) ) {
            wp_send_json_error(
                array(
                    'message' => 'Please complete every qualification question so we can tailor your results.',
                ),
                400
            );
        }

        $clean_qualification[ $question_id ] = $value;
    }

    /*
     * The maturity score is the submitted 1-5 diagnostic average expressed
     * as a percentage. Qualification answers never affect maturity.
     */
    $maturity_score = softkom_v3_funnel_clamp_score(
        (
            array_sum( $clean_answers ) /
            ( count( $clean_answers ) * 5 )
        ) * 100
    );

    $signals = softkom_v3_build_funnel_signals( $clean_answers );
    $qualification_signals = softkom_v3_build_qualification_signals(
        $clean_qualification
    );

    /*
     * Preserve the strongest evidence when diagnostic and qualification
     * questions contribute to the same opportunity signal.
     */
    foreach ( $qualification_signals as $signal => $value ) {
        $signals[ $signal ] = isset( $signals[ $signal ] )
            ? max( $signals[ $signal ], $value )
            : $value;
    }

    $commercial_signals = softkom_v3_build_commercial_fit_signals(
        $clean_qualification
    );
    $intent_signals = softkom_v3_build_purchase_intent_signals(
        $clean_qualification
    );

    $scores = softkom_v3_calculate_funnel_v2_scores(
        $maturity_score,
        $signals,
        $commercial_signals,
        $intent_signals
    );

    $maturity_level = softkom_v3_funnel_maturity_level(
        $scores['maturity_score']
    );
    $priorities = softkom_v3_funnel_top_opportunities( $signals, 3 );
    $recommendations = softkom_v3_funnel_top_recommendations( $signals, 3 );

    $priority_titles = wp_list_pluck( $priorities, 'title' );
    $priority_text   = ! empty( $priority_titles )
        ? implode( ', ', $priority_titles )
        : 'maintaining your connected systems foundations';

    $personalized_summary = sprintf(
        '%1$s, %2$s is currently at the %3$s maturity level. Your strongest priority areas are %4$s.',
        $first_name,
        $company,
        $maturity_level['title'],
        $priority_text
    );

    $result = array(
        'lead' => array(
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'email'      => $email,
            'company'    => $company,
        ),
        'scores' => array(
            'maturity'        => $scores['maturity_score'],
            'ai_opportunity'  => $scores['ai_opportunity_score'],
            'commercial_fit'  => $scores['commercial_fit_score'],
            'purchase_intent' => $scores['purchase_intent_score'],
            'overall_lead'    => $scores['overall_lead_score'],
        ),
        'maturity_level'       => $maturity_level,
        'personalized_summary' => $personalized_summary,
        'lead_temperature'     => $scores['lead_temperature'],
        'priority_opportunities' => $priorities,
        'recommendations'      => $recommendations,
        'signals'              => $signals,
        'next_step' => array(
            'title' => 'Turn these opportunities into a practical roadmap',
            'body'  => 'Book a Solution Mapping Session with Softkom Solutions to prioritize the right first move.',
            'label' => 'Book a Strategy Call',
            'url'   => home_url( '/contact/' ),
        ),
    );

    /*
     * Internal security and lead-routing data.
     *
     * This information is intentionally NOT returned to the visitor.
     */
    $risk_level = isset( $security['risk_level'] )
        ? $security['risk_level']
        : 'REVIEW';

    $routing_status = 'accept';
    $sales_eligible = true;
    $requires_review = false;

    if ( 'REVIEW' === $risk_level ) {

        $routing_status = 'review';
        $sales_eligible = false;
        $requires_review = true;

    } elseif ( 'HIGH RISK' === $risk_level ) {

        $routing_status = 'quarantine';
        $sales_eligible = false;
        $requires_review = true;
    }

    $internal_result = $result;

    /*
     * Internal traffic/acquisition intelligence.
     */
    $internal_result['attribution'] = $attribution;

    $internal_result['security'] = array(
        'risk_score'   => isset( $security['risk_score'] )
            ? (int) $security['risk_score']
            : 0,

        'risk_level'   => $risk_level,

        'risk_reasons' => isset( $security['risk_reasons'] )
            && is_array( $security['risk_reasons'] )
                ? $security['risk_reasons']
                : array(),

        'rate_count'   => isset( $security['rate_count'] )
            ? (int) $security['rate_count']
            : 0,
    );

    $internal_result['lead_routing'] = array(
        'status'          => $routing_status,
        'sales_eligible'  => $sales_eligible,
        'requires_review' => $requires_review,
    );

    /**
     * Integration hook for a future CRM or email workflow.
     *
     * Extra arguments expose the sanitized source answers without returning
     * them to the browser. Existing one-argument callbacks remain compatible.
     */
    do_action(
        'softkom_v3_assessment_lead_created',
        $internal_result,
        $clean_answers,
        $clean_qualification,
        $security
    );

    wp_send_json_success( $result );
}


add_action(
    'wp_ajax_softkom_assessment_submit',
    'softkom_v3_process_assessment_submission'
);

add_action(
    'wp_ajax_nopriv_softkom_assessment_submit',
    'softkom_v3_process_assessment_submission'
);



