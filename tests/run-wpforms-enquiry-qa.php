<?php
/** Isolated WPForms enquiry and first-touch attribution regression checks. */

define( 'ABSPATH', __DIR__ );
define( 'DAY_IN_SECONDS', 86400 );

$hooks = array();
$posts = array();
$form  = (object) array( 'process' => (object) array( 'errors' => array() ) );

function add_action( $hook, $callback, $priority = 10, $args = 1 ) {
	$GLOBALS['hooks'][ $hook ] = $callback;
}
function wpforms() { return $GLOBALS['form']; }
function is_admin() { return false; }
function wp_doing_ajax() { return false; }
function is_ssl() { return true; }
function is_front_page() { return '/' === $GLOBALS['page_path']; }
function is_page( $slug ) { return '/' . $slug . '/' === $GLOBALS['page_path']; }
function wp_parse_url( $url, $component ) { return parse_url( $url, $component ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function wp_json_encode( $value ) { return json_encode( $value ); }
function esc_url_raw( $value ) { return $value; }
function home_url( $path ) { return 'https://softkom.example' . $path; }
function current_time( $format, $gmt = false ) {
	return 'Y-m-d' === $format ? '2026-11-20' : '2026-11-20 08:00:00';
}
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function sanitize_text_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_textarea_field( $value ) { return trim( strip_tags( $value ) ); }
function sanitize_email( $value ) { return filter_var( $value, FILTER_SANITIZE_EMAIL ); }
function is_email( $value ) { return filter_var( $value, FILTER_VALIDATE_EMAIL ); }
function absint( $value ) { return abs( (int) $value ); }
function get_option( $name ) { return 'page_on_front' === $name ? 3965 : false; }
function get_post_field( $name, $id ) { return 4012 === $id ? 'contact' : ''; }
function post_type_exists( $type ) { return 'softkom_lead' === $type; }
function wp_slash( $value ) { return is_array( $value ) ? array_map( 'wp_slash', $value ) : addslashes( $value ); }
function is_wp_error( $value ) { return $value instanceof RuntimeException; }
function wp_insert_post( $data, $return_error ) {
	if ( ! empty( $GLOBALS['insert_failed'] ) ) {
		return new RuntimeException( 'Insert failed' );
	}
	$data['meta_input'] = array_map( 'wp_unslash', $data['meta_input'] );
	if ( ! empty( $GLOBALS['meta_failed'] ) ) {
		unset( $data['meta_input']['_softkom_email'] );
	}
	$id = count( $GLOBALS['posts'] ) + 1;
	$GLOBALS['posts'][ $id ] = $data;
	return $id;
}
function wp_delete_post( $id, $force ) { unset( $GLOBALS['posts'][ $id ] ); }
function get_post_type( $id ) { return $GLOBALS['posts'][ $id ]['post_type']; }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['posts'][ $id ]['meta_input'][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['posts'][ $id ]['meta_input'][ $key ] = $value; }

require __DIR__ . '/../wp-content/mu-plugins/softkom-homepage-conversion.php';
require __DIR__ . '/../wp-content/mu-plugins/softkom-organic-attribution.php';

$results = array();
$capture = $hooks['wpforms_process_2501'] ?? null;
if ( ! is_callable( $capture ) ) {
	fwrite( STDERR, "WPForms 2501 enquiry handler is missing.\n" );
	exit( 1 );
}

$page_path = '/';
$_SERVER['REQUEST_URI'] = '/?utm_source=linkedin&utm_medium=social&utm_campaign=workflow-review';
$_GET = array( 'utm_source' => 'linkedin', 'utm_medium' => 'social', 'utm_campaign' => 'workflow-review' );
$hooks['template_redirect']();

$page_path = '/contact/';
$_SERVER['REQUEST_URI'] = '/contact/';
$_GET = array();
$hooks['template_redirect']();

$message = "We can't get reports from C:\\Sales.\nCould you help?";
$fields = array(
	array( 'value' => 'Example Buyer' ),
	array( 'value' => 'buyer@example.com' ),
	array( 'value' => $message ),
);
$form_data = array( 'id' => 2501 );
$capture( $fields, array( 'post_id' => 4012 ), $form_data );

$results['Contact enquiry is saved as a private Softkom lead'] = 'private' === $posts[1]['post_status'] && 'softkom_lead' === $posts[1]['post_type'];
$results['Lead retains the contact details and original message'] = 'Example Buyer' === get_post_meta( 1, '_softkom_first_name', true ) && 'buyer@example.com' === get_post_meta( 1, '_softkom_email', true ) && $message === get_post_meta( 1, '_softkom_home_project_details', true );
$results['Contact lead retains the first campaign from the homepage'] = 'contact-enquiry' === get_post_meta( 1, '_softkom_source', true ) && 'linkedin' === get_post_meta( 1, '_softkom_traffic_source', true ) && 'workflow-review' === get_post_meta( 1, '_softkom_utm_campaign', true );
$results['Sales can see the enquiry in the follow-up queue today'] = 'New' === get_post_meta( 1, '_softkom_pipeline_stage', true ) && '2026-11-20' === get_post_meta( 1, '_softkom_next_follow_up', true );

$_COOKIE = array();
$capture( $fields, array( 'post_id' => 3965 ), $form_data );
$results['Direct homepage enquiries still reach the pipeline'] = 'homepage-enquiry' === get_post_meta( 2, '_softkom_source', true ) && 'direct' === get_post_meta( 2, '_softkom_traffic_source', true );

$capture( $fields, array(), array( 'id' => 2501, 'spam_reason' => 'Example spam' ) );
$form->process->errors[2501]['header'] = 'Existing validation error.';
$capture( $fields, array(), $form_data );
$results['Spam and rejected submissions create no lead'] = 2 === count( $posts ) && 'Existing validation error.' === $form->process->errors[2501]['header'];

$form->process->errors = array();
$insert_failed = true;
$capture( $fields, array(), $form_data );
$results['Insert failure blocks confirmation'] = 2 === count( $posts ) && ! empty( $form->process->errors[2501]['header'] );

$form->process->errors = array();
$insert_failed = false;
$meta_failed = true;
$capture( $fields, array(), $form_data );
$results['Metadata failure removes the partial lead and blocks confirmation'] = 2 === count( $posts ) && ! empty( $form->process->errors[2501]['header'] );

foreach ( $results as $label => $passed ) {
	echo ( $passed ? '[PASS] ' : '[FAIL] ' ) . $label . PHP_EOL;
}
exit( in_array( false, $results, true ) ? 1 : 0 );
