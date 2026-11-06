<?php
/**
 * Softkom Funnel V2 persistent lead storage.
 *
 * Stores completed assessment submissions as private
 * WordPress records for internal sales use.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Register internal Softkom Leads.
 */
function softkom_v3_register_lead_post_type() {

    register_post_type(
        'softkom_lead',
        array(
            'labels' => array(
                'name'          => 'Softkom Leads',
                'singular_name' => 'Softkom Lead',
                'menu_name'     => 'Softkom Leads',
                'add_new_item'  => 'Add Softkom Lead',
                'edit_item'     => 'View Softkom Lead',
            ),

            'public'              => false,
            'publicly_queryable'  => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => false,
            'menu_position'       => 26,
            'menu_icon'           => 'dashicons-chart-line',

            'supports' => array(
                'title',
            ),

            'capability_type' => 'post',
            'map_meta_cap'    => true,
        )
    );
}

if ( did_action( 'init' ) ) {
    softkom_v3_register_lead_post_type();
} else {
    add_action(
        'init',
        'softkom_v3_register_lead_post_type'
    );
}


/**
 * Save a Funnel V2 assessment as an internal lead.
 *
 * Hook receives:
 * 1. Internal result
 * 2. Assessment answers
 * 3. Qualification answers
 * 4. Security evaluation
 */
function softkom_v3_store_assessment_lead(
    $result,
    $assessment_answers,
    $qualification_answers,
    $security
) {

    if (
        ! is_array( $result ) ||
        empty( $result['lead'] ) ||
        ! is_array( $result['lead'] )
    ) {
        return;
    }

    $lead = $result['lead'];

    $first_name = isset( $lead['first_name'] )
        ? sanitize_text_field( $lead['first_name'] )
        : '';

    $last_name = isset( $lead['last_name'] )
        ? sanitize_text_field( $lead['last_name'] )
        : '';

    $email = isset( $lead['email'] )
        ? sanitize_email( $lead['email'] )
        : '';

    $company = isset( $lead['company'] )
        ? sanitize_text_field( $lead['company'] )
        : '';

    if ( ! is_email( $email ) ) {
        return;
    }


    /*
     * Do not create sales lead records for blocked submissions.
     */
    $risk_level = isset( $security['risk_level'] )
        ? sanitize_text_field( $security['risk_level'] )
        : 'REVIEW';

    if ( 'BLOCK' === $risk_level ) {
        return;
    }


    /*
     * Each assessment completion is stored as its own lead event.
     */
    $title_parts = array_filter(
        array(
            $company,
            trim( $first_name . ' ' . $last_name ),
        )
    );

    $post_title = implode(
        ' - ',
        $title_parts
    );

    if ( '' === $post_title ) {
        $post_title = $email;
    }

    $lead_id = wp_insert_post(
        array(
            'post_type'   => 'softkom_lead',
            'post_status' => 'private',
            'post_title'  => $post_title,
        ),
        true
    );

    if ( is_wp_error( $lead_id ) ) {

        error_log(
            'Softkom Funnel lead storage failed: ' .
            $lead_id->get_error_message()
        );

        return;
    }


    /*
     * Contact information.
     */
    update_post_meta(
        $lead_id,
        '_softkom_first_name',
        $first_name
    );

    update_post_meta(
        $lead_id,
        '_softkom_last_name',
        $last_name
    );

    update_post_meta(
        $lead_id,
        '_softkom_email',
        $email
    );

    update_post_meta(
        $lead_id,
        '_softkom_company',
        $company
    );


    /*
     * Funnel scores.
     */
    $scores = isset( $result['scores'] )
        && is_array( $result['scores'] )
            ? $result['scores']
            : array();

    foreach (
        array(
            'maturity',
            'ai_opportunity',
            'commercial_fit',
            'purchase_intent',
            'overall_lead',
        )
        as $score_key
    ) {

        if ( isset( $scores[ $score_key ] ) ) {

            update_post_meta(
                $lead_id,
                '_softkom_score_' . $score_key,
                (int) $scores[ $score_key ]
            );
        }
    }


    /*
     * Lead classification.
     */
    $temperature = isset( $result['lead_temperature'] )
        ? sanitize_text_field(
            $result['lead_temperature']
        )
        : '';

    update_post_meta(
        $lead_id,
        '_softkom_lead_temperature',
        $temperature
    );


    /*
     * Maturity level.
     */
    if (
        isset( $result['maturity_level'] ) &&
        is_array( $result['maturity_level'] )
    ) {

        update_post_meta(
            $lead_id,
            '_softkom_maturity_level',
            wp_json_encode(
                $result['maturity_level']
            )
        );
    }


    /*
     * Recommendations and priorities.
     */
    update_post_meta(
        $lead_id,
        '_softkom_priority_opportunities',
        wp_json_encode(
            isset( $result['priority_opportunities'] )
                ? $result['priority_opportunities']
                : array()
        )
    );

    update_post_meta(
        $lead_id,
        '_softkom_recommendations',
        wp_json_encode(
            isset( $result['recommendations'] )
                ? $result['recommendations']
                : array()
        )
    );


    /*
     * Sanitized source responses.
     */
    update_post_meta(
        $lead_id,
        '_softkom_assessment_answers',
        wp_json_encode(
            is_array( $assessment_answers )
                ? $assessment_answers
                : array()
        )
    );

    update_post_meta(
        $lead_id,
        '_softkom_qualification_answers',
        wp_json_encode(
            is_array( $qualification_answers )
                ? $qualification_answers
                : array()
        )
    );


    /*
     * Security and routing information.
     */
    update_post_meta(
        $lead_id,
        '_softkom_security_risk_score',
        isset( $security['risk_score'] )
            ? (int) $security['risk_score']
            : 0
    );

    update_post_meta(
        $lead_id,
        '_softkom_security_risk_level',
        $risk_level
    );

    update_post_meta(
        $lead_id,
        '_softkom_security_risk_reasons',
        wp_json_encode(
            isset( $security['risk_reasons'] )
                && is_array( $security['risk_reasons'] )
                    ? $security['risk_reasons']
                    : array()
        )
    );

    update_post_meta(
        $lead_id,
        '_softkom_security_rate_count',
        isset( $security['rate_count'] )
            ? (int) $security['rate_count']
            : 0
    );


    if (
        isset( $result['lead_routing'] ) &&
        is_array( $result['lead_routing'] )
    ) {

        update_post_meta(
            $lead_id,
            '_softkom_lead_routing',
            wp_json_encode(
                $result['lead_routing']
            )
        );
    }


    /*
     * Traffic Attribution V1.
     *
     * Store both individual queryable fields and the
     * complete attribution payload for reporting,
     * campaign analysis and revenue attribution.
     */
    $attribution = isset( $result['attribution'] )
        && is_array( $result['attribution'] )
            ? $result['attribution']
            : array();


    $traffic_fields = array(

        'utm_source' => '_softkom_utm_source',
        'utm_medium' => '_softkom_utm_medium',
        'utm_campaign' => '_softkom_utm_campaign',
        'utm_content' => '_softkom_utm_content',
        'utm_term' => '_softkom_utm_term',

        'source' => '_softkom_traffic_source',
        'medium' => '_softkom_traffic_medium',
    );


    foreach ( $traffic_fields as $field => $meta_key ) {

        $value = isset( $attribution[ $field ] )
            ? sanitize_text_field(
                $attribution[ $field ]
            )
            : '';

        update_post_meta(
            $lead_id,
            $meta_key,
            $value
        );
    }


    $landing_page = isset( $attribution['landing_page'] )
        ? esc_url_raw(
            $attribution['landing_page']
        )
        : '';

    $referrer = isset( $attribution['referrer'] )
        ? esc_url_raw(
            $attribution['referrer']
        )
        : '';


    update_post_meta(
        $lead_id,
        '_softkom_landing_page',
        $landing_page
    );

    update_post_meta(
        $lead_id,
        '_softkom_referrer',
        $referrer
    );


    update_post_meta(
        $lead_id,
        '_softkom_attribution',
        wp_json_encode(
            $attribution
        )
    );


    /*
     * General submission information.
     */
    update_post_meta(
        $lead_id,
        '_softkom_submission_source',
        'business-systems-assessment'
    );

    update_post_meta(
        $lead_id,
        '_softkom_submission_date_gmt',
        current_time( 'mysql', true )
    );

    update_post_meta(
        $lead_id,
        '_softkom_personalized_summary',
        isset( $result['personalized_summary'] )
            ? sanitize_textarea_field(
                $result['personalized_summary']
            )
            : ''
    );


    /**
     * Fires after the lead is persisted.
     *
     * Future CRM/email integrations should preferably
     * attach here.
     */
    do_action(
        'softkom_v3_assessment_lead_stored',
        $lead_id,
        $result,
        $security
    );
}

