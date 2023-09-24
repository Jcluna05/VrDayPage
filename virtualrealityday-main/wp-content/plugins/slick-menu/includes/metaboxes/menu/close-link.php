<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}close-link-hidden",
		'name'			=> esc_html__( 'Close Link Hidden', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "1"
	),
	array(
		'id' 			=> "{$prefix}close-link-position",
		'name'			=> esc_html__( 'Close Link Position', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'right'
	),	

	
	// COLORS	
	array(
		'id' 			=> "{$prefix}close-link-color",
		'name'			=> esc_html__( 'Close Link Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255,255,255,0.8)'
	),
	array(
		'id' 			=> "{$prefix}close-link-hover-color",
		'name'			=> esc_html__( 'Close Link Hover Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255,255,255,1)'
	),
	array(
		'id' 			=> "{$prefix}close-link-bg-color",
		'name'			=> esc_html__( 'Close Link Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}close-link-hover-bg-color",
		'name'			=> esc_html__( 'Close Link Hover Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> ''
	),	

	
	// ICON	
	array(
		'id' 			=> "{$prefix}close-link-icon",
		'name'			=> esc_html__( 'Close Link Icon', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set an arrow icon', 'slick-menu' ),
		'type'			=> 'icon_picker',
		'accordion'		=> 'icons',
		'std'			=> array(
			'type' 		=> 'genericon',
			'icon' 		=> 'genericon-close-alt'
		)
	),
	array(
		'id' 			=> "{$prefix}close-link-icon-size",
		'name'			=> esc_html__( 'Close Link Icon Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> '25px'
	),	
		
				
	// SPACING
	array(
		'id' 			=> "{$prefix}close-link-padding",
		'name'			=> esc_html__( 'Close Link Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),	
	array(
		'id' 			=> "{$prefix}close-link-margin",
		'name'			=> esc_html__( 'Close Link Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '10px 20px'
	),
	
	
	// BORDERS
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}close-link-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),		


	// ANIMATIONS 
	array(
		'id' 			=> "{$prefix}close-link-animation",
		'name'			=> esc_html__( 'Close Link Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'accordion'		=> 'animations',
		'std'			=> ''
	)	
);

