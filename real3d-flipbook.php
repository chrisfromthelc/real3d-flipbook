<?php

/*
 * Plugin Name: Real3D Flipbook PDF Viewer PRO
 * Plugin URI:  https://github.com/chrisfromthelc/real3d-flipbook
 * Description: Premium Responsive Real 3D FlipBook & PDF Viewer
 * Version:     4.23
 * Author:      chrisfromthelc
 * Author URI:  https://github.com/chrisfromthelc
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Text Domain: real3d-flipbook
 * Domain Path: /languages
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'REAL3D_FLIPBOOK_VERSION' ) ) {
	define( 'REAL3D_FLIPBOOK_VERSION', '4.23' );
	define( 'REAL3D_FLIPBOOK_FILE', __FILE__ );

	require_once plugin_dir_path( __FILE__ ) . '/includes/Real3DFlipbook.php';

	if ( ! class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
		require_once __DIR__ . '/lib/plugin-update-checker/plugin-update-checker.php';
	}
	$real3d_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/chrisfromthelc/real3d-flipbook/',
		__FILE__,
		'real3d-flipbook'
	);
	$real3d_update_checker->setBranch( 'main' );
	$real3d_update_checker->getVcsApi()->enableReleaseAssets();
}
