<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://webrockstar.net
 * @since      1.0.0
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/admin
 */

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/admin
 * @author     Web Rockstar <steve@webrockstar.net>
 */
class Wp_Admin_Notes_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	private $helpers;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->helpers     = new \WPAN\Wp_Admin_Notes_Admin_Helpers();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Admin_Notes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Admin_Notes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-admin-notes-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Admin_Notes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Admin_Notes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( "jquery-effects-core" );
		wp_enqueue_script( 'jquery-effects-bounce' );
		wp_enqueue_script( 'wp-admin-notes-admin', plugin_dir_url( __FILE__ ) . 'js/wp-admin-notes-admin.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		$localized_vars = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'ajax-nonce' )
		);
		wp_localize_script( 'wp-admin-notes-admin', 'wpan_ajax_object', $localized_vars );

		$localized_labels = array(
			'click_to_copy' => esc_html__( '(click to copy)', 'wp-admin-notes' ),
			'copied'        => esc_html__( '(copied!)', 'wp-admin-notes' )
		);
		wp_localize_script( 'wp-admin-notes-admin', 'wpan_labels', $localized_labels );

	}

	/**
	 * Promo notice for users to go to plugin page and rate plugin. This only displays on the ANWP settings page
	 */
	public function display_settings_page_wrs_feedback_notice() {
		$screen = get_current_screen();
		if ( $screen->id === 'settings_page_crb_carbon_fields_container_admin_notes_wp' ) {
			$rating_url        = 'https://wordpress.org/support/plugin/admin-notes-wp/reviews/#new-post';
			$support_forum_url = 'https://wordpress.org/support/plugin/admin-notes-wp/';
			$notice_text       = "If you find Admin Notes WP useful please consider <a href='{$rating_url}'>giving it a rating</a> in the WordPress plugin
			 repository. You can also post feature requests and bug reports in the <a href='{$support_forum_url}'>support forum</a>.";
			echo "<div class='notice notice-info'>
					<p>{$notice_text}</p>
				</div>";
		}

	}

	public function update_seen_help_tab_animation() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
			die ( 'Busted!' );
		}
		global $current_user;
		$current_user->ID;
		$updated = update_user_meta( $current_user->ID, 'wpan_has_seen_help_tab_animation', 1 );
		echo $current_user->ID . ' set to seen help tab animation';
		wp_die();
	}

	public function maybe_add_help_tab_animation_class( $classes ) {
		global $current_user;
		get_currentuserinfo();
		$wpan_animate_help_tab = get_option( '_wpan_animate_help_tab' );

		if ( $wpan_animate_help_tab ) {
			if ( $current_user ) {
				$has_seen_help_tab_animation = get_user_meta( $current_user->ID, 'wpan_has_seen_help_tab_animation', true );
				if ( ! empty( $has_seen_help_tab_animation ) ) {
					return $classes;
				}
			}

			return "$classes wpan-animate-help-tab";
		}

		return $classes;
	}

	function acf_settings_url( $url ) {
		return WPAN_ACF_URL;
	}

	/**
	 * If the actual ACF or ACF Pro plugins are installed, we don't want to hide the admin interface
	 * components.
	 *
	 * @param $show_admin
	 *
	 * @return bool
	 */
	public function acf_settings_show_admin( $show_admin ) {
		if ( is_plugin_active( 'advanced-custom-fields/acf.php' ) || is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
			return true;
		}

		return false;
	}

	public function screen_id_display_helper( WP_Admin_Bar $admin_bar ) {
		if ( ! current_user_can( 'manage_options' ) || ! is_admin() ) {
			return;
		}
		$wpan_disable_help_tabs    = get_option( '_wpan_disable_help_tabs' );
		$wpan_disable_notice_posts = get_option( '_wpan_disable_notice_posts' );
		/**
		 * If both help tabs and notice posts are disabled then there is no need to display this
		 * screen id helper link
		 */
		if ( $wpan_disable_help_tabs && $wpan_disable_notice_posts ) {
			return;
		}

		$admin_bar->add_menu( array(
			'id'     => 'wpan-screen-id-helper',
			'parent' => null,
			'group'  => null,
			'title'  => 'Screen ID',
			'href'   => '#',

		) );

		$screen                  = get_current_screen();
		$click_label             = esc_html__( '(click to copy)', 'wp-admin-notes' );
		$screen_id_display_title = "<span class='wpan_toolbar_screen_id' >{$screen->id}</span> <span class='wpan_toolbar_screen_id_click_label' >{$click_label}</span>";
		$admin_bar->add_menu( array(
			'id'     => 'wpan-screen-id-display',
			'parent' => 'wpan-screen-id-helper',
			'group'  => null,
			'title'  => $screen_id_display_title,
			'href'   => '#',
		) );

	}

	public function maybe_hide_default_help_tabs() {
		$wpan_hide_default_help_tabs = get_option( '_wpan_hide_default_help_tabs' );
		if ( $wpan_hide_default_help_tabs ) {
			$core_screen_ids = array(
				'edit-post',
				'post',
				'edit-category',
				'edit-post_tag',
				'upload',
				'media',
				'attachment',
				'edit-page',
				'page',
				'edit-comments',
				'comment',
				'themes',
				'widgets',
				'nav-menus',
				'theme-editor',
				'plugins',
				'plugin-install',
				'plugin-editor',
				'users',
				'user-new',
				'user-edit',
				'profile',
				'tools',
				'import',
				'admin',
				'export',
				'options-general',
				'options-writing',
				'options-reading',
				'options-discussion',
				'options-media',
				'options-permalink',
				'link-manager',
				'link',
				'edit-link_category',
				'dashboard',
				'update-core',
				'users-network',
				'plugins-network',
				'sites-network',
				'themes-network'
			);

			$screen = get_current_screen();
			if ( in_array( $screen->id, $core_screen_ids ) ) {
				$screen->remove_help_tabs();
				$screen->set_help_sidebar( '' );
			}
		}
	}

	public function maybe_add_global_help_tab() {

		$wpan_enable_global_help_tab = get_option( '_wpan_enable_global_help_tab' );
		if ( $wpan_enable_global_help_tab ) {
			$screen = get_current_screen();

			$global_help_tab_title = get_option( '_wpan_global_help_tab_title' );
			$global_help_tab_title = apply_filters( 'the_title', $global_help_tab_title );


			$global_help_tab_body     = get_option( '_wpan_global_help_tab_body' );
			$global_help_tab_image_id = get_option( '_wpan_global_help_tab_image_id' );
			$global_help_tab_image    = '';
			if ( $global_help_tab_image_id ) {
				$global_help_tab_image = wp_get_attachment_image( $global_help_tab_image_id, 'full', "", array( "class" => "wpan-global-help-tab__image" ) );
			}

			$global_help_tab_body = wp_kses_post( $global_help_tab_body );

			$global_help_tab_content = apply_filters( 'the_content', $global_help_tab_body ) . $global_help_tab_image;
			$global_help_tab_content = $this->helpers->convertURLs( $global_help_tab_content );
			$global_help_tab_content = $this->helpers->setUpLazyLoadIframeSrc( $global_help_tab_content );
			$screen->add_help_tab( array(
				'id'      => 'wpan-global-tab',
				'title'   => $global_help_tab_title,
				'content' => $global_help_tab_content
			) );

		}
	}

	public function admin_footer_text( string $text ) {
		$enable_footer_text = get_option( '_wpan_enable_admin_footer_text' );
		if ( $enable_footer_text ) {
			$footer_text = get_option( '_wpan_admin_footer_text' );

			$allowed_html_tags      = array(
				'a'      => array(
					'href'  => array(),
					'title' => array()
				),
				'em'     => array(),
				'strong' => array(),
			);
			$footer_text            = wp_kses( $footer_text, $allowed_html_tags );
			$footer_text            = $this->helpers->convertURLs( $footer_text );
			$footer_icon_image_id   = get_option( '_wpan_admin_footer_icon_image_id' );
			$footer_icon_image_html = '';
			if ( $footer_icon_image_id ) {
				$footer_icon_image_html = wp_get_attachment_image( $footer_icon_image_id, array(
					'24',
					'24'
				), "", array( "class" => "wpan-footer-credits__image" ) );
			}

			return "
				<span class='wpan-footer-credits__container' >
					{$footer_icon_image_html}
					<span>{$footer_text}</span>
				</span>
			";
		}

		return $text;
	}

	public function check_wpan_settings( $save, $value, $field ) {
		if ( ! $value ) {
			return true;
		}

		switch ( $field->get_base_name() ) {
			case 'wpan_enable_admin_footer_text':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
			case 'wpan_admin_footer_text':
				if ( strlen( $value ) > 300 ) {
					return false;
				}
				break;
			case 'wpan_admin_footer_icon_image_id_id':
				if ( ! ctype_digit( $value ) ) {
					return false;
				}
				break;
			case 'wpan_disable_notice_posts':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
			case 'wpan_disable_help_tabs':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
			case 'wpan_enable_global_help_tab':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
			case 'wpan_global_help_tab_title':
				if ( strlen( $value ) > 100 ) {
					return false;
				}
				break;
			case 'wpan_global_help_tab_body':
				if ( strlen( $value ) > 5000 ) {
					return false;
				}
				break;
			case 'wpan_global_help_tab_image_id_id':
				if ( ctype_digit( $value ) ) {
					return false;
				}
				break;
			case 'wpan_hide_default_help_tabs':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
			case 'wpan_animate_help_tab':
				if ( $value != 'yes' ) {
					return false;
				}
				break;
		}

		return true;
	}

	public function crb_attach_theme_options() {
		Container::make( 'theme_options', esc_html__( 'Admin Notes WP', 'wp-admin-notes' ) )
		         ->set_page_parent( 'options-general.php' )
		         ->where( 'current_user_capability', '=', 'manage_options' )
		         ->add_fields( array(
			         Field::make( 'separator', 'wpan_admin_footer_header', esc_html__( 'Admin Footer Text', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Overrides the default text in the very bottom left corner of every WordPress admin page.
					This is typically used to highlight the department or company that developed/configured the website.', 'wp-admin-notes' ) ),
			         Field::make( 'checkbox', 'wpan_enable_admin_footer_text', esc_html__( 'Enable Admin Footer Text', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' ),

			         Field::make( 'text', 'wpan_admin_footer_text', esc_html__( 'Footer Text', 'wp-admin-notes' ) )
			              ->set_width( 50 )
			              ->set_attribute( 'maxLength', '300' )
			              ->set_attribute( 'placeholder', esc_html__( 'Website built and configured by https://webrockstar.net', 'wp-admin-notes' ) )
			              ->set_conditional_logic( array(
				              array(
					              'field' => 'wpan_enable_admin_footer_text',
					              'value' => true,
				              )
			              ) )
			              ->set_help_text( esc_html__( 'Allowed HTML: a, strong, em. URLs are automatically converted to links.', 'wp-admin-notes' ) ),
			         Field::make( 'image', 'wpan_admin_footer_icon_image_id', esc_html__( 'Footer Text Icon Image', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Optional image icon for the admin footer text. Icon is displayed at 24px X 24px and left aligned.
				     Transparent PNG or SVG works best.', 'wp-admin-notes' ) )
			              ->set_conditional_logic( array(
				              array(
					              'field' => 'wpan_enable_admin_footer_text',
					              'value' => true,
				              )
			              ) )
			              ->set_value_type( 'id' ),

			         Field::make( 'separator', 'wpan_notice_posts_header', esc_html__( 'Notice Posts', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Custom notices on WordPress admin pages. Created and managed in the WordPress admin.' ) ),
			         Field::make( 'checkbox', 'wpan_disable_notice_posts', esc_html__( 'Disable Notice Posts', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' )
			              ->set_help_text( esc_html__( 'Toggles the display of the Admin Notices post type on the left menu bar.', 'wp-admin-notes' ) ),

			         Field::make( 'separator', 'wpan_admin_help_tabs_header', esc_html__( 'Help Tabs', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Custom help tabs on WordPress admin pages. Created and managed in the WordPress admin.', 'wp-admin-notes' ) ),
			         Field::make( 'checkbox', 'wpan_disable_help_tabs', esc_html__( 'Disable Help Tabs', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' )
			              ->set_help_text( esc_html__( 'Toggles the display of the Help Tab post type on the left menu bar.', 'wp-admin-notes' ) ),


			         Field::make( 'separator', 'wpan_admin_global_help_tab_header', esc_html__( 'Global Help Tab', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'A single global help tab that appears on every WordPress admin page.
					 This is typically used to display a message about who to contact regarding technical issues or feature requests.', 'wp-admin-notes' ) ),
			         Field::make( 'checkbox', 'wpan_enable_global_help_tab', esc_html__( 'Enable Global Help Tab', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' ),
			         Field::make( 'text', 'wpan_global_help_tab_title', esc_html__( 'Global Help Tab Title', 'wp-admin-notes' ) )
			              ->set_attribute( 'maxLength', '100' )
			              ->set_conditional_logic( array(
				              array(
					              'field' => 'wpan_enable_global_help_tab',
					              'value' => true,
				              )
			              ) ),

			         Field::make( 'rich_text', 'wpan_global_help_tab_body', esc_html__( 'Global Help Tab Body', 'wp-admin-notes' ) )
			              ->set_rows( 20 )
			              ->set_attribute( 'maxLength', '5000' )
			              ->set_conditional_logic( array(
				              array(
					              'field' => 'wpan_enable_global_help_tab',
					              'value' => true,
				              )
			              ) ),
			         Field::make( 'image', 'wpan_global_help_tab_image_id', __( 'Global Help Tab Logo Image', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Optional logo image to display at the bottom of the global help tab content.
						 This is typically the logo of the company that configured/developed the website, if applicable.
						 This image is displayed with a maximum width and height of 200px. Transparent PNG or SVG works best.', 'wp-admin-notes' ) )
			              ->set_conditional_logic( array(
				              array(
					              'field' => 'wpan_enable_global_help_tab',
					              'value' => true,
				              )
			              ) )
			              ->set_value_type( 'id' ),


			         Field::make( 'separator', 'wpan_other_settings_header', esc_html__( 'Other Settings', 'wp-admin-notes' ) )
			              ->set_help_text( esc_html__( 'Other miscellaneous settings' ) ),
			         Field::make( 'checkbox', 'wpan_hide_default_help_tabs', esc_html__( 'Hide Default Help Tabs', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' )
			              ->set_help_text( esc_html__( 'Hide all default help tab content. Note: enabling this may also hide help tabs
					 generated from your theme or other plugins.' ) ),
			         Field::make( 'checkbox', 'wpan_animate_help_tab', esc_html__( 'Animate Help Tab Before First Click', 'wp-admin-notes' ) )
			              ->set_option_value( 'yes' )
			              ->set_help_text( esc_html__( 'To help users discover the help tab, it will bounce and highlight until clicked.
					  After it is clicked once it will never animate again for that user.', 'wp-admin-notes' ) ),

		         ) );
	}

	public function crb_load() {
		Carbon_Fields::boot();
	}


}
