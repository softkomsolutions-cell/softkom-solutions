<?php
/**
 * Softkom Funnel V2 - Recurring Revenue Recommendation Engine V1.
 *
 * Converts assessment intelligence into commercial recommendations.
 *
 * IMPORTANT:
 * Recommendations do not automatically overwrite sales-managed
 * pipeline fields. Human sales control remains authoritative.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Softkom recurring service catalogue.
 *
 * Pricing is indicative for internal sales guidance only.
 */
function softkom_v3_recurring_service_catalogue() {

    return array(

        'managed_ai' => array(
            'name'           => 'Managed AI Operations',
            'implementation' => 'AI Automation Implementation',
            'mrr_min'        => 7500,
            'mrr_max'        => 15000,
        ),

        'managed_automation' => array(
            'name'           => 'Managed Business Automation',
            'implementation' => 'Business Process Automation Implementation',
            'mrr_min'        => 5000,
            'mrr_max'        => 12000,
        ),

        'managed_systems' => array(
            'name'           => 'Managed Business Systems',
            'implementation' => 'Business Systems Implementation',
            'mrr_min'        => 4500,
            'mrr_max'        => 10000,
        ),

        'managed_compliance' => array(
            'name'           => 'Managed Compliance Operations',
            'implementation' => 'Compliance Platform Implementation',
            'mrr_min'        => 5000,
            'mrr_max'        => 12000,
        ),

        'managed_growth' => array(
            'name'           => 'Managed Growth Automation',
            'implementation' => 'Lead Generation & Sales Automation Implementation',
            'mrr_min'        => 6000,
            'mrr_max'        => 15000,
        ),

        'managed_digital' => array(
            'name'           => 'Managed Digital Platform',
            'implementation' => 'Digital Platform Implementation',
            'mrr_min'        => 3500,
            'mrr_max'        => 8500,
        ),

    );
}


/**
 * Convert a value into searchable recommendation text.
 */
function softkom_v3_recurring_flatten_text( $value ) {

    if ( is_scalar( $value ) ) {
        return (string) $value;
    }

    if ( ! is_array( $value ) ) {
        return '';
    }

    $parts = array();

    array_walk_recursive(
        $value,
        function ( $item ) use ( &$parts ) {

            if ( is_scalar( $item ) ) {
                $parts[] = (string) $item;
            }
        }
    );

    return implode( ' ', $parts );
}


/**
 * Determine the best recurring service family.
 */
function softkom_v3_recurring_service_key(
    $recommendations,
    $opportunities,
    $ai_score
) {

    $search_text = strtolower(
        softkom_v3_recurring_flatten_text(
            array(
                $recommendations,
                $opportunities,
            )
        )
    );


    /*
     * Compliance signals.
     */
    if (
        false !== strpos( $search_text, 'compliance' ) ||
        false !== strpos( $search_text, 'governance' ) ||
        false !== strpos( $search_text, 'risk' )
    ) {
        return 'managed_compliance';
    }


    /*
     * Lead generation / sales signals.
     */
    if (
        false !== strpos( $search_text, 'lead engine' ) ||
        false !== strpos( $search_text, 'lead generation' ) ||
        false !== strpos( $search_text, 'sales automation' ) ||
        false !== strpos( $search_text, 'crm' )
    ) {
        return 'managed_growth';
    }


    /*
     * AI signals.
     */
    if (
        false !== strpos( $search_text, 'artificial intelligence' ) ||
        false !== strpos( $search_text, ' ai ' ) ||
        false !== strpos( $search_text, 'ai customer' ) ||
        false !== strpos( $search_text, 'ai automation' ) ||
        false !== strpos( $search_text, 'agent' ) ||
        (int) $ai_score >= 65
    ) {
        return 'managed_ai';
    }


    /*
     * Process automation / integration signals.
     */
    if (
        false !== strpos( $search_text, 'automation' ) ||
        false !== strpos( $search_text, 'integration' ) ||
        false !== strpos( $search_text, 'workflow' ) ||
        false !== strpos( $search_text, 'process' )
    ) {
        return 'managed_automation';
    }


    /*
     * Website / platform signals.
     */
    if (
        false !== strpos( $search_text, 'website' ) ||
        false !== strpos( $search_text, 'ecommerce' ) ||
        false !== strpos( $search_text, 'digital platform' )
    ) {
        return 'managed_digital';
    }


    return 'managed_systems';
}


