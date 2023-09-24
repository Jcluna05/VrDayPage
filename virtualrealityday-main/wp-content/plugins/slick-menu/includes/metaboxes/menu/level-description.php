<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}description-enabled",
		'name'			=> esc_html__( 'Description Enabled', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> array(
			'0'	=> esc_html__( 'No', 'slick-menu' ),
			'1'	=> esc_html__( 'Yes', 'slick-menu' ),	
		),
		'accordion'		=> 'general',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}description-from-page",
		'name'			=> esc_html__( 'Use WordPress page as content?', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> array(
			'0'	=> esc_html__( 'No', 'slick-menu' ),
			'1'	=> esc_html__( 'Yes', 'slick-menu' ),	
		),
		'accordion'		=> 'general',
		'std'			=> '0',
		'visible'		=> array(
			array("{$prefix}description-enabled", '1')
		)
	),	
	array(
		'id' 			=> "{$prefix}description-text",
		'name'			=> esc_html__( 'Description', 'slick-menu' ),
		'desc'			=> esc_html__( 'Shortcodes are supported however styling shortcode output is up to the user and might require some CSS knowledge. Also note that some shortcodes might not work properly if Slick Menu is set to load via Ajax.', 'slick-menu' ),
		'raw'			=> true,
		'type'			=> 'textarea',
		'std'			=> '',
		'accordion'		=> 'general',
		'visible'		=> array(
			array("{$prefix}description-enabled", '1'),
			array("{$prefix}description-from-page", '0')
		)
	),
	array(
		'id' 			=> "{$prefix}description-page",
		'name'			=> esc_html__( 'Select Description Page', 'slick-menu' ),
		'desc'			=> esc_html__( 'Shortcodes are supported however styling shortcode output is up to the user and might require some CSS knowledge. Also note that some shortcodes might not work properly if Slick Menu is set to load via Ajax.', 'slick-menu' ),
		'raw'			=> true,
		'type'			=> 'post',
		'post_type'		=> 'page',
		'field_type'	=> 'select',
		'std'			=> '',
		'accordion'		=> 'general',
		'visible'		=> array(
			array("{$prefix}description-enabled", '1'),
			array("{$prefix}description-from-page", '1')
		)
	),
	// FONT
	array(
		'id' 			=> "{$prefix}description-font-family",
		'name'			=> esc_html__( 'Description Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'std'			=> 'Lato:400',
		'accordion'		=> 'font'
	),
	array(
		'id' 			=> "{$prefix}description-font-size",
		'name'			=> esc_html__( 'Description Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '18px',
		'accordion'		=> 'font'
	),
	array(
		'id' 			=> "{$prefix}description-line-height",
		'name'			=> esc_html__( 'Description Line Height', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter line height in (px)', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '24px',
		'accordion'		=> 'font'
	),
	array(
		'id' 			=> "{$prefix}description-text-transform",
		'name'			=> esc_html__( 'Description Text Transform', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_text_transform(),
		'accordion'		=> 'font',
		'std'			=> 'none'
	),
	array(
		'id' 			=> "{$prefix}description-text-align",
		'name'			=> esc_html__( 'Description Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_align(),
		'std'			=> 'center',
		'accordion'		=> 'font'
	),	
	
	// COLORS
	array(
		'id' 			=> "{$prefix}description-font-color",
		'name'			=> esc_html__( 'Description Font Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'std'			=> '#ffffff',
		'accordion'		=> 'colors'
	),	
	array(
		'id' 			=> "{$prefix}description-bg-color",
		'name'			=> esc_html__( 'Description Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'std'			=> '',
		'accordion'		=> 'colors'
	),
	
	// SPACING

	array(
		'id' 			=> "{$prefix}description-width",
		'name'			=> esc_html__( 'Description Max Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set a max width in (%, px, vw). Use the vw unit to enter a percentage related to the viewport width. Ex 50vw would make the width equal to half the window width.', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '',
		'accordion'		=> 'spacing'
	),	
	array(
		'id' 			=> "{$prefix}description-padding",
		'name'			=> esc_html__( 'Description Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '20px',
		'accordion'		=> 'spacing'
	),
	array(
		'id' 			=> "{$prefix}description-margin",
		'name'			=> esc_html__( 'Description Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'std'			=> '0 0 15px 0',
		'accordion'		=> 'spacing'
	),
	
	
	// BORDER
	
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}description-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),   
	
	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}description-animation",
		'name'			=> esc_html__( 'Description Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'std'			=> '',
		'accordion'		=> 'animations'
	),	
);

