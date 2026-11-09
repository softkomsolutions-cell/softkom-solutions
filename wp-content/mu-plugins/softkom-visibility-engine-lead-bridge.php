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


function softkom_visibility_engine_health_check() {
    $cached = get_transient( 'softkom_visibility_engine_health' );
    if ( is_array( $cached ) ) {
        return $cached;
    }

    $config = softkom_visibility_engine_bridge_config();
    if ( ! $config['endpoint'] || ! $config['site_id'] || ! $config['secret'] ) {
        return array( 'connected' => false, 'status' => 'not_configured' );
    }

    $probe = array(
        'siteId'     => $config['site_id'],
        'landingUrl' => home_url( '/assessment/' ),
        'funnel'     => 'credential-probe',
        'leadId'     => 'credential-probe',
        'stage'      => 'INVALID_STAGE',
        'revenue'    => 0,
        'currency'   => 'ZAR',
    );

    $response = wp_remote_post( $config['endpoint'], array(
        'timeout'     => 8,
        'redirection' => 0,
        'headers'     => array(
            'Authorization' => 'Bearer ' . $config['secret'],
            'Content-Type'  => 'application/json',
        ),
        'body'        => wp_json_encode( $probe ),
        'data_format' => 'body',
    ) );

    if ( is_wp_error( $response ) ) {
        $result = array( 'connected' => false, 'status' => 'transport_error' );
    } else {
        $code = (int) wp_remote_retrieve_response_code( $response );
        $result = array(
            'connected' => 400 === $code,
            'status'    => 400 === $code ? 'connected' : ( 401 === $code ? 'unauthorised' : 'unexpected_response' ),
            'http'      => $code,
        );
    }

    set_transient( 'softkom_visibility_engine_health', $result, MINUTE_IN_SECONDS );
    return $result;
}

add_action( 'rest_api_init', function() {
    register_rest_route( 'softkom-visibility/v1', '/bridge-health', array(
        'methods'             => 'GET',
        'callback'            => function() {
            return rest_ensure_response( softkom_visibility_engine_health_check() );
        },
        'permission_callback' => '__return_true',
    ) );
} );

function softkom_visibility_engine_schedule_backfill() {
    if ( get_option( 'softkom_visibility_backfill_complete', '' ) ) {
        return;
    }
    if ( ! wp_next_scheduled( 'softkom_visibility_engine_backfill_batch' ) ) {
        wp_schedule_single_event( time() + 5, 'softkom_visibility_engine_backfill_batch' );
    }
}
add_action( 'init', 'softkom_visibility_engine_schedule_backfill', 50 );

function softkom_visibility_engine_backfill_batch() {
    if ( get_option( 'softkom_visibility_backfill_complete', '' ) ) {
        return;
    }

    $offset = max( 0, (int) get_option( 'softkom_visibility_backfill_offset', 0 ) );
    $batch_size = 5;
    $ids = get_posts( array(
        'post_type'              => 'softkom_lead',
        'post_status'            => array( 'publish', 'private', 'draft', 'pending' ),
        'posts_per_page'         => $batch_size,
        'offset'                 => $offset,
        'orderby'                => 'ID',
        'order'                  => 'ASC',
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'suppress_filters'       => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    if ( empty( $ids ) ) {
        update_option( 'softkom_visibility_backfill_complete', current_time( 'mysql', true ), false );
        delete_option( 'softkom_visibility_backfill_offset' );
        return;
    }

    foreach ( $ids as $lead_id ) {
        softkom_visibility_engine_push_lead( (int) $lead_id );
    }

    update_option( 'softkom_visibility_backfill_offset', $offset + count( $ids ), false );

    if ( count( $ids ) < $batch_size ) {
        update_option( 'softkom_visibility_backfill_complete', current_time( 'mysql', true ), false );
        delete_option( 'softkom_visibility_backfill_offset' );
        return;
    }

    wp_schedule_single_event( time() + MINUTE_IN_SECONDS, 'softkom_visibility_engine_backfill_batch' );
}
add_action( 'softkom_visibility_engine_backfill_batch', 'softkom_visibility_engine_backfill_batch' );
