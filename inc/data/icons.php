<?php
/**
 * Shared Softkom SVG icons (Lucide-style).
 *
 * @package Softkom_V3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param string $key Icon key.
 * @return string
 */
function softkom_v3_icon( $key ) {
	$icons = array(
		'layout'   => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="2" width="7" height="8"/><rect x="11" y="2" width="7" height="4"/><rect x="11" y="9" width="7" height="9"/><rect x="2" y="13" width="7" height="5"/></svg>',
		'code'     => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 16V4h12v12H4z"/><path d="M8 8h4M8 12h6"/></svg>',
		'spark'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10" cy="10" r="3"/><path d="M10 2v2M10 16v2M3.5 3.5l1.4 1.4M15.1 15.1l1.4 1.4M2 10h2M16 10h2"/></svg>',
		'flow'     => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 10h4l2-4 3 8 2-4h3"/></svg>',
		'bag'      => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 7h14v10H3z"/><path d="M7 7V5a3 3 0 016 0v2"/></svg>',
		'phone'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="6" y="2" width="8" height="16" rx="1"/><path d="M10 15h.01"/></svg>',
		'shield'   => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M10 18s6-3 6-8V4l-6-2-6 2v6c0 5 6 8 6 8z"/></svg>',
		'chart'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 16V4"/><path d="M3 16h14"/><path d="M6 12l3-4 3 2 4-5"/></svg>',
		'list'     => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h12v3H4zM4 10h8M4 14h10M4 18h6"/></svg>',
		'nodes'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="5" cy="5" r="2"/><circle cx="15" cy="5" r="2"/><circle cx="10" cy="15" r="2"/><path d="M7 5h6M6 7l3 6M14 7l-3 6"/></svg>',
		'clock'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10" cy="10" r="7"/><path d="M10 6v4l3 2"/></svg>',
		'bars'     => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h12v12H4z"/><path d="M7 14V9M10 14V7M13 14v-3"/></svg>',
		'factory'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 17V8l4-3 3 2 4-3 3 2v11H3z"/></svg>',
		'retail'   => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 7h14l-1 10H4L3 7z"/><path d="M8 7V5a2 2 0 014 0v2"/></svg>',
		'services' => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="4" width="14" height="12" rx="1"/><path d="M7 8h6M7 12h4"/></svg>',
		'gov'      => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 17V7h4v10H4zM10 17V4h6v13h-6z"/></svg>',
		'build'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 17h14M5 17V9l5-4 5 4v8"/></svg>',
		'truck'    => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="2" y="8" width="11" height="7"/><path d="M13 11h3l2 2v2h-5v-4z"/><circle cx="6" cy="16" r="1.5"/><circle cx="15" cy="16" r="1.5"/></svg>',
		'health'   => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M10 3c-2 3-6 5-6 9a6 6 0 0012 0c0-4-4-6-6-9z"/></svg>',
		'edu'      => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 16V6h12v10"/><path d="M8 9h4M10 7v4"/></svg>',
		'target'   => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="10" cy="10" r="7"/><circle cx="10" cy="10" r="3"/><path d="M10 2v2M10 16v2M2 10h2M16 10h2"/></svg>',
		'horizon'  => '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M3 14h14"/><path d="M5 14V8l5-4 5 4v6"/><path d="M8 14v-3h4v3"/></svg>',
		'handshake'=> '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 11l3-3 3 2 3-2 3 3"/><path d="M7 14l3 2 3-2"/><path d="M4 11v4M16 11v4"/></svg>',
	);
	return isset( $icons[ $key ] ) ? $icons[ $key ] : '';
}
