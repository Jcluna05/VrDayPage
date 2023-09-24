<?php
return array(
	array(
		'id' 			=> "{$prefix}menu-item-icon",
		'name'			=> esc_html__( 'Menu Item Icon', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set an icon', 'slick-menu' ),
		'type'			=> 'icon_picker',
		'accordion'		=> 'icons'
	),
	array(
		'id' 			=> "{$prefix}menu-item-icon-position",
		'name'			=> esc_html__( 'Menu Item Icon Position', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'before'	=>	esc_html__('Before', 'slick-menu'),
			'after'		=>	esc_html__('After', 'slick-menu'),
			'above'		=>	esc_html__('Above', 'slick-menu'),
			'below'		=>	esc_html__('Below', 'slick-menu'),
		),
		'accordion'		=> 'icons',
	),	
	array(
		'id' 			=> "{$prefix}menu-item-hide-label",
		'name'			=> esc_html__( 'Menu Item Hide Label', 'slick-menu' ),
		'desc'			=> esc_html__( 'Applicable only if a menu item icon is set', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'icons'
	)
);		