<?php
/**
 * Softkom Data Migration Utility.
 *
 * Admin-only migration tools for moving the Softkom commercial catalogue and
 * funnel records between LocalWP and staging without copying normal WordPress
 * pages, users, media, plugin settings or unrelated site content.
 *
 * @package Softkom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function softkom_catalogue_migration_option_name() {
	return 'softkom_v3_commercial_catalogue';
}

function softkom_catalogue_migration_admin_menu() {
	add_submenu_page(
		'softkom-commercial-catalogue',
		'Softkom Data Migration',
		'Migration',
		'manage_options',
		'softkom-catalogue-migration',
		'softkom_catalogue_migration_render_page'
	);
}
add_action( 'admin_menu', 'softkom_catalogue_migration_admin_menu', 99 );

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
			? $service['implementation'] : array();

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

	return empty( $clean ) ? new WP_Error( 'empty_catalogue', 'No valid catalogue services were found.' ) : $clean;
}

function softkom_catalogue_migration_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'softkom_catalogue_migration_export' );

	$option_name = softkom_catalogue_migration_option_name();
	$catalogue   = get_option( $option_name, array() );
	if ( empty( $catalogue ) && function_exists( 'softkom_v3_commercial_catalogue' ) ) {
		$catalogue = softkom_v3_commercial_catalogue();
	}

	$payload = array(
		'format'      => 'softkom-commercial-catalogue',
		'version'     => 1,
		'exported_at' => gmdate( 'c' ),
		'site_url'    => home_url( '/' ),
		'option_name' => $option_name,
		'catalogue'   => $catalogue,
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="softkom-commercial-catalogue-' . gmdate( 'Ymd-His' ) . '.json"' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'admin_post_softkom_catalogue_migration_export', 'softkom_catalogue_migration_export' );

function softkom_catalogue_migration_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'softkom_catalogue_migration_import' );

	if ( empty( $_FILES['softkom_catalogue_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['softkom_catalogue_file']['tmp_name'] ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => 'missing_file' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$raw     = file_get_contents( $_FILES['softkom_catalogue_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => 'invalid_json' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$clean = softkom_catalogue_migration_sanitize( isset( $decoded['catalogue'] ) ? $decoded['catalogue'] : $decoded );
	if ( is_wp_error( $clean ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_error' => rawurlencode( $clean->get_error_message() ) ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$option_name = softkom_catalogue_migration_option_name();
	$backup_key  = $option_name . '_backup_' . gmdate( 'Ymd_His' );
	update_option( $backup_key, get_option( $option_name, array() ), false );
	update_option( $option_name, $clean, false );

	wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'migration_done' => '1', 'migration_backup' => $backup_key ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_post_softkom_catalogue_migration_import', 'softkom_catalogue_migration_import' );

/**
 * Export all Softkom funnel records and their Softkom-owned metadata.
 */
function softkom_funnel_migration_collect_records() {
	$records = array();
	foreach ( array( 'softkom_lead', 'softkom_campaign' ) as $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'future' ),
				'posts_per_page' => -1,
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);

		foreach ( $posts as $post ) {
			$meta_out = array();
			$all_meta = get_post_meta( $post->ID );
			foreach ( $all_meta as $key => $values ) {
				if ( 0 !== strpos( $key, '_softkom_' ) ) {
					continue;
				}
				$meta_out[ $key ] = array_map( 'maybe_unserialize', $values );
			}

			$records[] = array(
				'source_id'   => (int) $post->ID,
				'post_type'   => $post_type,
				'post_title'  => $post->post_title,
				'post_status' => $post->post_status,
				'post_date'   => $post->post_date,
				'meta'        => $meta_out,
			);
		}
	}
	return $records;
}

function softkom_funnel_migration_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'softkom_funnel_migration_export' );

	$records = softkom_funnel_migration_collect_records();
	$payload = array(
		'format'      => 'softkom-funnel-data',
		'version'     => 1,
		'exported_at' => gmdate( 'c' ),
		'site_url'    => home_url( '/' ),
		'counts'      => array(
			'leads'     => count( array_filter( $records, function ( $r ) { return 'softkom_lead' === $r['post_type']; } ) ),
			'campaigns' => count( array_filter( $records, function ( $r ) { return 'softkom_campaign' === $r['post_type']; } ) ),
		),
		'records'     => $records,
	);

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="softkom-funnel-data-' . gmdate( 'Ymd-His' ) . '.json"' );
	echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit;
}
add_action( 'admin_post_softkom_funnel_migration_export', 'softkom_funnel_migration_export' );

