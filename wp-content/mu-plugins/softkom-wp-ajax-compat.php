<?php
/**
 * Softkom WordPress AJAX compatibility bootstrap.
 *
 * Some frontend bundles (including the notifications UI) expect the core
 * `wp.ajax` helper to exist. WordPress normally exposes that helper through
 * `wp-util`, but third-party/generated bundles can execute before it is ready.
 *
 * This MU plugin both enqueues the core dependency and installs a tiny fallback
 * in the document head so `window.wp.ajax` is always present before frontend
 * application bundles execute.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure WordPress AJAX utilities are queued as early as possible.
 */
function softkom_enqueue_wp_ajax_utilities() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wp-util' );
}
add_action( 'wp_enqueue_scripts', 'softkom_enqueue_wp_ajax_utilities', 1 );

/**
 * Provide an early `wp.ajax` compatibility layer when a bundle runs before
 * WordPress core's wp-util script has initialised.
 *
 * The API mirrors the commonly used `wp.ajax.post()` / `wp.ajax.send()` shape:
 * successful WordPress AJAX responses resolve with `response.data`, while
 * errors reject with the returned error payload/message.
 */
function softkom_print_wp_ajax_fallback() {
	$ajax_url = admin_url( 'admin-ajax.php' );
	?>
	<script id="softkom-wp-ajax-compat">
	(function (window) {
		'use strict';

		window.wp = window.wp || {};
		if (window.wp.ajax && typeof window.wp.ajax.post === 'function') {
			return;
		}

		var ajaxUrl = <?php echo wp_json_encode( esc_url_raw( $ajax_url ) ); ?>;

		function request(action, data) {
			var body = new URLSearchParams();
			body.append('action', action);

			Object.keys(data || {}).forEach(function (key) {
				var value = data[key];
				if (value === undefined || value === null) {
					return;
				}
				if (typeof value === 'object') {
					body.append(key, JSON.stringify(value));
				} else {
					body.append(key, String(value));
				}
			});

			return fetch(ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
				body: body.toString()
			}).then(function (response) {
				return response.text();
			}).then(function (text) {
				var payload;
				try {
					payload = JSON.parse(text);
				} catch (e) {
					throw new Error(text || 'Invalid WordPress AJAX response');
				}

				if (payload && payload.success === true) {
					return payload.data;
				}

				var message = payload && payload.data ? payload.data : 'WordPress AJAX request failed';
				throw message instanceof Error ? message : new Error(
					typeof message === 'string' ? message : JSON.stringify(message)
				);
			});
		}

		window.wp.ajax = window.wp.ajax || {};
		window.wp.ajax.post = function (action, data) {
			return request(action, data || {});
		};
		window.wp.ajax.send = function (action, options) {
			options = options || {};
			var promise = request(action, options.data || {});
			if (typeof options.success === 'function') {
				promise = promise.then(function (data) {
					options.success(data);
					return data;
				});
			}
			if (typeof options.error === 'function') {
				promise = promise.catch(function (error) {
					options.error(error);
					throw error;
				});
			}
			return promise;
		};
	})(window);
	</script>
	<?php
}
add_action( 'wp_head', 'softkom_print_wp_ajax_fallback', 0 );
