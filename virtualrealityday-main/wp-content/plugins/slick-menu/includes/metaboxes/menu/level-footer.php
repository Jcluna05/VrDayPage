<?php
	
return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}footer-min-height",
		'name'			=> esc_html__( 'Footer Minimum Height', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px, vh). Use the vh unit to enter a percentage related to the viewport height. Ex 50vh would make the height equal to half the window height', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'general',
		'std'			=> ''
	),		
	array(
		'id' 			=> "{$prefix}level-footer-stick-bottom",
		'name'			=> esc_html__( 'Level Footer Stick To Bottom', 'slick-menu' ),
		'desc'			=> esc_html__( 'The level footer that includes the social networks and footer text, will be sticked to the bottom', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '1'
	),	
	array(
		'id' 			=> "{$prefix}level-footer-over-content",
		'name'			=> esc_html__( 'Level Footer Over Content', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0',
		'visible'		=> array("{$prefix}level-footer-stick-bottom", '1')
	),	
	array(
		'id' 			=> "{$prefix}footer-text",
		'name'			=> esc_html__( 'Footer Text', 'slick-menu' ),
		'type'			=> 'textarea',
		'accordion'		=> 'general',
		'std'			=> '&copy; '.date('Y').' Your Slick Website.'
	),	
		
	// FONT
	array(
		'id' 			=> "{$prefix}footer-text-font-family",
		'name'			=> esc_html__( 'Footer Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'accordion'		=> 'font',
		'std'			=> 'Lato:400'
	),
	array(
		'id' 			=> "{$prefix}footer-text-font-size",
		'name'			=> esc_html__( 'Footer Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '14px'
	),	
	array(
		'id' 			=> "{$prefix}footer-text-align",
		'name'			=> esc_html__( 'Footer Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_align(),
		'accordion'		=> 'font',
		'std'			=> 'center'
	),	

	// BG
	SM_RWMB_EXTEND_Groups::background(array(
		'id' 			=> "{$prefix}footer-bg",
		'name' 		=> '',
        'toggle'		=> false,
		'accordion'		=> 'bg-image',
		'std' => array(
			'apply-sublevels' => 0
		)
    )),	

	// BG PATTERN
	SM_RWMB_EXTEND_Groups::pattern(array(
		'id' 			=> "{$prefix}footer-pattern",
		'name' 		=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-pattern',
		'std' => array(
			'opacity' => 0.5
		)
	)),

	// BG OVERLAY
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}footer-overlay",
		'name' 		=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-overlay',
		'std' => array(
			'type' => 'color'
		)
	)),
			
	// COLORS
	array(
		'id' 			=> "{$prefix}footer-text-color",
		'name'			=> esc_html__( 'Footer Text Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255, 255, 255, 0.9)'
	),	
	
	// SPACING
	array(
		'id' 			=> "{$prefix}footer-padding",
		'name'			=> esc_html__( 'Footer Wrapper Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '0 25px 25px'
	),
	array(
		'id' 			=> "{$prefix}footer-margin",
		'name'			=> esc_html__( 'Footer Wrapper Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 0', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),				
	array(
		'id' 			=> "{$prefix}footer-text-padding",
		'name'			=> esc_html__( 'Footer Text Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),
	
	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}footer-animation",
		'name'			=> esc_html__( 'Footer Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'std'			=> '',
		'accordion'		=> 'animations'
	),				
);				