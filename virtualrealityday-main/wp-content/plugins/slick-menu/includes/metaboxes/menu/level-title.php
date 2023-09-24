<?php

return array(
	
	// GENERAL
	
	array(
		'id' 			=> "{$prefix}title-hidden",
		'name'			=> esc_html__( 'Title Hidden', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}title-override",
		'name'			=> esc_html__( 'Title Override', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'general',
		'std'			=> ''
	),	
	array(
		'id' 			=> "{$prefix}title-fullwidth",
		'name'			=> esc_html__( 'Title Fullwidth', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '1'
	),		
	array(
		'id' 			=> "{$prefix}title-stick-top",
		'name'			=> esc_html__( 'Title Stick To Top', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}title-position",
		'name'			=> esc_html__( 'Title Position', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'center'
	),
			
	// FONT

	array(
		'id' 			=> "{$prefix}title-font-family",
		'name'			=> esc_html__( 'Title Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'accordion'		=> 'font',
		'std'			=> 'Lato:400'
	),
	array(
		'id' 			=> "{$prefix}title-font-size",
		'name'			=> esc_html__( 'Title Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '24px'
	),
	array(
		'id' 			=> "{$prefix}title-text-transform",
		'name'			=> esc_html__( 'Title Text Transform', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_text_transform(),
		'accordion'		=> 'font',
		'std'			=> 'none'
	),	
	array(
		'id' 			=> "{$prefix}title-text-align",
		'name'			=> esc_html__( 'Title Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_align(),
		'accordion'		=> 'font',
		'std'			=> 'center'
	),
	
	// COLORS
	array(
		'id' 			=> "{$prefix}title-font-color",
		'name'			=> esc_html__( 'Title Font Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> '#ffffff'
	),	
	array(
		'id' 			=> "{$prefix}title-bg-color",
		'name'			=> esc_html__( 'Title Background Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(0,0,0,0.1)'
	),
		
		
	// ICONS	
	
	array(
		'id' 			=> "{$prefix}title-show-icon",
		'name'			=> esc_html__( 'Title Icon', 'slick-menu' ),
		'desc'			=> esc_html__( 'The title will inherit the item icon', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'icons',
		'std'			=> '0'
	),		
	array(
		'id' 			=> "{$prefix}title-main-icon",
		'name'			=> esc_html__( 'Title Icon', 'slick-menu' ),
		'desc'			=> esc_html__( ' Applies to main level only.', 'slick-menu' ),
		'type'			=> 'icon_picker',
		'accordion'		=> 'icons',
		'std'			=> '',
		'visible'		=> array("{$prefix}title-show-icon", '1')
	),
	array(
		'id' 			=> "{$prefix}title-icon-size",
		'name'			=> esc_html__( 'Title Icon Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> '24px',
		'visible'		=> array("{$prefix}title-show-icon", '1')
	),	
	array(
		'id' 			=> "{$prefix}title-icon-color",
		'name'			=> esc_html__( 'Title Icon Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'icons',
		'std'			=> '#ffffff',
		'visible'		=> array("{$prefix}title-show-icon", '1')
	),	

	// SPACING
	array(
		'id' 			=> "{$prefix}title-padding",
		'name'			=> esc_html__( 'Title Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '20px'
	),
	array(
		'id' 			=> "{$prefix}title-margin",
		'name'			=> esc_html__( 'Title Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '0 0 15px 0'
	),
	
	
	// BORDER
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}title-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),	


	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}title-animation",
		'name'			=> esc_html__( 'Title Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'accordion'		=> 'animations',
		'std'			=> ''
	),	    
);