add_action(
    'softkom_v3_assessment_lead_created',
    'softkom_v3_store_assessment_lead',
    10,
    4
);
/**
 * Softkom Leads admin dashboard columns.
 */
function softkom_v3_lead_admin_columns( $columns ) {

    return array(
        'cb'                => isset( $columns['cb'] )
            ? $columns['cb']
            : '<input type="checkbox" />',

        'title'             => 'Lead',
        'softkom_email'     => 'Email',
        'softkom_company'   => 'Company',
        'softkom_maturity'  => 'Maturity',
        'softkom_leadscore' => 'Lead Score',
        'softkom_temp'      => 'Temperature',
        'softkom_risk'      => 'Risk',
        'softkom_routing'   => 'Routing',
        'date'              => 'Date',
    );
}

add_filter(
    'manage_softkom_lead_posts_columns',
    'softkom_v3_lead_admin_columns'
);


/**
 * Render Softkom Leads admin dashboard columns.
 */
function softkom_v3_render_lead_admin_column(
    $column,
    $post_id
) {

    switch ( $column ) {

        case 'softkom_email':

            $email = get_post_meta(
                $post_id,
                '_softkom_email',
                true
            );

            if ( is_email( $email ) ) {

                printf(
                    '<a href="%1$s">%2$s</a>',
                    esc_attr( 'mailto:' . $email ),
                    esc_html( $email )
                );

            } else {

                echo '&mdash;';
            }

            break;


        case 'softkom_company':

            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_softkom_company',
                    true
                )
            );

            break;


        case 'softkom_maturity':

            $score = get_post_meta(
                $post_id,
                '_softkom_score_maturity',
                true
            );

            echo '' !== $score
                ? esc_html( $score ) . '/100'
                : '&mdash;';

            break;


        case 'softkom_leadscore':

            $score = get_post_meta(
                $post_id,
                '_softkom_score_overall_lead',
                true
            );

            echo '' !== $score
                ? '<strong>' .
                    esc_html( $score ) .
                    '/100</strong>'
                : '&mdash;';

            break;


        case 'softkom_temp':

            $temperature = get_post_meta(
                $post_id,
                '_softkom_lead_temperature',
                true
            );

            if ( '' === $temperature ) {
                echo '&mdash;';
                break;
            }

            printf(
                '<span class="softkom-lead-badge softkom-temp-%1$s">%2$s</span>',
                esc_attr(
                    sanitize_html_class(
                        strtolower( $temperature )
                    )
                ),
                esc_html( $temperature )
            );

            break;


        case 'softkom_risk':

            $risk = get_post_meta(
                $post_id,
                '_softkom_security_risk_level',
                true
            );

            if ( '' === $risk ) {
                echo '&mdash;';
                break;
            }

            printf(
                '<span class="softkom-lead-badge softkom-risk-%1$s">%2$s</span>',
                esc_attr(
                    sanitize_html_class(
                        strtolower(
                            str_replace(
                                ' ',
                                '-',
                                $risk
                            )
                        )
                    )
                ),
                esc_html( $risk )
            );

            break;


        case 'softkom_routing':

            $routing_raw = get_post_meta(
                $post_id,
                '_softkom_lead_routing',
                true
            );

            $routing = is_string( $routing_raw )
                ? json_decode(
                    $routing_raw,
                    true
                )
                : array();

            $status = is_array( $routing )
                && isset( $routing['status'] )
                    ? $routing['status']
                    : '';

            if ( '' === $status ) {
                echo '&mdash;';
                break;
            }

            printf(
                '<span class="softkom-lead-badge">%s</span>',
                esc_html(
                    strtoupper( $status )
                )
            );

            break;
    }
}

add_action(
    'manage_softkom_lead_posts_custom_column',
    'softkom_v3_render_lead_admin_column',
    10,
    2
);


/**
 * Admin styling for Softkom Leads.
 */
