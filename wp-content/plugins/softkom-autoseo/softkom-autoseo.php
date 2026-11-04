<?php
/**
 * Plugin Name: Softkom AutoSEO
 * Description: Continuous SEO, AI-search visibility and commercial-priority engine for Softkom.
 * Version: 0.1.0
 * Author: Softkom Solutions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SOFTKOM_AUTOSEO_VERSION', '0.1.0' );
define( 'SOFTKOM_AUTOSEO_PATH', plugin_dir_path( __FILE__ ) );

require_once SOFTKOM_AUTOSEO_PATH . 'includes/class-autoseo-db.php';
require_once SOFTKOM_AUTOSEO_PATH . 'includes/class-autoseo-scoring.php';
require_once SOFTKOM_AUTOSEO_PATH . 'includes/class-autoseo-rest.php';
require_once SOFTKOM_AUTOSEO_PATH . 'includes/class-autoseo-admin.php';

register_activation_hook( __FILE__, array( 'Softkom_AutoSEO_DB', 'install' ) );

add_action(
	'plugins_loaded',
	function () {
		Softkom_AutoSEO_REST::init();
		Softkom_AutoSEO_Admin::init();
	}
);
