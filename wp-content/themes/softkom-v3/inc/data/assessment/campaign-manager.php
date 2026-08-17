<?php
/**
 * Softkom Campaign Manager V1.
 *
 * Provides campaign records for traffic acquisition,
 * attribution and revenue measurement.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Register Softkom Campaigns.
 */
function softkom_v3_register_campaign_post_type() {

    $labels = array(
        'name'               => 'Campaigns',
        'singular_name'      => 'Campaign',
        'menu_name'          => 'Campaigns',
        'add_new'            => 'Add Campaign',
        'add_new_item'       => 'Add New Campaign',
        'edit_item'          => 'Edit Campaign',
        'new_item'           => 'New Campaign',
        'view_item'          => 'View Campaign',
        'search_items'       => 'Search Campaigns',
        'not_found'          => 'No campaigns found',
        'not_found_in_trash' => 'No campaigns found in Trash',
    );

    register_post_type(
        'softkom_campaign',
        array(
            'labels' => $labels,

            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_admin_bar'  => false,
            'show_in_nav_menus'  => false,
            'exclude_from_search'=> true,

            'menu_icon' => 'dashicons-megaphone',

            'supports' => array(
                'title',
            ),

            'capability_type' => 'post',

            'map_meta_cap' => true,

            'has_archive' => false,

            'rewrite' => false,

            'query_var' => false,
        )
    );
}

add_action(
    'init',
    'softkom_v3_register_campaign_post_type'
);



/**
 * Register campaign configuration box.
 */
function softkom_v3_register_campaign_details_box() {

    add_meta_box(
        'softkom_campaign_details',
        'Campaign Configuration',
        'softkom_v3_render_campaign_details_box',
        'softkom_campaign',
        'normal',
        'high'
    );
}

add_action(
    'add_meta_boxes_softkom_campaign',
    'softkom_v3_register_campaign_details_box'
);


/**
 * Build tracked campaign URL.
 */
function softkom_v3_campaign_tracked_url( $post_id ) {

    $landing_page = trim(
        (string) get_post_meta(
            $post_id,
            '_softkom_campaign_landing_page',
            true
        )
    );

    if ( '' === $landing_page ) {
        $landing_page = home_url(
            '/?pagename=assessment'
        );
    }

    $params = array(
        'utm_source' => get_post_meta(
            $post_id,
            '_softkom_campaign_utm_source',
            true
        ),
        'utm_medium' => get_post_meta(
            $post_id,
            '_softkom_campaign_utm_medium',
            true
        ),
        'utm_campaign' => get_post_meta(
            $post_id,
            '_softkom_campaign_utm_campaign',
            true
        ),
        'utm_content' => get_post_meta(
            $post_id,
            '_softkom_campaign_utm_content',
            true
        ),
        'utm_term' => get_post_meta(
            $post_id,
            '_softkom_campaign_utm_term',
            true
        ),
    );

    $params = array_filter(
        $params,
        function ( $value ) {
            return '' !== trim( (string) $value );
        }
    );

    return add_query_arg(
        $params,
        $landing_page
    );
}


/**
 * Render campaign configuration.
 */