function softkom_v3_lead_admin_styles() {

    $screen = get_current_screen();

    if (
        ! $screen ||
        'softkom_lead' !== $screen->post_type
    ) {
        return;
    }

    ?>
    <style>
        .post-type-softkom_lead .wp-list-table {
            table-layout: auto;
        }

        .post-type-softkom_lead .column-title {
            min-width: 190px;
        }

        .post-type-softkom_lead .column-softkom_email {
            min-width: 190px;
        }

        .post-type-softkom_lead .column-softkom_company {
            min-width: 150px;
        }

        .post-type-softkom_lead .column-softkom_maturity,
        .post-type-softkom_lead .column-softkom_leadscore {
            width: 90px;
        }

        .softkom-lead-badge {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 999px;
            background: #eef0f3;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.4;
            white-space: nowrap;
        }

        .softkom-temp-hot {
            background: #fee2e2;
            color: #991b1b;
        }

        .softkom-temp-warm {
            background: #ffedd5;
            color: #9a3412;
        }

        .softkom-temp-nurture {
            background: #dbeafe;
            color: #1e40af;
        }

        .softkom-temp-educate {
            background: #f3f4f6;
            color: #374151;
        }

        .softkom-risk-low-risk {
            background: #dcfce7;
            color: #166534;
        }

        .softkom-risk-review {
            background: #fef3c7;
            color: #92400e;
        }

        .softkom-risk-high-risk {
            background: #ffedd5;
            color: #9a3412;
        }

        .softkom-risk-block {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
    <?php
}

add_action(
    'admin_head',
    'softkom_v3_lead_admin_styles'
);

/**
 * Softkom Lead Intelligence Dashboard.
 */
function softkom_v3_register_lead_dashboard() {

    add_meta_box(
        'softkom_lead_intelligence',
        'Softkom Lead Intelligence',
        'softkom_v3_render_lead_dashboard',
        'softkom_lead',
        'normal',
        'high'
    );
}
add_action(
    'add_meta_boxes_softkom_lead',
    'softkom_v3_register_lead_dashboard'
);


/**
 * Decode JSON lead meta safely.
 */
function softkom_v3_lead_json_meta( $post_id, $key ) {

    $raw = get_post_meta(
        $post_id,
        $key,
        true
    );

    if ( is_array( $raw ) ) {
        return $raw;
    }

    if ( ! is_string( $raw ) || '' === $raw ) {
        return array();
    }

    $decoded = json_decode(
        $raw,
        true
    );

    return is_array( $decoded )
        ? $decoded
        : array();
}


/**
 * Render Lead Intelligence Dashboard.
 */
function softkom_v3_render_lead_dashboard( $post ) {

    $post_id = $post->ID;

    $first_name = get_post_meta(
        $post_id,
        '_softkom_first_name',
        true
    );

    $last_name = get_post_meta(
        $post_id,
        '_softkom_last_name',
        true
    );

    $email = get_post_meta(
        $post_id,
        '_softkom_email',
        true
    );

    $company = get_post_meta(
        $post_id,
        '_softkom_company',
        true
    );

    $temperature = get_post_meta(
        $post_id,
        '_softkom_lead_temperature',
        true
    );

    $maturity_score = (int) get_post_meta(
        $post_id,
        '_softkom_score_maturity',
        true
    );

    $ai_score = (int) get_post_meta(
        $post_id,
        '_softkom_score_ai_opportunity',
        true
    );

    $commercial_score = (int) get_post_meta(
        $post_id,
        '_softkom_score_commercial_fit',
        true
    );

    $purchase_score = (int) get_post_meta(
        $post_id,
        '_softkom_score_purchase_intent',
        true
    );

    $lead_score = (int) get_post_meta(
        $post_id,
        '_softkom_score_overall_lead',
        true
    );

    $risk_score = (int) get_post_meta(
        $post_id,
        '_softkom_security_risk_score',
        true
    );

    $risk_level = get_post_meta(
        $post_id,
        '_softkom_security_risk_level',
        true
    );

    $rate_count = (int) get_post_meta(
        $post_id,
        '_softkom_security_rate_count',
        true
    );

    $summary = get_post_meta(
        $post_id,
        '_softkom_personalized_summary',
        true
    );

    $submission_source = get_post_meta(
        $post_id,
        '_softkom_submission_source',
        true
    );

    $submission_date = get_post_meta(
        $post_id,
        '_softkom_submission_date_gmt',
        true
    );

    $maturity = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_maturity_level'
    );

    $opportunities = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_priority_opportunities'
    );

    $recommendations = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_recommendations'
    );

    $qualification = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_qualification_answers'
    );

    $assessment = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_assessment_answers'
    );

    $risk_reasons = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_security_risk_reasons'
    );

    $routing = softkom_v3_lead_json_meta(
        $post_id,
        '_softkom_lead_routing'
    );

    $routing_status = isset( $routing['status'] )
        ? $routing['status']
        : '';


    /*
     * Traffic Attribution V1.
     */
    $traffic_source = get_post_meta(
        $post_id,
        '_softkom_traffic_source',
        true
    );

    $traffic_medium = get_post_meta(
        $post_id,
        '_softkom_traffic_medium',
        true
    );

    $utm_source = get_post_meta(
        $post_id,
        '_softkom_utm_source',
        true
    );

    $utm_medium = get_post_meta(
        $post_id,
        '_softkom_utm_medium',
        true
    );

    $utm_campaign = get_post_meta(
        $post_id,
        '_softkom_utm_campaign',
        true
    );

    $utm_content = get_post_meta(
        $post_id,
        '_softkom_utm_content',
        true
    );

    $utm_term = get_post_meta(
        $post_id,
        '_softkom_utm_term',
        true
    );

    $landing_page = get_post_meta(
        $post_id,
        '_softkom_landing_page',
        true
    );

    $referrer = get_post_meta(
        $post_id,
        '_softkom_referrer',
        true
    );

    $sales_eligible = isset( $routing['sales_eligible'] )
        ? (bool) $routing['sales_eligible']
        : false;

    $requires_review = isset( $routing['requires_review'] )
        ? (bool) $routing['requires_review']
        : false;

    $full_name = trim(
        $first_name . ' ' . $last_name
    );

    ?>
    <style>

        .softkom-lead-dashboard {
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            max-width: 1200px;
        }

        .softkom-lead-hero {
            background: #0f172a;
            color: #fff;
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .softkom-lead-hero h2 {
            color: #fff;
            font-size: 26px;
            margin: 0 0 8px;
        }

        .softkom-lead-hero p {
            color: #cbd5e1;
            margin: 4px 0;
        }

        .softkom-badges {
            margin-top: 18px;
        }

        .softkom-badge {
            display: inline-block;
            background: #e2e8f0;
            color: #0f172a;
            border-radius: 20px;
            padding: 6px 12px;
            margin-right: 8px;
            margin-bottom: 5px;
            font-size: 12px;
            font-weight: 700;
        }

        .softkom-score-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }

        .softkom-score-card {
            border: 1px solid #dbe3ef;
            background: #f8fafc;
            border-radius: 10px;
            padding: 18px;
        }

        .softkom-score-number {
            color: #1d4ed8;
            font-size: 25px;
            font-weight: 800;
        }

        .softkom-score-label {
            color: #64748b;
            font-size: 12px;
            margin-top: 5px;
        }

        .softkom-panel {
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 18px;
        }

        .softkom-panel h3 {
            margin-top: 0;
            font-size: 18px;
        }

        .softkom-panel table {
            width: 100%;
            border-collapse: collapse;
        }

        .softkom-panel th,
        .softkom-panel td {
            text-align: left;
            padding: 9px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .softkom-panel th {
            width: 32%;
        }

        .softkom-opportunity {
            padding: 12px;
            margin-bottom: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }

        .softkom-security-low {
            background: #dcfce7;
        }

        .softkom-security-review {
            background: #fef3c7;
        }

        .softkom-security-high {
            background: #fee2e2;
        }

        .softkom-next-action {
            background: #eff6ff;
            border-left: 4px solid #2563eb;
        }

    </style>

    <div class="softkom-lead-dashboard">

        <div class="softkom-lead-hero">

            <h2>
                <?php
                echo esc_html(
                    $full_name
                        ? $full_name
                        : 'Softkom Lead'
                );
                ?>
            </h2>

            <?php if ( $company ) : ?>
                <p>
                    <?php echo esc_html( $company ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $email ) : ?>
                <p>
                    <a
                        href="mailto:<?php echo esc_attr( $email ); ?>"
                        style="color:#93c5fd;"
                    >
                        <?php echo esc_html( $email ); ?>
                    </a>
                </p>
            <?php endif; ?>

            <div class="softkom-badges">

                <?php if ( $temperature ) : ?>
                    <span class="softkom-badge">
                        <?php
                        echo esc_html(
                            strtoupper( $temperature )
                        );
                        ?>
                    </span>
                <?php endif; ?>

                <?php if ( $risk_level ) : ?>
                    <span class="softkom-badge">
                        <?php echo esc_html( $risk_level ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( $routing_status ) : ?>
                    <span class="softkom-badge">
                        <?php
                        echo esc_html(
                            strtoupper( $routing_status )
                        );
                        ?>
                    </span>
                <?php endif; ?>

            </div>

        </div>


        <div class="softkom-score-grid">

            <?php

            $score_cards = array(
                'Maturity'       => $maturity_score,
                'AI Opportunity' => $ai_score,
                'Commercial Fit' => $commercial_score,
                'Purchase Intent'=> $purchase_score,
                'Lead Score'     => $lead_score,
            );

            foreach ( $score_cards as $label => $score ) :
            ?>

                <div class="softkom-score-card">

                    <div class="softkom-score-number">
                        <?php echo esc_html( $score ); ?>/100
                    </div>

                    <div class="softkom-score-label">
                        <?php echo esc_html( $label ); ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <?php if ( $summary ) : ?>

            <div class="softkom-panel">

                <h3>Lead Summary</h3>

                <p>
                    <?php
                    echo nl2br(
                        esc_html( $summary )
                    );
                    ?>
                </p>

            </div>

        <?php endif; ?>


        <div class="softkom-panel softkom-next-action">

            <h3>Next Best Action</h3>

            <?php

            if (
                'HOT' === strtoupper( $temperature ) &&
                'LOW RISK' === strtoupper( $risk_level ) &&
                $sales_eligible
            ) {

                echo '<p><strong>Priority sales lead.</strong> Contact the prospect and move toward a Solution Mapping Session.</p>';

            } elseif ( $requires_review ) {

                echo '<p><strong>Review required.</strong> Validate the submission before beginning sales outreach.</p>';

            } elseif ( $lead_score >= 60 ) {

                echo '<p><strong>Qualified opportunity.</strong> Begin personalised sales follow-up.</p>';

            } else {

                echo '<p><strong>Nurture opportunity.</strong> Add the prospect to the appropriate follow-up sequence.</p>';
            }

            ?>

        </div>


        <?php
        $has_traffic_data =
            '' !== $traffic_source ||
            '' !== $utm_source ||
            '' !== $utm_campaign ||
            '' !== $landing_page ||
            '' !== $referrer;
        ?>

        <?php if ( $has_traffic_data ) : ?>

            <div class="softkom-panel">

                <h3>Traffic &amp; Acquisition</h3>

                <table>

                    <tr>
                        <th>Traffic Source</th>
                        <td>
                            <?php
                            echo esc_html(
                                $traffic_source
                                    ? $traffic_source
                                    : 'Direct'
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Traffic Medium</th>
                        <td>
                            <?php
                            echo esc_html(
                                $traffic_medium
                                    ? $traffic_medium
                                    : 'Unknown'
                            );
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th>UTM Source</th>
                        <td>
                            <?php echo esc_html( $utm_source ? $utm_source : '—' ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>UTM Medium</th>
                        <td>
                            <?php echo esc_html( $utm_medium ? $utm_medium : '—' ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Campaign</th>
                        <td>
                            <?php echo esc_html( $utm_campaign ? $utm_campaign : '—' ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Content</th>
                        <td>
                            <?php echo esc_html( $utm_content ? $utm_content : '—' ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Term</th>
                        <td>
                            <?php echo esc_html( $utm_term ? $utm_term : '—' ); ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Landing Page</th>
                        <td>
                            <?php if ( $landing_page ) : ?>
                                <a
                                    href="<?php echo esc_url( $landing_page ); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <?php echo esc_html( $landing_page ); ?>
                                </a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <th>Referrer</th>
                        <td>
                            <?php if ( $referrer ) : ?>
                                <a
                                    href="<?php echo esc_url( $referrer ); ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <?php echo esc_html( $referrer ); ?>
                                </a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>

                </table>

            </div>

        <?php endif; ?>


        <?php if ( ! empty( $maturity ) ) : ?>

            <div class="softkom-panel">

                <h3>Maturity Profile</h3>

                <table>

                    <?php foreach ( $maturity as $key => $value ) : ?>

                        <tr>
                            <th>
                                <?php
                                echo esc_html(
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $key
                                        )
                                    )
                                );
                                ?>
                            </th>

                            <td>
                                <?php
                                echo esc_html(
                                    is_scalar( $value )
                                        ? $value
                                        : wp_json_encode( $value )
                                );
                                ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </table>

            </div>

        <?php endif; ?>


        <div class="softkom-panel">

            <h3>Top Opportunities</h3>

            <?php if ( ! empty( $opportunities ) ) : ?>

                <?php foreach ( $opportunities as $opportunity ) : ?>

                    <div class="softkom-opportunity">

                        <?php

                        if ( is_array( $opportunity ) ) {

                            foreach ( $opportunity as $key => $value ) {

                                if ( is_scalar( $value ) ) {

                                    echo '<strong>' .
                                        esc_html(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $key
                                                )
                                            )
                                        ) .
                                        ':</strong> ' .
                                        esc_html( $value ) .
                                        '<br>';
                                }
                            }

                        } else {

                            echo esc_html( $opportunity );
                        }

                        ?>

                    </div>

                <?php endforeach; ?>

            <?php else : ?>

                <p>No opportunity data stored.</p>

            <?php endif; ?>

        </div>


        <div class="softkom-panel">

            <h3>Recommended Softkom Solutions</h3>

            <?php if ( ! empty( $recommendations ) ) : ?>

                <?php foreach ( $recommendations as $recommendation ) : ?>

                    <div class="softkom-opportunity">

                        <?php

                        if ( is_array( $recommendation ) ) {

                            foreach ( $recommendation as $key => $value ) {

                                if ( is_scalar( $value ) ) {

                                    echo '<strong>' .
                                        esc_html(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $key
                                                )
                                            )
                                        ) .
                                        ':</strong> ' .
                                        esc_html( $value ) .
                                        '<br>';
                                }
                            }

                        } else {

                            echo esc_html( $recommendation );
                        }

                        ?>

                    </div>

                <?php endforeach; ?>

            <?php else : ?>

                <p>No recommendations stored.</p>

            <?php endif; ?>

        </div>


        <div class="softkom-panel">

            <h3>Qualification & Commercial Intent</h3>

            <?php if ( ! empty( $qualification ) ) : ?>

                <table>

                    <?php foreach ( $qualification as $key => $value ) : ?>

                        <tr>

                            <th>
                                <?php
                                echo esc_html(
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $key
                                        )
                                    )
                                );
                                ?>
                            </th>

                            <td>
                                <?php
                                echo esc_html(
                                    is_scalar( $value )
                                        ? $value
                                        : wp_json_encode( $value )
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>

            <?php else : ?>

                <p>No qualification data stored.</p>

            <?php endif; ?>

        </div>


        <div class="softkom-panel">

            <h3>Assessment Answers</h3>

            <?php if ( ! empty( $assessment ) ) : ?>

                <table>

                    <?php foreach ( $assessment as $key => $value ) : ?>

                        <tr>

                            <th>
                                <?php
                                echo esc_html(
                                    ucwords(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $key
                                        )
                                    )
                                );
                                ?>
                            </th>

                            <td>
                                <?php
                                echo esc_html(
                                    is_scalar( $value )
                                        ? $value
                                        : wp_json_encode( $value )
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </table>

            <?php else : ?>

                <p>No assessment answers stored.</p>

            <?php endif; ?>

        </div>


        <div class="softkom-panel">

            <h3>Security Intelligence</h3>

            <table>

                <tr>
                    <th>Risk Level</th>
                    <td>
                        <?php echo esc_html( $risk_level ); ?>
                    </td>
                </tr>

                <tr>
                    <th>Risk Score</th>
                    <td>
                        <?php echo esc_html( $risk_score ); ?>
                    </td>
                </tr>

                <tr>
                    <th>Rate Count</th>
                    <td>
                        <?php echo esc_html( $rate_count ); ?>
                    </td>
                </tr>

                <tr>
                    <th>Routing</th>
                    <td>
                        <?php echo esc_html( $routing_status ); ?>
                    </td>
                </tr>

                <tr>
                    <th>Sales Eligible</th>
                    <td>
                        <?php
                        echo $sales_eligible
                            ? 'Yes'
                            : 'No';
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Requires Review</th>
                    <td>
                        <?php
                        echo $requires_review
                            ? 'Yes'
                            : 'No';
                        ?>
                    </td>
                </tr>

            </table>

            <?php if ( ! empty( $risk_reasons ) ) : ?>

                <h4>Security Signals</h4>

                <ul>

                    <?php foreach ( $risk_reasons as $reason ) : ?>

                        <li>
                            <?php
                            echo esc_html(
                                is_scalar( $reason )
                                    ? $reason
                                    : wp_json_encode( $reason )
                            );
                            ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            <?php endif; ?>

        </div>


        <div class="softkom-panel">

            <h3>Submission Information</h3>

            <table>

                <tr>
                    <th>Source</th>
                    <td>
                        <?php
                        echo esc_html(
                            $submission_source
                        );
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Submitted GMT</th>
                    <td>
                        <?php
                        echo esc_html(
                            $submission_date
                        );
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Lead Record</th>
                    <td>
                        #<?php echo esc_html( $post_id ); ?>
                    </td>
                </tr>

            </table>

        </div>

    </div>

    <?php
}


