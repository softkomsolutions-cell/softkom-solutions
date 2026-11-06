<?php
/**
 * Leadership profile — schema for verified About evidence only.
 *
 * Never invent achievements, certifications, speaking or partnerships.
 * Empty collections stay empty until logged in docs/phase-4/leadership/.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Empty leadership profile shell.
 *
 * @return array<string, mixed>
 */
function softkom_v3_leadership_profile_blank() {
	return array(
		'name'           => '',
		'role'           => '',
		'summary'        => '',
		'involvement'    => array(),
		'focus'          => array(),
		'philosophy'     => '',
		'articles'       => array(),
		'interviews'     => array(),
		'speaking'       => array(),
		'community'      => array(),
		'partnerships'   => array(),
		'certifications' => array(),
		'publications'   => array(),
		'advisory_work'  => array(),
		'notes'          => 'Update only from docs/phase-4/leadership/evidence-log.md after Softkom can defend the claim.',
	);
}

/**
 * Evidence entry types Softkom may log.
 *
 * @return string[]
 */
function softkom_v3_leadership_evidence_types() {
	return array(
		'philosophy',
		'articles',
		'interviews',
		'speaking',
		'community',
		'partnerships',
		'certifications',
		'publications',
		'advisory_work',
	);
}

/**
 * Blank evidence item.
 *
 * @param string $type Evidence type.
 * @return array<string, mixed>
 */
function softkom_v3_leadership_evidence_blank( $type = '' ) {
	return array(
		'type'         => $type,
		'title'        => '',
		'summary'      => '',
		'date'         => '',
		'url'          => '',
		'venue'        => '',
		'verified'     => false,
		'public'       => false,
		'on_about'     => false,
		'evidence_ref' => '',
	);
}

/**
 * Public leadership fields only (verified + public).
 *
 * @param array<string, mixed> $profile Profile row.
 * @return array<string, mixed>
 */
function softkom_v3_leadership_public_view( $profile ) {
	$out = array();

	foreach ( array( 'name', 'role', 'summary', 'philosophy' ) as $key ) {
		if ( ! empty( $profile[ $key ] ) && is_string( $profile[ $key ] ) ) {
			$out[ $key ] = $profile[ $key ];
		}
	}

	foreach ( array( 'involvement', 'focus' ) as $key ) {
		if ( empty( $profile[ $key ] ) || ! is_array( $profile[ $key ] ) ) {
			continue;
		}
		$items = array();
		foreach ( $profile[ $key ] as $item ) {
			if ( is_string( $item ) && '' !== trim( $item ) ) {
				$items[] = $item;
			} elseif ( is_array( $item ) && ! empty( $item['verified'] ) && ! empty( $item['public'] ) && ! empty( $item['summary'] ) ) {
				$items[] = $item['summary'];
			}
		}
		if ( $items ) {
			$out[ $key ] = $items;
		}
	}

	foreach ( softkom_v3_leadership_evidence_types() as $type ) {
		if ( 'philosophy' === $type ) {
			continue;
		}
		if ( empty( $profile[ $type ] ) || ! is_array( $profile[ $type ] ) ) {
			continue;
		}
		$items = array();
		foreach ( $profile[ $type ] as $item ) {
			if ( empty( $item['verified'] ) || empty( $item['public'] ) ) {
				continue;
			}
			$items[] = $item;
		}
		if ( $items ) {
			$out[ $type ] = $items;
		}
	}
	return $out;
}
