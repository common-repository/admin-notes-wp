<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://webrockstar.net
 * @since      1.0.0
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/includes
 * @author     Web Rockstar <steve@webrockstar.net>
 */
class Wp_Admin_Notes_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-admin-notes',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