function softkom_v3_render_campaign_details_box( $post ) {
wp_nonce_field(
        'softkom_v3_save_campaign_details',
        'softkom_v3_campaign_nonce'
    );

    $source = get_post_meta(
        $post->ID,
        '_softkom_campaign_utm_source',
        true
    );

    $medium = get_post_meta(
        $post->ID,
        '_softkom_campaign_utm_medium',
        true
    );

    $campaign_key = get_post_meta(
        $post->ID,
        '_softkom_campaign_utm_campaign',
        true
    );

    $content_value = get_post_meta(
        $post->ID,
        '_softkom_campaign_utm_content',
        true
    );

    $term = get_post_meta(
        $post->ID,
        '_softkom_campaign_utm_term',
        true
    );

    $landing_page = get_post_meta(
        $post->ID,
        '_softkom_campaign_landing_page',
        true
    );

    if ( '' === $landing_page ) {
        $landing_page = home_url(
            '/?pagename=assessment'
        );
    }

    $status = get_post_meta(
        $post->ID,
        '_softkom_campaign_status',
        true
    );

    if ( '' === $status ) {
        $status = 'draft';
    }

    $start_date = get_post_meta(
        $post->ID,
        '_softkom_campaign_start_date',
        true
    );

    $end_date = get_post_meta(
        $post->ID,
        '_softkom_campaign_end_date',
        true
    );

    $budget = get_post_meta(
        $post->ID,
        '_softkom_campaign_budget',
        true
    );

    /*
     * Build the displayed tracked URL from the values
     * already loaded for this campaign.
     */
    $tracked_params = array(
        'utm_source'   => $source,
        'utm_medium'   => $medium,
        'utm_campaign' => $campaign_key,
        'utm_content'  => $content_value,
        'utm_term'     => $term,
    );

    $tracked_params = array_filter(
        $tracked_params,
        function ( $value ) {
            return '' !== trim( (string) $value );
        }
    );

    $tracked_url = add_query_arg(
        $tracked_params,
        $landing_page
    );

    ?>

    <style>
        .softkom-campaign-grid {
            display:grid;
            grid-template-columns:
                repeat(auto-fit,minmax(280px,1fr));
            gap:18px;
            margin-top:12px;
        }

        .softkom-campaign-field label {
            display:block;
            font-weight:600;
            margin-bottom:6px;
        }

        .softkom-campaign-field input,
        .softkom-campaign-field select {
            width:100%;
        }

        .softkom-campaign-full {
            grid-column:1 / -1;
        }

        .softkom-tracked-url {
            font-family:monospace;
            background:#f6f7f7;
        }
    </style>

    <div class="softkom-campaign-grid">

        <div class="softkom-campaign-field">

            <label for="softkom_campaign_utm_source">
                Traffic Source
            </label>

            <input
                type="text"
                id="softkom_campaign_utm_source"
                name="softkom_campaign_utm_source"
                value="<?php echo esc_attr( $source ); ?>"
                placeholder="e.g. linkedin"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_utm_medium">
                Medium
            </label>

            <input
                type="text"
                id="softkom_campaign_utm_medium"
                name="softkom_campaign_utm_medium"
                value="<?php echo esc_attr( $medium ); ?>"
                placeholder="e.g. social"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_utm_campaign">
                Campaign Key
            </label>

            <input
                type="text"
                id="softkom_campaign_utm_campaign"
                name="softkom_campaign_utm_campaign"
                value="<?php echo esc_attr( $campaign_key ); ?>"
                placeholder="e.g. sme-automation-assessment"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_utm_content">
                Content / Creative
            </label>

            <input
                type="text"
                id="softkom_campaign_utm_content"
                name="softkom_campaign_utm_content"
                value="<?php echo esc_attr( $content_value ); ?>"
                placeholder="e.g. founder-post-01"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_utm_term">
                Term / Audience
            </label>

            <input
                type="text"
                id="softkom_campaign_utm_term"
                name="softkom_campaign_utm_term"
                value="<?php echo esc_attr( $term ); ?>"
                placeholder="e.g. south-africa-sme-owners"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_status">
                Campaign Status
            </label>

            <select
                id="softkom_campaign_status"
                name="softkom_campaign_status"
            >

                <?php

                $statuses = array(
                    'draft'     => 'Draft',
                    'planned'   => 'Planned',
                    'active'    => 'Active',
                    'paused'    => 'Paused',
                    'completed' => 'Completed',
                );

                foreach ( $statuses as $key => $label ) {

                    echo '<option value="' .
                        esc_attr( $key ) .
                        '" ' .
                        selected(
                            $status,
                            $key,
                            false
                        ) .
                        '>' .
                        esc_html( $label ) .
                        '</option>';
                }

                ?>

            </select>

        </div>


        <div class="softkom-campaign-field softkom-campaign-full">

            <label for="softkom_campaign_landing_page">
                Landing Page
            </label>

            <input
                type="url"
                id="softkom_campaign_landing_page"
                name="softkom_campaign_landing_page"
                value="<?php echo esc_attr( $landing_page ); ?>"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_start_date">
                Start Date
            </label>

            <input
                type="date"
                id="softkom_campaign_start_date"
                name="softkom_campaign_start_date"
                value="<?php echo esc_attr( $start_date ); ?>"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_end_date">
                End Date
            </label>

            <input
                type="date"
                id="softkom_campaign_end_date"
                name="softkom_campaign_end_date"
                value="<?php echo esc_attr( $end_date ); ?>"
            >

        </div>


        <div class="softkom-campaign-field">

            <label for="softkom_campaign_budget">
                Campaign Budget (R)
            </label>

            <input
                type="number"
                min="0"
                step="0.01"
                id="softkom_campaign_budget"
                name="softkom_campaign_budget"
                value="<?php echo esc_attr( $budget ); ?>"
                placeholder="0"
            >

        </div>


        <div class="softkom-campaign-field softkom-campaign-full">

            <label>
                Tracked URL
            </label>

            <input
                type="text"
                id="softkom_campaign_tracked_url"
                class="softkom-tracked-url"
                readonly
                value="<?php echo esc_attr( $tracked_url ); ?>"
            >

            <p class="description">
                Save or update the campaign to regenerate
                this URL from the latest campaign fields.
            </p>

        </div>

    </div>


    <script>
    document.addEventListener(
        'DOMContentLoaded',
        function () {

            const trackedUrl =
                document.getElementById(
                    'softkom_campaign_tracked_url'
                );

            const landing =
                document.getElementById(
                    'softkom_campaign_landing_page'
                );

            const fields = {
                utm_source:
                    document.getElementById(
                        'softkom_campaign_utm_source'
                    ),

                utm_medium:
                    document.getElementById(
                        'softkom_campaign_utm_medium'
                    ),

                utm_campaign:
                    document.getElementById(
                        'softkom_campaign_utm_campaign'
                    ),

                utm_content:
                    document.getElementById(
                        'softkom_campaign_utm_content'
                    ),

                utm_term:
                    document.getElementById(
                        'softkom_campaign_utm_term'
                    )
            };


            function rebuildTrackedUrl() {

                if (
                    !trackedUrl ||
                    !landing ||
                    !landing.value
                ) {
                    return;
                }

                try {

                    const url =
                        new URL(
                            landing.value,
                            window.location.origin
                        );

                    Object.keys(fields).forEach(
                        function (key) {

                            const field = fields[key];

                            const value =
                                field
                                    ? field.value.trim()
                                    : '';

                            if (value) {
                                url.searchParams.set(
                                    key,
                                    value
                                );
                            } else {
                                url.searchParams.delete(
                                    key
                                );
                            }
                        }
                    );

                    trackedUrl.value =
                        url.toString();

                } catch (error) {

                    trackedUrl.value =
                        landing.value;
                }
            }


            Object.keys(fields).forEach(
                function (key) {

                    if (fields[key]) {

                        fields[key].addEventListener(
                            'input',
                            rebuildTrackedUrl
                        );
                    }
                }
            );


            if (landing) {
                landing.addEventListener(
                    'input',
                    rebuildTrackedUrl
                );
            }


            rebuildTrackedUrl();
        }
    );
    </script>

    <?php

    /*
     * Campaign Performance V1.
     */
    softkom_v3_render_campaign_performance(
        $post->ID
    );

    /*
     * Campaign Creative & Audience Performance V2.
     */
    softkom_v3_render_campaign_attribution_breakdown(
        $post->ID
    );

}


