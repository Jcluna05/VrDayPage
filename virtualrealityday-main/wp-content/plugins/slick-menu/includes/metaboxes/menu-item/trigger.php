<?php

return array(
	array(
		'id' 		=> "{$prefix}trigger-menu",
		'name' 		=> esc_html__( 'Trigger a Slick Menu ?', 'slick-menu' ),
		'type'		=> 'radio',
		'options'	=> slick_menu_data_get_true_false(),
		'std' 		=> "0",
		'accordion' => 'general'
	),
	array(
		'id' 		=> "{$prefix}menu-id",
		'name' 		=> esc_html__( 'Select Slick Menu', 'slick-menu' ),
		'type' 		=> 'select',
		'placeholder'	=> esc_html__( 'Select Menu', 'slick-menu' ),
		'options' 	=> !empty($menus) ? $menus : array(),
		'visible' 	=> array("{$prefix}trigger-menu", 1),
		'std' 		=> '',
		'accordion' => 'general'
	)
);