<?php
/**
 * Section: service decision-depth blocks (RC2.3).
 * Args: service (slug) — required.
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$service = isset( $service ) ? sanitize_title( (string) $service ) : '';
$data    = $service ? softkom_v3_data_service_depth( $service ) : null;
if ( ! $data ) {
	return;
}

/**
 * Render a depth block.
 *
 * @param array<string,mixed> $block Block config.
 * @return string
 */
$render_block = static function ( $block ) {
	$type = isset( $block['type'] ) ? $block['type'] : '';
	ob_start();

	switch ( $type ) {
		case 'prose':
			$text = isset( $block['text'] ) ? (string) $block['text'] : '';
			if ( '' !== $text ) {
				echo '<div class="sk-prose"><p>' . esc_html( $text ) . '</p></div>';
			}
			break;

		case 'list':
			$items = isset( $block['items'] ) && is_array( $block['items'] ) ? $block['items'] : array();
			if ( $items ) {
				echo '<ul class="sk-depth-list">';
				foreach ( $items as $item ) {
					echo '<li>' . esc_html( (string) $item ) . '</li>';
				}
				echo '</ul>';
			}
			break;

		case 'checklist':
			$items = isset( $block['items'] ) && is_array( $block['items'] ) ? $block['items'] : array();
			if ( $items ) {
				echo '<ul class="sk-depth-checklist">';
				foreach ( $items as $item ) {
					echo '<li>' . esc_html( (string) $item ) . '</li>';
				}
				echo '</ul>';
			}
			break;

		case 'table':
			$headers = isset( $block['headers'] ) && is_array( $block['headers'] ) ? $block['headers'] : array();
			$rows    = isset( $block['rows'] ) && is_array( $block['rows'] ) ? $block['rows'] : array();
			if ( $headers && $rows ) {
				echo '<div class="sk-depth-table-wrap"><table class="sk-depth-table">';
				echo '<thead><tr>';
				foreach ( $headers as $h ) {
					echo '<th scope="col">' . esc_html( (string) $h ) . '</th>';
				}
				echo '</tr></thead><tbody>';
				foreach ( $rows as $row ) {
					echo '<tr>';
					foreach ( (array) $row as $cell ) {
						echo '<td>' . esc_html( (string) $cell ) . '</td>';
					}
					echo '</tr>';
				}
				echo '</tbody></table></div>';
			}
			break;

		case 'matrix':
			$headers = isset( $block['headers'] ) && is_array( $block['headers'] ) ? $block['headers'] : array();
			$rows    = isset( $block['rows'] ) && is_array( $block['rows'] ) ? $block['rows'] : array();
			if ( $headers && $rows ) {
				echo '<div class="sk-depth-table-wrap"><table class="sk-depth-table sk-depth-table--matrix">';
				echo '<thead><tr>';
				foreach ( $headers as $h ) {
					echo '<th scope="col">' . esc_html( (string) $h ) . '</th>';
				}
				echo '</tr></thead><tbody>';
				foreach ( $rows as $row ) {
					echo '<tr>';
					foreach ( (array) $row as $i => $cell ) {
						$tag = 0 === (int) $i ? 'th' : 'td';
						$scope = 0 === (int) $i ? ' scope="row"' : '';
						echo '<' . $tag . $scope . '>' . esc_html( (string) $cell ) . '</' . $tag . '>';
					}
					echo '</tr>';
				}
				echo '</tbody></table></div>';
			}
			break;

		case 'when':
			$yes = isset( $block['appropriate'] ) && is_array( $block['appropriate'] ) ? $block['appropriate'] : array();
			$no  = isset( $block['not'] ) && is_array( $block['not'] ) ? $block['not'] : array();
			echo '<div class="sk-depth-when">';
			echo '<div class="sk-depth-when-panel sk-depth-when-panel--yes">';
			echo '<h3>When this is appropriate</h3><ul>';
			foreach ( $yes as $item ) {
				echo '<li>' . esc_html( (string) $item ) . '</li>';
			}
			echo '</ul></div>';
			echo '<div class="sk-depth-when-panel sk-depth-when-panel--no">';
			echo '<h3>When this is not appropriate</h3><ul>';
			foreach ( $no as $item ) {
				echo '<li>' . esc_html( (string) $item ) . '</li>';
			}
			echo '</ul></div></div>';
			break;

		case 'phases':
			$items = isset( $block['items'] ) && is_array( $block['items'] ) ? $block['items'] : array();
			if ( $items ) {
				echo '<ol class="sk-depth-phases">';
				foreach ( $items as $i => $phase ) {
					$label = isset( $phase['label'] ) ? (string) $phase['label'] : '';
					$band  = isset( $phase['band'] ) ? (string) $phase['band'] : '';
					$text  = isset( $phase['text'] ) ? (string) $phase['text'] : '';
					echo '<li>';
					echo '<span class="sk-diagram-num">' . esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ) . '</span>';
					echo '<div>';
					echo '<strong>' . esc_html( $label ) . '</strong>';
					if ( $band ) {
						echo '<span class="sk-depth-phase-band">' . esc_html( $band ) . '</span>';
					}
					echo '<p>' . esc_html( $text ) . '</p>';
					echo '</div></li>';
				}
				echo '</ol>';
			}
			break;

		case 'callout':
			$tone  = isset( $block['tone'] ) ? sanitize_html_class( (string) $block['tone'] ) : 'note';
			$title = isset( $block['title'] ) ? (string) $block['title'] : '';
			$text  = isset( $block['text'] ) ? (string) $block['text'] : '';
			echo '<aside class="sk-depth-callout sk-depth-callout--' . esc_attr( $tone ) . '">';
			if ( $title ) {
				echo '<strong>' . esc_html( $title ) . '</strong>';
			}
			if ( $text ) {
				echo '<p>' . esc_html( $text ) . '</p>';
			}
			echo '</aside>';
			break;

		case 'decision-snapshot':
			$fields = array(
				'best_suited'   => 'Best suited for',
				'engagement'    => 'Typical engagement',
				'stakeholders'  => 'Typical stakeholders',
				'objective'     => 'Primary operational objective',
				'disruption'    => 'Typical disruption level',
				'not_right'     => 'When this is not the right approach',
			);
			$rows = array();
			foreach ( $fields as $key => $label ) {
				$value = isset( $block[ $key ] ) ? trim( (string) $block[ $key ] ) : '';
				if ( '' !== $value ) {
					$rows[] = array(
						'label' => $label,
						'value' => $value,
					);
				}
			}
			if ( $rows ) {
				echo '<div class="sk-depth-snapshot" role="region" aria-label="Decision Snapshot">';
				echo '<dl class="sk-depth-snapshot-list">';
				foreach ( $rows as $row ) {
					echo '<div class="sk-depth-snapshot-row">';
					echo '<dt>' . esc_html( $row['label'] ) . '</dt>';
					echo '<dd>' . esc_html( $row['value'] ) . '</dd>';
					echo '</div>';
				}
				echo '</dl></div>';
			}
			break;
	}

	return ob_get_clean();
};

