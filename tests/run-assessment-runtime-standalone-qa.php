<?php
/**
 * Assessment runtime regression QA - STANDALONE configuration.
 *
 * Reproduces the fallback deployment: a site whose active theme is NOT
 * softkom-v3, where the bundle runtime must load fully from
 * wp-content/softkom-assessment-runtime/data without any theme help.
 *
 * The fix must NOT regress this path: when the theme does not provide the V3
 * runtime, the standalone loader must still load all data, render the runtime
 * template and register its own shortcode.
 *
 * Methodology (fully isolated, self-restoring):
 *  - Boots WordPress with a non-softkom-v3 active theme (twentytwentyfive)
 *    switched in the DB for this sub-process only, then restored immediately
 *    after boot (a shutdown fallback restores if boot fatals).
 *  - Loads ONLY the standalone mu-plugin from a disposable mu-plugins dir.
 *
 * Intended for the local staging environment only - DO NOT DEPLOY.
 *
 * Usage:
 *   "C:\...\php.exe" -n -d "extension_dir=...\ext" -d "extension=php_mysqli.dll" tests\run-assessment-runtime-standalone-qa.php
 */

$abspath = 'C:/Users/Darren/Local Sites/softkom-solutions/app/public/';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', $abspath ); }

$GLOBALS['qa_pass'] = 0;
$GLOBALS['qa_fail'] = 0;

function qa_assert( $label, $condition ) {
	if ( $condition ) {
		$GLOBALS['qa_pass']++;
		echo "[PASS] {$label}\n";
	} else {
		$GLOBALS['qa_fail']++;
		echo "[FAIL] {$label}\n";
	}
}

$sandbox_mu = 'C:/Users/Darren/AppData/Local/Temp/opencode/softkom-standalone-mu';
if ( ! is_dir( $sandbox_mu ) ) { mkdir( $sandbox_mu, 0777, true ); }
copy( $abspath . 'wp-content/mu-plugins/softkom-assessment-standalone.php', $sandbox_mu . '/softkom-assessment-standalone.php' );

$mysqli = new mysqli( '127.0.0.1:10037', 'root', 'root', 'local' );
if ( $mysqli->connect_errno ) {
	echo '[ERROR] mysqli connect failed: ' . $mysqli->connect_error . "\n";
	exit( 1 );
}
$mysqli->set_charset( 'utf8mb4' );

function softkom_fetch_option( $mysqli, $name ) {
	$stmt = $mysqli->prepare( 'SELECT option_value FROM wp9v_options WHERE option_name = ? LIMIT 1' );
	$stmt->bind_param( 's', $name );
	$stmt->execute();
	$res = $stmt->get_result();
	if ( ! $res ) { return null; }
	$row = $res->fetch_assoc();
	return $row ? $row['option_value'] : null;
}

function softkom_set_option( $mysqli, $name, $value ) {
	$stmt = $mysqli->prepare( 'UPDATE wp9v_options SET option_value = ? WHERE option_name = ?' );
	$stmt->bind_param( 'ss', $value, $name );
	$stmt->execute();
	return $stmt->affected_rows !== -1;
}

$old_template  = softkom_fetch_option( $mysqli, 'template' );
$old_stylesheet = softkom_fetch_option( $mysqli, 'stylesheet' );
$restored = false;

register_shutdown_function(
	function () use ( $mysqli, $old_template, $old_stylesheet, &$restored ) {
		if ( ! $restored ) {
			if ( null !== $old_template ) { softkom_set_option( $mysqli, 'template', $old_template ); }
			if ( null !== $old_stylesheet ) { softkom_set_option( $mysqli, 'stylesheet', $old_stylesheet ); }
		}
	}
);

echo "========================================================================\n";
echo "Assessment Runtime Regression QA - STANDALONE (non-softkom-v3 theme)\n";
echo "========================================================================\n\n";

$switch_ok = softkom_set_option( $mysqli, 'template', 'twentytwentyfive' )
	&& softkom_set_option( $mysqli, 'stylesheet', 'twentytwentyfive' );

if ( ! $switch_ok ) {
	echo "[ERROR] Could not switch active theme for sandbox boot.\n";
	exit( 1 );
}