/**
 * Calculate a recurring revenue recommendation for a lead.
 */
function softkom_v3_calculate_recurring_recommendation( $post_id ) {

    $catalogue = softkom_v3_recurring_service_catalogue();

    $recommendations = function_exists(
        'softkom_v3_lead_json_meta'
    )
        ? softkom_v3_lead_json_meta(
            $post_id,
            '_softkom_recommendations'
        )
        : array();

    $opportunities = function_exists(
        'softkom_v3_lead_json_meta'
    )
        ? softkom_v3_lead_json_meta(
            $post_id,
            '_softkom_priority_opportunities'
        )
        : array();


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

    $temperature = strtoupper(
        (string) get_post_meta(
            $post_id,
            '_softkom_lead_temperature',
            true
        )
    );

    $risk_level = strtoupper(
        (string) get_post_meta(
            $post_id,
            '_softkom_security_risk_level',
            true
        )
    );


    $service_key = softkom_v3_recurring_service_key(
        $recommendations,
        $opportunities,
        $ai_score
    );

    if ( ! isset( $catalogue[ $service_key ] ) ) {
        $service_key = 'managed_systems';
    }

    $service = $catalogue[ $service_key ];


    /*
     * Commercial readiness.
     */
    $readiness_score = (int) round(
        (
            $commercial_score +
            $purchase_score +
            $lead_score
        ) / 3
    );


    if ( $readiness_score >= 75 ) {

        $readiness = 'High';

    } elseif ( $readiness_score >= 55 ) {

        $readiness = 'Medium';

    } else {

        $readiness = 'Developing';
    }


    /*
     * Recommended pipeline stage.
     *
     * Security always takes precedence over commercial scoring.
     */
    if (
        'HIGH RISK' === $risk_level ||
        'BLOCK' === $risk_level ||
        'REVIEW' === $risk_level
    ) {

        $recommended_stage = 'nurture';
        $next_action = 'Review and validate the lead before sales outreach.';

    } elseif (
        'HOT' === $temperature ||
        $lead_score >= 75 ||
        $purchase_score >= 75
    ) {

        $recommended_stage = 'qualified';
        $next_action = 'Book a Solution Mapping Session and validate the recommended managed service.';

    } elseif (
        $lead_score >= 55 ||
        $commercial_score >= 60
    ) {

        $recommended_stage = 'contacted';
        $next_action = 'Begin personalised outreach and qualify the recurring service opportunity.';

    } else {

        $recommended_stage = 'nurture';
        $next_action = 'Place the lead into a nurture sequence and continue educating the prospect.';
    }


    /*
     * Resolve the commercial package from the central catalogue.
     *
     * The legacy recurring catalogue remains available as a
     * compatibility fallback, but pricing/package selection now
     * comes from Commercial Catalogue V1 when available.
     */
    $commercial_offer = array();

    if ( function_exists( 'softkom_v3_commercial_offer' ) ) {

        $commercial_offer =
            softkom_v3_commercial_offer(
                $service_key,
                $readiness_score
            );
    }


    return array(
        'service_key' => $service_key,

        'implementation_offer' =>
            ! empty( $commercial_offer['implementation_name'] )
                ? $commercial_offer['implementation_name']
                : $service['implementation'],

        'managed_service' =>
            ! empty( $commercial_offer['service_name'] )
                ? $commercial_offer['service_name']
                : $service['name'],

        'suggested_mrr_min' =>
            isset( $commercial_offer['mrr_min'] )
                ? (int) $commercial_offer['mrr_min']
                : (int) $service['mrr_min'],

        'suggested_mrr_max' =>
            isset( $commercial_offer['mrr_max'] )
                ? (int) $commercial_offer['mrr_max']
                : (int) $service['mrr_max'],

        'commercial_plan_key' =>
            isset( $commercial_offer['plan_key'] )
                ? $commercial_offer['plan_key']
                : '',

        'commercial_plan_name' =>
            isset( $commercial_offer['plan_name'] )
                ? $commercial_offer['plan_name']
                : '',

        'commercial_monthly' =>
            isset( $commercial_offer['monthly'] )
                ? (int) $commercial_offer['monthly']
                : 0,

        'implementation_price_from' =>
            isset( $commercial_offer['implementation_price_from'] )
                ? (int) $commercial_offer['implementation_price_from']
                : 0,

        'commercial_category' =>
            isset( $commercial_offer['category'] )
                ? $commercial_offer['category']
                : '',

        'commercial_readiness'       => $readiness,
        'commercial_readiness_score' => $readiness_score,
        'recommended_stage'          => $recommended_stage,
        'next_action'                => $next_action,
    );
}


