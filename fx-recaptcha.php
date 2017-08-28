<?php
/**
 * Plugin Name: f(x) reCAPTCHA
 * Plugin URI: http://genbumedia.com/plugins/fx-recaptcha/
 * Description: Simple Google reCAPTCHA integration for WordPress.
 * Version: 1.0.0
 * Author: David Chandra Purnama
 * Author URI: http://shellcreeper.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: fx-recaptcha
 * Domain Path: /languages/
 *
 * @author David Chandra Purnama <david@genbumedia.com>
 * @copyright Copyright (c) 2017, Genbu Media
**/
if ( ! defined( 'WPINC' ) ) {
	die;
}

/* Constants
------------------------------------------ */

define( 'FX_RECAPTCHA_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'FX_RECAPTCHA_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FX_RECAPTCHA_FILE', __FILE__ );
define( 'FX_RECAPTCHA_PLUGIN', plugin_basename( __FILE__ ) );
define( 'FX_RECAPTCHA_VERSION', '1.0.0' );

/* Init
------------------------------------------ */

/* Load plugin in "plugins_loaded" hook */
add_action( 'plugins_loaded', 'fx_recaptcha_init' );

/**
 * Plugin Init.
 *
 * @since 0.1.0
 */
function fx_recaptcha_init() {

	// Functions.
	require_once( FX_RECAPTCHA_PATH . 'includes/functions.php' );

	// Settings.
	require_once( FX_RECAPTCHA_PATH . 'includes/class-settings.php' );

	// Get Option.
	$site_key = fx_recaptcha_get_option( 'site_key' );
	$secret_key = fx_recaptcha_get_option( 'secret_key' );
	$locations = fx_recaptcha_get_option( 'locations' );
	$locations = is_array( $locations ) ? $locations : array();
	// Bail if not set.
	if ( ! $site_key || ! $secret_key || ! $locations ) {
		return;
	}

	// Load location class.
	foreach ( $locations as $location ) {
		$file = FX_RECAPTCHA_PATH . "includes/class-{$location}.php";
		if ( file_exists( $file ) ) {
			require_once( $file );
		}
	}
}
