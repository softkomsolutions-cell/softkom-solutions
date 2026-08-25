<?php
/**
 * Softkom Commercial Catalogue Migration Utility.
 *
 * Admin-only export/import tool for moving the exact commercial catalogue
 * between LocalWP, staging and other controlled environments without copying
 * leads, campaigns, users, pages or unrelated WordPress data.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Catalogue option handled by this utility.
 */
function softkom_catalogue_migration_option_name() {
	return 'softkom_v3_commercial_catalogue';
}

/**
 * Register a migration submenu under the Softkom Catalogue menu.
 */
function softkom_catalogue_migration_admin_menu() {
	add_submenu_page(
		'softkom-commercial-catalogue',
		'Catalogue Migration',
		'Migration',
		'manage_options',
		'softkom-catalogue-migration',
		'softkom_catalogue_migration_render_page'
	);
}
add_action( 'admin_menu', 'softkom_catalogue_migration_admin_menu', 99 );

/**
 * Validate and sanitize an imported catalogue payload.
 *
 * @param mixed $catalogue Raw decoded JSON payload.
 * @return array|WP_Error
 */
function softkom_catalogue_migration_sanitize( $catalogue ) {
	if ( ! is_array( $catalogue ) || empty( $catalogue ) ) {
		return new WP_Error( 'invalid_catalogue', 'The imported JSON does not contain a catalogue.' );
	}

	$clean = array();

	foreach ( $catalogue as $service_key => $service ) {
		$service_key = sanitize_key( $service_key );
		if ( '' === $service_key || ! is_array( $service ) ) {
			continue;
		}

		$plans = array();
		if ( isset( $service['plans'] ) && is_array( $service['plans'] ) ) {
			foreach ( $service['plans'] as $plan_key => $plan ) {
				$plan_key = sanitize_key( $plan_key );
				if ( '' === $plan_key || ! is_array( $plan ) ) {
					continue;
				}

				$plans[ $plan_key ] = array(
					'name'    => isset( $plan['name'] ) ? sanitize_text_field( $plan['name'] ) : '',
					'monthly' => isset( $plan['monthly'] ) ? max( 0, (int) $plan['monthly'] ) : 0,
				);
			}
		}

		$implementation = isset( $service['implementation'] ) && is_array( $service['implementation'] )
			? $service['implementation']
			: array();

		$clean[ $service_key ] = array(
			'name'           => isset( $service['name'] ) ? sanitize_text_field( $service['name'] ) : '',
			'category'       => isset( $service['category'] ) ? sanitize_text_field( $service['category'] ) : '',
			'implementation' => array(
				'name'       => isset( $implementation['name'] ) ? sanitize_text_field( $implementation['name'] ) : '',
				'price_from' => isset( $implementation['price_from'] ) ? max( 0, (int) $implementation['price_from'] ) : 0,
			),
			'plans'          => $plans,
		);
	}

	if ( empty( $clean ) ) {
		return new WP_Error( 'empty_catalogue', 'No valid catalogue services were found in the imported JSON.' );
	}

	return $clean;
}

/**
 * Handle JSON export download.
 */
function softkom_catalogue_migration_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to export this catalogue.', 'softkom-v3' ) );
	}

	check_admin_referer( 'softkom_catalogue_migration_export' );

	$option_name = softkom_catalogue_migration_option_name();
	$catalogue   = get_option( $option_name, array() );

	if ( empty( $catalogue ) && function_exists( 'softkom_v3_commercial_catalogue' ) ) {
		$catalogue = softkom_v3_commercial_catalogue();
	}

	$payload = array(
		'format'       => 'softkom-commercial-catalogue',
		'version'      => 1,
		'exported_at'  => gmdate( 'c' ),
		'site_url'     => home_url( '/' ),
		'option_name'  => $option_name,
		'catalogue'    => $catalogue,
	);

	$filename = 'softkom-commercial-catalogue-' . gmdate( 'Ymd-His' ) . '.json';

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'admin_post_softkom_catalogue_migration_export', 'softkom_catalogue_migration_export' );