/**
 * Softkom Lead Pipeline V1 meta box.
 */
function softkom_v3_register_lead_pipeline_meta_box() {

    add_meta_box(
        'softkom_lead_pipeline',
        'Softkom Sales Pipeline',
        'softkom_v3_render_lead_pipeline_meta_box',
        'softkom_lead',
        'side',
        'high'
    );
}

add_action(
    'add_meta_boxes_softkom_lead',
    'softkom_v3_register_lead_pipeline_meta_box'
);


/**
 * Available sales pipeline stages.
 */
function softkom_v3_lead_pipeline_stages() {

    return array(
        'new'       => 'New',
        'qualified' => 'Qualified',
        'contacted' => 'Contacted',
        'meeting'   => 'Meeting',
        'proposal'  => 'Proposal',
        'won'       => 'Won',
        'lost'      => 'Lost',
        'nurture'   => 'Nurture',
    );
}


/**
 * Render editable sales pipeline fields.
 */
function softkom_v3_render_lead_pipeline_meta_box( $post ) {

    wp_nonce_field(
        'softkom_v3_save_lead_pipeline',
        'softkom_v3_lead_pipeline_nonce'
    );

    $stage = get_post_meta(
        $post->ID,
        '_softkom_pipeline_stage',
        true
    );

    if ( '' === $stage ) {
        $stage = 'new';
    }

    $assigned_offer = get_post_meta(
        $post->ID,
        '_softkom_assigned_offer',
        true
    );

    $monthly_value = get_post_meta(
        $post->ID,
        '_softkom_estimated_mrr',
        true
    );

    $follow_up = get_post_meta(
        $post->ID,
        '_softkom_follow_up_date',
        true
    );

    $notes = get_post_meta(
        $post->ID,
        '_softkom_internal_notes',
        true
    );

    $stages = softkom_v3_lead_pipeline_stages();

    ?>
    <p>
        <label for="softkom_pipeline_stage">
            <strong>Pipeline Stage</strong>
        </label>
    </p>

    <p>
        <select
            id="softkom_pipeline_stage"
            name="softkom_pipeline_stage"
            style="width:100%;"
        >
            <?php foreach ( $stages as $value => $label ) : ?>

                <option
                    value="<?php echo esc_attr( $value ); ?>"
                    <?php selected( $stage, $value ); ?>
                >
                    <?php echo esc_html( $label ); ?>
                </option>

            <?php endforeach; ?>
        </select>
    </p>


    <p>
        <label for="softkom_assigned_offer">
            <strong>Assigned Offer</strong>
        </label>
    </p>

    <p>
        <input
            type="text"
            id="softkom_assigned_offer"
            name="softkom_assigned_offer"
            value="<?php echo esc_attr( $assigned_offer ); ?>"
            placeholder="e.g. Managed AI Automation"
            style="width:100%;"
        >
    </p>


    <p>
        <label for="softkom_estimated_mrr">
            <strong>Estimated Monthly Revenue</strong>
        </label>
    </p>

    <p>
        <input
            type="number"
            min="0"
            step="1"
            id="softkom_estimated_mrr"
            name="softkom_estimated_mrr"
            value="<?php echo esc_attr( $monthly_value ); ?>"
            placeholder="e.g. 7500"
            style="width:100%;"
        >
    </p>


    <p>
        <label for="softkom_follow_up_date">
            <strong>Follow-up Date</strong>
        </label>
    </p>

    <p>
        <input
            type="date"
            id="softkom_follow_up_date"
            name="softkom_follow_up_date"
            value="<?php echo esc_attr( $follow_up ); ?>"
            style="width:100%;"
        >
    </p>


    <p>
        <label for="softkom_internal_notes">
            <strong>Internal Notes</strong>
        </label>
    </p>

    <p>
        <textarea
            id="softkom_internal_notes"
            name="softkom_internal_notes"
            rows="6"
            style="width:100%;"
            placeholder="Sales notes, objections, next steps..."
        ><?php echo esc_textarea( $notes ); ?></textarea>
    </p>
    <?php
}


