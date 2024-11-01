<?php
/**
 * Plugin Name: Super Simple Tracking Codes
 * Plugin URI: https://wordpress.org/plugins/super-simple-tracking-codes
 * Description: Just because it's super simple to add tracking codes to your website with this plugin.
 * Version: 1.0.0
 * Author: wepic
 * Author URI: https://wepic.be/
 * Text Domain: super-simple-tracking-codes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SSTC_VERSION', '1.0.0' );

/**
 * The name of the plugin used to uniquely identify it within the context of
 * WordPress and to define internationalization functionality.
 */
define( 'SSTC_PLUGIN_NAME', 'super_simple_tracking_codes' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-super-simple-tracking-codes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_super_simple_tracking_codes() {

	$plugin = new Super_Simple_Tracking_Codes();
	$plugin->run();

}
run_super_simple_tracking_codes();
