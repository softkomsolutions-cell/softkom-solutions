<?php
/**
 * Softkom IndexNow Admin Queue Action Regression Tests
 *
 * Runs via WP-CLI:
 * wp eval-file tests/run-indexnow-admin-qa.php
 *
 * Verifies the Tools-page "Queue All 13 Acquisition URLs" action is
 * production-safe: it must run inline on the Tools page (no admin-post.php),
 * verify permissions, verify the CSRF nonce, queue exactly the cluster URLs in
 * the background, tolerate a refresh without double-submitting, and keep the
 * outbound IndexNow HTTP call inside the cron event rather than the browser
 * request. The test restores every option and cron entry it changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo "[ERROR] ABSPATH not defined. This script must be executed via WP-CLI:\n";
	echo "wp eval-file tests/run-indexnow-admin-qa.php\n";
	exit( 1 );
}

if ( ! defined( 'DOING_CRON' ) ) {
	define( 'DOING_CRON', true );
}

$GLOBALS['softkom_idx_passed'] = 0;
$GLOBALS['softkom_idx_failed'] = 0;

function softkom_idx_assert( $label, $condition ) {
	if ( $condition ) {
		$GLOBALS['softkom_idx_passed']++;
		echo "[PASS] {$label}\n";
	} else {
		$GLOBALS['softkom_idx_failed']++;
		echo "[FAIL] {$label}\n";
	}
}

$softkom_idx_tracked = array(
	'softkom_indexnow_last_queued_gmt',
	'softkom_indexnow_last_queued_count',
	'softkom_indexnow_last_code',
	'softkom_indexnow_last_submit_gmt',
	'softkom_indexnow_last_urls',
	'softkom_indexnow_last_error',
	'softkom_indexnow_queue_token',
);
$softkom_idx_saved   = array();
foreach ( $softkom_idx_tracked as $softkom_idx_opt ) {
	$softkom_idx_saved[ $softkom_idx_opt ] = get_option( $softkom_idx_opt, null );
}
$softkom_idx_cron_before = get_option( 'cron' );

function softkom_idx_restore() {
	global $softkom_idx_saved, $softkom_idx_tracked, $softkom_idx_cron_before;
	foreach ( $softkom_idx_tracked as $opt ) {
		if ( null === $softkom_idx_saved[ $opt ] ) {
			delete_option( $opt );
		} else {
			update_option( $opt, $softkom_idx_saved[ $opt ], false );
		}
	}
	update_option( 'cron', $softkom_idx_cron_before, false );
}
register_shutdown_function( 'softkom_idx_restore' );

function softkom_idx_render_tools_page() {
	ob_start();
	softkom_indexnow_diagnostics_page();
	return ob_get_clean();
}

function softkom_idx_post( $user_id, $nonce, $token ) {
	wp_set_current_user( $user_id );
	$_POST['softkom_indexnow_submit_all']    = '1';
	$_POST['_wpnonce']                       = $nonce;
	$_POST['softkom_indexnow_submit_token']  = $token;
	$out = softkom_idx_render_tools_page();
	unset( $_POST['softkom_indexnow_submit_all'], $_POST['_wpnonce'], $_POST['softkom_indexnow_submit_token'] );
	return $out;
}

function softkom_idx_event_count( $hook ) {
	$count = 0;
	$cron  = get_option( 'cron' );
	if ( ! is_array( $cron ) ) {
		return 0;
	}
	foreach ( $cron as $timestamp => $events ) {
		if ( is_array( $events ) && isset( $events[ $hook ] ) ) {
			$count += count( $events[ $hook ] );
		}
	}
	return $count;
}

function softkom_idx_event_payloads( $hook ) {
	$payloads = array();
	$cron     = get_option( 'cron' );
	if ( ! is_array( $cron ) ) {
		return $payloads;
	}
	foreach ( $cron as $timestamp => $events ) {
		if ( is_array( $events ) && isset( $events[ $hook ] ) ) {
			foreach ( $events[ $hook ] as $key => $event ) {
				$payloads[] = isset( $event['args'] ) ? (array) $event['args'] : array();
			}
		}
	}
	return $payloads;
}

echo "=========================================================\n";
echo "Softkom IndexNow Admin Queue Action Regression Tests\n";
echo "=========================================================\n\n";

try {
	$admins = get_users( array(
		'role'   => 'administrator',
		'number' => 1,
		'fields' => 'ID',
	) );
	$admin  = ! empty( $admins[0] ) ? (int) $admins[0] : 0;
	softkom_idx_assert( 'Administrator user found for tests', $admin > 0 );
	if ( $admin <= 0 ) {
		throw new RuntimeException( 'No administrator user available on this environment.' );
	}

	wp_set_current_user( $admin );
	$nonce     = wp_create_nonce( 'softkom_indexnow_submit_all' );
	$cluster   = softkom_indexnow_cluster_urls();
	$slugcount = count( softkom_indexnow_slugs() );

	softkom_idx_assert( 'IndexNow handler functions are loaded', function_exists( 'softkom_indexnow_handle_queue_submit' ) && function_exists( 'softkom_indexnow_diagnostics_page' ) );
	softkom_idx_assert( 'Queue action no longer uses admin-post.php', strpos( (string) file_get_contents( ABSPATH . 'wp-content/mu-plugins/softkom-indexnow.php' ), 'admin-post.php' ) === false );
	softkom_idx_assert( 'Queue action verifies the CSRF nonce', strpos( (string) file_get_contents( ABSPATH . 'wp-content/mu-plugins/softkom-indexnow.php' ), 'wp_verify_nonce' ) !== false );

	// ---------------------------------------------------------------------
	// 1. Tools page loads normally (GET) for an administrator.
	// ---------------------------------------------------------------------
	$page = softkom_idx_render_tools_page();
	softkom_idx_assert( 'Tools page renders without a fatal', is_string( $page ) && '' !== $page );
	softkom_idx_assert( 'Tools page shows the IndexNow panel title', false !== strpos( $page, 'Softkom IndexNow' ) );
	softkom_idx_assert( 'Tools page shows the verified control button', false !== strpos( $page, 'Queue All 13 Acquisition URLs' ) );
	softkom_idx_assert( 'Tools page shows verification key row', false !== strpos( $page, 'Verification key' ) );
	softkom_idx_assert( 'Tools page posts inline (no admin-post action)', false === strpos( $page, 'admin-post.php' ) );

	// ---------------------------------------------------------------------
	// 2. Permission check: a non-admin cannot queue anything.
	// ---------------------------------------------------------------------
	$before_gmt      = get_option( 'softkom_indexnow_last_queued_gmt', '' );
	$before_events   = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	$non_admin_req   = softkom_idx_post( 0, $nonce, softkom_indexnow_queue_token() );
	softkom_idx_assert( 'Non-admin request produces no page output', '' === $non_admin_req );
	$after_events    = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	softkom_idx_assert( 'Non-admin request queues nothing', $after_events === $before_events );
	softkom_idx_assert( 'Non-admin request does not update queued time', get_option( 'softkom_indexnow_last_queued_gmt', '' ) === $before_gmt );

	// ---------------------------------------------------------------------
	// 3. Exactly the cluster URLs are queued by an inline POST.
	// ---------------------------------------------------------------------
	$before_events = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	$token   = softkom_indexnow_queue_token();
	$req       = softkom_idx_post( $admin, $nonce, $token );
	$after     = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	softkom_idx_assert( 'Queue action does not blank/fatal and renders the page', is_string( $req ) && false !== strpos( $req, 'acquisition URLs queued for background submission' ) );
	softkom_idx_assert( 'Queue action schedules one background event', $after === $before_events + 1 );
	softkom_idx_assert( 'Exactly 13 slugs are in the cluster', 13 === $slugcount );
	softkom_idx_assert( 'Exactly 13 published URLs are queued', 13 === count( $cluster ) );

	$queued_payloads = softkom_idx_event_payloads( 'softkom_indexnow_submit_event' );
	$has_13          = false;
	foreach ( $queued_payloads as $payload ) {
		if ( isset( $payload[0] ) && is_array( $payload[0] ) && count( $payload[0] ) === 13 ) {
			$has_13 = true;
		}
	}
	softkom_idx_assert( 'Queued background payload carries all 13 URLs', $has_13 );
	$queued_gmt = get_option( 'softkom_indexnow_last_queued_gmt', '' );
	softkom_idx_assert( 'Last queued timestamp is recorded', '' !== $queued_gmt );
	softkom_idx_assert( 'Last queued count is recorded as 13', 13 === (int) get_option( 'softkom_indexnow_last_queued_count', 0 ) );

	// ---------------------------------------------------------------------
	// 4. Nonce validation: a bad nonce must not queue anything.
	// ---------------------------------------------------------------------
	$before_events = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	$bad_req       = softkom_idx_post( $admin, 'forged-9b3d51', softkom_indexnow_queue_token() );
	softkom_idx_assert( 'Bad nonce shows the security-failed notice', false !== strpos( $bad_req, 'Security check failed' ) );
	softkom_idx_assert( 'Bad nonce queues nothing', softkom_idx_event_count( 'softkom_indexnow_submit_event' ) === $before_events );

	// ---------------------------------------------------------------------
	// 5. Refresh / double-submit safety: the consumed token cannot re-queue.
	// ---------------------------------------------------------------------
	$token          = softkom_indexnow_queue_token();
	$first          = softkom_idx_post( $admin, $nonce, $token );
	$events_after_1 = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	$second         = softkom_idx_post( $admin, $nonce, $token );
	$events_after_2 = softkom_idx_event_count( 'softkom_indexnow_submit_event' );
	softkom_idx_assert( 'First submit from a fresh token succeeds', false !== strpos( $first, 'acquisition URLs queued for background submission' ) );
	softkom_idx_assert( 'Refresh with the same token shows already-queued notice', false !== strpos( $second, 'already queued' ) );
	softkom_idx_assert( 'Refresh with the same token does not double-submit', $events_after_2 === $events_after_1 );

	// ---------------------------------------------------------------------
	// 6. Async separation: no outbound IndexNow call inside the browser request.
	// ---------------------------------------------------------------------
	$GLOBALS['softkom_idx_outbound'] = array();
	$GLOBALS['softkom_idx_mock_http_code'] = 202;
	add_filter( 'pre_http_request', function ( $pre, $args, $url ) {
		$GLOBALS['softkom_idx_outbound'][] = (string) $url;
		if ( false !== strpos( (string) $url, 'api.indexnow.org' ) ) {
			return array(
				'headers'  => array(),
				'body'     => '',
				'response' => array( 'code' => (int) $GLOBALS['softkom_idx_mock_http_code'] ),
				'cookies'  => array(),
				'filename' => null,
			);
		}
		return $pre;
	}, 10, 3 );

	$before_code = get_option( 'softkom_indexnow_last_code', 'Not submitted yet' );
	$token       = softkom_indexnow_queue_token();
	softkom_idx_post( $admin, $nonce, $token );
	$hits_to_indexnow = 0;
	foreach ( (array) $GLOBALS['softkom_idx_outbound'] as $url ) {
		if ( false !== strpos( $url, 'api.indexnow.org' ) ) {
			$hits_to_indexnow++;
		}
	}
	softkom_idx_assert( 'Browser-request handler makes no outbound IndexNow call', 0 === $hits_to_indexnow );
	softkom_idx_assert( 'Browser-request handler does not record a submission response', get_option( 'softkom_indexnow_last_code', 'Not submitted yet' ) === $before_code );

	// ---------------------------------------------------------------------
	// 7. The async cron callback still records HTTP 202 and stores 13 URLs.
	// ---------------------------------------------------------------------
	$GLOBALS['softkom_idx_mock_http_code'] = 202;
	$ok202 = softkom_indexnow_submit_urls( $cluster );
	softkom_idx_assert( 'Async submission accepts HTTP 202', true === $ok202 );
	softkom_idx_assert( 'Async submission records last HTTP response 202', 202 === (int) get_option( 'softkom_indexnow_last_code', 0 ) );
	softkom_idx_assert( 'Async submission records last submission time', '' !== (string) get_option( 'softkom_indexnow_last_submit_gmt', '' ) );
	softkom_idx_assert( 'Stored last URL count becomes 13', 13 === count( (array) get_option( 'softkom_indexnow_last_urls', array() ) ) );

	// ---------------------------------------------------------------------
	// 8. HTTP 200 is also still accepted and recorded.
	// ---------------------------------------------------------------------
	$GLOBALS['softkom_idx_mock_http_code'] = 200;
	$ok200 = softkom_indexnow_submit_urls( $cluster );
	softkom_idx_assert( 'Async submission accepts HTTP 200', true === $ok200 );
	softkom_idx_assert( 'Async submission records last HTTP response 200', 200 === (int) get_option( 'softkom_indexnow_last_code', 0 ) );
	softkom_idx_assert( 'Stored last URL count remains 13 after 200', 13 === count( (array) get_option( 'softkom_indexnow_last_urls', array() ) ) );
} catch ( Throwable $e ) {
	softkom_idx_assert( 'Test suite ran without a fatal exception: ' . $e->getMessage(), false );
}

echo "\n=========================================================\n";
printf( "RESULT: %d passed / %d failed\n", $GLOBALS['softkom_idx_passed'], $GLOBALS['softkom_idx_failed'] );
echo "NOTE: options/cron modified by this suite were restored.\n";
echo "=========================================================\n";

exit( $GLOBALS['softkom_idx_failed'] > 0 ? 1 : 0 );