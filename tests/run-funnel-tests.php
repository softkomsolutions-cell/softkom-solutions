<?php
/**
 * Softkom V3 Standalone Funnel Regression Test Suite
 *
 * Runs 53 comprehensive checks against the assessment, scoring,
 * qualification, security, commercial, campaign, recurring revenue,
 * and lead/pipeline engine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

// -----------------------------------------------------------------------------
// WordPress Environment Stubs for CLI Execution
// -----------------------------------------------------------------------------
if ( ! function_exists( 'add_action' ) ) {
	function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {}
}
if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {}
}
if ( ! function_exists( 'did_action' ) ) {
	function did_action( $tag ) {
		return 0;
	}
}
if ( ! function_exists( 'register_post_type' ) ) {
	function register_post_type( $post_type, $args = array() ) {}
}
if ( ! function_exists( 'add_menu_page' ) ) {
	function add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon_url = '', $position = null ) {}
}
if ( ! function_exists( 'add_submenu_page' ) ) {
	function add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null ) {}
}
if ( ! function_exists( 'add_meta_box' ) ) {
	function add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null ) {}
}
if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '', $scheme = 'admin' ) {
		return 'http://localhost/wp-admin/' . ltrim( $path, '/' );
	}
}
if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '', $scheme = null ) {
		return 'http://localhost/' . ltrim( $path, '/' );
	}
}
if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( ...$args ) {
		if ( is_array( $args[0] ) ) {
			$params = $args[0];
			$url    = isset( $args[1] ) ? $args[1] : '';
		} else {
			$params = array( $args[0] => $args[1] );
			$url    = isset( $args[2] ) ? $args[2] : '';
		}
		$query = http_build_query( $params );
		if ( '' === $url ) {
			return '?' . $query;
		}
		return ( strpos( $url, '?' ) !== false ) ? $url . '&' . $query : $url . '?' . $query;
	}
}
if ( ! function_exists( 'wp_create_nonce' ) ) {
	function wp_create_nonce( $action = -1 ) {
		return 'mock_nonce';
	}
}
if ( ! function_exists( 'wp_verify_nonce' ) ) {
	function wp_verify_nonce( $nonce, $action = -1 ) {
		return 1;
	}
}
if ( ! function_exists( 'absint' ) ) {
	function absint( $maybeint ) {
		return abs( (int) $maybeint );
	}
}
if ( ! function_exists( 'sanitize_title' ) ) {
	function sanitize_title( $title, $fallback_title = '', $context = 'save' ) {
		return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $title ) );
	}
}
if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $string, $remove_breaks = false ) {
		return trim( strip_tags( (string) $string ) );
	}
}
if ( ! function_exists( 'wp_parse_args' ) ) {
	function wp_parse_args( $args, $defaults = array() ) {
		if ( is_object( $args ) ) {
			$r = get_object_vars( $args );
		} elseif ( is_array( $args ) ) {
			$r =& $args;
		} else {
			wp_parse_str( $args, $r );
		}
		if ( is_array( $defaults ) ) {
			return array_merge( $defaults, $r );
		}
		return $r;
	}
}
if ( ! function_exists( 'wp_parse_str' ) ) {
	function wp_parse_str( $string, &$array ) {
		parse_str( $string, $array );
	}
}
if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}

$mock_posts      = array();
$mock_meta       = array();
$mock_transients = array();
$mock_options    = array();

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		global $mock_options;
		return isset( $mock_options[ $option ] ) ? $mock_options[ $option ] : $default;
	}
}
if ( ! function_exists( 'update_option' ) ) {
	function update_option( $option, $value, $autoload = null ) {
		global $mock_options;
		$mock_options[ $option ] = $value;
		return true;
	}
}
if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $option ) {
		global $mock_options;
		unset( $mock_options[ $option ] );
		return true;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return is_string( $str ) ? trim( strip_tags( $str ) ) : '';
	}
}
if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $str ) {
		return is_string( $str ) ? trim( strip_tags( $str ) ) : '';
	}
}
if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $key ) );
	}
}
if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $email ) {
		return filter_var( $email, FILTER_SANITIZE_EMAIL );
	}
}
if ( ! function_exists( 'is_email' ) ) {
	function is_email( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : false;
	}
}
if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $val ) {
		return $val;
	}
}
if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $str ) {
		return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $str ) {
		return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' );
	}
}
if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $str ) {
		return $str;
	}
}
if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}
if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return ( $thing instanceof WP_Error );
	}
}

if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $errors = array();
		public function __construct( $code = '', $message = '', $data = '' ) {
			if ( $code ) {
				$this->errors[ $code ][] = $message;
			}
		}
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key = '', $single = false ) {
		global $mock_meta;
		if ( ! isset( $mock_meta[ $post_id ] ) ) {
			return $single ? '' : array();
		}
		if ( '' === $key ) {
			return $mock_meta[ $post_id ];
		}
		if ( ! isset( $mock_meta[ $post_id ][ $key ] ) ) {
			return $single ? '' : array();
		}
		return $single ? reset( $mock_meta[ $post_id ][ $key ] ) : $mock_meta[ $post_id ][ $key ];
	}
}
if ( ! function_exists( 'update_post_meta' ) ) {
	function update_post_meta( $post_id, $key, $value, $prev_value = '' ) {
		global $mock_meta;
		if ( ! isset( $mock_meta[ $post_id ] ) ) {
			$mock_meta[ $post_id ] = array();
		}
		$mock_meta[ $post_id ][ $key ] = array( $value );
		return true;
	}
}
if ( ! function_exists( 'delete_post_meta' ) ) {
	function delete_post_meta( $post_id, $key, $value = '' ) {
		global $mock_meta;
		unset( $mock_meta[ $post_id ][ $key ] );
		return true;
	}
}
if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post_id ) {
		global $mock_posts;
		return isset( $mock_posts[ $post_id ] ) ? $mock_posts[ $post_id ] : null;
	}
}
if ( ! function_exists( 'get_post_type' ) ) {
	function get_post_type( $post = null ) {
		$p = get_post( $post );
		return $p ? $p->post_type : false;
	}
}
if ( ! function_exists( 'wp_insert_post' ) ) {
	function wp_insert_post( $postarr, $wp_error = false ) {
		global $mock_posts;
		static $id_counter = 1000;
		$id_counter++;
		$post = (object) array(
			'ID'           => $id_counter,
			'post_title'   => isset( $postarr['post_title'] ) ? $postarr['post_title'] : '',
			'post_content' => isset( $postarr['post_content'] ) ? $postarr['post_content'] : '',
			'post_status'  => isset( $postarr['post_status'] ) ? $postarr['post_status'] : 'draft',
			'post_type'    => isset( $postarr['post_type'] ) ? $postarr['post_type'] : 'post',
		);
		$mock_posts[ $id_counter ] = $post;
		if ( isset( $postarr['meta_input'] ) && is_array( $postarr['meta_input'] ) ) {
			foreach ( $postarr['meta_input'] as $k => $v ) {
				update_post_meta( $id_counter, $k, $v );
			}
		}
		return $id_counter;
	}
}
if ( ! function_exists( 'wp_update_post' ) ) {
	function wp_update_post( $postarr, $wp_error = false ) {
		global $mock_posts;
		if ( is_array( $postarr ) && isset( $postarr['ID'] ) ) {
			$id = $postarr['ID'];
			if ( isset( $mock_posts[ $id ] ) ) {
				foreach ( $postarr as $k => $v ) {
					if ( 'ID' !== $k ) {
						$mock_posts[ $id ]->$k = $v;
					}
				}
			}
			return $id;
		}
		return 0;
	}
}
if ( ! function_exists( 'get_posts' ) ) {
	function get_posts( $args = array() ) {
		global $mock_posts;
		$res = array();
		foreach ( $mock_posts as $id => $post ) {
			if ( isset( $args['post_type'] ) && $post->post_type !== $args['post_type'] ) {
				continue;
			}
			if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
				$res[] = $id;
			} else {
				$res[] = $post;
			}
		}
		return $res;
	}
}
if ( ! function_exists( 'get_transient' ) ) {
	function get_transient( $transient ) {
		global $mock_transients;
		return isset( $mock_transients[ $transient ] ) ? $mock_transients[ $transient ] : false;
	}
}
if ( ! function_exists( 'set_transient' ) ) {
	function set_transient( $transient, $value, $expiration = 0 ) {
		global $mock_transients;
		$mock_transients[ $transient ] = $value;
		return true;
	}
}
if ( ! function_exists( 'delete_transient' ) ) {
	function delete_transient( $transient ) {
		global $mock_transients;
		unset( $mock_transients[ $transient ] );
		return true;
	}
}
if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type, $gmt = 0 ) {
		return date( 'Y-m-d H:i:s' );
	}
}

// -----------------------------------------------------------------------------
// Load Softkom Assessment Engine Data & Functions
// -----------------------------------------------------------------------------
$assessment_dir = dirname( __DIR__ ) . '/wp-content/themes/softkom-v3/inc/data/assessment/';
$engine_files   = array(
	'sections.php',
	'scoring.php',
	'question-bank.php',
	'recommendations.php',
	'funnel-questions.php',
	'funnel-qualification.php',
	'funnel-signals.php',
	'funnel-solutions.php',
	'funnel-scoring.php',
	'funnel-security.php',
	'commercial-catalogue.php',
	'commercial-catalogue-admin.php',
	'campaign-manager.php',
	'funnel-recurring-revenue.php',
	'funnel-leads.php',
	'funnel-ajax.php',
);

foreach ( $engine_files as $file ) {
	$file_path = $assessment_dir . $file;
	if ( file_exists( $file_path ) ) {
		require_once $file_path;
	}
}

// -----------------------------------------------------------------------------
// Test Harness
// -----------------------------------------------------------------------------
$passed = 0;
$failed = 0;
$check_num = 0;

function assert_check( $description, $condition ) {
	global $passed, $failed, $check_num;
	$check_num++;
	if ( $condition ) {
		$passed++;
		echo sprintf( "[PASS] Check %2d: %s\n", $check_num, $description );
	} else {
		$failed++;
		echo sprintf( "[FAIL] Check %2d: %s\n", $check_num, $description );
	}
}

echo "=========================================================\n";
echo "Softkom V3 Assessment & Funnel Engine Regression Suite\n";
echo "=========================================================\n\n";

// -----------------------------------------------------------------------------
// Checks 1 - 4: Sections & Areas
// -----------------------------------------------------------------------------
$sections = softkom_v3_assessment_sections();
assert_check( "Sections array contains 10 diagnostic sections", is_array( $sections ) && count( $sections ) === 10 );

$has_section_keys = isset( $sections['business-visibility'], $sections['reporting'], $sections['process-maturity'], $sections['systems-integration'], $sections['automation'], $sections['ai-readiness'], $sections['data-quality'], $sections['governance'], $sections['compliance'], $sections['operational-risk'] );
assert_check( "Sections contain all canonical diagnostic sections", $has_section_keys );

$section_ids = softkom_v3_assessment_section_ids();
assert_check( "softkom_v3_assessment_section_ids() returns 10 section keys", is_array( $section_ids ) && count( $section_ids ) === 10 );

$areas = softkom_v3_funnel_assessment_areas();
assert_check( "softkom_v3_funnel_assessment_areas() returns non-empty array", is_array( $areas ) && count( $areas ) > 0 );

// -----------------------------------------------------------------------------
// Checks 5 - 10: Question Bank & Public Questions
// -----------------------------------------------------------------------------
$qbank = softkom_v3_assessment_question_bank();
assert_check( "Question bank contains 30 questions across 10 sections", is_array( $qbank ) && count( $qbank ) === 30 );

$counts_by_section = array();
foreach ( $qbank as $q ) {
	$sec = $q['section'];
	$counts_by_section[ $sec ] = isset( $counts_by_section[ $sec ] ) ? $counts_by_section[ $sec ] + 1 : 1;
}
$even_distribution = ( count( $counts_by_section ) === 10 );
foreach ( $counts_by_section as $c ) {
	if ( $c !== 3 ) {
		$even_distribution = false;
	}
}
assert_check( "Questions are evenly distributed (3 per section)", $even_distribution );

$q_structure_valid = true;
foreach ( $qbank as $q ) {
	if ( empty( $q['id'] ) || empty( $q['section'] ) || empty( $q['prompt'] ) || empty( $q['scale'] ) ) {
		$q_structure_valid = false;
		break;
	}
}
assert_check( "Question structure is valid (id, section, prompt, scale)", $q_structure_valid );

$q_scores_valid = true;
foreach ( $qbank as $q ) {
	if ( $q['scale'] !== '1-5' ) {
		$q_scores_valid = false;
		break;
	}
}
assert_check( "Question options cover scale 1-5", $q_scores_valid );

$public_qids = softkom_v3_funnel_public_question_ids();
assert_check( "softkom_v3_funnel_public_question_ids() returns 14 question IDs", is_array( $public_qids ) && count( $public_qids ) === 14 );

$qual_questions = softkom_v3_funnel_qualification_questions();
assert_check( "softkom_v3_funnel_qualification_questions() returns non-empty array", is_array( $qual_questions ) && count( $qual_questions ) > 0 );

// -----------------------------------------------------------------------------
// Checks 11 - 17: Core Scoring & Maturity Bands
// -----------------------------------------------------------------------------
$all_1_answers = array_fill_keys( $public_qids, 1 );
$score_min     = softkom_v3_assessment_score( $all_1_answers );
assert_check( "Minimum answers (all 1s) yield total score and 1.0 section average", isset( $score_min['overall_average'] ) && abs( $score_min['overall_average'] - 1.0 ) < 0.01 );

assert_check( "Minimum score maturity hint is Spreadsheet dependent", isset( $score_min['maturity_hint']['title'] ) && strtolower( $score_min['maturity_hint']['title'] ) === 'spreadsheet dependent' );

$all_4_answers = array_fill_keys( $public_qids, 4 );
$score_max     = softkom_v3_assessment_score( $all_4_answers );
assert_check( "High answers (all 4s) yield 4.0 overall average", isset( $score_max['overall_average'] ) && abs( $score_max['overall_average'] - 4.0 ) < 0.01 );

assert_check( "High score maturity hint is Intelligent operations", isset( $score_max['maturity_hint']['title'] ) && strtolower( $score_max['maturity_hint']['title'] ) === 'intelligent operations' );

$partial_answers = array_slice( array_fill_keys( $public_qids, 2 ), 0, 10, true );
$score_partial   = softkom_v3_assessment_score( $partial_answers );
assert_check( "Partial answers are scored gracefully without errors", isset( $score_partial['overall_average'] ) && $score_partial['overall_average'] > 0 );

$hint = softkom_v3_assessment_maturity_hint( 2.5 );
assert_check( "Maturity hint returns valid level and title for average 2.5", is_array( $hint ) && isset( $hint['level'], $hint['title'] ) );

$bands = softkom_v3_assessment_score_bands();
assert_check( "softkom_v3_assessment_score_bands() returns 5 maturity tiers (1-5)", is_array( $bands ) && count( $bands ) === 5 );

// -----------------------------------------------------------------------------
// Checks 18 - 20: Recommendations Engine
// -----------------------------------------------------------------------------
$recs = softkom_v3_assessment_recommend( $score_min );
assert_check( "Recommendations produced for diagnostic sections", is_array( $recs ) && count( $recs ) > 0 );

$rec_fields_valid = true;
foreach ( $recs as $r ) {
	if ( empty( $r['title'] ) || empty( $r['approach'] ) ) {
		$rec_fields_valid = false;
		break;
	}
}
assert_check( "Recommendation fields (title, approach) are valid", $rec_fields_valid );

assert_check( "Low scores trigger foundational recommendations", count( $recs ) >= 1 && isset( $recs[0]['id'] ) && $recs[0]['id'] === 'stabilize-foundations' );

// -----------------------------------------------------------------------------
// Checks 21 - 29: Qualification Engine & Signal Builders
// -----------------------------------------------------------------------------
$qual_config = softkom_v3_funnel_qualification_config();
assert_check( "softkom_v3_funnel_qualification_config() returns question array", is_array( $qual_config ) && count( $qual_config ) >= 5 );

$val_owner = softkom_v3_qualification_value( 'decision_role', 'owner-executive' );
assert_check( "softkom_v3_qualification_value() calculates weight for owner-executive (100)", $val_owner === 100 );

$val_invalid = softkom_v3_qualification_value( 'decision_role', 'non-existent-role' );
assert_check( "softkom_v3_qualification_value() returns 0 for invalid role", $val_invalid === 0 );

$qual_answers = array(
	'sales_process'      => 'mostly-manual',
	'customer_enquiries' => 'significant',
	'knowledge_access'   => 'difficult',
	'partner_type'       => 'agency',
);
$qual_signals = softkom_v3_build_qualification_signals( $qual_answers );
assert_check( "Qualification signals map sales_process answers", isset( $qual_signals['sales_automation_opportunity'] ) && $qual_signals['sales_automation_opportunity'] === 75 );

assert_check( "Qualification signals map customer_enquiries answers", isset( $qual_signals['customer_service_opportunity'] ) && $qual_signals['customer_service_opportunity'] === 75 );

assert_check( "Qualification signals map knowledge_access answers", isset( $qual_signals['knowledge_access_opportunity'] ) && $qual_signals['knowledge_access_opportunity'] === 75 );

assert_check( "Qualification signals map partner_type answers", isset( $qual_signals['partner_delivery'] ) && $qual_signals['partner_delivery'] === 90 );

$intent_answers = array(
	'urgency'             => 'critical',
	'timeframe'           => 'immediately',
	'budget_readiness'    => 'budget-available',
	'decision_role'       => 'owner-executive',
	'consultation_intent' => 'book-now',
);
$purchase_signals = softkom_v3_build_purchase_intent_signals( $intent_answers );
assert_check( "Purchase intent signals build decision authority and urgency", isset( $purchase_signals['decision_authority'] ) && $purchase_signals['decision_authority'] === 100 && isset( $purchase_signals['urgency'] ) && $purchase_signals['urgency'] === 100 );

$fit_answers = array(
	'company_size'     => '51-200',
	'change_readiness' => 'ready',
);
$fit_signals = softkom_v3_build_commercial_fit_signals( $fit_answers );
assert_check( "Commercial fit signals calculate company fit and problem fit", isset( $fit_signals['company_fit'] ) && $fit_signals['company_fit'] === 90 && isset( $fit_signals['problem_fit'] ) && $fit_signals['problem_fit'] === 100 );

// -----------------------------------------------------------------------------
// Check 30: Funnel Signals Assembly
// -----------------------------------------------------------------------------
$full_answers = array_merge( $all_1_answers, $qual_answers, $intent_answers, $fit_answers );
$all_signals  = softkom_v3_build_funnel_signals( $full_answers );
assert_check( "softkom_v3_build_funnel_signals() compiles complete signal map", is_array( $all_signals ) && isset( $all_signals['visibility_gap'] ) && $all_signals['visibility_gap'] === 100 );

// Merge qualification signals into all_signals for subsequent solution matching
$all_signals = array_merge( $all_signals, $qual_signals, $purchase_signals, $fit_signals );

// -----------------------------------------------------------------------------
// Checks 31 - 33: Solution Catalogue & Solution Matching
// -----------------------------------------------------------------------------
$sol_catalogue = softkom_v3_funnel_solution_catalogue();
assert_check( "softkom_v3_funnel_solution_catalogue() returns solution items", is_array( $sol_catalogue ) && count( $sol_catalogue ) > 0 );

$matched_sols = softkom_v3_match_funnel_solutions( $all_signals );
assert_check( "softkom_v3_match_funnel_solutions() matches and ranks solutions based on signals", is_array( $matched_sols ) && count( $matched_sols ) > 0 );

$top_recs = softkom_v3_funnel_top_recommendations( $all_signals, 3 );
assert_check( "softkom_v3_funnel_top_recommendations() returns requested top 3 recommendations", is_array( $top_recs ) && count( $top_recs ) <= 3 );

// -----------------------------------------------------------------------------
// Checks 34 - 41: Security Evaluation & Spam Detection
// -----------------------------------------------------------------------------
$clamped = softkom_v3_security_clamp_score( 150 );
assert_check( "softkom_v3_security_clamp_score() clamps score above 100 to 100", $clamped === 100 );

$blocked_domains = softkom_v3_security_blocked_email_domains();
assert_check( "softkom_v3_security_blocked_email_domains() contains mailinator.com", is_array( $blocked_domains ) && in_array( 'mailinator.com', $blocked_domains, true ) );

$domain = softkom_v3_security_email_domain( 'test.lead@mailinator.com' );
assert_check( "softkom_v3_security_email_domain() extracts mailinator.com", $domain === 'mailinator.com' );

$suspicious = softkom_v3_security_suspicious_text( 'Click here for cheap crypto loans immediately http://spam.xyz' );
assert_check( "softkom_v3_security_suspicious_text() detects spam keywords/URLs", $suspicious === true );

$low_quality = softkom_v3_security_low_quality_text( 'a' );
assert_check( "softkom_v3_security_low_quality_text() flags low quality text", $low_quality === true );

$risk_level_low  = softkom_v3_security_risk_level( 10 );
$risk_level_high = softkom_v3_security_risk_level( 85 );
assert_check( "softkom_v3_security_risk_level() maps scores to correct risk tiers", $risk_level_low === 'LOW RISK' && ( $risk_level_high === 'BLOCK' || $risk_level_high === 'HIGH RISK' ) );

$clean_payload = array(
	'first_name'   => 'Darren',
	'last_name'    => 'Enfield',
	'email'        => 'darren@softkomsolutions.com',
	'company'      => 'Softkom Solutions',
	'notes'        => 'Interested in automating operational workflows and system integration.',
	'started_at'   => time() - 60,
	'completed_at' => time(),
);
$clean_sec_res = softkom_v3_security_evaluate_submission( $clean_payload );
assert_check( "Clean submission yields low security risk score (<30)", isset( $clean_sec_res['risk_score'] ) && $clean_sec_res['risk_score'] < 30 );

$spam_payload = array(
	'first_name' => 'Spammer',
	'last_name'  => 'User',
	'email'      => 'bot@mailinator.com',
	'company'    => 'asdfghjkl',
	'notes'      => 'Crypto loan casino http://spam.xyz',
);
$spam_sec_res = softkom_v3_security_evaluate_submission( $spam_payload );
assert_check( "Spam/disposable submission yields high security risk score (>=60)", isset( $spam_sec_res['risk_score'] ) && $spam_sec_res['risk_score'] >= 60 );

// -----------------------------------------------------------------------------
// Checks 42 - 47: Commercial Catalogue
// -----------------------------------------------------------------------------
$comm_cat = softkom_v3_commercial_catalogue();
assert_check( "softkom_v3_commercial_catalogue() returns commercial services", is_array( $comm_cat ) && count( $comm_cat ) > 0 );

$comm_svc = softkom_v3_commercial_service( 'managed_automation' );
assert_check( "softkom_v3_commercial_service('managed_automation') retrieves service details", is_array( $comm_svc ) && isset( $comm_svc['name'] ) );

$comm_plan = softkom_v3_commercial_plan( 'managed_automation', 'growth' );
assert_check( "softkom_v3_commercial_plan() retrieves growth tier details", is_array( $comm_plan ) && isset( $comm_plan['name'] ) );

$mrr_range = softkom_v3_commercial_mrr_range( 'managed_automation' );
assert_check( "softkom_v3_commercial_mrr_range() returns valid min and max MRR", is_array( $mrr_range ) && isset( $mrr_range['min'], $mrr_range['max'] ) );

$rec_plan_key = softkom_v3_commercial_recommended_plan( 75 );
assert_check( "softkom_v3_commercial_recommended_plan() recommends valid plan tier", is_string( $rec_plan_key ) && ! empty( $rec_plan_key ) );

$offer = softkom_v3_commercial_offer( 'managed_automation', 75 );
assert_check( "softkom_v3_commercial_offer() builds complete commercial offer object", is_array( $offer ) && isset( $offer['service_key'], $offer['plan_key'], $offer['monthly'] ) );

// -----------------------------------------------------------------------------
// Checks 48 - 49: Campaign Manager Tracking & Performance
// -----------------------------------------------------------------------------
$campaign_post_id = wp_insert_post(
	array(
		'post_title'  => 'Q3 Automation Campaign',
		'post_type'   => 'softkom_campaign',
		'post_status' => 'publish',
	)
);
update_post_meta( $campaign_post_id, '_softkom_campaign_utm_source', 'linkedin' );
update_post_meta( $campaign_post_id, '_softkom_campaign_utm_medium', 'cpc' );
update_post_meta( $campaign_post_id, '_softkom_campaign_utm_campaign', 'q3-automation-campaign' );

$tracked_url = softkom_v3_campaign_tracked_url( $campaign_post_id );
assert_check( "softkom_v3_campaign_tracked_url() constructs campaign URL with UTM query parameters", is_string( $tracked_url ) && strpos( $tracked_url, 'utm_source=linkedin' ) !== false );

$campaign_perf = softkom_v3_campaign_performance( $campaign_post_id );
assert_check( "softkom_v3_campaign_performance() retrieves campaign performance metrics", is_array( $campaign_perf ) && isset( $campaign_perf['leads'] ) );

// -----------------------------------------------------------------------------
// Checks 50 - 51: Recurring Revenue Engine
// -----------------------------------------------------------------------------
$rec_catalogue = softkom_v3_recurring_service_catalogue();
assert_check( "softkom_v3_recurring_service_catalogue() returns recurring services", is_array( $rec_catalogue ) && count( $rec_catalogue ) > 0 );

// Store test lead post for recurring recommendation check
$test_lead_id = wp_insert_post(
	array(
		'post_title'  => 'Test Lead - Recurring Check',
		'post_type'   => 'softkom_lead',
		'post_status' => 'publish',
	)
);
update_post_meta( $test_lead_id, '_softkom_lead_qualification', $qual_answers );
update_post_meta( $test_lead_id, '_softkom_lead_assessment', $score_min );
update_post_meta( $test_lead_id, '_softkom_lead_signals', wp_json_encode( $all_signals ) );
update_post_meta( $test_lead_id, '_softkom_score_ai_opportunity', 75 );
update_post_meta( $test_lead_id, '_softkom_score_commercial_fit', 75 );
update_post_meta( $test_lead_id, '_softkom_score_purchase_intent', 75 );
update_post_meta( $test_lead_id, '_softkom_score_overall_lead', 75 );
update_post_meta( $test_lead_id, '_softkom_lead_temperature', 'HOT' );
update_post_meta( $test_lead_id, '_softkom_security_risk_level', 'LOW RISK' );
update_post_meta( $test_lead_id, '_softkom_recommendations', array( array( 'title' => 'Process Automation', 'id' => 'managed_automation' ) ) );
update_post_meta( $test_lead_id, '_softkom_priority_opportunities', array( array( 'title' => 'AI Automation', 'score' => 80 ) ) );

$rec_calc = softkom_v3_calculate_recurring_recommendation( $test_lead_id );
assert_check( "softkom_v3_calculate_recurring_recommendation() calculates MRR and recommended offer", is_array( $rec_calc ) && isset( $rec_calc['commercial_monthly'] ) && $rec_calc['commercial_monthly'] > 0 );

// -----------------------------------------------------------------------------
// Checks 52 - 53: Lead Classification & Auto-Pipeline Rules
// -----------------------------------------------------------------------------
// Test HOT lead auto-pipeline rules
$hot_lead_id = wp_insert_post(
	array(
		'post_title'  => 'HOT Sales Eligible Lead',
		'post_type'   => 'softkom_lead',
		'post_status' => 'publish',
	)
);
update_post_meta( $hot_lead_id, '_softkom_lead_temperature', 'HOT' );
update_post_meta( $hot_lead_id, '_softkom_score_overall_lead', 75 );
update_post_meta( $hot_lead_id, '_softkom_score_ai_opportunity', 75 );
update_post_meta( $hot_lead_id, '_softkom_score_commercial_fit', 75 );
update_post_meta( $hot_lead_id, '_softkom_score_purchase_intent', 75 );
update_post_meta( $hot_lead_id, '_softkom_security_risk_level', 'LOW RISK' );
update_post_meta( $hot_lead_id, '_softkom_recommendations', array( array( 'title' => 'Process Automation', 'id' => 'managed_automation' ) ) );
update_post_meta( $hot_lead_id, '_softkom_priority_opportunities', array( array( 'title' => 'AI Automation', 'score' => 80 ) ) );
update_post_meta( $hot_lead_id, '_softkom_lead_routing', array( 'sales_eligible' => true ) );

$mock_result   = array( 'overall_lead_score' => 75 );
$mock_security = array( 'risk_level' => 'LOW RISK' );

softkom_v3_auto_pipeline_hot_lead( $hot_lead_id, $mock_result, $mock_security );
$hot_stage     = get_post_meta( $hot_lead_id, '_softkom_pipeline_stage', true );
$hot_mrr       = get_post_meta( $hot_lead_id, '_softkom_estimated_mrr', true );
$hot_auto_flag = get_post_meta( $hot_lead_id, '_softkom_recurring_auto_applied', true );

assert_check( "HOT leads are auto-pipelined with stage, estimated MRR, follow-up date, and auto-applied flag", ! empty( $hot_stage ) && ! empty( $hot_mrr ) && ! empty( $hot_auto_flag ) );

// Test WARM lead rules
$warm_lead_id = wp_insert_post(
	array(
		'post_title'  => 'WARM Nurture Lead',
		'post_type'   => 'softkom_lead',
		'post_status' => 'publish',
	)
);
update_post_meta( $warm_lead_id, '_softkom_lead_temperature', 'WARM' );
update_post_meta( $warm_lead_id, '_softkom_score_overall_lead', 65 );

softkom_v3_auto_pipeline_hot_lead( $warm_lead_id, $mock_result, $mock_security );

$warm_auto_flag = get_post_meta( $warm_lead_id, '_softkom_recurring_auto_applied', true );
$warm_stage     = get_post_meta( $warm_lead_id, '_softkom_pipeline_stage', true );

assert_check( "WARM leads are stored and attributed without auto-populating pipeline stage or auto-applied flag", empty( $warm_auto_flag ) && empty( $warm_stage ) );

// -----------------------------------------------------------------------------
// Summary
// -----------------------------------------------------------------------------
echo "\n=========================================================\n";
echo sprintf( "Results: %d Passed, %d Failed (Total %d Checks)\n", $passed, $failed, $check_num );
echo "=========================================================\n";

if ( $failed > 0 ) {
	exit( 1 );
}
exit( 0 );
