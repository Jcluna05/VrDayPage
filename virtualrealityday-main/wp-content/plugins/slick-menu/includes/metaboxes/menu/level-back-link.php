<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}back-link-hidden",
		'name'			=> esc_html__( 'Back Link Hidden', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> '0',
		'accordion'		=> 'general',
		'visible'		=> array("{$prefix}level-animation-type", 'overlap')
	),
	array(
		'id' 			=> "{$prefix}back-link-vposition",
		'name'			=> esc_html__( 'Back Link Position', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'top'		=> esc_html__( 'Stick to top', 'slick-menu' ),
			'above'		=> esc_html__( 'Above', 'slick-menu' ),
			'below'		=> esc_html__( 'Below', 'slick-menu' ),
			'bottom'	=> esc_html__( 'Stick to bottom', 'slick-menu' ),
		),
		'accordion'		=> 'general',
		'std'			=> 'above'
	),	
	array(
		'id' 			=> "{$prefix}back-link-position",
		'name'			=> esc_html__( 'Back Link Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'right'
	),	
	
	// FONT
	array(
		'id' 			=> "{$prefix}back-link-font-family",
		'name'			=> esc_html__( 'Back Link Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'accordion'		=> 'font',
		'std'			=> 'Lato:400'
	),
	array(
		'id' 			=> "{$prefix}back-link-font-size",
		'name'			=> esc_html__( 'Back Link Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '16px'
	),		

	
	// COLORS			
	array(
		'id' 			=> "{$prefix}back-link-color",
		'name'			=> esc_html__( 'Back Link Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255,255,255,0.8)'
	),
	array(
		'id' 			=> "{$prefix}back-link-hover-color",
		'name'			=> esc_html__( 'Back Link Hover Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255,255,255,1)'
	),
		
	// ICONS
	array(
		'id' 			=> "{$prefix}back-link-icon",
		'name'			=> esc_html__( 'Back Link Icon', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set an arrow icon', 'slick-menu' ),
		'type'			=> 'icon_picker',
		'accordion'		=> 'icons',
		'std'			=> array(
			'type' 		=> 'fa',
			'icon' 		=> 'fa-long-arrow-right'
		)
	),
	array(
		'id' 			=> "{$prefix}back-link-icon-only",
		'name'			=> esc_html__( 'Back Link Show Icon Without Text', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> '0',
		'accordion'		=> 'icons'
	),		
	array(
		'id' 			=> "{$prefix}back-link-icon-position",
		'name'			=> esc_html__( 'Back Link Icon Position', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'before'	=>	esc_html__('Before', 'slick-menu'),
			'after'		=>	esc_html__('After', 'slick-menu'),
			'above'		=>	esc_html__('Above', 'slick-menu'),
			'below'		=>	esc_html__('Below', 'slick-menu'),
		),
		'accordion'		=> 'icons',
		'std'			=> 'after',
		'visible'		=> array("{$prefix}back-link-icon-only", '0')
	),
	array(
		'id' 			=> "{$prefix}back-link-icon-size",
		'name'			=> esc_html__( 'Back Link Icon Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> '22px'
	),

	
	
	// SPACING	

	array(
		'id' 			=> "{$prefix}back-link-padding",
		'name'			=> esc_html__( 'Back Link Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),	
	array(
		'id' 			=> "{$prefix}back-link-margin",
		'name'			=> esc_html__( 'Back Link Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '20px'
	)

);

