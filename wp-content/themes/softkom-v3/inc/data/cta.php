<?php
/**
 * Canonical CTA registry â€” RC2.4.
 *
 * Level 1 Explore Â· Level 2 Discuss Â· Level 3 Commit.
 * Prefer this registry over hardcoded CTA labels.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All canonical CTA definitions.
 *
 * @return array<string, array{id:string,level:int,label:string,url:string,notes:string}>
 */
function softkom_v3_cta_registry() {
	return array(
		'explore-platforms'     => array(
			'id'    => 'explore-platforms',
			'level' => 1,
			'label' => 'Explore Our Platforms',
			'url'   => '/platforms/',
			'notes' => 'Primary product browse â€” homepage hero and closing CTA.',
		),
		'explore-solutions'     => array(
			'id'    => 'explore-solutions',
			'level' => 1,
			'label' => 'Explore Solutions',
			'url'   => '/services/',
			'notes' => 'Legacy solutions browse â€” retained for depth pages.',
		),
		'review-selected-work'  => array(
			'id'    => 'review-selected-work',
			'level' => 1,
			'label' => 'Review Selected Work',
			'url'   => '/case-studies/',
			'notes' => 'Early-stage proof browse.',
		),
		'read-insights'         => array(
			'id'    => 'read-insights',
			'level' => 1,
			'label' => 'Read Insights',
			'url'   => '/insights/',
			'notes' => 'Decision-support reading.',
		),
		'view-decision-guide'   => array(
			'id'    => 'view-decision-guide',
			'level' => 1,
			'label' => 'View Decision Guide',
			'url'   => '/services/',
			'notes' => 'Route to Solutions hub; deep links may override URL.',
		),
		'view-marketplaceos'    => array(
			'id'    => 'view-marketplaceos',
			'level' => 1,
			'label' => 'Explore MarketplaceOS',
			'url'   => '/platforms/marketplaceos/',
			'notes' => 'Product page entry.',
		),
		'view-brick-alpha'      => array(
			'id'    => 'view-brick-alpha',
			'level' => 1,
			'label' => 'Explore Brick Alpha',
			'url'   => '/platforms/brick-alpha/',
			'notes' => 'Product page entry.',
		),
		'discuss-problem'       => array(
			'id'    => 'discuss-problem',
			'level' => 2,
			'label' => 'Discuss an Operational Problem',
			'url'   => '/contact/#discovery',
			'notes' => 'Problem-aware discuss.',
		),
		'start-discovery'       => array(
			'id'    => 'start-discovery',
			'level' => 2,
			'label' => 'Book a Discovery Call',
			'url'   => '/contact/#discovery',
			'notes' => 'Preferred discuss CTA â€” product-led wording.',
		),
		'start-conversation'    => array(
			'id'    => 'start-conversation',
			'level' => 2,
			'label' => 'Start the Conversation',
			'url'   => '/contact/#discovery',
			'notes' => 'Closing-band CTA.',
		),
		'talk-to-softkom'       => array(
			'id'    => 'talk-to-softkom',
			'level' => 2,
			'label' => 'Talk to Softkom',
			'url'   => '/contact/',
			'notes' => 'General discuss.',
		),
		'book-demo'             => array(
			'id'    => 'book-demo',
			'level' => 3,
			'label' => 'Book a Demo',
			'url'   => '/contact/#discovery',
			'notes' => 'Product demo commit.',
		),
		'book-strategy-call'    => array(
			'id'    => 'book-strategy-call',
			'level' => 3,
			'label' => 'Book a Discovery Call',
			'url'   => '/contact/#discovery',
			'notes' => 'Header commit CTA â€” aligned to discovery wording.',
		),
		'request-scoped-proposal' => array(
			'id'    => 'request-scoped-proposal',
			'level' => 3,
			'label' => 'Request a Scoped Proposal',
			'url'   => '/contact/#enquiry',
			'notes' => 'Only where discovery or sufficient scope is established in surrounding copy.',
		),
	);
}

/**
 * Resolve a CTA by id.
 *
 * @param string               $id      CTA id.
 * @param array<string,mixed>  $overrides Optional label/url overrides.
 * @return array{id:string,level:int,label:string,url:string,notes:string}|null
 */
function softkom_v3_cta( $id, $overrides = array() ) {
	$registry = softkom_v3_cta_registry();
	if ( ! isset( $registry[ $id ] ) ) {
		return null;
	}
	$cta = $registry[ $id ];
	if ( ! empty( $overrides['label'] ) ) {
		$cta['label'] = (string) $overrides['label'];
	}
	if ( ! empty( $overrides['url'] ) ) {
		$cta['url'] = (string) $overrides['url'];
	}
	return $cta;
}

/**
 * Label for a CTA id (empty string if unknown).
 *
 * @param string $id CTA id.
 * @return string
 */
function softkom_v3_cta_label( $id ) {
	$cta = softkom_v3_cta( $id );
	return $cta ? $cta['label'] : '';
}

/**
 * URL for a CTA id (empty string if unknown).
 *
 * @param string $id CTA id.
 * @return string
 */
function softkom_v3_cta_url( $id ) {
	$cta = softkom_v3_cta( $id );
	return $cta ? $cta['url'] : '';
}

/**
 * Page â†’ primary / secondary CTA map (documentation + optional consumers).
 *
 * @return array<string, array{primary:string,secondary:string}>
 */
function softkom_v3_cta_page_map() {
	return array(
		'home'         => array(
			'primary'   => 'start-discovery',
			'secondary' => 'explore-platforms',
			'closing'   => 'start-discovery',
		),
		'platforms'    => array(
			'primary'   => 'start-discovery',
			'secondary' => 'view-marketplaceos',
		),
		'about'        => array(
			'primary'   => 'start-discovery',
			'secondary' => 'explore-platforms',
		),
		'services'     => array(
			'primary'   => 'discuss-problem',
			'secondary' => 'explore-platforms',
		),
		'industries'   => array(
			'primary'   => 'discuss-problem',
			'secondary' => 'explore-platforms',
		),
		'selected-work'=> array(
			'primary'   => 'discuss-problem',
			'secondary' => 'explore-platforms',
		),
		'insights'     => array(
			'primary'   => 'start-discovery',
			'secondary' => 'explore-platforms',
		),
		'contact'      => array(
			'primary'   => 'start-discovery',
			'secondary' => 'explore-platforms',
		),
		'header'       => array(
			'primary'   => 'book-strategy-call',
			'secondary' => '',
		),
		'cta-band'     => array(
			'primary'   => 'start-discovery',
			'secondary' => 'explore-platforms',
		),
	);
}


