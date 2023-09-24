<?php

return array(
	
	// GENERAL
	array(
		'id' 			=> "{$prefix}logo-main-level",
		'name'			=> esc_html__( 'Show logo on main level only', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=>'general',
		'std'			=> '0'
	),  
	array(
		'id' 			=> "{$prefix}logo-use-avatar",
		'name'			=> esc_html__( 'Use logged in user Avatar as a logo', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=>'general',
		'std'			=> '0'
	), 
	array(
		'id' 			=> "{$prefix}logo",
		'name' 			=> esc_html__('Logo Image', 'slick-menu'),
        'type' 			=> 'image_advanced',
        'multiple'		=> false,
        'max_file_uploads' => 1,
        'accordion'		=>'general',
        'std'			=> '',
        'visible'		=> array("{$prefix}logo-use-avatar", '!=', '1')
    ),  
	array(
		'id' 			=> "{$prefix}logo-url",
		'name'			=> esc_html__( 'Logo Url', 'slick-menu' ),
		'type'			=> 'url',
		'accordion'		=>'general',
		'std'			=> '',
		'visible'		=> array("{$prefix}logo-use-avatar", '!=', '1')
	),
	array(
		'id' 			=> "{$prefix}logo-width",
		'name'			=> esc_html__( 'Logo Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter Width in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=>'general',
		'std'			=> '100px'
	),
	array(
		'id' 			=> "{$prefix}logo-height",
		'name'			=> esc_html__( 'Logo Height', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter Height in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=>'general',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}logo-align",
		'name'			=> esc_html__( 'Logo Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=>'general',
		'std'			=> 'center',
	),	
	
	// SPACING
	array(
		'id' 			=> "{$prefix}logo-padding",
		'name'			=> esc_html__( 'Logo Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=>'spacing',
		'std'			=> '40px 25px'
	),
	
	// BORDER
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}logo-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=>'border',
		'std' 			=> ''
	)),
		
	// ANIMATIONS	
	array(
		'id' 			=> "{$prefix}logo-animation",
		'name'			=> esc_html__( 'Logo Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'accordion'		=>'animations',
		'std'			=> ''
	)
);