/**
 * Handle JSON import.
 */
function softkom_catalogue_migration_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to import this catalogue.', 'softkom-v3' ) );
	}

	check_admin_referer( 'softkom_catalogue_migration_import' );

	if ( empty( $_FILES['softkom_catalogue_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['softkom_catalogue_file']['tmp_name'] ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => 'missing_file' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$raw = file_get_contents( $_FILES['softkom_catalogue_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$decoded = json_decode( $raw, true );

	if ( ! is_array( $decoded ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => 'invalid_json' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$catalogue = isset( $decoded['catalogue'] ) ? $decoded['catalogue'] : $decoded;
	$clean     = softkom_catalogue_migration_sanitize( $catalogue );

	if ( is_wp_error( $clean ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => rawurlencode( $clean->get_error_message() ) ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$option_name = softkom_catalogue_migration_option_name();
	$existing    = get_option( $option_name, array() );

	$backup_key = $option_name . '_backup_' . gmdate( 'Ymd_His' );
	update_option( $backup_key, $existing, false );
	update_option( $option_name, $clean, false );

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'             => 'softkom-catalogue-migration',
				'migration_done'   => '1',
				'migration_backup' => $backup_key,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_softkom_catalogue_migration_import', 'softkom_catalogue_migration_import' );

/**
 * Render migration admin page.
 */
function softkom_catalogue_migration_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$catalogue = function_exists( 'softkom_v3_commercial_catalogue' )
		? softkom_v3_commercial_catalogue()
		: get_option( softkom_catalogue_migration_option_name(), array() );
	?>
	<div class="wrap">
		<h1>Softkom Catalogue Migration</h1>
		<p>Move only the commercial catalogue between environments. Leads, campaigns, users, pages and other WordPress data are not included.</p>

		<?php if ( isset( $_GET['migration_done'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>
				Catalogue imported successfully.
				<?php if ( ! empty( $_GET['migration_backup'] ) ) : ?>
					Backup option: <code><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['migration_backup'] ) ) ); ?></code>
				<?php endif; ?>
			</p></div>
		<?php endif; ?>

		<?php if ( ! empty( $_GET['migration_error'] ) ) : ?>
			<div class="notice notice-error"><p>Import failed: <?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['migration_error'] ) ) ); ?></p></div>
		<?php endif; ?>

		<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;max-width:900px;margin:20px 0;">
			<h2>Current Catalogue</h2>
			<p><strong><?php echo esc_html( count( $catalogue ) ); ?></strong> services currently available.</p>
			<?php if ( isset( $catalogue['managed_growth']['plans']['scale']['monthly'] ) ) : ?>
				<p>Managed Growth / Scale: <strong>R<?php echo esc_html( number_format_i18n( (int) $catalogue['managed_growth']['plans']['scale']['monthly'] ) ); ?>/month</strong></p>
			<?php endif; ?>
		</div>

		<div style="display:flex;gap:24px;flex-wrap:wrap;max-width:1100px;">
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h2>1. Export from LocalWP</h2>
				<p>Download the exact current catalogue as a JSON migration file.</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="softkom_catalogue_migration_export">
					<?php wp_nonce_field( 'softkom_catalogue_migration_export' ); ?>
					<?php submit_button( 'Download Catalogue JSON', 'primary', 'submit', false ); ?>
				</form>
			</div>

			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h2>2. Import into Staging</h2>
				<p>The current staging catalogue is backed up automatically before import.</p>
				<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="softkom_catalogue_migration_import">
					<?php wp_nonce_field( 'softkom_catalogue_migration_import' ); ?>
					<input type="file" name="softkom_catalogue_file" accept="application/json,.json" required>
					<p><?php submit_button( 'Import Catalogue JSON', 'primary', 'submit', false ); ?></p>
				</form>
			</div>
		</div>
	</div>
	<?php
}