/**
 * Save Lead Pipeline V1 fields.
 */
function softkom_v3_save_lead_pipeline( $post_id ) {

    if (
        ! isset( $_POST['softkom_v3_lead_pipeline_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['softkom_v3_lead_pipeline_nonce']
                )
            ),
            'softkom_v3_save_lead_pipeline'
        )
    ) {
        return;
    }

    if (
        defined( 'DOING_AUTOSAVE' ) &&
        DOING_AUTOSAVE
    ) {
        return;
    }

    if (
        'softkom_lead' !== get_post_type( $post_id ) ||
        ! current_user_can( 'edit_post', $post_id )
    ) {
        return;
    }


    $old_stage = get_post_meta(
        $post_id,
        '_softkom_pipeline_stage',
        true
    );


    $stages = softkom_v3_lead_pipeline_stages();

    $stage = isset( $_POST['softkom_pipeline_stage'] )
        ? sanitize_key(
            wp_unslash(
                $_POST['softkom_pipeline_stage']
            )
        )
        : 'new';

    if ( ! isset( $stages[ $stage ] ) ) {
        $stage = 'new';
    }


    $assigned_offer = isset( $_POST['softkom_assigned_offer'] )
        ? sanitize_text_field(
            wp_unslash(
                $_POST['softkom_assigned_offer']
            )
        )
        : '';


    $monthly_value = isset( $_POST['softkom_estimated_mrr'] )
        ? max(
            0,
            (float) wp_unslash(
                $_POST['softkom_estimated_mrr']
            )
        )
        : 0;


    $follow_up = isset( $_POST['softkom_follow_up_date'] )
        ? sanitize_text_field(
            wp_unslash(
                $_POST['softkom_follow_up_date']
            )
        )
        : '';


    if (
        '' !== $follow_up &&
        ! preg_match(
            '/^\d{4}-\d{2}-\d{2}$/',
            $follow_up
        )
    ) {
        $follow_up = '';
    }


    $notes = isset( $_POST['softkom_internal_notes'] )
        ? sanitize_textarea_field(
            wp_unslash(
                $_POST['softkom_internal_notes']
            )
        )
        : '';


    update_post_meta(
        $post_id,
        '_softkom_pipeline_stage',
        $stage
    );

    update_post_meta(
        $post_id,
        '_softkom_assigned_offer',
        $assigned_offer
    );

    update_post_meta(
        $post_id,
        '_softkom_estimated_mrr',
        $monthly_value
    );

    update_post_meta(
        $post_id,
        '_softkom_follow_up_date',
        $follow_up
    );

    update_post_meta(
        $post_id,
        '_softkom_internal_notes',
        $notes
    );


    /*
     * Track stage changes as activity history.
     */
    if ( $old_stage !== $stage ) {

        $history = get_post_meta(
            $post_id,
            '_softkom_pipeline_history',
            true
        );

        if ( ! is_array( $history ) ) {
            $history = array();
        }

        $history[] = array(
            'timestamp_gmt' => current_time(
                'mysql',
                true
            ),
            'event'         => 'stage_changed',
            'from'          => $old_stage,
            'to'            => $stage,
            'user_id'       => get_current_user_id(),
        );

        update_post_meta(
            $post_id,
            '_softkom_pipeline_history',
            $history
        );
    }
}

add_action(
    'save_post_softkom_lead',
    'softkom_v3_save_lead_pipeline'
);


/**
 * Show pipeline stage and recurring value in lead list.
 */
function softkom_v3_add_pipeline_admin_columns( $columns ) {

    $new = array();

    foreach ( $columns as $key => $label ) {

        $new[ $key ] = $label;

        if ( 'softkom_routing' === $key ) {

            $new['softkom_stage'] = 'Stage';
            $new['softkom_mrr']   = 'MRR';
        }
    }

    return $new;
}

add_filter(
    'manage_softkom_lead_posts_columns',
    'softkom_v3_add_pipeline_admin_columns',
    20
);


/**
 * Render pipeline list columns.
 */
function softkom_v3_render_pipeline_admin_columns(
    $column,
    $post_id
) {

    if ( 'softkom_stage' === $column ) {

        $stage = get_post_meta(
            $post_id,
            '_softkom_pipeline_stage',
            true
        );

        $stages = softkom_v3_lead_pipeline_stages();

        echo esc_html(
            isset( $stages[ $stage ] )
                ? $stages[ $stage ]
                : 'New'
        );
    }


    if ( 'softkom_mrr' === $column ) {

        $value = get_post_meta(
            $post_id,
            '_softkom_estimated_mrr',
            true
        );

        if ( '' === $value ) {
            echo '&mdash;';
            return;
        }

        echo 'R ' .
            esc_html(
                number_format_i18n(
                    (float) $value,
                    0
                )
            );
    }
}

add_action(
    'manage_softkom_lead_posts_custom_column',
    'softkom_v3_render_pipeline_admin_columns',
    20,
    2
);


/**
 * Add activity history panel.
 */
function softkom_v3_register_lead_activity_meta_box() {

    add_meta_box(
        'softkom_lead_activity',
        'Lead Activity',
        'softkom_v3_render_lead_activity',
        'softkom_lead',
        'normal',
        'default'
    );
}

add_action(
    'add_meta_boxes_softkom_lead',
    'softkom_v3_register_lead_activity_meta_box'
);


/**
 * Render lead activity history.
 */
function softkom_v3_render_lead_activity( $post ) {

    $history = get_post_meta(
        $post->ID,
        '_softkom_pipeline_history',
        true
    );

    if ( ! is_array( $history ) || empty( $history ) ) {

        echo '<p>No pipeline activity recorded yet.</p>';
        return;
    }

    $stages = softkom_v3_lead_pipeline_stages();

    echo '<table class="widefat striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Date GMT</th>';
    echo '<th>Activity</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach ( array_reverse( $history ) as $item ) {

        $from = isset( $item['from'] )
            ? $item['from']
            : '';

        $to = isset( $item['to'] )
            ? $item['to']
            : '';

        $from_label = isset( $stages[ $from ] )
            ? $stages[ $from ]
            : $from;

        $to_label = isset( $stages[ $to ] )
            ? $stages[ $to ]
            : $to;

        echo '<tr>';

        echo '<td>' .
            esc_html(
                isset( $item['timestamp_gmt'] )
                    ? $item['timestamp_gmt']
                    : ''
            ) .
            '</td>';

        echo '<td>';

        echo 'Stage changed from <strong>' .
            esc_html(
                $from_label
                    ? $from_label
                    : 'Unassigned'
            ) .
            '</strong> to <strong>' .
            esc_html(
                $to_label
            ) .
            '</strong>.';

        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}


/**
 * Register Acquisition Performance V1.
 */
function softkom_v3_register_acquisition_performance_page() {

    add_submenu_page(
        'edit.php?post_type=softkom_lead',
        'Acquisition Performance',
        'Acquisition Performance',
        'edit_posts',
        'softkom-acquisition-performance',
        'softkom_v3_render_acquisition_performance_page'
    );
}

add_action(
    'admin_menu',
    'softkom_v3_register_acquisition_performance_page'
);


/**
 * Build acquisition performance metrics.
 */
