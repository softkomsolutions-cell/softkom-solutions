<?php
/**
 * Section: about mission / positioning cards.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$id    = isset( $id ) ? $id : 'mission';
$title = isset( $title ) ? $title : 'What Softkom does — and does not do';
$body  = isset( $body ) ? $body : "Softkom solves operational business problems using software, automation and custom systems.\n\nSoftkom is not a digital agency chasing vanity metrics or unverifiable “transformation” claims.";
$note  = isset( $note ) ? $note : '';

$cards = array(
	array(
		'title' => 'What Softkom is',
		'body'  => 'An operational systems partner. Softkom maps how work moves, designs the system, builds what is needed, and stays for the long term — so operators can scale with control.',
	),
	array(
		'title' => 'What Softkom is not',
		'body'  => 'A generic digital agency chasing vanity metrics, stock photography heroes, or unverifiable “transformation” claims Softkom cannot defend in a boardroom.',
	),
	array(
		'title' => 'How Softkom measures success',
		'body'  => 'Clearer workflows, fewer handoffs, living visibility for leaders — and systems teams can actually run day to day without Softkom living in the building.',
	),
);

ob_start();
echo softkom_v3_component( 'section-head', array( 'title' => $title, 'body' => $body, 'note' => $note ) );
echo '<div class="sk-grid sk-grid--3">';
foreach ( $cards as $item ) {
	echo softkom_v3_component( 'card', $item );
}
echo '</div>';
$content = ob_get_clean();

echo softkom_v3_component( 'section', array( 'id' => $id, 'muted' => false, 'content' => $content ) );