/**
 * Save calculated commercial intelligence to the lead.
 *
 * This saves recommendation data only.
 * It does NOT overwrite salesperson-managed fields.
 */
function softkom_v3_refresh_recurring_recommendation( $post_id ) {

    if (
        'softkom_lead' !== get_post_type( $post_id )
    ) {
        return array();
    }

    $recommendation =
        softkom_v3_calculate_recurring_recommendation(
            $post_id
        );

    update_post_meta(
        $post_id,
        '_softkom_recurring_recommendation',
        wp_json_encode( $recommendation )
    );

    return $recommendation;
}


/**
 * Render recurring revenue intelligence.
 */
function softkom_v3_render_recurring_revenue_box( $post ) {

    $recommendation =
        softkom_v3_refresh_recurring_recommendation(
            $post->ID
        );

    if ( empty( $recommendation ) ) {
        echo '<p>No commercial recommendation available.</p>';
        return;
    }

    ?>

    <div style="font-size:14px;">

        <p>
            <strong>Recommended Implementation</strong><br>
            <?php
            echo esc_html(
                $recommendation['implementation_offer']
            );
            ?>
        </p>

        <p>
            <strong>Recommended Managed Service</strong><br>
            <?php
            echo esc_html(
                $recommendation['managed_service']
            );
            ?>
        </p>
        <?php if ( ! empty( $recommendation['commercial_plan_name'] ) ) : ?>

            <p>
                <strong>Recommended Commercial Plan</strong><br>
                <?php
                echo esc_html(
                    $recommendation['commercial_plan_name']
                );
                ?>
            </p>

            <p>
                <strong>Recommended Monthly Price</strong><br>

                <span style="font-size:18px;font-weight:700;">
                    R <?php
                    echo esc_html(
                        number_format_i18n(
                            $recommendation['commercial_monthly'],
                            0
                        )
                    );
                    ?>
                    / month
                </span>
            </p>

        <?php endif; ?>


        <?php if ( ! empty( $recommendation['implementation_price_from'] ) ) : ?>

            <p>
                <strong>Implementation Price From</strong><br>

                R <?php
                echo esc_html(
                    number_format_i18n(
                        $recommendation['implementation_price_from'],
                        0
                    )
                );
                ?>
            </p>

        <?php endif; ?>


        <p>
            <strong>Available Monthly Range</strong><br>

            <span style="font-size:18px;font-weight:700;">
                R <?php
                echo esc_html(
                    number_format_i18n(
                        $recommendation['suggested_mrr_min'],
                        0
                    )
                );
                ?>

                -

                R <?php
                echo esc_html(
                    number_format_i18n(
                        $recommendation['suggested_mrr_max'],
                        0
                    )
                );
                ?>
                / month
            </span>
        </p>

        <p>
            <strong>Commercial Readiness</strong><br>

            <?php
            echo esc_html(
                $recommendation['commercial_readiness']
            );
            ?>

            (
            <?php
            echo esc_html(
                $recommendation['commercial_readiness_score']
            );
            ?>/100 )
        </p>

        <p>
            <strong>Recommended Pipeline Stage</strong><br>

            <?php
            echo esc_html(
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $recommendation['recommended_stage']
                    )
                )
            );
            ?>
        </p>

        <div
            style="
                margin-top:15px;
                padding:12px;
                background:#eff6ff;
                border-left:4px solid #2563eb;
            "
        >

            <strong>Next Sales Action</strong>

            <p style="margin-bottom:0;">
                <?php
                echo esc_html(
                    $recommendation['next_action']
                );
                ?>
            </p>

        </div>

        <p style="margin-top:18px;">

            <a
                href="<?php echo esc_url( softkom_v3_recurring_apply_url( $post->ID ) ); ?>"
                class="button button-primary"
            >
                Apply Recommendation to Pipeline
            </a>

        </p>

        <p
            style="
                margin-top:12px;
                color:#64748b;
                font-size:12px;
            "
        >
            Internal recommendation only. Sales can override the
            assigned offer, pipeline stage and estimated MRR.
        </p>

    </div>

    <?php
}


