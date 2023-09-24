<?php
	
return array(
	
	// GENERAL
	
	array(
		'id' 			=> "{$prefix}search-enabled",
		'name'			=> esc_html__( 'Search Enabled', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0"
	),
	array(
		'id' 			=> "{$prefix}search-text-align",
		'name'			=> esc_html__( 'Search Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'left'
	),
	array(
		'id' 			=> "{$prefix}search-show-placeholder",
		'name'			=> esc_html__( 'Show Search Placeholder Text', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0"
	),
	
	// FONT	
	array(
		'id' 			=> "{$prefix}search-font-family",
		'name'			=> esc_html__( 'Search Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'accordion'		=> 'font',
		'std'			=> 'Lato:300',
	),
	array(
		'id' 			=> "{$prefix}search-font-size",
		'name'			=> esc_html__( 'Search Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '20px'
	),

	
	// COLORS	
	array(
		'id' 			=> "{$prefix}search-bg-color",
		'name'			=> esc_html__( 'Search Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255, 255, 255, 0.1)'
	),
	array(
		'id' 			=> "{$prefix}search-font-color",
		'name'			=> esc_html__( 'Search Font Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255, 255, 255, 0.7)'
	),
		
	// ICON
	array(
		'id' 			=> "{$prefix}search-icon-position",
		'name'			=> esc_html__( 'Search Icon Position', 'slick-menu' ),
		'type'			=> 'radio',
		'accordion'		=> 'icons',
		'options'		=> slick_menu_data_get_hpositions(true),
		'std'			=> 'right'
	),	
	array(
		'id' 			=> "{$prefix}search-icon-size",
		'name'			=> esc_html__( 'Search Icon Size', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> '20px'
	),	
	array(
		'id' 			=> "{$prefix}search-icon-color",
		'name'			=> esc_html__( 'Search Icon Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'icons',
		'std'			=> 'rgba(255, 255, 255, 0.7)'
	),	
	
	// SPACING	
	array(
		'id' 			=> "{$prefix}search-width",
		'name'			=> esc_html__( 'Search Box Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Width is 100% by default', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),			
	array(
		'id' 			=> "{$prefix}search-margin",
		'name'			=> esc_html__( 'Search Box Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '15px'
	),	
	array(
		'id' 			=> "{$prefix}search-hpadding",
		'name'			=> esc_html__( 'Search Box Horizontal Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Horizontal Padding in (%, px). Ex: 15px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '15px'
	),
	
	// BORDERS
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}search-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),	

	
	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}search-animation",
		'name'			=> esc_html__( 'Search Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'accordion'		=> 'animations',
		'std'			=> ''
	),		    
);

