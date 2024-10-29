<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webrockstar.net
 * @since      1.0.0
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/includes
 * @author     Web Rockstar <steve@webrockstar.net>
 */
class Wp_Admin_Notes {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Admin_Notes_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_ADMIN_NOTES_VERSION' ) ) {
			$this->version = WP_ADMIN_NOTES_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-admin-notes';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Admin_Notes_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Admin_Notes_i18n. Defines internationalization functionality.
	 * - Wp_Admin_Notes_Admin. Defines all hooks for the admin area.
	 * - Wp_Admin_Notes_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-admin-notes-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-admin-notes-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-admin-notes-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-admin-notes-admin-notice.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-admin-notes-admin-help-tab.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-admin-notes-public.php';

		$this->loader = new Wp_Admin_Notes_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Admin_Notes_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Admin_Notes_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Admin_Notes_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_help_tab = new Wp_Admin_Notes_Admin_Help_Tab( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_notice = new Wp_Admin_Notes_Admin_Notice( $this->get_plugin_name(), $this->get_version() );

		/**
		 * General Admin Hooks
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'acf/settings/url', $plugin_admin, 'acf_settings_url' );
		$this->loader->add_filter( 'acf/settings/show_admin', $plugin_admin, 'acf_settings_show_admin' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'screen_id_display_helper', 500 );
		$this->loader->add_action( 'carbon_fields_register_fields', $plugin_admin, 'crb_attach_theme_options' );
		$this->loader->add_action( 'after_setup_theme', $plugin_admin, 'crb_load' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'admin_footer_text' );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'maybe_hide_default_help_tabs',998 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'maybe_add_global_help_tab',999 );
		$this->loader->add_filter( 'admin_body_class', $plugin_admin, 'maybe_add_help_tab_animation_class' );
		$this->loader->add_action( 'wp_ajax_update_seen_help_tab_animation', $plugin_admin, 'update_seen_help_tab_animation' );
		$this->loader->add_filter( 'carbon_fields_should_save_field_value', $plugin_admin, 'check_wpan_settings',0, 3 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_settings_page_wrs_feedback_notice' );

		/**
		 * Admin Help Tab Related Hooks
		 */
		$this->loader->add_action( 'save_post', $plugin_admin_help_tab, 'maybe_clear_help_tab_transient' );
		$this->loader->add_action( 'init', $plugin_admin_help_tab, 'wpan_help_tab_post_type_init' );
		$this->loader->add_action( 'manage_wpan_help_tab_posts_custom_column', $plugin_admin_help_tab, 'help_tab_listing_table_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-wpan_help_tab_sortable_columns', $plugin_admin_help_tab, 'set_custom_help_tab_listing_sortable_columns' );
		$this->loader->add_filter( 'manage_wpan_help_tab_posts_columns', $plugin_admin_help_tab, 'help_tab_listing_table_head' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin_help_tab, 'help_tab_listing_custom_orderby' );
		$this->loader->add_action( 'admin_head', $plugin_admin_help_tab, 'maybe_add_help_tab',999 );
		$this->loader->add_filter( 'acf/update_value/name=wpan_help_tab_content', $plugin_admin_help_tab, 'wpan_help_tab_content_acf_update_value', 10, 4 );
		$this->loader->add_filter( 'acf/update_value/name=wpan_associated_admin_screen_id', $plugin_admin_help_tab, 'wpan_associated_admin_screen_id_acf_update_value', 10, 4);
		$this->loader->add_action( 'admin_init', $plugin_admin_help_tab, 'add_user_capabilities' );

		/**
		 * Admin Notice Related Hooks
		 */
		$this->loader->add_action( 'save_post', $plugin_admin_notice, 'maybe_clear_notice_transient' );
		$this->loader->add_action( 'init', $plugin_admin_notice, 'wpan_notice_post_type_init' );
		$this->loader->add_action( 'manage_wpan_notice_posts_custom_column', $plugin_admin_notice, 'notice_listing_table_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-wpan_notice_sortable_columns', $plugin_admin_notice, 'set_custom_notice_listing_sortable_columns' );
		$this->loader->add_filter( 'manage_wpan_notice_posts_columns', $plugin_admin_notice, 'notice_listing_table_head' );
		$this->loader->add_action( 'admin_notices', $plugin_admin_notice, 'maybe_display_notice' );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin_notice, 'notice_listing_custom_orderby' );
		$this->loader->add_filter( 'acf/update_value/name=wpan_notice_post_text', $plugin_admin_notice, 'wpan_notice_post_text_acf_update_value', 10, 4 );
		$this->loader->add_filter( 'acf/update_value/name=wpan_notice_post_notice_type', $plugin_admin_notice, 'wpan_notice_post_notice_type_acf_update_value', 10, 4);
		$this->loader->add_filter( 'acf/update_value/name=wpan_associated_admin_screen_id', $plugin_admin_notice, 'wpan_associated_admin_screen_id_acf_update_value', 10, 4 );
		$this->loader->add_action( 'admin_init', $plugin_admin_notice, 'add_user_capabilities' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Admin_Notes_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Wp_Admin_Notes_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