$muted_toggle = false;
foreach ( $data['sections'] as $section ) {
	$id    = isset( $section['id'] ) ? (string) $section['id'] : '';
	$title = isset( $section['title'] ) ? (string) $section['title'] : '';
	$body  = isset( $section['body'] ) ? (string) $section['body'] : '';
	$muted = array_key_exists( 'muted', $section ) ? (bool) $section['muted'] : $muted_toggle;
	$muted_toggle = ! $muted_toggle;

	ob_start();
	echo softkom_v3_component(
		'section-head',
		array(
			'title' => $title,
			'body'  => $body,
		)
	);

	if ( ! empty( $section['blocks'] ) && is_array( $section['blocks'] ) ) {
		echo '<div class="sk-depth-blocks">';
		foreach ( $section['blocks'] as $block ) {
			echo $render_block( $block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}

	if ( ! empty( $section['faq'] ) && is_array( $section['faq'] ) ) {
		echo '<div class="sk-stack sk-stack--measure sk-depth-faq">';
		foreach ( $section['faq'] as $item ) {
			echo softkom_v3_component(
				'card',
				array(
					'variant' => 'faq',
					'title'   => isset( $item['title'] ) ? $item['title'] : '',
					'body'    => isset( $item['body'] ) ? $item['body'] : '',
				)
			);
		}
		echo '</div>';
	}

	$content = ob_get_clean();
	echo softkom_v3_component(
		'section',
		array(
			'id'      => $id,
			'muted'   => $muted,
			'content' => $content,
		)
	);
}