/**
 * Register recurring revenue intelligence panel.
 */
function softkom_v3_register_recurring_revenue_box() {

    add_meta_box(
        'softkom_recurring_revenue',
        'Recurring Revenue Opportunity',
        'softkom_v3_render_recurring_revenue_box',
        'softkom_lead',
        'normal',
        'high'
    );
}

add_action(
    'add_meta_boxes_softkom_lead',
    'softkom_v3_register_recurring_revenue_box'
);

/**
 * Build the admin URL for applying a recurring recommendation.
 */
function softkom_v3_recurring_apply_url( $post_id ) {

    return wp_nonce_url(
        add_query_arg(
            array(
                'action'  => 'softkom_apply_recurring_recommendation',
                'post_id' => (int) $post_id,
            ),
            admin_url( 'admin-post.php' )
        ),
        'softkom_apply_recurring_' . (int) $post_id
    );
}


/**
 * Apply the calculated recurring recommendation to the sales pipeline.
 *
 * This only runs after an explicit admin action.
 */
function softkom_v3_apply_recurring_recommendation() {

    $post_id = isset( $_GET['post_id'] )
        ? absint( $_GET['post_id'] )
        : 0;

    if (
        ! $post_id ||
        'softkom_lead' !== get_post_type( $post_id )
    ) {
        wp_die(
            esc_html__( 'Invalid Softkom lead.', 'softkom-v3' )
        );
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        wp_die(
            esc_html__(
                'You do not have permission to update this lead.',
                'softkom-v3'
            )
        );
    }

    check_admin_referer(
        'softkom_apply_recurring_' . $post_id
    );


    $recommendation =
        softkom_v3_calculate_recurring_recommendation(
            $post_id
        );

    if ( empty( $recommendation ) ) {

        wp_safe_redirect(
            add_query_arg(
                'softkom_recurring_apply',
                'failed',
                get_edit_post_link(
                    $post_id,
                    'url'
                )
            )
        );

        exit;
    }


    /*
     * Existing salesperson-managed values.
     */
    $old_stage = get_post_meta(
        $post_id,
        '_softkom_pipeline_stage',
        true
    );

    $old_offer = get_post_meta(
        $post_id,
        '_softkom_assigned_offer',
        true
    );

    $old_mrr = get_post_meta(
        $post_id,
        '_softkom_estimated_mrr',
        true
    );


    /*
     * Recommended pipeline values.
     */
    $stage = isset(
        $recommendation['recommended_stage']
    )
        ? sanitize_key(
            $recommendation['recommended_stage']
        )
        : 'new';


    if ( function_exists( 'softkom_v3_lead_pipeline_stages' ) ) {

        $valid_stages =
            softkom_v3_lead_pipeline_stages();

        if ( ! isset( $valid_stages[ $stage ] ) ) {
            $stage = 'new';
        }
    }


    $offer = isset(
        $recommendation['managed_service']
    )
        ? sanitize_text_field(
            $recommendation['managed_service']
        )
        : '';


    $mrr_min = isset(
        $recommendation['suggested_mrr_min']
    )
        ? (float) $recommendation['suggested_mrr_min']
        : 0;

    $mrr_max = isset(
        $recommendation['suggested_mrr_max']
    )
        ? (float) $recommendation['suggested_mrr_max']
        : 0;


    /*
     * Use the midpoint as an internal working estimate.
     */
    $estimated_mrr = (float) round(
        (
            $mrr_min +
            $mrr_max
        ) / 2
    );


    $next_action = isset(
        $recommendation['next_action']
    )
        ? sanitize_textarea_field(
            $recommendation['next_action']
        )
        : '';


    /*
     * Apply recommendation.
     */
    update_post_meta(
        $post_id,
        '_softkom_pipeline_stage',
        $stage
    );

    update_post_meta(
        $post_id,
        '_softkom_assigned_offer',
        $offer
    );

    update_post_meta(
        $post_id,
        '_softkom_estimated_mrr',
        $estimated_mrr
    );


    /*
     * Only create a follow-up date if sales has not
     * already selected one.
     */
    $existing_follow_up = get_post_meta(
        $post_id,
        '_softkom_follow_up_date',
        true
    );

    if ( '' === $existing_follow_up ) {

        update_post_meta(
            $post_id,
            '_softkom_follow_up_date',
            gmdate(
                'Y-m-d',
                strtotime( '+1 day' )
            )
        );
    }


    /*
     * Append the recommendation to internal notes instead
     * of deleting existing salesperson notes.
     */
    if ( '' !== $next_action ) {

        $existing_notes = get_post_meta(
            $post_id,
            '_softkom_internal_notes',
            true
        );

        $note = 'Recommended next action: ' .
            $next_action;

        if (
            '' !== $existing_notes &&
            false === strpos(
                $existing_notes,
                $note
            )
        ) {

            $new_notes =
                rtrim( $existing_notes ) .
                "\n\n" .
                $note;

        } elseif ( '' === $existing_notes ) {

            $new_notes = $note;

        } else {

            $new_notes = $existing_notes;
        }

        update_post_meta(
            $post_id,
            '_softkom_internal_notes',
            $new_notes
        );
    }


    /*
     * Activity history.
     */
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

        'event' => 'recommendation_applied',

        'from' => $old_stage,

        'to' => $stage,

        'offer_before' => $old_offer,

        'offer_after' => $offer,

        'mrr_before' => $old_mrr,

        'mrr_after' => $estimated_mrr,

        'user_id' => get_current_user_id(),
    );

    update_post_meta(
        $post_id,
        '_softkom_pipeline_history',
        $history
    );


    wp_safe_redirect(
        add_query_arg(
            'softkom_recurring_apply',
            'success',
            get_edit_post_link(
                $post_id,
                'url'
            )
        )
    );

    exit;
}