/**
 * Save Campaign Manager V1.1 fields.
 */
function softkom_v3_save_campaign_details( $post_id ) {

    if (
        ! isset( $_POST['softkom_v3_campaign_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['softkom_v3_campaign_nonce']
                )
            ),
            'softkom_v3_save_campaign_details'
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
        'softkom_campaign' !== get_post_type( $post_id ) ||
        ! current_user_can(
            'edit_post',
            $post_id
        )
    ) {
        return;
    }
$text_fields = array(
        'softkom_campaign_utm_source'
            => '_softkom_campaign_utm_source',

        'softkom_campaign_utm_medium'
            => '_softkom_campaign_utm_medium',

        'softkom_campaign_utm_campaign'
            => '_softkom_campaign_utm_campaign',

        'softkom_campaign_utm_content'
            => '_softkom_campaign_utm_content',

        'softkom_campaign_utm_term'
            => '_softkom_campaign_utm_term',
    );


    foreach ( $text_fields as $field => $meta_key ) {

        $value = isset( $_POST[ $field ] )
            ? sanitize_text_field(
                wp_unslash(
                    $_POST[ $field ]
                )
            )
            : '';

        update_post_meta(
            $post_id,
            $meta_key,
            $value
        );
    }


    $landing_page =
        isset( $_POST['softkom_campaign_landing_page'] )
            ? esc_url_raw(
                wp_unslash(
                    $_POST['softkom_campaign_landing_page']
                )
            )
            : '';

    update_post_meta(
        $post_id,
        '_softkom_campaign_landing_page',
        $landing_page
    );


    $allowed_statuses = array(
        'draft',
        'planned',
        'active',
        'paused',
        'completed',
    );

    $status =
        isset( $_POST['softkom_campaign_status'] )
            ? sanitize_key(
                wp_unslash(
                    $_POST['softkom_campaign_status']
                )
            )
            : 'draft';

    if (
        ! in_array(
            $status,
            $allowed_statuses,
            true
        )
    ) {
        $status = 'draft';
    }

    update_post_meta(
        $post_id,
        '_softkom_campaign_status',
        $status
    );


    foreach (
        array(
            'start_date',
            'end_date',
        )
        as $date_key
    ) {

        $field =
            'softkom_campaign_' .
            $date_key;

        $value = isset( $_POST[ $field ] )
            ? sanitize_text_field(
                wp_unslash(
                    $_POST[ $field ]
                )
            )
            : '';

        if (
            '' !== $value &&
            ! preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $value
            )
        ) {
            $value = '';
        }

        update_post_meta(
            $post_id,
            '_softkom_campaign_' .
            $date_key,
            $value
        );
    }


    $budget =
        isset( $_POST['softkom_campaign_budget'] )
            ? max(
                0,
                (float) wp_unslash(
                    $_POST['softkom_campaign_budget']
                )
            )
            : 0;

    update_post_meta(
        $post_id,
        '_softkom_campaign_budget',
        $budget
    );


    update_post_meta(
        $post_id,
        '_softkom_campaign_tracked_url',
        softkom_v3_campaign_tracked_url(
            $post_id
        )
    );
}

