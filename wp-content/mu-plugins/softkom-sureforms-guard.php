<?php
/**
 * Softkom SureForms enquiry guard — server-side REST validation.
 *
 * Enforces required fields, email format, size limits, honeypot,
 * submit-token presence, and duplicate/rate limiting before SureForms
 * creates entries or sends email.
 *
 * @package Softkom_Security
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact enquiry form ID (SureForms).
 */
const SOFTKOM_ENQUIRY_FORM_ID = 2722;

/**
 * Field map for form 2722 (slug => max length).
 *
 * @return array<string,int>
 */
function softkom_enquiry_field_limits() {
	return array(
		'first-name'     => 100,
		'last-name'      => 100,
		'email-address'  => 254,
		'phone-number'   => 40,
		'company-name'   => 200,
		'enquiry-type'   => 120,
		'message'        => 5000,
		'gdpr-compliance'=> 10,
	);
}

/**
 * Required field slugs for form 2722.
 *
 * @return string[]
 */
function softkom_enquiry_required_slugs() {
	return array(
		'first-name',
		'last-name',
		'email-address',
		'enquiry-type',
		'message',
		'gdpr-compliance',
	);
}

/**
 * Extract SureForms field slug from a request key.
 *
 * @param string $key Request key.
 * @return string Empty if not a SureForms field key.
 */
function softkom_enquiry_slug_from_key( $key ) {
	if ( ! is_string( $key ) || false === strpos( $key, '-lbl-' ) ) {
		return '';
	}
	$parts = explode( '-lbl-', $key, 2 );
	if ( empty( $parts[1] ) ) {
		return '';
	}
	$label_parts = explode( '-', $parts[1] );
	array_shift( $label_parts ); // drop base64 label segment
	return implode( '-', $label_parts );
}

/**
 * Build slug => value map from request params.
 *
 * @param array<string,mixed> $params Request params.
 * @return array<string,string>
 */
function softkom_enquiry_slug_map( $params ) {
	$map = array();
	foreach ( $params as $key => $value ) {
		$slug = softkom_enquiry_slug_from_key( (string) $key );
		if ( '' === $slug ) {
			continue;
		}
		if ( is_array( $value ) ) {
			$value = implode( ', ', array_map( 'strval', $value ) );
		}
		$map[ $slug ] = trim( wp_strip_all_tags( (string) $value ) );
	}
	return $map;
}

/**
 * Validate enquiry payload. Returns WP_Error on failure, true on success.
 *
 * @param array<string,mixed> $params Request params.
 * @param \WP_REST_Request    $request Request.
 * @return true|\WP_Error
 */
