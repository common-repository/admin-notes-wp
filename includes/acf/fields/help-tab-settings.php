<?php
add_action( 'acf/init', 'wpan_register_help_tab_post_type_settings' );

/**
 * Add help tab post type ACF settings. gettext function calls have been added to aide with translation
 */
function wpan_register_help_tab_post_type_settings() {
	$screen_id_label              = esc_html__( 'Screen ID', 'wp-admin-notes' );
	$screen_id_screenshot_img_src = plugin_dir_url( __FILE__ ) . '../../../admin/images/screen-id-tool.png';
	$screen_id_image_src_link     = "<a href='{$screen_id_screenshot_img_src}' target='_blank' >{$screen_id_label}</a>";

	$admin_screen_reference_label   = esc_html__( 'Admin Screen Reference', 'wp-admin-notes' );
	$screen_id_codex_url            = esc_html__( 'https://codex.wordpress.org/Plugin_API/Admin_Screen_Reference', 'wp-admin-notes' );
	$wordpress_screen_id_codex_link = "<a href='{$screen_id_codex_url}' target='_blank' >{$admin_screen_reference_label}</a>";

	$embedding_videos_info_page_url  = esc_html__( 'https://www.wpbeginner.com/beginners-guide/how-to-easily-embed-videos-in-wordpress-blog-posts/', 'wp-admin-notes' );
	$embedding_videos_info_page_link = "<a href='{$embedding_videos_info_page_url}' target='_blank' >" . esc_html__( 'Learn more about embedding in WordPress.', 'wp-admin-notes' ) . "</a>";

	acf_add_local_field_group( array(
		'key'                   => 'group_6006516c069c1',
		'title'                 => esc_html__( 'Help Tab Settings', 'wp-admin-notes' ),
		'fields'                => array(
			array(
				'key'               => 'field_6006518c76c1f',
				'label'             => esc_html__( 'Help Tab Content', 'wp-admin-notes' ),
				'name'              => 'wpan_help_tab_content',
				'type'              => 'wysiwyg',
				'instructions'      => sprintf( esc_html__( 'Instructional \'how to\' videos from youtube.com, slide shows from slideshare.net, and more can easily be embedded into help tab content. %1$s', 'wp-admin-notes' ),
					$embedding_videos_info_page_link ),
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'tabs'              => 'all',
				'toolbar'           => 'full',
				'media_upload'      => 1,
				'delay'             => 0,
			),
			array(
				'key'               => 'field_600651c576c20',
				'label'             => esc_html__( 'Admin Screen ID', 'wp-admin-notes' ),
				'name'              => 'wpan_associated_admin_screen_id',
				'type'              => 'text',
				'instructions'      => sprintf( esc_html__( 'The screen id of the WordPress admin page the notice should appear on. If you are unsure of the screen id, use the %1$s link in the tool bar at the very top of the admin screen you would like the help tab to appear on. You can also refer to %2$s for a list of the core WordPress screen ids.', 'wp-admin-notes' ),
					$screen_id_image_src_link, $wordpress_screen_id_codex_link ),
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => 'profile',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'wpan_help_tab',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	) );

}