/**
 * Import Softkom funnel records. Existing imports from the same source record
 * are updated instead of duplicated.
 */
function softkom_funnel_migration_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'softkom_funnel_migration_import' );

	if ( empty( $_FILES['softkom_funnel_file']['tmp_name'] ) || ! is_uploaded_file( $_FILES['softkom_funnel_file']['tmp_name'] ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'funnel_error' => 'missing_file' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$raw     = file_get_contents( $_FILES['softkom_funnel_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$decoded = json_decode( $raw, true );
	if ( ! is_array( $decoded ) || 'softkom-funnel-data' !== ( $decoded['format'] ?? '' ) || empty( $decoded['records'] ) || ! is_array( $decoded['records'] ) ) {
		wp_safe_redirect( add_query_arg( array( 'page' => 'softkom-catalogue-migration', 'funnel_error' => 'invalid_funnel_json' ), admin_url( 'admin.php' ) ) );
		exit;
	}

	$source_site = isset( $decoded['site_url'] ) ? esc_url_raw( $decoded['site_url'] ) : '';
	$existing_backup = softkom_funnel_migration_collect_records();
	$backup_key = 'softkom_funnel_backup_' . gmdate( 'Ymd_His' );
	update_option( $backup_key, $existing_backup, false );

	$created = 0;
	$updated = 0;
	$skipped = 0;

	foreach ( $decoded['records'] as $record ) {
		if ( ! is_array( $record ) ) {
			$skipped++;
			continue;
		}
		$post_type = isset( $record['post_type'] ) ? sanitize_key( $record['post_type'] ) : '';
		if ( ! in_array( $post_type, array( 'softkom_lead', 'softkom_campaign' ), true ) ) {
			$skipped++;
			continue;
		}
		$source_id = isset( $record['source_id'] ) ? (int) $record['source_id'] : 0;
		if ( $source_id <= 0 ) {
			$skipped++;
			continue;
		}

		$existing = get_posts(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array( 'key' => '_softkom_migration_source_id', 'value' => $source_id ),
					array( 'key' => '_softkom_migration_source_site', 'value' => $source_site ),
				),
			)
		);

		$post_data = array(
			'post_type'   => $post_type,
			'post_title'  => isset( $record['post_title'] ) ? sanitize_text_field( $record['post_title'] ) : '',
			'post_status' => isset( $record['post_status'] ) ? sanitize_key( $record['post_status'] ) : ( 'softkom_lead' === $post_type ? 'private' : 'draft' ),
		);
		if ( ! empty( $record['post_date'] ) ) {
			$post_data['post_date'] = sanitize_text_field( $record['post_date'] );
		}

		if ( ! empty( $existing ) ) {
			$post_data['ID'] = (int) $existing[0];
			$post_id = wp_update_post( $post_data, true );
			$updated++;
		} else {
			$post_id = wp_insert_post( $post_data, true );
			$created++;
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			$skipped++;
			continue;
		}

		update_post_meta( $post_id, '_softkom_migration_source_id', $source_id );
		update_post_meta( $post_id, '_softkom_migration_source_site', $source_site );

		if ( isset( $record['meta'] ) && is_array( $record['meta'] ) ) {
			foreach ( $record['meta'] as $key => $values ) {
				$key = sanitize_key( $key );
				if ( 0 !== strpos( $key, '_softkom_' ) || in_array( $key, array( '_softkom_migration_source_id', '_softkom_migration_source_site' ), true ) ) {
					continue;
				}
				delete_post_meta( $post_id, $key );
				foreach ( (array) $values as $value ) {
					add_post_meta( $post_id, $key, $value );
				}
			}
		}
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'page'          => 'softkom-catalogue-migration',
				'funnel_done'   => 1,
				'funnel_created'=> $created,
				'funnel_updated'=> $updated,
				'funnel_skipped'=> $skipped,
				'funnel_backup' => $backup_key,
			),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_softkom_funnel_migration_import', 'softkom_funnel_migration_import' );

function softkom_catalogue_migration_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$catalogue = function_exists( 'softkom_v3_commercial_catalogue' )
		? softkom_v3_commercial_catalogue()
		: get_option( softkom_catalogue_migration_option_name(), array() );
	$lead_count = (int) wp_count_posts( 'softkom_lead' )->private + (int) wp_count_posts( 'softkom_lead' )->publish + (int) wp_count_posts( 'softkom_lead' )->draft;
	$campaign_counts = wp_count_posts( 'softkom_campaign' );
	$campaign_count = (int) $campaign_counts->publish + (int) $campaign_counts->private + (int) $campaign_counts->draft + (int) $campaign_counts->pending;
	?>
	<div class="wrap">
		<h1>Softkom Data Migration</h1>
		<p>Controlled LocalWP → staging migration for Softkom-owned commercial and funnel data only.</p>

		<?php if ( isset( $_GET['migration_done'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Catalogue imported successfully. Backup: <code><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['migration_backup'] ?? '' ) ) ); ?></code></p></div>
		<?php endif; ?>
		<?php if ( ! empty( $_GET['migration_error'] ) ) : ?>
			<div class="notice notice-error"><p>Catalogue import failed: <?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['migration_error'] ) ) ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['funnel_done'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Funnel import complete: <?php echo (int) $_GET['funnel_created']; ?> created, <?php echo (int) $_GET['funnel_updated']; ?> updated, <?php echo (int) $_GET['funnel_skipped']; ?> skipped. Backup: <code><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['funnel_backup'] ?? '' ) ) ); ?></code></p></div>
		<?php endif; ?>
		<?php if ( ! empty( $_GET['funnel_error'] ) ) : ?>
			<div class="notice notice-error"><p>Funnel import failed: <?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['funnel_error'] ) ) ); ?></p></div>
		<?php endif; ?>

		<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;max-width:900px;margin:20px 0;">
			<h2>Current Environment</h2>
			<p><strong><?php echo esc_html( count( $catalogue ) ); ?></strong> catalogue services &nbsp;·&nbsp; <strong><?php echo esc_html( $lead_count ); ?></strong> leads &nbsp;·&nbsp; <strong><?php echo esc_html( $campaign_count ); ?></strong> campaigns</p>
			<?php if ( isset( $catalogue['managed_growth']['plans']['scale']['monthly'] ) ) : ?>
				<p>Managed Growth / Scale: <strong>R<?php echo esc_html( number_format_i18n( (int) $catalogue['managed_growth']['plans']['scale']['monthly'] ) ); ?>/month</strong></p>
			<?php endif; ?>
		</div>

		<h2>Commercial Catalogue</h2>
		<div style="display:flex;gap:24px;flex-wrap:wrap;max-width:1100px;margin-bottom:30px;">
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h3>Export from LocalWP</h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="softkom_catalogue_migration_export"><?php wp_nonce_field( 'softkom_catalogue_migration_export' ); ?><?php submit_button( 'Download Catalogue JSON', 'primary', 'submit', false ); ?></form>
			</div>
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h3>Import into Staging</h3>
				<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="softkom_catalogue_migration_import"><?php wp_nonce_field( 'softkom_catalogue_migration_import' ); ?><input type="file" name="softkom_catalogue_file" accept="application/json,.json" required><p><?php submit_button( 'Import Catalogue JSON', 'primary', 'submit', false ); ?></p></form>
			</div>
		</div>

		<h2>Funnel Data</h2>
		<p>Includes Softkom Leads and Campaigns plus all <code>_softkom_*</code> metadata: scoring, qualification, maturity, attribution, routing, commercial recommendation, pipeline values and recurring revenue fields. It does not include normal WordPress pages, users, media or unrelated plugin data.</p>
		<div style="display:flex;gap:24px;flex-wrap:wrap;max-width:1100px;">
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h3>1. Export Funnel from LocalWP</h3>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="softkom_funnel_migration_export"><?php wp_nonce_field( 'softkom_funnel_migration_export' ); ?><?php submit_button( 'Download Funnel JSON', 'primary', 'submit', false ); ?></form>
			</div>
			<div style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:20px;flex:1;min-width:320px;">
				<h3>2. Import Funnel into Staging</h3>
				<p>Existing staging Softkom funnel records are backed up first. Re-importing the same LocalWP file updates previously migrated records instead of duplicating them.</p>
				<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="softkom_funnel_migration_import"><?php wp_nonce_field( 'softkom_funnel_migration_import' ); ?><input type="file" name="softkom_funnel_file" accept="application/json,.json" required><p><?php submit_button( 'Import Funnel JSON', 'primary', 'submit', false ); ?></p></form>
			</div>
		</div>
	</div>
	<?php
}