function softkom_v3_acquisition_performance_data() {

    $lead_ids = get_posts(
        array(
            'post_type'      => 'softkom_lead',
            'post_status'    => array( 'private', 'publish' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    $rows = array();

    $totals = array(
        'leads'        => 0,
        'qualified'    => 0,
        'won'          => 0,
        'pipeline_mrr' => 0,
        'won_mrr'      => 0,
    );


    foreach ( $lead_ids as $lead_id ) {

        $source = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_traffic_source',
                true
            )
        );

        if ( '' === $source ) {
            $source = 'direct';
        }


        $campaign = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_utm_campaign',
                true
            )
        );

        if ( '' === $campaign ) {
            $campaign = 'unattributed';
        }


        $stage = sanitize_key(
            (string) get_post_meta(
                $lead_id,
                '_softkom_pipeline_stage',
                true
            )
        );

        if ( '' === $stage ) {
            $stage = 'new';
        }


        $temperature = strtoupper(
            trim(
                (string) get_post_meta(
                    $lead_id,
                    '_softkom_lead_temperature',
                    true
                )
            )
        );


        $mrr = max(
            0,
            (float) get_post_meta(
                $lead_id,
                '_softkom_estimated_mrr',
                true
            )
        );


        $key = strtolower(
            $source . '|' . $campaign
        );


        if ( ! isset( $rows[ $key ] ) ) {

            $rows[ $key ] = array(
                'source'       => $source,
                'campaign'     => $campaign,
                'leads'        => 0,
                'qualified'    => 0,
                'hot'          => 0,
                'won'          => 0,
                'pipeline_mrr' => 0,
                'won_mrr'      => 0,
            );
        }


        $rows[ $key ]['leads']++;
        $totals['leads']++;


        /*
         * Qualified means a lead has progressed to a
         * commercially meaningful active sales stage.
         */
        if (
            in_array(
                $stage,
                array(
                    'qualified',
                    'contacted',
                    'meeting',
                    'proposal',
                    'won',
                ),
                true
            )
        ) {

            $rows[ $key ]['qualified']++;
            $totals['qualified']++;
        }


        if ( 'HOT' === $temperature ) {
            $rows[ $key ]['hot']++;
        }


        if ( 'won' === $stage ) {

            $rows[ $key ]['won']++;
            $rows[ $key ]['won_mrr'] += $mrr;

            $totals['won']++;
            $totals['won_mrr'] += $mrr;
        }


        /*
         * Active pipeline excludes lost and nurture.
         */
        if (
            ! in_array(
                $stage,
                array(
                    'lost',
                    'nurture',
                ),
                true
            )
        ) {

            $rows[ $key ]['pipeline_mrr'] += $mrr;
            $totals['pipeline_mrr'] += $mrr;
        }
    }


    usort(
        $rows,
        function ( $a, $b ) {

            if ( $a['pipeline_mrr'] === $b['pipeline_mrr'] ) {
                return $b['leads'] <=> $a['leads'];
            }

            return $b['pipeline_mrr'] <=> $a['pipeline_mrr'];
        }
    );


    return array(
        'totals' => $totals,
        'rows'   => $rows,
    );
}


/**
 * Render Acquisition Performance V1.
 */
function softkom_v3_render_acquisition_performance_page() {

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die(
            esc_html__(
                'You do not have permission to view this report.',
                'softkom-v3'
            )
        );
    }


    $data = softkom_v3_acquisition_performance_data();

    $totals = isset( $data['totals'] )
        ? $data['totals']
        : array();

    $rows = isset( $data['rows'] )
        ? $data['rows']
        : array();

    ?>

    <div class="wrap">

        <h1>Acquisition Performance</h1>

        <p>
            Track traffic sources and campaigns through lead
            qualification, pipeline value and recurring revenue.
        </p>


        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(180px,1fr));
                gap:16px;
                max-width:1200px;
                margin:20px 0;
            "
        >

            <?php

            $cards = array(
                'Total Leads' => isset( $totals['leads'] )
                    ? $totals['leads']
                    : 0,

                'Qualified Leads' => isset( $totals['qualified'] )
                    ? $totals['qualified']
                    : 0,

                'Won Leads' => isset( $totals['won'] )
                    ? $totals['won']
                    : 0,

                'Pipeline MRR' => 'R ' . number_format_i18n(
                    isset( $totals['pipeline_mrr'] )
                        ? $totals['pipeline_mrr']
                        : 0,
                    0
                ),

                'Won MRR' => 'R ' . number_format_i18n(
                    isset( $totals['won_mrr'] )
                        ? $totals['won_mrr']
                        : 0,
                    0
                ),
            );


            foreach ( $cards as $label => $value ) :
            ?>

                <div
                    style="
                        background:#fff;
                        border:1px solid #dcdcde;
                        border-radius:8px;
                        padding:20px;
                    "
                >

                    <div
                        style="
                            font-size:12px;
                            color:#646970;
                            text-transform:uppercase;
                            font-weight:600;
                        "
                    >
                        <?php echo esc_html( $label ); ?>
                    </div>

                    <div
                        style="
                            font-size:28px;
                            font-weight:700;
                            margin-top:8px;
                        "
                    >
                        <?php echo esc_html( $value ); ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <table
            class="widefat striped"
            style="max-width:1400px;"
        >

            <thead>

                <tr>
                    <th>Source</th>
                    <th>Campaign</th>
                    <th>Leads</th>
                    <th>Qualified</th>
                    <th>Hot</th>
                    <th>Won</th>
                    <th>Qualification Rate</th>
                    <th>Pipeline MRR</th>
                    <th>Won MRR</th>
                </tr>

            </thead>

            <tbody>

                <?php if ( empty( $rows ) ) : ?>

                    <tr>
                        <td colspan="9">
                            No acquisition data available yet.
                        </td>
                    </tr>

                <?php else : ?>

                    <?php foreach ( $rows as $row ) : ?>

                        <?php

                        $qualification_rate =
                            $row['leads'] > 0
                                ? round(
                                    (
                                        $row['qualified'] /
                                        $row['leads']
                                    ) * 100,
                                    1
                                )
                                : 0;

                        ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php
                                    echo esc_html(
                                        ucfirst(
                                            $row['source']
                                        )
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    $row['campaign']
                                );
                                ?>
                            </td>

                            <td>
                                <?php echo esc_html( $row['leads'] ); ?>
                            </td>

                            <td>
                                <?php echo esc_html( $row['qualified'] ); ?>
                            </td>

                            <td>
                                <?php echo esc_html( $row['hot'] ); ?>
                            </td>

                            <td>
                                <?php echo esc_html( $row['won'] ); ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    $qualification_rate . '%'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    'R ' .
                                    number_format_i18n(
                                        $row['pipeline_mrr'],
                                        0
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    'R ' .
                                    number_format_i18n(
                                        $row['won_mrr'],
                                        0
                                    )
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>


        <?php
        if ( function_exists( 'softkom_v3_render_acquisition_intelligence_v3' ) ) {
            softkom_v3_render_acquisition_intelligence_v3();
        }

        if ( function_exists( 'softkom_v3_render_campaign_comparison_v2' ) ) {
            softkom_v3_render_campaign_comparison_v2();
        }
        ?>


    </div>

    <?php
}


/**
 * Render Campaign Comparison V2.
 */
