<?php

/**
 * The admin help tab post type specific functionality of the plugin.
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/admin
 * @author     Web Rockstar <steve@webrockstar.net>
 */
class Wp_Admin_Notes_Admin_Help_Tab {
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

		$this->helpers = new \WPAN\Wp_Admin_Notes_Admin_Helpers();
	}

	function wpan_help_tab_content_acf_update_value( $value, $post_id, $field, $original ): string {
		$value = wp_kses_post( $value );

		return $value;
	}

	function wpan_associated_admin_screen_id_acf_update_value( $value, $post_id, $field, $original ): string {
		$value = sanitize_title( $value );
		$value = substr( $value, 0, 200 );

		return $value;
	}

	function add_user_capabilities() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( 'edit_wpan_help_tab' );
			$role->add_cap( 'edit_wpan_help_tabs' );
			$role->add_cap( 'edit_others_wpan_help_tabs' );
			$role->add_cap( 'publish_wpan_help_tabs' );
			$role->add_cap( 'read_wpan_help_tab' );
			$role->add_cap( 'read_private_wpan_help_tabs' );
			$role->add_cap( 'delete_wpan_help_tab' );
			$role->add_cap( 'edit_published_wpan_help_tabs' );
			$role->add_cap( 'delete_published_wpan_help_tabs' );
		}
	}

	function maybe_clear_help_tab_transient( $post_id ) {
		global $post;
		if ( $post->post_type != 'wpan_help_tab' ) {
			return;
		}
		$associated_admin_screen = get_field( 'wpan_associated_admin_screen_id', $post_id );
		delete_transient( 'wpan_help_tabs_' . $associated_admin_screen );
	}


	private function get_help_tabs(): array {
		$screen             = get_current_screen();
		$filtered_screen_id = sanitize_title( $screen->id );
		$transient          = get_transient( 'wpan_help_tabs_' . $filtered_screen_id );

		if ( ! empty( $transient ) && false ) {
			return $transient;
		} else {
			$help_tabs = array();

			$args = array(
				'post_type'        => 'wpan_help_tab',
				'post_status'      => 'publish',
				'posts_per_page'   => 50,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'meta_query'       => array(
					array(
						'key'     => 'wpan_associated_admin_screen_id',
						'compare' => '=',
						'value'   => $screen->id,
						'type'    => 'CHAR',
					),
				),
				'suppress_filters' => false,
			);

			$help_tab_query = get_posts( $args );
			if ( $help_tab_query ) :
				foreach ( $help_tab_query as $help_tab ) :

					$help_tab_content = get_field( "wpan_help_tab_content", $help_tab->ID );
					$help_tab_content = $this->helpers->convertURLs( $help_tab_content );
					$help_tab_content = $this->helpers->setUpLazyLoadIframeSrc( $help_tab_content );

					$title = get_the_title( $help_tab->ID );

					$help_tabs[] = array(
						'title' => $title,
						'body'  => $help_tab_content,
						'id'    => $help_tab->ID
					);
				endforeach;
			endif;
			set_transient( 'wpan_help_tabs_' . $filtered_screen_id, $help_tabs, DAY_IN_SECONDS );

			return $help_tabs;
		}

	}

	function maybe_add_help_tab() {
		$screen    = get_current_screen();
		$help_tabs = $this->get_help_tabs();

		if ( $help_tabs ) :
			foreach ( $help_tabs as $help_tab ) :
				$screen->add_help_tab( array(
					'id'      => 'wpan-help-tab-' . $help_tab['id'],
					'title'   => $help_tab['title'],
					'content' => $help_tab['body']
				) );
			endforeach;
		endif;

	}

	function help_tab_listing_table_head( $defaults ) {
		$head_label                                     = esc_html__( 'Screen ID', 'wp-admin-notes' );
		$defaults['wpan-help-tab-associated-screen-id'] = "<span >{$head_label}</span>";

		return $defaults;
	}

	function help_tab_listing_table_content( $column_name, $post_id ) {
		if ( $column_name == 'wpan-help-tab-associated-screen-id' ) {
			$associated_admin_screen = get_field( "wpan_associated_admin_screen_id", $post_id );
			echo $associated_admin_screen;
		}
	}

	function set_custom_help_tab_listing_sortable_columns( $columns ) {
		$columns['wpan-help-tab-associated-screen-id'] = 'wpan-help-tab-associated-screen-id';

		return $columns;
	}

	function help_tab_listing_custom_orderby( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'wpan-help-tab-associated-screen-id' == $orderby ) {
			$query->set( 'meta_key', '_wpan_associated_admin_screen_id' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}


	function wpan_help_tab_post_type_init() {

		$disable_help_tab_posts = get_option( '_wpan_disable_help_tabs' );
		if ( $disable_help_tab_posts ) {
			return;
		}

		$labels = array(
			'name'               => esc_html_x( 'Help Tabs', 'post type general name', 'wp-admin-notes' ),
			'singular_name'      => esc_html_x( 'Help Tab', 'post type singular name', 'wp-admin-notes' ),
			'menu_name'          => esc_html_x( 'Help Tabs', 'admin menu', 'wp-admin-notes' ),
			'name_admin_bar'     => esc_html_x( 'Help Tab', 'add new on admin bar', 'wp-admin-notes' ),
			'add_new'            => esc_html_x( 'Add New', 'wpan_help_tab', 'wp-admin-notes' ),
			'add_new_item'       => esc_html__( 'Add New Help Tab', 'wp-admin-notes' ),
			'new_item'           => esc_html__( 'New Help Tab', 'wp-admin-notes' ),
			'edit_item'          => esc_html__( 'Edit Help Tab', 'wp-admin-notes' ),
			'view_item'          => esc_html__( 'View Help Tab', 'wp-admin-notes' ),
			'all_items'          => esc_html__( 'All Help Tabs', 'wp-admin-notes' ),
			'search_items'       => esc_html__( 'Search Help Tabs', 'wp-admin-notes' ),
			'parent_item_colon'  => esc_html__( 'Parent Help Tabs:', 'wp-admin-notes' ),
			'not_found'          => esc_html__( 'No Help Tabs found.', 'wp-admin-notes' ),
			'not_found_in_trash' => esc_html__( 'No Help Tabs found in Trash.', 'wp-admin-notes' )
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => 'wpan_help_tab',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array( 'title', 'page-attributes' ),
			'menu_icon'           => 'dashicons-editor-help',
		);
		register_post_type( 'wpan_help_tab', $args );
	}
}