add_action(
    'save_post_softkom_campaign',
    'softkom_v3_save_campaign_details'
);



/**
 * Calculate Campaign Performance V1.
 */
function softkom_v3_campaign_performance( $post_id ) {

    $campaign_key = get_post_meta(
        $post_id,
        '_softkom_campaign_utm_campaign',
        true
    );

    $budget = (float) get_post_meta(
        $post_id,
        '_softkom_campaign_budget',
        true
    );

    $performance = array(
        'leads'                 => 0,
        'qualified'             => 0,
        'warm_hot'              => 0,
        'pipeline_leads'        => 0,
        'estimated_mrr'         => 0,
        'average_score'         => 0,
        'budget'                => $budget,
        'cost_per_lead'         => 0,
        'cost_per_qualified'    => 0,
        'qualification_rate'    => 0,
        'pipeline_rate'         => 0,
        'mrr_budget_efficiency' => 0,
    );

    if ( '' === $campaign_key ) {
        return $performance;
    }

    $lead_ids = get_posts(
        array(
            'post_type'      => 'softkom_lead',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',

            'meta_query' => array(
                array(
                    'key'     => '_softkom_utm_campaign',
                    'value'   => $campaign_key,
                    'compare' => '=',
                ),
            ),
        )
    );

    if ( empty( $lead_ids ) ) {
        return $performance;
    }

    $score_total = 0;

    foreach ( $lead_ids as $lead_id ) {

        $performance['leads']++;

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
            is_array( $routing ) &&
            ! empty( $routing['sales_eligible'] )
        ) {
            $performance['qualified']++;
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

        if (
            in_array(
                $temperature,
                array(
                    'WARM',
                    'HOT',
                ),
                true
            )
        ) {
            $performance['warm_hot']++;
        }


        $pipeline_stage = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_pipeline_stage',
                true
            )
        );

        if ( '' !== $pipeline_stage ) {
            $performance['pipeline_leads']++;
        }


        $performance['estimated_mrr'] +=
            (float) get_post_meta(
                $lead_id,
                '_softkom_estimated_mrr',
                true
            );


        $score_total +=
            (float) get_post_meta(
                $lead_id,
                '_softkom_score_overall_lead',
                true
            );
    }


    if ( $performance['leads'] > 0 ) {

        $performance['average_score'] = round(
            $score_total /
            $performance['leads']
        );

        $performance['qualification_rate'] = round(
            (
                $performance['qualified'] /
                $performance['leads']
            ) * 100,
            1
        );

        $performance['pipeline_rate'] = round(
            (
                $performance['pipeline_leads'] /
                $performance['leads']
            ) * 100,
            1
        );

        if ( $performance['budget'] > 0 ) {

            $performance['cost_per_lead'] =
                $performance['budget'] /
                $performance['leads'];
        }
    }


    if (
        $performance['qualified'] > 0 &&
        $performance['budget'] > 0
    ) {

        $performance['cost_per_qualified'] =
            $performance['budget'] /
            $performance['qualified'];
    }


    if ( $performance['budget'] > 0 ) {

        $performance['mrr_budget_efficiency'] = round(
            (
                $performance['estimated_mrr'] /
                $performance['budget']
            ) * 100,
            1
        );
    }


    return $performance;
}