function softkom_v3_render_campaign_comparison_v2() {

    if (
        ! function_exists( 'softkom_v3_campaign_performance' )
    ) {
        return;
    }

    $campaign_ids = get_posts(
        array(
            'post_type'      => 'softkom_campaign',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    if ( empty( $campaign_ids ) ) {
        return;
    }

    $rows = array();

    $totals = array(
        'budget'             => 0,
        'leads'              => 0,
        'qualified'          => 0,
        'pipeline'           => 0,
        'estimated_mrr'      => 0,
        'cost_per_lead'      => 0,
        'cost_per_qualified' => 0,
        'pipeline_rate'      => 0,
        'efficiency'         => 0,
    );


    foreach ( $campaign_ids as $campaign_id ) {

        $performance =
            softkom_v3_campaign_performance(
                $campaign_id
            );

        $source = get_post_meta(
            $campaign_id,
            '_softkom_campaign_utm_source',
            true
        );

        $medium = get_post_meta(
            $campaign_id,
            '_softkom_campaign_utm_medium',
            true
        );

        $campaign_key = get_post_meta(
            $campaign_id,
            '_softkom_campaign_utm_campaign',
            true
        );

        $rows[] = array(
            'id'          => $campaign_id,
            'title'       => get_the_title( $campaign_id ),
            'source'      => $source,
            'medium'      => $medium,
            'campaign'    => $campaign_key,
            'performance' => $performance,
        );

        $totals['budget'] +=
            isset( $performance['budget'] )
                ? (float) $performance['budget']
                : 0;

        $totals['leads'] +=
            isset( $performance['leads'] )
                ? (int) $performance['leads']
                : 0;

        $totals['qualified'] +=
            isset( $performance['qualified'] )
                ? (int) $performance['qualified']
                : 0;

        $totals['pipeline'] +=
            isset( $performance['pipeline_leads'] )
                ? (int) $performance['pipeline_leads']
                : 0;

        $totals['estimated_mrr'] +=
            isset( $performance['estimated_mrr'] )
                ? (float) $performance['estimated_mrr']
                : 0;
    }


    if ( $totals['leads'] > 0 ) {

        $totals['cost_per_lead'] =
            $totals['budget'] /
            $totals['leads'];

        $totals['pipeline_rate'] = round(
            (
                $totals['pipeline'] /
                $totals['leads']
            ) * 100,
            1
        );
    }


    if ( $totals['qualified'] > 0 ) {

        $totals['cost_per_qualified'] =
            $totals['budget'] /
            $totals['qualified'];
    }


    if ( $totals['budget'] > 0 ) {

        $totals['efficiency'] = round(
            (
                $totals['estimated_mrr'] /
                $totals['budget']
            ) * 100,
            1
        );
    }

    ?>

    <div
        style="
            margin-top:32px;
            padding-top:24px;
            border-top:1px solid #dcdcde;
        "
    >

        <h2>Campaign Comparison</h2>

        <p class="description">
            Compare campaign-level acquisition, pipeline and recurring revenue performance.
        </p>


        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(170px,1fr));
                gap:12px;
                margin:18px 0;
                max-width:1400px;
            "
        >

            <?php

            $summary_cards = array(
                'Campaign Budget' =>
                    'R ' .
                    number_format_i18n(
                        $totals['budget'],
                        0
                    ),

                'Campaign Leads' =>
                    number_format_i18n(
                        $totals['leads']
                    ),

                'Qualified' =>
                    number_format_i18n(
                        $totals['qualified']
                    ),

                'Pipeline' =>
                    number_format_i18n(
                        $totals['pipeline']
                    ),

                'Estimated MRR' =>
                    'R ' .
                    number_format_i18n(
                        $totals['estimated_mrr'],
                        0
                    ),

                'Blended CPL' =>
                    'R ' .
                    number_format_i18n(
                        $totals['cost_per_lead'],
                        0
                    ),

                'Cost / Qualified' =>
                    'R ' .
                    number_format_i18n(
                        $totals['cost_per_qualified'],
                        0
                    ),

                'Pipeline Rate' =>
                    number_format_i18n(
                        $totals['pipeline_rate'],
                        1
                    ) .
                    '%',

                'MRR Efficiency' =>
                    number_format_i18n(
                        $totals['efficiency'],
                        1
                    ) .
                    '%',
            );

            foreach ( $summary_cards as $label => $value ) :

            ?>

                <div
                    style="
                        background:#fff;
                        border:1px solid #dcdcde;
                        border-radius:8px;
                        padding:16px;
                    "
                >

                    <div
                        style="
                            font-size:12px;
                            text-transform:uppercase;
                            color:#646970;
                            font-weight:600;
                        "
                    >
                        <?php echo esc_html( $label ); ?>
                    </div>

                    <div
                        style="
                            margin-top:6px;
                            font-size:22px;
                            font-weight:700;
                        "
                    >
                        <?php echo esc_html( $value ); ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div style="overflow-x:auto;">

            <table
                class="widefat striped"
                style="min-width:1400px;"
            >

                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Source</th>
                        <th>Medium</th>
                        <th>Leads</th>
                        <th>Qualified</th>
                        <th>Pipeline</th>
                        <th>Estimated MRR</th>
                        <th>Budget</th>
                        <th>CPL</th>
                        <th>Cost / Qualified</th>
                        <th>Pipeline Rate</th>
                        <th>MRR Efficiency</th>
                        <th>View</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ( $rows as $row ) : ?>

                    <?php
                    $p = $row['performance'];
                    ?>

                    <tr>

                        <td>
                            <strong>
                                <?php
                                echo esc_html(
                                    $row['title']
                                        ? $row['title']
                                        : '(Untitled Campaign)'
                                );
                                ?>
                            </strong>
                        </td>

                        <td>
                            <?php echo esc_html( $row['source'] ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $row['medium'] ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $p['leads'] ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $p['qualified'] ); ?>
                        </td>

                        <td>
                            <?php echo esc_html( $p['pipeline_leads'] ); ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                'R ' .
                                number_format_i18n(
                                    $p['estimated_mrr'],
                                    0
                                )
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                'R ' .
                                number_format_i18n(
                                    $p['budget'],
                                    0
                                )
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                'R ' .
                                number_format_i18n(
                                    $p['cost_per_lead'],
                                    0
                                )
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                'R ' .
                                number_format_i18n(
                                    $p['cost_per_qualified'],
                                    0
                                )
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                number_format_i18n(
                                    $p['pipeline_rate'],
                                    1
                                ) .
                                '%'
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo esc_html(
                                number_format_i18n(
                                    $p['mrr_budget_efficiency'],
                                    1
                                ) .
                                '%'
                            );
                            ?>
                        </td>

                        <td>
                            <a
                                href="<?php echo esc_url(
                                    get_edit_post_link(
                                        $row['id'],
                                        'url'
                                    )
                                ); ?>"
                            >
                                View Campaign
                            </a>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php
}


/**
 * Acquisition Intelligence V3.
 *
 * Produces commercial leaders from registered campaigns
 * and campaign creative/audience attribution.
 */
