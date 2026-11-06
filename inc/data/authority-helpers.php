<?php
/**
 * Shared helpers for Phase 4 authority models.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a value should be omitted from public authority surfaces.
 *
 * @param mixed $value Value.
 * @return bool
 */
function softkom_v3_authority_value_empty( $value ) {
	if ( null === $value || false === $value ) {
		return true;
	}
	if ( is_string( $value ) ) {
		return '' === trim( $value );
	}
	if ( is_array( $value ) ) {
		if ( array() === $value ) {
			return true;
		}
		foreach ( $value as $item ) {
			if ( ! softkom_v3_authority_value_empty( $item ) ) {
				return false;
			}
		}
		return true;
	}
	return false;
}