/**
 * Calculate Campaign Creative / Audience Performance V2.
 *
 * Groups attributed leads by UTM content + UTM term.
 */
function softkom_v3_campaign_attribution_breakdown( $post_id ) {

    $campaign_key = trim(
        (string) get_post_meta(
            $post_id,
            '_softkom_campaign_utm_campaign',
            true
        )
    );

    if ( '' === $campaign_key ) {
        return array();
    }

    $lead_ids = get_posts(
        array(
            'post_type'      => 'softkom_lead',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',

            'meta_query' => array(
                array(
                    'key'     => '_softkom_utm_campaign',
                    'value'   => $campaign_key,
                    'compare' => '=',
                ),
            ),
        )
    );

    if ( empty( $lead_ids ) ) {
        return array();
    }

    $groups = array();

    foreach ( $lead_ids as $lead_id ) {

        $content = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_utm_content',
                true
            )
        );

        $term = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_utm_term',
                true
            )
        );

        if ( '' === $content ) {
            $content = 'Unspecified';
        }

        if ( '' === $term ) {
            $term = 'Unspecified';
        }

        $group_key = md5(
            $content .
            '|' .
            $term
        );

        if ( ! isset( $groups[ $group_key ] ) ) {

            $groups[ $group_key ] = array(
                'content'        => $content,
                'term'           => $term,
                'leads'          => 0,
                'qualified'      => 0,
                'warm_hot'       => 0,
                'pipeline_leads' => 0,
                'estimated_mrr'  => 0,
                'average_score'  => 0,
                '_score_total'   => 0,
            );
        }

        $groups[ $group_key ]['leads']++;

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
            is_array( $routing ) &&
            ! empty( $routing['sales_eligible'] )
        ) {
            $groups[ $group_key ]['qualified']++;
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

        if (
            in_array(
                $temperature,
                array(
                    'WARM',
                    'HOT',
                ),
                true
            )
        ) {
            $groups[ $group_key ]['warm_hot']++;
        }

        $pipeline_stage = trim(
            (string) get_post_meta(
                $lead_id,
                '_softkom_pipeline_stage',
                true
            )
        );

        if ( '' !== $pipeline_stage ) {
            $groups[ $group_key ]['pipeline_leads']++;
        }

        $groups[ $group_key ]['estimated_mrr'] +=
            (float) get_post_meta(
                $lead_id,
                '_softkom_estimated_mrr',
                true
            );

        $groups[ $group_key ]['_score_total'] +=
            (float) get_post_meta(
                $lead_id,
                '_softkom_score_overall_lead',
                true
            );
    }

    foreach ( $groups as $group_key => $group ) {

        if ( $group['leads'] > 0 ) {

            $groups[ $group_key ]['average_score'] =
                round(
                    $group['_score_total'] /
                    $group['leads']
                );
        }

        unset(
            $groups[ $group_key ]['_score_total']
        );
    }

    uasort(
        $groups,
        function ( $a, $b ) {

            if (
                $a['estimated_mrr'] ===
                $b['estimated_mrr']
            ) {
                return $b['leads'] <=> $a['leads'];
            }

            return
                $b['estimated_mrr']
                <=>
                $a['estimated_mrr'];
        }
    );

    return array_values(
        $groups
    );
}

/**
 * Render Campaign Performance V1.
 */
