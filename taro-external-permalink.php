<?php
/**
 * Plugin Name: Taro External Permalink
 * Plugin URI: https://wordpress.org/plugins/taro-external-permalink/
 * Description: Allow post to have external permalink including attachments.
 * Author: Tarosky INC.
 * Version: nightly
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author URI: https://tarosky.co.jp/
 * License: GPL3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: tsep
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) or die();

/**
 * Init plugins.
 */
function tsep_init() {
	// Register translations.
	load_plugin_textdomain( 'tsep', false, basename( __DIR__ ) . '/languages' );
	// Composer.
	$composer = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $composer ) ) {
		// Boostrap.
		require $composer;
	}
	// Load hooks.
	require_once __DIR__ . '/includes/settings.php';
	require_once __DIR__ . '/includes/editor.php';
	require_once __DIR__ . '/includes/templates.php';
}

/**
 * Get URL.
 *
 * @return string
 */
function tsep_url() {
	return untrailingslashit( plugin_dir_url( __FILE__ ) );
}

/**
 * Get version.
 *
 * @return string
 */
function tsep_version() {
	static $version = null;
	if ( is_null( $version ) ) {
		$data    = get_file_data( __FILE__, [
			'version' => 'Version',
		] );
		$version = $data['version'];
	}
	return $version;
}

// Register hooks.
add_action( 'plugins_loaded', 'tsep_init' );
