<?php
/**
 * AutoSEO live data + controlled publishing layer.
 *
 * Configuration can be supplied with constants or filters:
 * - SOFTKOM_AUTOSEO_GOOGLE_CLIENT_ID
 * - SOFTKOM_AUTOSEO_GOOGLE_CLIENT_SECRET
 * - SOFTKOM_AUTOSEO_GOOGLE_REFRESH_TOKEN
 * - SOFTKOM_AUTOSEO_GSC_SITE_URL
 * - SOFTKOM_AUTOSEO_GA4_PROPERTY_ID
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read AutoSEO config without exposing secrets to rendered HTML.
 *
 * @param string $key Config key.
 * @param string $default Default.
 * @return string
 */
function softkom_v3_autoseo_config( $key, $default = '' ) {
	$map = array(
		'google_client_id'     => 'SOFTKOM_AUTOSEO_GOOGLE_CLIENT_ID',
		'google_client_secret' => 'SOFTKOM_AUTOSEO_GOOGLE_CLIENT_SECRET',
		'google_refresh_token' => 'SOFTKOM_AUTOSEO_GOOGLE_REFRESH_TOKEN',
		'gsc_site_url'         => 'SOFTKOM_AUTOSEO_GSC_SITE_URL',
		'ga4_property_id'      => 'SOFTKOM_AUTOSEO_GA4_PROPERTY_ID',
		'visibility_bridge_url'=> 'SOFTKOM_AUTOSEO_VISIBILITY_BRIDGE_URL',
		'visibility_bridge_secret'=> 'SOFTKOM_AUTOSEO_VISIBILITY_BRIDGE_SECRET',
	);

	$value = $default;
	if ( isset( $map[ $key ] ) && defined( $map[ $key ] ) ) {
		$value = (string) constant( $map[ $key ] );
	}

	if ( '' === $value && in_array( $key, array( 'visibility_bridge_url', 'visibility_bridge_secret' ), true ) ) {
		$option = get_option( 'softkom_autoseo_' . $key, '' );
		if ( is_string( $option ) ) {
			$value = $option;
		}
	}

	return (string) apply_filters( 'softkom_v3_autoseo_config_' . $key, $value );
}

/**
 * Refresh and cache Google OAuth access token.
 *
 * @return string|WP_Error
 */
