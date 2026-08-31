<?php
/**
 * Plugin Name: Softkom Organic Discovery Loader
 * Description: Explicitly loads the Softkom organic and AI-search discovery layer.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$softkom_organic_file = __DIR__ . '/softkom-organic-ai-discovery.php';
if ( is_readable( $softkom_organic_file ) ) {
    require_once $softkom_organic_file;
}
