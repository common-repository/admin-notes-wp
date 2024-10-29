<?php

add_action( 'acf/init', 'wpan_register_notice_post_type_settings' );

/**
 * Add help tab post type ACF settings. gettext function calls have been added to aide with translation
 */
function wpan_register_notice_post_type_settings() {
	$screen_id_label = esc_html__('Screen ID', 'wp-admin-notes');
	$screen_id_screenshot_img_src = plugin_dir_url( __FILE__ ) . '../../../admin/images/screen-id-tool.png';
	$screen_id_image_src_link = "<a href='{$screen_id_screenshot_img_src}' target='_blank' >{$screen_id_label}</a>";
	$admin_screen_reference_label = esc_html__('Admin Screen Reference', 'wp-admin-notes');
	$screen_id_codex_url = esc_html__('https://codex.wordpress.org/Plugin_API/Admin_Screen_Reference', 'wp-admin-notes');
	$wordpress_screen_id_codex_link = "<a href='{$screen_id_codex_url}' target='_blank' >{$admin_screen_reference_label}</a>";

	acf_add_local_field_group( array(
		'key'                   => 'group_6006523f4ae6d',
		'title'                 => esc_html__('Notice Settings', 'wp-admin-notes'),
		'fields'                => array(
			array(
				'key'               => 'field_60065250222eb',
				'label'             => esc_html__('Notice Text', 'wp-admin-notes'),
				'name'              => 'wpan_notice_post_text',
				'type'              => 'text',
				'instructions'      => esc_html__('a, br, em, and strong HTML tags are allowed in the notice text. URLs are automatically converted to links.', 'wp-admin-notes'),
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => 1000,
			),
			array(
				'key'               => 'field_6006527f222ec',
				'label'             => esc_html__('Notice Type', 'wp-admin-notes'),
				'name'              => 'wpan_notice_post_notice_type',
				'type'              => 'radio',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'choices'           => array(
					'info'    => esc_html__('General Information (blue)', 'wp-admin-notes'),
					'success' => esc_html__('Success (green)', 'wp-admin-notes'),
					'warning' => esc_html__('Warning (yellow)', 'wp-admin-notes'),
					'error'   => esc_html__('Error (red)', 'wp-admin-notes'),
				),
				'allow_null'        => 0,
				'other_choice'      => 0,
				'default_value'     => 'info',
				'layout'            => 'vertical',
				'return_format'     => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key'               => 'field_6006543f222ed',
				'label'             => esc_html__('Admin Screen ID', 'wp-admin-notes'),
				'name'              => 'wpan_associated_admin_screen_id',
				'type'              => 'text',
				'instructions'      => sprintf( esc_html__('The screen id of the WordPress admin page the notice should appear on. If you are unsure of the screen id, use the %1$s link in the tool bar at the very top of the admin screen you would like the notice to appear on. You can also refer to %2$s for a list of the core WordPress screen ids.', 'wp-admin-notes'),
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
					'value'    => 'wpan_notice',
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
