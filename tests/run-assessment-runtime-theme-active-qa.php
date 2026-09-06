<?php
/**
 * Assessment runtime regression QA - THEME-ACTIVE configuration.
 *
 * Reproduces the staging configuration that previously returned HTTP 500 on
 * wp-admin and assessment AJAX submits after the production bundle deployed:
 * softkom-v3 active theme + wp-content/softkom-assessment-runtime present.
 *
 * The fix must make the standalone layer detect the theme-provided runtime and
 * stay completely out of the way (no duplicate data load, no shortcode
 * override, no asset duplication, no decorative body class) while the theme
 * remains the authoritative assessment runtime. This suite guards that no
 * softkom_v3_* function can be redeclared and that the assessment page renders
 * the full app via the theme shortcode.
 *
 * Self-contained local staging bootstrap (LocalWP MySQL port patch). Intended
 * for the local staging environment only - DO NOT DEPLOY.
 *
 * Usage:
 *   "C:\...\php.exe" -n -d "extension_dir=...\ext" -d "extension=php_mysqli.dll" tests\run-assessment-runtime-theme-active-qa.php
 */

$abspath = 'C:/Users/Darren/Local Sites/softkom-solutions/app/public/';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', $abspath ); }
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
ini_set( 'display_errors', '1' );
eval( '?>' . $cfg );

if ( ! defined( 'ABSPATH' ) ) {
	echo "[ERROR] Bootstrap failed.\n";
	exit( 1 );
}

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

echo "========================================================================\n";
echo "Assessment Runtime Regression QA - THEME-ACTIVE (softkom-v3 + runtime)\n";
echo "========================================================================\n\n";

try {
	qa_assert( 'WordPress bootstrap active', function_exists( 'get_option' ) && function_exists( 'get_stylesheet_directory' ) );

	$active_slug = get_stylesheet();
	qa_assert( 'Active theme is softkom-v3', 'softkom-v3' === $active_slug );

	qa_assert( 'Theme marker SOFTKOM_V3_VERSION defined', defined( 'SOFTKOM_V3_VERSION' ) );
	qa_assert( 'Theme loader softkom_v3_load_data() exists', function_exists( 'softkom_v3_load_data' ) );

	$runtime_dir = WP_CONTENT_DIR . '/softkom-assessment-runtime/data';
	qa_assert( 'Bundle runtime data dir present', is_dir( $runtime_dir ) );

	qa_assert(
		'Standalone guard detects theme-provided runtime',
		function_exists( 'softkom_assessment_runtime_theme_provides_runtime' ) && softkom_assessment_runtime_theme_provides_runtime()
	);

	softkom_v3_load_data();

	qa_assert( 'Lead storage handler exists (no redeclare)', function_exists( 'softkom_v3_store_assessment_lead' ) );
	qa_assert( 'Lead post type registrar exists (no redeclare)', function_exists( 'softkom_v3_register_lead_post_type' ) );
	qa_assert( 'Campaign registrar exists (no redeclare)', function_exists( 'softkom_v3_register_campaign_post_type' ) );
	qa_assert( 'Commercial catalogue exists (no redeclare)', function_exists( 'softkom_v3_commercial_catalogue' ) );
	qa_assert( 'Recurring catalogue exists (no redeclare)', function_exists( 'softkom_v3_recurring_service_catalogue' ) );
	qa_assert( 'Assessment submission handler exists (no redeclare)', function_exists( 'softkom_v3_process_assessment_submission' ) );
	qa_assert( 'Question bank exists (no redeclare)', function_exists( 'softkom_v3_assessment_question_bank' ) );
	qa_assert( 'HOT lead auto-pipeline exists (no redeclare)', function_exists( 'softkom_v3_auto_pipeline_hot_lead' ) );

	softkom_assessment_runtime_load_data();

	qa_assert( 'Standalone load_data() no-ops without redeclaring', function_exists( 'softkom_v3_register_lead_post_type' ) );

	qa_assert(
		'softkom_assessment_v3 shortcode maps to theme renderer',
		isset( $GLOBALS['shortcode_tags']['softkom_assessment_v3'] )
		&& 'softkom_v3_assessment_shortcode' === $GLOBALS['shortcode_tags']['softkom_assessment_v3']
	);

	$html = do_shortcode( '[softkom_assessment_v3]' );
	qa_assert( 'Assessment page renders via theme template', false !== strpos( $html, 'sk-assessment' ) );
	qa_assert( 'Assessment app start button present', false !== strpos( $html, 'data-assessment-start' ) );
	qa_assert( 'No runtime fallback message shown', false === strpos( $html, 'Assessment runtime is not available' ) );

	$classes = apply_filters( 'body_class', array() );
	qa_assert( 'Runtime decorative body class not forced by standalone', ! in_array( 'softkom-assessment-live', $classes, true ) );

	echo "\n========================================================================\n";
	echo 'RESULT: ' . $GLOBALS['qa_pass'] . ' passed, ' . $GLOBALS['qa_fail'] . ' failed' . "\n";
	echo "========================================================================\n";
	exit( $GLOBALS['qa_fail'] > 0 ? 1 : 0 );
} catch ( Throwable $e ) {
	echo "\n[ERROR] " . get_class( $e ) . ': ' . $e->getMessage() . "\n";
	exit( 2 );
}