<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin architecture is based on https://github.com/DevinVinson/WordPress-Plugin-Boilerplate
 *
 * @link              https://webrockstar.net
 * @since             1.0.0
 * @package           Wp_Admin_Notes
 *
 * @wordpress-plugin
 * Plugin Name:       Admin Notes WP
 * Description:       Create and manage admin footer text, help tabs, and notices directly from the WordPress admin.
 * Version:           1.1.0
 * Author:            Web Rockstar
 * Author URI:        https://webrockstar.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-admin-notes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Loads the 'Carbon Fields' custom fields library. This is needed to manage the
 * WP Admin Notes setting fields and makes the process easier/faster compared to creating it
 * using the 'vanilla' WordPress Settings API. Ideally the ACF (Advanced Custom Fields) settings
 * page could have been used but that feature is only available in the ACF Pro version, thus it would
 * not be allowed in this free, public plugin.
 *
 * More info at https://carbonfields.net/
 */
require_once( 'vendor/autoload.php' );

/**
 * Embedding the free, public version of ACF for use on the 'help tab' and 'notice' post types.
 *
 * More info at https://www.advancedcustomfields.com/resources/including-acf-within-a-plugin-or-theme/
 */
define( 'WPAN_ACF_PATH', plugin_dir_path( __FILE__ ) . 'includes/acf/advanced-custom-fields/' );
define( 'WPAN_ACF_URL', plugin_dir_url( __FILE__ ) . 'includes/acf/advanced-custom-fields/' );

/**
 * The ACF code included in this plugin is only used if the actual ACF or ACF Pro plugin is not already installed
 */
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(!in_array('advanced-custom-fields/acf.php', $active_plugins) && !in_array('advanced-custom-fields-pro/acf.php', $active_plugins) ){
	require_once( WPAN_ACF_PATH . 'acf.php' );
}

/**
 * Include the ACF field exports for the notice and help tab post types
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/acf/fields/help-tab-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/acf/fields/notice-settings.php' );

/**
 * Helper Class
 */
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-admin-notes-admin-helpers.php' );


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_ADMIN_NOTES_VERSION', '1.1.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-admin-notes-activator.php
 */
function activate_wp_admin_notes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-notes-activator.php';
	Wp_Admin_Notes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-admin-notes-deactivator.php
 */
function deactivate_wp_admin_notes() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-notes-deactivator.php';
	Wp_Admin_Notes_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_admin_notes' );
register_deactivation_hook( __FILE__, 'deactivate_wp_admin_notes' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-admin-notes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_admin_notes() {

	$plugin = new Wp_Admin_Notes();
	$plugin->run();

}
run_wp_admin_notes();
