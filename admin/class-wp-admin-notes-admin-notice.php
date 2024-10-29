<?php

/**
 * The admin notice post type specific functionality of the plugin.
 *
 *
 * @package    Wp_Admin_Notes
 * @subpackage Wp_Admin_Notes/admin
 * @author     Web Rockstar <steve@webrockstar.net>
 */
class Wp_Admin_Notes_Admin_Notice {
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

	private function sanitize_notice_text( string $text ): string {
		$allowed_notice_html = array(
			'a'      => array(
				'href'           => array(),
				'title'          => array(),
				'ping'           => array(),
				'referrerpolicy' => array(),
				'rel'            => array(),
				'target'         => array(),
				'type'           => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
		);

		return wp_kses( $text, $allowed_notice_html );
	}

	function wpan_notice_post_text_acf_update_value( $value, $post_id, $field, $original ): string {
		$value = $this->sanitize_notice_text( $value );
		$value = substr( $value, 0, 2000 );

		return $value;
	}

	function wpan_notice_post_notice_type_acf_update_value( $value, $post_id, $field, $original ): string {
		$allowed_values = array( 'info', 'success', 'warning', 'error' );
		if ( in_array( $value, $allowed_values ) ) {
			return $value;
		}

		return 'info';
	}

	function wpan_associated_admin_screen_id_acf_update_value( $value, $post_id, $field, $original ): string {
		$value = sanitize_title( $value );
		$value = substr( $value, 0, 200 );

		return $value;
	}

	function maybe_clear_notice_transient( $post_id ) {
		global $post;
		if ( $post->post_type != 'wpan_notice' ) {
			return;
		}
		$associated_admin_screen = get_field( 'wpan_associated_admin_screen_id', $post_id );
		delete_transient( 'wpan_notices_' . $associated_admin_screen );
	}

	private function get_notices(): array {
		$screen             = get_current_screen();
		$filtered_screen_id = sanitize_title( $screen->id );
		$transient          = get_transient( 'wpan_notices_' . $filtered_screen_id );
		if ( ! empty( $transient ) ) {
			return $transient;
		} else {
			$notices = array();

			$args = array(
				'post_type'        => 'wpan_notice',
				'post_status'      => 'publish',
				'posts_per_page'   => 10,
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

			$notice_query = get_posts( $args );
			if ( $notice_query ) :
				foreach ( $notice_query as $notice ) :
					$notice_text = get_field( "wpan_notice_post_text", $notice->ID );

					$allowed_notice_html = array(
						'a'      => array(
							'href'  => array(),
							'title' => array()
						),
						'br'     => array(),
						'em'     => array(),
						'strong' => array(),
					);
					$notice_text         = wp_kses( $notice_text, $allowed_notice_html );
					$notice_text         = $this->helpers->convertURLs( $notice_text );
					$notice_type         = get_field( "wpan_notice_post_notice_type", $notice->ID );
					$notice_type_class   = '';
					switch ( $notice_type ) {
						case 'info':
							$notice_type_class = 'notice-info';
							break;
						case 'success':
							$notice_type_class = 'notice-success';
							break;
						case 'warning':
							$notice_type_class = 'notice-warning';
							break;
						case 'error':
							$notice_type_class = 'notice-error';
							break;
					}

					$notices[] =
						"<div class='notice {$notice_type_class}'>
							<p>{$notice_text}</p>
						</div>";

				endforeach;
			endif;
			set_transient( 'wpan_notices_' . $filtered_screen_id, $notices, DAY_IN_SECONDS );

			return $notices;
		}
	}

	function maybe_display_notice() {
		$screen  = get_current_screen();
		$notices = $this->get_notices();

		if ( $notices ) :
			foreach ( $notices as $notice ) :
				echo $notice;
			endforeach;
		endif;
	}

	function add_user_capabilities() {
		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( 'edit_wpan_notice' );
			$role->add_cap( 'edit_wpan_notices' );
			$role->add_cap( 'edit_others_wpan_notices' );
			$role->add_cap( 'publish_wpan_notices' );
			$role->add_cap( 'read_wpan_notice' );
			$role->add_cap( 'read_private_wpan_notices' );
			$role->add_cap( 'delete_wpan_notice' );
			$role->add_cap( 'edit_published_wpan_notices' );
			$role->add_cap( 'delete_published_wpan_notices' );
		}
	}

	function notice_listing_table_head( $defaults ) {
		$head_label                                   = esc_html__( 'Screen ID', 'wp-admin-notes' );
		$defaults['wpan-notice-associated-screen-id'] = "<span >{$head_label}</span>";

		return $defaults;
	}

	function notice_listing_table_content( $column_name, $post_id ) {
		if ( $column_name == 'wpan-notice-associated-screen-id' ) {
			$associated_admin_screen = get_field( "wpan_associated_admin_screen_id", $post_id );
			echo $associated_admin_screen;
		}
	}

	function set_custom_notice_listing_sortable_columns( $columns ) {
		$columns['wpan-notice-associated-screen-id'] = 'wpan-notice-associated-screen-id';

		return $columns;
	}

	function notice_listing_custom_orderby( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'wpan-notice-associated-screen-id' == $orderby ) {
			$query->set( 'meta_key', '_wpan_associated_admin_screen_id' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}


	function wpan_notice_post_type_init() {

		$disable_notice_posts = get_option( '_wpan_disable_notice_posts' );
		if ( $disable_notice_posts ) {
			return;
		}
		$labels = array(
			'name'               => esc_html_x( 'Admin Notices', 'post type general name', 'wp-admin-notes' ),
			'singular_name'      => esc_html_x( 'Admin Notice', 'post type singular name', 'wp-admin-notes' ),
			'menu_name'          => esc_html_x( 'Admin Notices', 'admin menu', 'wp-admin-notes' ),
			'name_admin_bar'     => esc_html_x( 'Admin Notice', 'add new on admin bar', 'wp-admin-notes' ),
			'add_new'            => esc_html_x( 'Add New', 'wpan_notice', 'wp-admin-notes' ),
			'add_new_item'       => esc_html__( 'Add New Admin Notice', 'wp-admin-notes' ),
			'new_item'           => esc_html__( 'New Admin Notice', 'wp-admin-notes' ),
			'edit_item'          => esc_html__( 'Edit Admin Notice', 'wp-admin-notes' ),
			'view_item'          => esc_html__( 'View Admin Notice', 'wp-admin-notes' ),
			'all_items'          => esc_html__( 'All Admin Notices', 'wp-admin-notes' ),
			'search_items'       => esc_html__( 'Search Admin Notices', 'wp-admin-notes' ),
			'parent_item_colon'  => esc_html__( 'Parent Admin Notices:', 'wp-admin-notes' ),
			'not_found'          => esc_html__( 'No Admin Notices found.', 'wp-admin-notes' ),
			'not_found_in_trash' => esc_html__( 'No Admin Notices found in Trash.', 'wp-admin-notes' )
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
			'capability_type'     => 'wpan_notice',
			'has_archive'         => false,
			'hierarchical'        => true,
			'menu_position'       => null,
			'supports'            => array( 'title', 'page-attributes' ),
			'menu_icon'           => 'dashicons-bell',
		);
		register_post_type( 'wpan_notice', $args );
	}
}
