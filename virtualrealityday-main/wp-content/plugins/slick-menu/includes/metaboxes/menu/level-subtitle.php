<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}subtitle-enabled",
		'name'			=> esc_html__( 'Sub Title Enabled', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> array(
			'0'	=> esc_html__( 'No', 'slick-menu' ),
			'1'	=> esc_html__( 'Yes', 'slick-menu' ),	
		),
		'accordion'		=> 'general',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}subtitle-text",
		'name'			=> esc_html__( 'Sub Title', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '',
		'accordion'		=> 'general'
	),
	
	// FONT
	array(
		'id' 			=> "{$prefix}subtitle-font-family",
		'name'			=> esc_html__( 'Sub Title Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'std'			=> 'Lato:400',
		'accordion'		=> 'font'
	),
	array(
		'id' 			=> "{$prefix}subtitle-font-size",
		'name'			=> esc_html__( 'Sub Title Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '24px',
		'accordion'		=> 'font'
	),
	array(
		'id' 			=> "{$prefix}subtitle-text-transform",
		'name'			=> esc_html__( 'Sub Title Text Transform', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_text_transform(),
		'accordion'		=> 'font',
		'std'			=> 'none'
	),
	array(
		'id' 			=> "{$prefix}subtitle-text-align",
		'name'			=> esc_html__( 'Sub Title Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_align(),
		'std'			=> 'center',
		'accordion'		=> 'font'
	),	
	
	// COLORS
	array(
		'id' 			=> "{$prefix}subtitle-font-color",
		'name'			=> esc_html__( 'Sub Title Font Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'std'			=> '#ffffff',
		'accordion'		=> 'colors'
	),	
	array(
		'id' 			=> "{$prefix}subtitle-bg-color",
		'name'			=> esc_html__( 'Sub Title Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'std'			=> '',
		'accordion'		=> 'colors'
	),
	
	// SPACING
	
	array(
		'id' 			=> "{$prefix}subtitle-padding",
		'name'			=> esc_html__( 'Sub Title Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '20px',
		'accordion'		=> 'spacing'
	),
	array(
		'id' 			=> "{$prefix}subtitle-margin",
		'name'			=> esc_html__( 'Sub Title Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '0 0 15px 0',
		'accordion'		=> 'spacing'
	),
	
	
	// BORDER
	
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}subtitle-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),   
	
	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}subtitle-animation",
		'name'			=> esc_html__( 'Sub Title Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'std'			=> '',
		'accordion'		=> 'animations'
	),	
);