function softkom_v3_render_campaign_performance( $post_id ) {

    $performance =
        softkom_v3_campaign_performance(
            $post_id
        );

    $campaign_key = get_post_meta(
        $post_id,
        '_softkom_campaign_utm_campaign',
        true
    );

    $source = get_post_meta(
        $post_id,
        '_softkom_campaign_utm_source',
        true
    );

    $medium = get_post_meta(
        $post_id,
        '_softkom_campaign_utm_medium',
        true
    );

    ?>

    <div
        style="
            margin-top:24px;
            padding-top:20px;
            border-top:1px solid #dcdcde;
        "
    >

        <h2 style="margin-top:0;">
            Campaign Performance
        </h2>

        <p class="description">
            Revenue and lead performance attributed
            to this campaign.
        </p>

        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(auto-fit,minmax(160px,1fr));
                gap:12px;
                margin-top:16px;
            "
        >

            <?php

            $metrics = array(

                'Leads' =>
                    number_format_i18n(
                        $performance['leads']
                    ),

                'Qualified Leads' =>
                    number_format_i18n(
                        $performance['qualified']
                    ),

                'Warm / Hot' =>
                    number_format_i18n(
                        $performance['warm_hot']
                    ),

                'Pipeline Leads' =>
                    number_format_i18n(
                        $performance['pipeline_leads']
                    ),

                'Estimated MRR' =>
                    'R ' .
                    number_format_i18n(
                        $performance['estimated_mrr'],
                        0
                    ),

                'Average Lead Score' =>
                    number_format_i18n(
                        $performance['average_score']
                    ),

                'Campaign Budget' =>
                    'R ' .
                    number_format_i18n(
                        $performance['budget'],
                        0
                    ),

                'Cost per Lead' =>
                    'R ' .
                    number_format_i18n(
                        $performance['cost_per_lead'],
                        0
                    ),

                'Cost per Qualified Lead' =>
                    'R ' .
                    number_format_i18n(
                        $performance['cost_per_qualified'],
                        0
                    ),

                'Qualification Rate' =>
                    number_format_i18n(
                        $performance['qualification_rate'],
                        1
                    ) .
                    '%',

                'Pipeline Rate' =>
                    number_format_i18n(
                        $performance['pipeline_rate'],
                        1
                    ) .
                    '%',

                'Pipeline MRR Efficiency' =>
                    number_format_i18n(
                        $performance['mrr_budget_efficiency'],
                        1
                    ) .
                    '%',
            );


            foreach ( $metrics as $label => $value ) :

            ?>

                <div
                    style="
                        border:1px solid #dcdcde;
                        background:#fff;
                        border-radius:6px;
                        padding:14px;
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
                            margin-top:4px;
                            font-size:22px;
                            font-weight:700;
                        "
                    >
                        <?php echo esc_html( $value ); ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <p style="margin-top:16px;">

            <strong>
                Attribution:
            </strong>

            <?php

            echo esc_html(
                (
                    $source
                        ? $source
                        : 'direct'
                ) .
                ' → ' .
                (
                    $medium
                        ? $medium
                        : 'unknown'
                ) .
                ' → ' .
                (
                    $campaign_key
                        ? $campaign_key
                        : 'unattributed'
                )
            );

            ?>

        </p>

    </div>

    <?php
}


/**
 * Render Campaign Creative & Audience Performance V2.
 */
function softkom_v3_render_campaign_attribution_breakdown( $post_id ) {

    $rows = softkom_v3_campaign_attribution_breakdown(
        $post_id
    );

    ?>

    <div
        style="
            margin-top:24px;
            padding-top:20px;
            border-top:1px solid #dcdcde;
        "
    >

        <h2 style="margin-top:0;">
            Creative &amp; Audience Performance
        </h2>

        <p class="description">
            Compare campaign creative and audience segments
            using UTM content and UTM term attribution.
        </p>

        <?php if ( empty( $rows ) ) : ?>

            <p>
                No attributed lead data is available yet.
            </p>

        <?php else : ?>

            <div style="overflow-x:auto;margin-top:16px;">

                <table class="widefat striped">

                    <thead>
                        <tr>
                            <th>Content</th>
                            <th>Audience / Term</th>
                            <th>Leads</th>
                            <th>Qualified</th>
                            <th>Warm / Hot</th>
                            <th>Pipeline</th>
                            <th>Estimated MRR</th>
                            <th>Avg Score</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ( $rows as $row ) : ?>

                        <tr>

                            <td>
                                <?php
                                echo esc_html(
                                    $row['content']
                                        ? $row['content']
                                        : '(not set)'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    $row['term']
                                        ? $row['term']
                                        : '(not set)'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $row['leads']
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $row['qualified']
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $row['warm_hot']
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $row['pipeline_leads']
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    'R ' .
                                    number_format_i18n(
                                        $row['estimated_mrr'],
                                        0
                                    )
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $row['average_score']
                                    )
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

    <?php
}