function softkom_enquiry_validate_payload( $params, $request ) {
	$form_id = isset( $params['form-id'] ) ? absint( $params['form-id'] ) : 0;
	if ( $form_id && SOFTKOM_ENQUIRY_FORM_ID !== $form_id ) {
		// Only harden the public contact enquiry form.
		return true;
	}
	if ( ! $form_id ) {
		return new WP_Error(
			'softkom_missing_form_id',
			'Form ID is missing.',
			array( 'status' => 400 )
		);
	}

	// Submit token must be present (SureForms permission_callback also checks).
	$token = $request->get_header( 'X-WP-Submit-Token' );
	if ( empty( $token ) || ! is_string( $token ) ) {
		return new WP_Error(
			'softkom_missing_submit_token',
			'Security verification failed. Missing submit token.',
			array( 'status' => 403 )
		);
	}

	// Honeypot — must be empty.
	$honeypot = isset( $params['srfm-sender-email-field'] ) ? trim( (string) $params['srfm-sender-email-field'] ) : '';
	if ( '' !== $honeypot ) {
		return new WP_Error(
			'softkom_spam_rejected',
			'Submission rejected.',
			array( 'status' => 400 )
		);
	}

	$fields = softkom_enquiry_slug_map( $params );
	if ( empty( $fields ) ) {
		return new WP_Error(
			'softkom_empty_payload',
			'Form data is incomplete. Please fill in all required fields.',
			array( 'status' => 400 )
		);
	}

	$errors = array();
	foreach ( softkom_enquiry_required_slugs() as $slug ) {
		if ( empty( $fields[ $slug ] ) ) {
			$errors[ $slug ] = sprintf( 'The %s field is required.', str_replace( '-', ' ', $slug ) );
		}
	}

	if ( ! empty( $fields['email-address'] ) && ! is_email( $fields['email-address'] ) ) {
		$errors['email-address'] = 'Please enter a valid email address.';
	}

	$limits = softkom_enquiry_field_limits();
	foreach ( $fields as $slug => $value ) {
		$max = isset( $limits[ $slug ] ) ? $limits[ $slug ] : 2000;
		if ( strlen( $value ) > $max ) {
			$errors[ $slug ] = sprintf( 'The %s field is too long.', str_replace( '-', ' ', $slug ) );
		}
	}

	// Reject unexpected oversized keys / non-string arrays already flattened.
	foreach ( $params as $key => $value ) {
		if ( ! is_string( $key ) ) {
			continue;
		}
		if ( is_string( $value ) && strlen( $value ) > 20000 ) {
			$errors['_payload'] = 'Submission payload is too large.';
			break;
		}
	}

	if ( ! empty( $errors ) ) {
		$first = reset( $errors );
		return new WP_Error(
			'softkom_validation_failed',
			$first,
			array(
				'status'       => 400,
				'field_errors' => $errors,
			)
		);
	}

	// Rate / duplicate limit: same IP + email within 60 seconds.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$email = strtolower( $fields['email-address'] );
	$finger = 'softkom_enq_' . md5( $ip . '|' . $email . '|' . $fields['message'] );
	if ( get_transient( $finger ) ) {
		return new WP_Error(
			'softkom_duplicate_submission',
			'A similar enquiry was just submitted. Please wait before trying again.',
			array( 'status' => 429 )
		);
	}

	// Soft spam heuristics.
	$msg_lower = strtolower( $fields['message'] );
	$spam_hits = 0;
	foreach ( array( 'viagra', 'casino', 'crypto airdrop', 'seo backlink package', 'http://', 'https://' ) as $needle ) {
		if ( false !== strpos( $msg_lower, $needle ) ) {
			$spam_hits++;
		}
	}
	if ( $spam_hits >= 2 ) {
		return new WP_Error(
			'softkom_spam_rejected',
			'Submission rejected.',
			array( 'status' => 400 )
		);
	}

	// Mark fingerprint only after validation passes — set in rest_post filter after success is harder;
	// set here so duplicates within window fail. Valid first request proceeds.
	set_transient( $finger, 1, 60 );

	return true;
}

/**
 * Intercept SureForms submit-form REST route before callback.
 *
 * @param mixed            $result  Response to replace.
 * @param \WP_REST_Server  $server  Server.
 * @param \WP_REST_Request $request Request.
 * @return mixed
 */
function softkom_enquiry_rest_pre_dispatch( $result, $server, $request ) {
	if ( ! ( $request instanceof WP_REST_Request ) ) {
		return $result;
	}

	$route = $request->get_route();
	if ( ! is_string( $route ) || false === strpos( $route, '/sureforms/v1/submit-form' ) ) {
		return $result;
	}

	if ( ! in_array( strtoupper( $request->get_method() ), array( 'POST', 'PUT', 'PATCH' ), true ) ) {
		return $result;
	}

	$params = $request->get_params();
	if ( ! is_array( $params ) ) {
		$params = array();
	}

	// Malformed / empty body with no params.
	$raw = $request->get_body();
	if ( ( empty( $params ) || ( count( $params ) === 1 && isset( $params['_locale'] ) ) ) && ( null === $raw || '' === $raw ) ) {
		return new WP_Error(
			'softkom_empty_payload',
			'Form data is not found.',
			array( 'status' => 400 )
		);
	}

	// If Content-Type claims JSON but body is not valid JSON object/array.
	$content_type = (string) $request->get_header( 'Content-Type' );
	if ( false !== stripos( $content_type, 'application/json' ) && is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
		if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
			return new WP_Error(
				'softkom_malformed_json',
				'Malformed JSON payload.',
				array( 'status' => 400 )
			);
		}
	}

	$check = softkom_enquiry_validate_payload( $params, $request );
	if ( is_wp_error( $check ) ) {
		return $check;
	}

	return $result;
}
add_filter( 'rest_pre_dispatch', 'softkom_enquiry_rest_pre_dispatch', 5, 3 );