function softkom_v3_autoseo_google_access_token() {
	$cached = get_transient( 'softkom_autoseo_google_access_token' );
	if ( is_string( $cached ) && '' !== $cached ) {
		return $cached;
	}

	$client_id     = softkom_v3_autoseo_config( 'google_client_id' );
	$client_secret = softkom_v3_autoseo_config( 'google_client_secret' );
	$refresh_token = softkom_v3_autoseo_config( 'google_refresh_token' );

	if ( '' === $client_id || '' === $client_secret || '' === $refresh_token ) {
		return new WP_Error( 'autoseo_google_not_configured', 'Google OAuth credentials are not configured.' );
	}

	$response = wp_remote_post(
		'https://oauth2.googleapis.com/token',
		array(
			'timeout' => 20,
			'body'    => array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'refresh_token' => $refresh_token,
				'grant_type'    => 'refresh_token',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || empty( $body['access_token'] ) ) {
		return new WP_Error(
			'autoseo_google_token_failed',
			'Google OAuth token refresh failed.',
			array( 'status' => $code )
		);
	}

	$ttl = max( 300, (int) ( $body['expires_in'] ?? 3600 ) - 120 );
	set_transient( 'softkom_autoseo_google_access_token', sanitize_text_field( $body['access_token'] ), $ttl );

	return sanitize_text_field( $body['access_token'] );
}

/**
 * Authenticated Google API request.
 *
 * @param string $url Endpoint.
 * @param array<string,mixed> $payload POST body.
 * @return array<string,mixed>|WP_Error
 */
function softkom_v3_autoseo_google_post( $url, $payload ) {
	$token = softkom_v3_autoseo_google_access_token();
	if ( is_wp_error( $token ) ) {
		return $token;
	}

	$response = wp_remote_post(
		$url,
		array(
			'timeout' => 25,
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json',
			),
			'body' => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code < 200 || $code >= 300 || ! is_array( $body ) ) {
		return new WP_Error(
			'autoseo_google_api_failed',
			'Google API request failed.',
			array( 'status' => $code )
		);
	}

	return $body;
}

/**
 * Collect 28-day Search Console query/page performance.
 *
 * @param bool $force Ignore cache.
 * @return array<string,mixed>|WP_Error
 */
function softkom_v3_autoseo_collect_gsc( $force = false ) {
	$cache_key = 'softkom_autoseo_gsc_28d';
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$site = softkom_v3_autoseo_config( 'gsc_site_url', home_url( '/' ) );
	if ( '' === $site ) {
		return new WP_Error( 'autoseo_gsc_site_missing', 'Search Console site URL is not configured.' );
	}

	$end   = gmdate( 'Y-m-d', strtotime( '-1 day' ) );
	$start = gmdate( 'Y-m-d', strtotime( '-28 days' ) );

	$url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . rawurlencode( $site ) . '/searchAnalytics/query';
	$data = softkom_v3_autoseo_google_post(
		$url,
		array(
			'startDate'  => $start,
			'endDate'    => $end,
			'dimensions' => array( 'query', 'page' ),
			'rowLimit'   => 250,
		)
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$rows = array();
	foreach ( (array) ( $data['rows'] ?? array() ) as $row ) {
		$keys = (array) ( $row['keys'] ?? array() );
		$rows[] = array(
			'query'       => sanitize_text_field( $keys[0] ?? '' ),
			'page'        => esc_url_raw( $keys[1] ?? '' ),
			'clicks'      => (float) ( $row['clicks'] ?? 0 ),
			'impressions' => (float) ( $row['impressions'] ?? 0 ),
			'ctr'         => (float) ( $row['ctr'] ?? 0 ),
			'position'    => (float) ( $row['position'] ?? 0 ),
		);
	}

	$result = array(
		'start_date' => $start,
		'end_date'   => $end,
		'rows'       => $rows,
		'fetched_at' => current_time( 'mysql', true ),
	);

	set_transient( $cache_key, $result, 6 * HOUR_IN_SECONDS );
	return $result;
}

/**
 * Derive Search Console quick-win opportunities from real rows.
 *
 * @param array<string,mixed> $gsc Search Console result.
 * @return array<int,array<string,mixed>>
 */
function softkom_v3_autoseo_gsc_opportunities( $gsc ) {
	$out = array();
	$labels = function_exists( 'softkom_v3_autoseo_action_labels' )
		? softkom_v3_autoseo_action_labels()
		: array();

	foreach ( (array) ( $gsc['rows'] ?? array() ) as $row ) {
		$position    = (float) ( $row['position'] ?? 0 );
		$impressions = (float) ( $row['impressions'] ?? 0 );
		$ctr         = (float) ( $row['ctr'] ?? 0 );

		$near_page_one = $position >= 5 && $position <= 15 && $impressions >= 20;
		$low_ctr       = $impressions >= 50 && $ctr < 0.03;

		if ( ! $near_page_one && ! $low_ctr ) {
			continue;
		}

		$traffic = min( 100, 35 + log( max( 1, $impressions ), 1.7 ) * 7 );
		$intent  = apply_filters( 'softkom_v3_autoseo_buyer_intent_score', 60, $row );
		$value   = apply_filters( 'softkom_v3_autoseo_conversion_value_score', 65, $row );
		$effort  = $low_ctr ? 22 : 38;

		$signals = array(
			'traffic_potential'     => $traffic,
			'buyer_intent'          => $intent,
			'conversion_value'      => $value,
			'implementation_effort' => $effort,
			'ranking_page'          => true,
			'low_ctr'               => $low_ctr,
		);

		$score  = function_exists( 'softkom_v3_autoseo_opportunity_score' )
			? softkom_v3_autoseo_opportunity_score( $signals )
			: 0;
		$action = function_exists( 'softkom_v3_autoseo_recommended_action' )
			? softkom_v3_autoseo_recommended_action( $signals )
			: 'monitor-and-retest';

		$out[] = array(
			'source'      => 'gsc',
			'query'       => $row['query'],
			'page'        => $row['page'],
			'impressions' => $impressions,
			'clicks'      => (float) $row['clicks'],
			'ctr'         => $ctr,
			'position'    => $position,
			'score'       => $score,
			'action'      => $labels[ $action ] ?? $action,
		);
	}

	usort(
		$out,
		function ( $a, $b ) {
			return (int) $b['score'] <=> (int) $a['score'];
		}
	);

	return array_slice( $out, 0, 25 );
}

/**
 * Collect GA4 traffic/conversion summary for the previous 28 days.
 *
 * @param bool $force Ignore cache.
 * @return array<string,mixed>|WP_Error
 */
function softkom_v3_autoseo_collect_ga4( $force = false ) {
	$cache_key = 'softkom_autoseo_ga4_28d';
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$property = preg_replace( '/\D+/', '', softkom_v3_autoseo_config( 'ga4_property_id' ) );
	if ( '' === $property ) {
		return new WP_Error( 'autoseo_ga4_property_missing', 'GA4 property ID is not configured.' );
	}

	$url = 'https://analyticsdata.googleapis.com/v1beta/properties/' . $property . ':runReport';
	$data = softkom_v3_autoseo_google_post(
		$url,
		array(
			'dateRanges' => array(
				array( 'startDate' => '28daysAgo', 'endDate' => 'yesterday' ),
			),
			'dimensions' => array(
				array( 'name' => 'sessionDefaultChannelGroup' ),
			),
			'metrics' => array(
				array( 'name' => 'sessions' ),
				array( 'name' => 'keyEvents' ),
				array( 'name' => 'totalRevenue' ),
			),
		)
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$channels = array();
	foreach ( (array) ( $data['rows'] ?? array() ) as $row ) {
		$dims = (array) ( $row['dimensionValues'] ?? array() );
		$metrics = (array) ( $row['metricValues'] ?? array() );
		$channels[] = array(
			'channel'     => sanitize_text_field( $dims[0]['value'] ?? '' ),
			'sessions'    => (float) ( $metrics[0]['value'] ?? 0 ),
			'key_events'  => (float) ( $metrics[1]['value'] ?? 0 ),
			'revenue'     => (float) ( $metrics[2]['value'] ?? 0 ),
		);
	}

	$result = array(
		'channels'   => $channels,
		'fetched_at' => current_time( 'mysql', true ),
	);

	set_transient( $cache_key, $result, 6 * HOUR_IN_SECONDS );
	return $result;
}

/**
 * Provider-neutral AI visibility runner.
 *
 * A provider plugin/service can attach to the filter and return:
 * array( 'provider' => '...', 'prompts' => array(...), 'summary' => array(...) )
 *
 * @param array<int,string> $prompts Buyer prompts.
 * @param string $brand Brand to look for.
 * @return array<string,mixed>|WP_Error
 */
function softkom_v3_autoseo_collect_ai_visibility( $prompts, $brand ) {
	$prompts = array_values( array_filter( array_map( 'sanitize_text_field', (array) $prompts ) ) );
	$brand   = sanitize_text_field( $brand );

	if ( empty( $prompts ) || '' === $brand ) {
		return new WP_Error( 'autoseo_ai_input_missing', 'AI visibility prompts and brand are required.' );
	}

	$result = apply_filters( 'softkom_v3_autoseo_ai_visibility_run', null, $prompts, $brand );

	if ( null === $result ) {
		return new WP_Error(
			'autoseo_ai_provider_not_configured',
			'No AI visibility provider is configured. Attach a provider to softkom_v3_autoseo_ai_visibility_run.'
		);
	}

	return is_array( $result )
		? $result
		: new WP_Error( 'autoseo_ai_provider_invalid', 'AI visibility provider returned an invalid response.' );
}

/**
 * Register a private AutoSEO recommendation post type used as the approval queue.
 */
function softkom_v3_autoseo_register_recommendation_type() {
	register_post_type(
		'autoseo_action',
		array(
			'label'           => 'AutoSEO Actions',
			'public'          => false,
			'show_ui'         => current_user_can( 'manage_options' ),
			'show_in_rest'    => false,
			'supports'        => array( 'title', 'editor', 'custom-fields' ),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'softkom_v3_autoseo_register_recommendation_type', 25 );

/**
 * Queue a prepared website change for approval.
 *
 * @param array<string,mixed> $action Action data.
 * @return int|WP_Error
 */
function softkom_v3_autoseo_queue_action( $action ) {
	$title = sanitize_text_field( $action['title'] ?? 'AutoSEO recommendation' );
	$body  = wp_kses_post( $action['content'] ?? '' );
	$target = esc_url_raw( $action['target_url'] ?? '' );

	$id = wp_insert_post(
		array(
			'post_type'    => 'autoseo_action',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_content' => $body,
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		return $id;
	}

	update_post_meta( $id, '_autoseo_target_url', $target );
	update_post_meta( $id, '_autoseo_action_type', sanitize_key( $action['action_type'] ?? '' ) );
	update_post_meta( $id, '_autoseo_before_snapshot', wp_json_encode( $action['before'] ?? array() ) );
	update_post_meta( $id, '_autoseo_status', 'awaiting-approval' );

	return (int) $id;
}

/**
 * Apply an approved AutoSEO content update to an existing WordPress page.
 *
 * Only administrators can invoke this. It stores a rollback snapshot.
 *
 * @param int $action_id Queue item.
 * @param int $target_post_id WordPress post/page to update.
 * @return int|WP_Error
 */
function softkom_v3_autoseo_apply_content_action( $action_id, $target_post_id ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'autoseo_forbidden', 'Administrator approval is required.' );
	}

	$action = get_post( $action_id );
	$target = get_post( $target_post_id );

	if ( ! $action instanceof WP_Post || 'autoseo_action' !== $action->post_type ) {
		return new WP_Error( 'autoseo_action_missing', 'AutoSEO action not found.' );
	}
	if ( ! $target instanceof WP_Post ) {
		return new WP_Error( 'autoseo_target_missing', 'Target post not found.' );
	}

	$rollback = array(
		'post_title'   => $target->post_title,
		'post_content' => $target->post_content,
		'post_excerpt' => $target->post_excerpt,
		'post_status'  => $target->post_status,
	);

	update_post_meta( $action_id, '_autoseo_rollback_snapshot', wp_json_encode( $rollback ) );

	$update = array(
		'ID'           => $target_post_id,
		'post_content' => $action->post_content,
	);

	$new_title = get_post_meta( $action_id, '_autoseo_proposed_title', true );
	if ( is_string( $new_title ) && '' !== trim( $new_title ) ) {
		$update['post_title'] = sanitize_text_field( $new_title );
	}

	$result = wp_update_post( wp_slash( $update ), true );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	update_post_meta( $action_id, '_autoseo_status', 'published' );
	update_post_meta( $action_id, '_autoseo_target_post_id', (int) $target_post_id );
	update_post_meta( $action_id, '_autoseo_published_at', current_time( 'mysql', true ) );

	return (int) $result;
}

/**
 * Build combined dashboard payload.
 *
 * @param bool $force Refresh cached collectors.
 * @return array<string,mixed>
 */
function softkom_v3_autoseo_live_dashboard( $force = false ) {
	$gsc = softkom_v3_autoseo_collect_gsc( $force );
	$ga4 = softkom_v3_autoseo_collect_ga4( $force );

	return array(
		'status' => array(
			'gsc' => is_wp_error( $gsc ) ? $gsc->get_error_code() : 'connected',
			'ga4' => is_wp_error( $ga4 ) ? $ga4->get_error_code() : 'connected',
		),
		'gsc' => is_wp_error( $gsc ) ? null : $gsc,
		'ga4' => is_wp_error( $ga4 ) ? null : $ga4,
		'opportunities' => is_wp_error( $gsc ) ? array() : softkom_v3_autoseo_gsc_opportunities( $gsc ),
		'fetched_at' => current_time( 'mysql', true ),
	);
}

/**
 * Private REST endpoint for the AutoSEO dashboard.
 */
function softkom_v3_autoseo_register_rest_routes() {
	register_rest_route(
		'softkom-autoseo/v1',
		'/dashboard',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
			'callback'            => function ( WP_REST_Request $request ) {
				return rest_ensure_response(
					softkom_v3_autoseo_live_dashboard( (bool) $request->get_param( 'refresh' ) )
				);
			},
		)
	);
}
add_action( 'rest_api_init', 'softkom_v3_autoseo_register_rest_routes' );

/**
 * Twice-daily cache refresh.
 */
function softkom_v3_autoseo_schedule_refresh() {
	if ( ! wp_next_scheduled( 'softkom_v3_autoseo_refresh' ) ) {
		wp_schedule_event( time() + 300, 'twicedaily', 'softkom_v3_autoseo_refresh' );
	}
}
add_action( 'init', 'softkom_v3_autoseo_schedule_refresh', 40 );

add_action(
	'softkom_v3_autoseo_refresh',
	function () {
		softkom_v3_autoseo_live_dashboard( true );
	}
);


/**
 * Fetch consolidated Search Console, GA4, AI visibility and lead data
 * from the existing Softkom Visibility Engine.
 *
 * @param bool $force Ignore cache.
 * @return array<string,mixed>|WP_Error
 */
function softkom_v3_autoseo_collect_visibility_bridge( $force = false ) {
	$cache_key = 'softkom_autoseo_visibility_bridge';
	if ( ! $force ) {
		$cached = get_transient( $cache_key );
		if ( is_array( $cached ) ) {
			return $cached;
		}
	}

	$base   = rtrim( softkom_v3_autoseo_config( 'visibility_bridge_url' ), '/' );
	$secret = softkom_v3_autoseo_config( 'visibility_bridge_secret' );

	if ( '' === $base || '' === $secret ) {
		return new WP_Error( 'autoseo_bridge_not_configured', 'Visibility Engine bridge is not configured.' );
	}

	$url = add_query_arg(
		array( 'site' => home_url( '/' ) ),
		$base . '/api/autoseo/export'
	);

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 25,
			'headers' => array(
				'Authorization' => 'Bearer ' . $secret,
				'Accept'        => 'application/json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( 200 !== $code || ! is_array( $body ) ) {
		return new WP_Error(
			'autoseo_bridge_failed',
			'Visibility Engine bridge request failed.',
			array( 'status' => $code )
		);
	}

	set_transient( $cache_key, $body, 30 * MINUTE_IN_SECONDS );
	return $body;
}

/**
 * Convert bridge Search Console rows into AutoSEO opportunities.
 *
 * @param array<string,mixed> $bridge Bridge payload.
 * @return array<int,array<string,mixed>>
 */
function softkom_v3_autoseo_bridge_opportunities( $bridge ) {
	$gsc = array(
		'rows' => array_map(
			function ( $row ) {
				return array(
					'query'       => $row['query'] ?? '',
					'page'        => $row['pageUrl'] ?? '',
					'clicks'      => (float) ( $row['clicks'] ?? 0 ),
					'impressions' => (float) ( $row['impressions'] ?? 0 ),
					'ctr'         => (float) ( $row['ctr'] ?? 0 ),
					'position'    => (float) ( $row['position'] ?? 0 ),
				);
			},
			(array) ( $bridge['search'] ?? array() )
		),
	);

	return softkom_v3_autoseo_gsc_opportunities( $gsc );
}

/**
 * Prefer Visibility Engine as the live source of truth when configured.
 *
 * @param bool $force Refresh cached collectors.
 * @return array<string,mixed>
 */
function softkom_v3_autoseo_live_dashboard_v2( $force = false ) {
	$bridge = softkom_v3_autoseo_collect_visibility_bridge( $force );

	if ( ! is_wp_error( $bridge ) ) {
		return array(
			'status' => array(
				'bridge' => 'connected',
				'gsc'    => ! empty( $bridge['search'] ) ? 'connected' : 'empty',
				'ga4'    => ! empty( $bridge['analytics']['channels'] ) ? 'connected' : 'empty',
				'ai'     => ! empty( $bridge['ai']['platforms'] ) ? 'connected' : 'empty',
			),
			'bridge'        => $bridge,
			'opportunities' => softkom_v3_autoseo_bridge_opportunities( $bridge ),
			'fetched_at'    => current_time( 'mysql', true ),
		);
	}

	return softkom_v3_autoseo_live_dashboard( $force );
}
