<?php
/**
 * Plugin Name: Softkom Visibility Engine Lead Bridge
 * Description: Pushes persisted Softkom assessment lead attribution into the private Visibility Engine.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function softkom_visibility_engine_bridge_config() {
    $endpoint = defined( 'SOFTKOM_VISIBILITY_ATTRIBUTION_URL' )
        ? (string) SOFTKOM_VISIBILITY_ATTRIBUTION_URL
        : (string) get_option( 'softkom_visibility_attribution_url', '' );

    $site_id = defined( 'SOFTKOM_VISIBILITY_SITE_ID' )
        ? (string) SOFTKOM_VISIBILITY_SITE_ID
        : (string) get_option( 'softkom_visibility_site_id', '' );

    $secret = defined( 'SOFTKOM_VISIBILITY_WEBHOOK_SECRET' )
        ? (string) SOFTKOM_VISIBILITY_WEBHOOK_SECRET
        : (string) get_option( 'softkom_visibility_webhook_secret', '' );

    return array(
        'endpoint' => esc_url_raw( trim( $endpoint ) ),
        'site_id'  => sanitize_text_field( trim( $site_id ) ),
        'secret'   => trim( $secret ),
    );
}

function softkom_visibility_engine_stage( $lead_id ) {
    $stage = sanitize_key(
        (string) get_post_meta( $lead_id, '_softkom_pipeline_stage', true )
    );

    $map = array(
        'qualified' => 'QUALIFIED',
        'meeting'   => 'OPPORTUNITY',
        'proposal'  => 'OPPORTUNITY',
        'won'       => 'WON',
        'lost'      => 'LOST',
    );

    return isset( $map[ $stage ] ) ? $map[ $stage ] : 'LEAD';
}

function softkom_visibility_engine_lead_payload( $lead_id ) {
    if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
        return array();
    }

    $config = softkom_visibility_engine_bridge_config();
    if ( ! $config['endpoint'] || ! $config['site_id'] || ! $config['secret'] ) {
        return array();
    }

    $landing = esc_url_raw(
        (string) get_post_meta( $lead_id, '_softkom_landing_page', true )
    );
    if ( ! $landing ) {
        $landing = home_url( '/assessment/' );
    }

    return array(
        'siteId'     => $config['site_id'],
        'landingUrl' => $landing,
        'source'     => sanitize_text_field(
            (string) get_post_meta( $lead_id, '_softkom_traffic_source', true )
        ),
        'medium'     => sanitize_text_field(
            (string) get_post_meta( $lead_id, '_softkom_traffic_medium', true )
        ),
        'campaign'   => sanitize_text_field(
            (string) get_post_meta( $lead_id, '_softkom_utm_campaign', true )
        ),
        'funnel'     => 'Business Systems & AI Readiness Assessment',
        'leadId'     => 'wp-softkom-lead-' . absint( $lead_id ),
        'stage'      => softkom_visibility_engine_stage( $lead_id ),
        // Estimated MRR is intentionally not sent as realised revenue.
        'revenue'    => 0,
        'currency'   => 'ZAR',
        'createdAt'  => get_post_time( DATE_ATOM, true, $lead_id ),
    );
}

function softkom_visibility_engine_schedule_retry( $lead_id ) {
    $attempts = (int) get_post_meta(
        $lead_id,
        '_softkom_visibility_sync_attempts',
        true
    );

    if ( $attempts >= 5 ) {
        return;
    }

    $args = array( absint( $lead_id ) );
    if ( ! wp_next_scheduled( 'softkom_visibility_engine_retry_lead', $args ) ) {
        wp_schedule_single_event(
            time() + ( 5 * MINUTE_IN_SECONDS ),
            'softkom_visibility_engine_retry_lead',
            $args
        );
    }
}

function softkom_visibility_engine_push_lead( $lead_id ) {
    if ( ! $lead_id || 'softkom_lead' !== get_post_type( $lead_id ) ) {
        return false;
    }

    $config  = softkom_visibility_engine_bridge_config();
    $payload = softkom_visibility_engine_lead_payload( $lead_id );

    if ( empty( $payload ) ) {
        update_post_meta(
            $lead_id,
            '_softkom_visibility_sync_status',
            'not_configured'
        );
        return false;
    }

    $attempts = (int) get_post_meta(
        $lead_id,
        '_softkom_visibility_sync_attempts',
        true
    );
    update_post_meta(
        $lead_id,
        '_softkom_visibility_sync_attempts',
        $attempts + 1
    );

    $response = wp_remote_post(
        $config['endpoint'],
        array(
            'timeout'     => 8,
            'redirection' => 0,
            'headers'     => array(
                'Authorization' => 'Bearer ' . $config['secret'],
                'Content-Type'  => 'application/json',
            ),
            'body'        => wp_json_encode( $payload ),
            'data_format' => 'body',
        )
    );

    if ( is_wp_error( $response ) ) {
        update_post_meta(
            $lead_id,
            '_softkom_visibility_sync_status',
            'transport_error'
        );
        softkom_visibility_engine_schedule_retry( $lead_id );
        return false;
    }

    $code = (int) wp_remote_retrieve_response_code( $response );
    update_post_meta( $lead_id, '_softkom_visibility_sync_http', $code );

    if ( $code < 200 || $code >= 300 ) {
        update_post_meta(
            $lead_id,
            '_softkom_visibility_sync_status',
            'http_error'
        );
        softkom_visibility_engine_schedule_retry( $lead_id );
        return false;
    }

    update_post_meta(
        $lead_id,
        '_softkom_visibility_sync_status',
        'synced'
    );
    update_post_meta(
        $lead_id,
        '_softkom_visibility_synced_at_gmt',
        current_time( 'mysql', true )
    );
    update_post_meta( $lead_id, '_softkom_visibility_sync_attempts', 0 );

    return true;
}

function softkom_visibility_engine_after_assessment_lead(
    $lead_id,
    $result = array(),
    $security = array()
) {
    unset( $result, $security );
    softkom_visibility_engine_push_lead( $lead_id );
}
add_action(
    'softkom_v3_assessment_lead_stored',
    'softkom_visibility_engine_after_assessment_lead',
    100,
    3
);

function softkom_visibility_engine_after_lead_save(
    $post_id,
    $post = null,
    $update = false
) {
    unset( $post, $update );

    if (
        wp_is_post_autosave( $post_id ) ||
        wp_is_post_revision( $post_id )
    ) {
        return;
    }

    softkom_visibility_engine_push_lead( $post_id );
}
add_action(
    'save_post_softkom_lead',
    'softkom_visibility_engine_after_lead_save',
    999,
    3
);

add_action(
    'softkom_visibility_engine_retry_lead',
    'softkom_visibility_engine_push_lead',
    10,
    1
);