/**
 * Campaign Admin List V1.
 */
function softkom_v3_campaign_admin_columns( $columns ) {

    return array(
        'cb' => isset( $columns['cb'] )
            ? $columns['cb']
            : '<input type="checkbox" />',

        'title' => 'Campaign',
        'softkom_status' => 'Status',
        'softkom_source' => 'Source',
        'softkom_medium' => 'Medium',
        'softkom_campaign_key' => 'Campaign Key',
        'softkom_leads' => 'Leads',
        'softkom_qualified' => 'Qualified',
        'softkom_pipeline' => 'Pipeline',
        'softkom_mrr' => 'Estimated MRR',
        'softkom_budget' => 'Budget',
        'softkom_efficiency' => 'MRR Efficiency',

        'date' => isset( $columns['date'] )
            ? $columns['date']
            : 'Date',
    );
}

add_filter(
    'manage_softkom_campaign_posts_columns',
    'softkom_v3_campaign_admin_columns'
);


/**
 * Render Campaign Admin List V1 columns.
 */
function softkom_v3_campaign_admin_column( $column, $post_id ) {

    $performance = softkom_v3_campaign_performance(
        $post_id
    );

    switch ( $column ) {

        case 'softkom_status':

            $status = get_post_meta(
                $post_id,
                '_softkom_campaign_status',
                true
            );

            echo esc_html(
                $status
                    ? ucfirst( $status )
                    : 'Draft'
            );

            break;

        case 'softkom_source':

            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_softkom_campaign_utm_source',
                    true
                )
            );

            break;

        case 'softkom_medium':

            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_softkom_campaign_utm_medium',
                    true
                )
            );

            break;

        case 'softkom_campaign_key':

            echo esc_html(
                get_post_meta(
                    $post_id,
                    '_softkom_campaign_utm_campaign',
                    true
                )
            );

            break;

        case 'softkom_leads':

            echo esc_html(
                number_format_i18n(
                    $performance['leads']
                )
            );

            break;

        case 'softkom_qualified':

            echo esc_html(
                number_format_i18n(
                    $performance['qualified']
                )
            );

            break;

        case 'softkom_pipeline':

            echo esc_html(
                number_format_i18n(
                    $performance['pipeline_leads']
                )
            );

            break;

        case 'softkom_mrr':

            echo esc_html(
                'R ' .
                number_format_i18n(
                    $performance['estimated_mrr'],
                    0
                )
            );

            break;

        case 'softkom_budget':

            echo esc_html(
                'R ' .
                number_format_i18n(
                    $performance['budget'],
                    0
                )
            );

            break;

        case 'softkom_efficiency':

            echo esc_html(
                number_format_i18n(
                    $performance['mrr_budget_efficiency'],
                    1
                ) .
                '%'
            );

            break;
    }
}

add_action(
    'manage_softkom_campaign_posts_custom_column',
    'softkom_v3_campaign_admin_column',
    10,
    2
);


/**
 * Improve campaign admin column widths.
 */
function softkom_v3_campaign_admin_column_styles() {

    $screen = get_current_screen();

    if (
        ! $screen ||
        'edit-softkom_campaign' !== $screen->id
    ) {
        return;
    }

    ?>
    <style>
        .column-softkom_status {
            width:80px;
        }

        .column-softkom_source,
        .column-softkom_medium {
            width:90px;
        }

        .column-softkom_leads,
        .column-softkom_qualified,
        .column-softkom_pipeline {
            width:72px;
            text-align:center;
        }

        .column-softkom_mrr,
        .column-softkom_budget,
        .column-softkom_efficiency {
            width:110px;
        }
    </style>
    <?php
}

add_action(
    'admin_head',
    'softkom_v3_campaign_admin_column_styles'
);