add_action(
    'admin_post_softkom_apply_recurring_recommendation',
    'softkom_v3_apply_recurring_recommendation'
);


/**
 * Confirmation notice after applying a recommendation.
 */
function softkom_v3_recurring_pipeline_notice() {

    if (
        ! isset( $_GET['softkom_recurring_apply'] )
    ) {
        return;
    }

    $status = sanitize_key(
        wp_unslash(
            $_GET['softkom_recurring_apply']
        )
    );

    if ( 'success' === $status ) {

        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Softkom recommendation applied to the sales pipeline.</strong></p>';
        echo '</div>';

    } elseif ( 'failed' === $status ) {

        echo '<div class="notice notice-error">';
        echo '<p>Softkom could not apply the recurring revenue recommendation.</p>';
        echo '</div>';
    }
}

add_action(
    'admin_notices',
    'softkom_v3_recurring_pipeline_notice'
);



/**
 * Automatically commercialize a newly stored HOT sales-eligible lead.
 *
 * Only fills pipeline values that are currently empty.
 */
function softkom_v3_auto_pipeline_hot_lead(
    $lead_id,
    $result,
    $security
) {

    if (
        ! $lead_id ||
        'softkom_lead' !== get_post_type( $lead_id )
    ) {
        return;
    }

    if (
        get_post_meta(
            $lead_id,
            '_softkom_recurring_auto_applied',
            true
        )
    ) {
        return;
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

    if ( 'HOT' !== $temperature ) {
        return;
    }

    $routing = get_post_meta(
        $lead_id,
        '_softkom_lead_routing',
        true
    );

    if ( is_string( $routing ) ) {
        $routing = json_decode(
            $routing,
            true
        );
    }

    if (
        ! is_array( $routing ) ||
        empty( $routing['sales_eligible'] )
    ) {
        return;
    }

    $risk_level = isset( $security['risk_level'] )
        ? strtoupper(
            trim(
                (string) $security['risk_level']
            )
        )
        : '';

    if (
        in_array(
            $risk_level,
            array(
                'BLOCK',
                'HIGH RISK',
            ),
            true
        )
    ) {
        return;
    }

    if (
        ! function_exists(
            'softkom_v3_calculate_recurring_recommendation'
        )
    ) {
        return;
    }

    $recommendation =
        softkom_v3_calculate_recurring_recommendation(
            $lead_id
        );

    if ( empty( $recommendation ) ) {
        return;
    }

    update_post_meta(
        $lead_id,
        '_softkom_recurring_recommendation',
        wp_json_encode( $recommendation )
    );

    $stage = isset(
        $recommendation['recommended_stage']
    )
        ? sanitize_key(
            $recommendation['recommended_stage']
        )
        : 'new';

    if (
        function_exists(
            'softkom_v3_lead_pipeline_stages'
        )
    ) {
        $valid_stages =
            softkom_v3_lead_pipeline_stages();

        if ( ! isset( $valid_stages[ $stage ] ) ) {
            $stage = 'new';
        }
    }

    $offer = isset(
        $recommendation['managed_service']
    )
        ? sanitize_text_field(
            $recommendation['managed_service']
        )
        : '';

    $mrr_min = isset(
        $recommendation['suggested_mrr_min']
    )
        ? (float) $recommendation['suggested_mrr_min']
        : 0;

    $mrr_max = isset(
        $recommendation['suggested_mrr_max']
    )
        ? (float) $recommendation['suggested_mrr_max']
        : 0;

    $estimated_mrr = (float) round(
        (
            $mrr_min +
            $mrr_max
        ) / 2
    );

    $old_stage = get_post_meta(
        $lead_id,
        '_softkom_pipeline_stage',
        true
    );

    $old_offer = get_post_meta(
        $lead_id,
        '_softkom_assigned_offer',
        true
    );

    $old_mrr = get_post_meta(
        $lead_id,
        '_softkom_estimated_mrr',
        true
    );

    if ( '' === $old_stage ) {
        update_post_meta(
            $lead_id,
            '_softkom_pipeline_stage',
            $stage
        );
    }

    if ( '' === $old_offer ) {
        update_post_meta(
            $lead_id,
            '_softkom_assigned_offer',
            $offer
        );
    }

    if ( '' === $old_mrr ) {
        update_post_meta(
            $lead_id,
            '_softkom_estimated_mrr',
            $estimated_mrr
        );
    }

    $existing_follow_up = get_post_meta(
        $lead_id,
        '_softkom_follow_up_date',
        true
    );

    if ( '' === $existing_follow_up ) {
        update_post_meta(
            $lead_id,
            '_softkom_follow_up_date',
            gmdate(
                'Y-m-d',
                strtotime( '+1 day' )
            )
        );
    }

    $next_action = isset(
        $recommendation['next_action']
    )
        ? sanitize_textarea_field(
            $recommendation['next_action']
        )
        : '';

    if ( '' !== $next_action ) {

        $existing_notes = get_post_meta(
            $lead_id,
            '_softkom_internal_notes',
            true
        );

        $note =
            'Recommended next action: ' .
            $next_action;

        if ( '' === $existing_notes ) {

            update_post_meta(
                $lead_id,
                '_softkom_internal_notes',
                $note
            );

        } elseif (
            false === strpos(
                $existing_notes,
                $note
            )
        ) {

            update_post_meta(
                $lead_id,
                '_softkom_internal_notes',
                rtrim( $existing_notes ) .
                "\n\n" .
                $note
            );
        }
    }

    $history = get_post_meta(
        $lead_id,
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
        'event' => 'recommendation_auto_applied',
        'from' => $old_stage,
        'to' => '' === $old_stage
            ? $stage
            : $old_stage,
        'offer_before' => $old_offer,
        'offer_after' => '' === $old_offer
            ? $offer
            : $old_offer,
        'mrr_before' => $old_mrr,
        'mrr_after' => '' === $old_mrr
            ? $estimated_mrr
            : $old_mrr,
        'user_id' => 0,
    );

    update_post_meta(
        $lead_id,
        '_softkom_pipeline_history',
        $history
    );

    update_post_meta(
        $lead_id,
        '_softkom_recurring_auto_applied',
        current_time(
            'mysql',
            true
        )
    );
}

add_action(
    'softkom_v3_assessment_lead_stored',
    'softkom_v3_auto_pipeline_hot_lead',
    20,
    3
);
