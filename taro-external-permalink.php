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

/**
 * Register assets.
 *
 * @return void
 */
function tsep_registers_assets() {
	$json = __DIR__ . '/wp-dependencies.json';
	if ( ! file_exists( $json ) ) {
		return;
	}
	$json = json_decode( file_get_contents( $json ), true );
	if ( ! is_array( $json ) ) {
		return;
	}
	foreach ( $json as $dep ) {
		if ( empty( $dep['path'] ) ) {
			continue;
		}
		$url = plugin_dir_url( __FILE__ ) . $dep['path'];
		switch ( $dep['ext'] ) {
			case 'js':
				$footer = [
					'in_footer' => $dep['footer'],
				];
				if ( in_array( $dep['strategy'], [ 'defer', 'async' ], true ) ) {
					$footer['strategy'] = $dep['strategy'];
				}
				wp_register_script( $dep['handle'], $url, $dep['deps'], $dep['hash'], $footer );
				if ( in_arary( 'wp-i18n', $dep['deps'], true ) ) {
					wp_set_script_translations( $dep['handle'], 'tsep' );
				}
				break;
			case 'css':
				wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], $dep['screen'] );
				break;
		}
	}
}
add_action( 'init', 'tsep_register_assets' );
