<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wordpress.org/plugins/super-simple-tracking-codes
 * @since      1.0.0
 *
 * @package    Super_Simple_Tracking_Codes
 * @subpackage Super_Simple_Tracking_Codes/includes
 */

class Super_Simple_Tracking_Codes_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'super-simple-tracking-codes',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