try {
	$cfg = file_get_contents( $abspath . 'wp-config.php' );
	$cfg = str_replace(
		"define( 'DB_HOST', 'localhost' );",
		"define( 'DB_HOST', '127.0.0.1:10037' );",
		$cfg
	);
	$cfg = str_replace(
		"define( 'WP_DEBUG_DISPLAY', false );",
		"define( 'WP_DEBUG_DISPLAY', true );",
		$cfg
	);
	$cfg = str_replace(
		"require_once ABSPATH . 'wp-settings.php';",
		"define( 'WPMU_PLUGIN_DIR', 'C:/Users/Darren/AppData/Local/Temp/opencode/softkom-standalone-mu' );\n"
		. "define( 'WPMU_PLUGIN_URL', 'http://softkom-solutions.local/wp-content/softkom-standalone-mu' );\n"
		. "require_once ABSPATH . 'wp-settings.php';",
		$cfg
	);
	ini_set( 'display_errors', '1' );
	eval( '?>' . $cfg );

	softkom_set_option( $mysqli, 'template', $old_template );
	softkom_set_option( $mysqli, 'stylesheet', $old_stylesheet );
	$restored = true;

	if ( ! defined( 'ABSPATH' ) || ! function_exists( 'get_option' ) ) {
		echo "[ERROR] Sandbox bootstrap failed.\n";
		exit( 2 );
	}

	qa_assert( 'Sandbox active theme is NOT softkom-v3', 'softkom-v3' !== get_stylesheet() );
	qa_assert( 'Theme loader softkom_v3_load_data() absent (as expected)', ! function_exists( 'softkom_v3_load_data' ) );

	qa_assert(
		'Standalone guard does NOT claim a theme-provided runtime',
		function_exists( 'softkom_assessment_runtime_theme_provides_runtime' ) && ! softkom_assessment_runtime_theme_provides_runtime()
	);

	qa_assert(
		'softkom_assessment_v3 shortcode maps to runtime renderer',
		isset( $GLOBALS['shortcode_tags']['softkom_assessment_v3'] )
		&& 'softkom_assessment_runtime_render' === $GLOBALS['shortcode_tags']['softkom_assessment_v3']
	);

	$html = do_shortcode( '[softkom_assessment_v3]' );
	qa_assert( 'Standalone render uses runtime template', false !== strpos( $html, 'sk-assessment' ) );
	qa_assert( 'Standalone app start button present', false !== strpos( $html, 'data-assessment-start' ) );
	qa_assert( 'No runtime fallback message shown', false === strpos( $html, 'Assessment runtime is not available' ) );
	qa_assert( 'No theme-delegation message shown', false === strpos( $html, 'provided by the active theme' ) );

	qa_assert( 'Lead storage handler exists via runtime', function_exists( 'softkom_v3_store_assessment_lead' ) );
	qa_assert( 'Lead post type registrar exists via runtime', function_exists( 'softkom_v3_register_lead_post_type' ) );
	qa_assert( 'Campaign registrar exists via runtime', function_exists( 'softkom_v3_register_campaign_post_type' ) );
	qa_assert( 'Commercial catalogue exists via runtime', function_exists( 'softkom_v3_commercial_catalogue' ) );
	qa_assert( 'Recurring catalogue exists via runtime', function_exists( 'softkom_v3_recurring_service_catalogue' ) );
	qa_assert( 'Assessment submission handler exists via runtime', function_exists( 'softkom_v3_process_assessment_submission' ) );
	qa_assert( 'Question bank exists via runtime', function_exists( 'softkom_v3_assessment_question_bank' ) );
	qa_assert( 'HOT lead auto-pipeline exists via runtime', function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) );
	qa_assert( 'Recurring recommendation calculator exists via runtime', function_exists( 'softkom_v3_calculate_recurring_recommendation' ) );

	softkom_assessment_runtime_load_data();
	qa_assert( 'load_data() is idempotent (no duplicate declarations)', function_exists( 'softkom_v3_register_lead_post_type' ) );

	qa_assert( 'Standalone asset enqueue helper available', function_exists( 'softkom_assessment_runtime_assets' ) );

	echo "\n========================================================================\n";
	echo 'RESULT: ' . $GLOBALS['qa_pass'] . ' passed, ' . $GLOBALS['qa_fail'] . ' failed' . "\n";
	echo "========================================================================\n";
	exit( $GLOBALS['qa_fail'] > 0 ? 1 : 0 );
} catch ( Throwable $e ) {
	softkom_set_option( $mysqli, 'template', $old_template );
	softkom_set_option( $mysqli, 'stylesheet', $old_stylesheet );
	$restored = true;
	echo "\n[ERROR] " . get_class( $e ) . ': ' . $e->getMessage() . "\n";
	exit( 2 );
}