function softkom_v3_acquisition_intelligence_v3() {

    $result = array(
        'top_source'           => null,
        'top_campaign'         => null,
        'top_creative'         => null,
        'top_audience'         => null,
        'highest_mrr_campaign' => null,
        'best_efficiency'      => null,
    );

    if (
        ! function_exists( 'softkom_v3_campaign_performance' )
    ) {
        return $result;
    }

    $campaign_ids = get_posts(
        array(
            'post_type'      => 'softkom_campaign',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    if ( empty( $campaign_ids ) ) {
        return $result;
    }

    $sources   = array();
    $campaigns = array();
    $creatives = array();
    $audiences = array();


    foreach ( $campaign_ids as $campaign_id ) {

        $performance =
            softkom_v3_campaign_performance(
                $campaign_id
            );

        $title = get_the_title( $campaign_id );

        if ( '' === trim( (string) $title ) ) {
            $title = '(Untitled Campaign)';
        }

        $source = trim(
            (string) get_post_meta(
                $campaign_id,
                '_softkom_campaign_utm_source',
                true
            )
        );

        if ( '' === $source ) {
            $source = 'direct';
        }

        $campaign_key = trim(
            (string) get_post_meta(
                $campaign_id,
                '_softkom_campaign_utm_campaign',
                true
            )
        );

        $campaign_row = array(
            'id'          => $campaign_id,
            'name'        => $title,
            'campaign'    => $campaign_key,
            'source'      => $source,
            'leads'       => isset( $performance['leads'] )
                ? (int) $performance['leads']
                : 0,
            'qualified'   => isset( $performance['qualified'] )
                ? (int) $performance['qualified']
                : 0,
            'pipeline'    => isset( $performance['pipeline_leads'] )
                ? (int) $performance['pipeline_leads']
                : 0,
            'mrr'         => isset( $performance['estimated_mrr'] )
                ? (float) $performance['estimated_mrr']
                : 0,
            'budget'      => isset( $performance['budget'] )
                ? (float) $performance['budget']
                : 0,
            'efficiency'  => isset( $performance['mrr_budget_efficiency'] )
                ? (float) $performance['mrr_budget_efficiency']
                : 0,
        );

        $campaigns[] = $campaign_row;


        if ( ! isset( $sources[ $source ] ) ) {

            $sources[ $source ] = array(
                'name'      => $source,
                'leads'     => 0,
                'qualified' => 0,
                'pipeline'  => 0,
                'mrr'       => 0,
            );
        }

        $sources[ $source ]['leads'] +=
            $campaign_row['leads'];

        $sources[ $source ]['qualified'] +=
            $campaign_row['qualified'];

        $sources[ $source ]['pipeline'] +=
            $campaign_row['pipeline'];

        $sources[ $source ]['mrr'] +=
            $campaign_row['mrr'];


        if (
            function_exists(
                'softkom_v3_campaign_attribution_breakdown'
            )
        ) {

            $breakdown =
                softkom_v3_campaign_attribution_breakdown(
                    $campaign_id
                );

            if ( is_array( $breakdown ) ) {

                foreach ( $breakdown as $row ) {

                    $creative = isset( $row['content'] )
                        ? trim( (string) $row['content'] )
                        : '';

                    $audience = isset( $row['term'] )
                        ? trim( (string) $row['term'] )
                        : '';

                    $row_mrr = isset( $row['estimated_mrr'] )
                        ? (float) $row['estimated_mrr']
                        : 0;

                    $row_leads = isset( $row['leads'] )
                        ? (int) $row['leads']
                        : 0;

                    $row_qualified = isset( $row['qualified'] )
                        ? (int) $row['qualified']
                        : 0;

                    $row_pipeline = isset( $row['pipeline_leads'] )
                        ? (int) $row['pipeline_leads']
                        : 0;


                    if ( '' !== $creative ) {

                        if ( ! isset( $creatives[ $creative ] ) ) {

                            $creatives[ $creative ] = array(
                                'name'      => $creative,
                                'leads'     => 0,
                                'qualified' => 0,
                                'pipeline'  => 0,
                                'mrr'       => 0,
                            );
                        }

                        $creatives[ $creative ]['leads'] +=
                            $row_leads;

                        $creatives[ $creative ]['qualified'] +=
                            $row_qualified;

                        $creatives[ $creative ]['pipeline'] +=
                            $row_pipeline;

                        $creatives[ $creative ]['mrr'] +=
                            $row_mrr;
                    }


                    if ( '' !== $audience ) {

                        if ( ! isset( $audiences[ $audience ] ) ) {

                            $audiences[ $audience ] = array(
                                'name'      => $audience,
                                'leads'     => 0,
                                'qualified' => 0,
                                'pipeline'  => 0,
                                'mrr'       => 0,
                            );
                        }

                        $audiences[ $audience ]['leads'] +=
                            $row_leads;

                        $audiences[ $audience ]['qualified'] +=
                            $row_qualified;

                        $audiences[ $audience ]['pipeline'] +=
                            $row_pipeline;

                        $audiences[ $audience ]['mrr'] +=
                            $row_mrr;
                    }
                }
            }
        }
    }


    /*
     * Commercial ranking:
     * MRR first, qualified leads second, total leads third.
     */
    $commercial_sort = function ( $a, $b ) {

        if ( $a['mrr'] !== $b['mrr'] ) {
            return $b['mrr'] <=> $a['mrr'];
        }

        if ( $a['qualified'] !== $b['qualified'] ) {
            return $b['qualified'] <=> $a['qualified'];
        }

        return $b['leads'] <=> $a['leads'];
    };


    if ( ! empty( $sources ) ) {

        $source_rows = array_values( $sources );

        usort(
            $source_rows,
            $commercial_sort
        );

        $result['top_source'] = $source_rows[0];
    }


    if ( ! empty( $campaigns ) ) {

        $commercial_campaigns = $campaigns;

        usort(
            $commercial_campaigns,
            $commercial_sort
        );

        $result['top_campaign'] =
            $commercial_campaigns[0];

        $result['highest_mrr_campaign'] =
            $commercial_campaigns[0];


        $efficiency_campaigns = array_filter(
            $campaigns,
            function ( $row ) {
                return (
                    $row['budget'] > 0 &&
                    $row['mrr'] > 0
                );
            }
        );

        if ( ! empty( $efficiency_campaigns ) ) {

            $efficiency_campaigns =
                array_values(
                    $efficiency_campaigns
                );

            usort(
                $efficiency_campaigns,
                function ( $a, $b ) {

                    if (
                        $a['efficiency'] ===
                        $b['efficiency']
                    ) {
                        return $b['mrr'] <=> $a['mrr'];
                    }

                    return
                        $b['efficiency'] <=>
                        $a['efficiency'];
                }
            );

            $result['best_efficiency'] =
                $efficiency_campaigns[0];
        }
    }


    if ( ! empty( $creatives ) ) {

        $creative_rows =
            array_values( $creatives );

        usort(
            $creative_rows,
            $commercial_sort
        );

        $result['top_creative'] =
            $creative_rows[0];
    }


    if ( ! empty( $audiences ) ) {

        $audience_rows =
            array_values( $audiences );

        usort(
            $audience_rows,
            $commercial_sort
        );

        $result['top_audience'] =
            $audience_rows[0];
    }


    return $result;
}


/**
 * Render Acquisition Intelligence V3.
 */
function softkom_v3_render_acquisition_intelligence_v3() {

    $leaders =
        softkom_v3_acquisition_intelligence_v3();

    $cards = array();


    $cards[] = array(
        'label' => 'Top Traffic Source',
        'data'  => isset( $leaders['top_source'] )
            ? $leaders['top_source']
            : null,
        'type'  => 'commercial',
    );

    $cards[] = array(
        'label' => 'Top Campaign',
        'data'  => isset( $leaders['top_campaign'] )
            ? $leaders['top_campaign']
            : null,
        'type'  => 'commercial',
    );

    $cards[] = array(
        'label' => 'Top Creative',
        'data'  => isset( $leaders['top_creative'] )
            ? $leaders['top_creative']
            : null,
        'type'  => 'commercial',
    );

    $cards[] = array(
        'label' => 'Top Audience',
        'data'  => isset( $leaders['top_audience'] )
            ? $leaders['top_audience']
            : null,
        'type'  => 'commercial',
    );

    $cards[] = array(
        'label' => 'Highest MRR Campaign',
        'data'  => isset( $leaders['highest_mrr_campaign'] )
            ? $leaders['highest_mrr_campaign']
            : null,
        'type'  => 'commercial',
    );

    $cards[] = array(
        'label' => 'Best Efficiency',
        'data'  => isset( $leaders['best_efficiency'] )
            ? $leaders['best_efficiency']
            : null,
        'type'  => 'efficiency',
    );

    ?>

    <div
        style="
            margin-top:32px;
            padding-top:24px;
            border-top:1px solid #dcdcde;
        "
    >

        <h2>Acquisition Intelligence</h2>

        <p class="description">
            Commercial leaders ranked from campaign,
            attribution, pipeline and recurring revenue data.
        </p>


        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(210px,1fr));
                gap:14px;
                max-width:1400px;
                margin:18px 0 8px;
            "
        >

            <?php foreach ( $cards as $card ) : ?>

                <?php

                $row = $card['data'];

                $name = (
                    is_array( $row ) &&
                    isset( $row['name'] )
                )
                    ? $row['name']
                    : 'No data yet';

                ?>

                <div
                    style="
                        background:#fff;
                        border:1px solid #dcdcde;
                        border-radius:8px;
                        padding:18px;
                    "
                >

                    <div
                        style="
                            font-size:12px;
                            text-transform:uppercase;
                            font-weight:600;
                            color:#646970;
                        "
                    >
                        <?php echo esc_html( $card['label'] ); ?>
                    </div>


                    <div
                        style="
                            margin-top:8px;
                            font-size:20px;
                            font-weight:700;
                            word-break:break-word;
                        "
                    >
                        <?php echo esc_html( $name ); ?>
                    </div>


                    <?php if ( is_array( $row ) ) : ?>

                        <?php if ( 'efficiency' === $card['type'] ) : ?>

                            <div style="margin-top:8px;color:#50575e;">
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        isset( $row['efficiency'] )
                                            ? $row['efficiency']
                                            : 0,
                                        1
                                    ) .
                                    '% MRR efficiency'
                                );
                                ?>
                            </div>

                            <div style="margin-top:4px;color:#646970;">
                                <?php
                                echo esc_html(
                                    'R ' .
                                    number_format_i18n(
                                        isset( $row['mrr'] )
                                            ? $row['mrr']
                                            : 0,
                                        0
                                    ) .
                                    ' estimated MRR'
                                );
                                ?>
                            </div>

                        <?php else : ?>

                            <div style="margin-top:8px;color:#50575e;">
                                <?php
                                echo esc_html(
                                    'R ' .
                                    number_format_i18n(
                                        isset( $row['mrr'] )
                                            ? $row['mrr']
                                            : 0,
                                        0
                                    ) .
                                    ' estimated MRR'
                                );
                                ?>
                            </div>

                            <div style="margin-top:4px;color:#646970;">
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        isset( $row['qualified'] )
                                            ? $row['qualified']
                                            : 0
                                    ) .
                                    ' qualified / ' .
                                    number_format_i18n(
                                        isset( $row['leads'] )
                                            ? $row['leads']
                                            : 0
                                    ) .
                                    ' leads'
                                );
                                ?>
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

    <?php
}
