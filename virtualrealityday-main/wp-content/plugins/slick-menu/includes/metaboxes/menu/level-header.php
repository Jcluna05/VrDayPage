<?php
	
return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}level-header-stick-top",
		'name'			=> esc_html__( 'Level Header Stick To Top', 'slick-menu' ),
		'desc'			=> esc_html__( 'The level header that includes the Logo and the search box, will be sticked to the top', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}level-header-over-content",
		'name'			=> esc_html__( 'Level Header Over Content', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0',
		'visible'		=> array("{$prefix}level-header-stick-top", '1')
	),

	
	// BG
	SM_RWMB_EXTEND_Groups::background(array(
		'id' 			=> "{$prefix}header-bg",
		'name' 		=> '',
        'toggle'		=> false,
		'accordion'		=> 'bg-image',
		'std' => array(
			'apply-sublevels' => 0
		)
    )),	

	// BG PATTERN
	SM_RWMB_EXTEND_Groups::pattern(array(
		'id' 			=> "{$prefix}header-pattern",
		'name' 		=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-pattern',
		'std' => array(
			'opacity' => 0.5
		)
	)),

	// BG OVERLAY
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}header-overlay",
		'name' 		=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-overlay',
		'std' => array(
			'type' => 'color'
		)
	)),
	
	// SPACING
	array(
		'id' 			=> "{$prefix}header-padding",
		'name'			=> esc_html__( 'Header Wrapper Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '0 25px 25px'
	),
	array(
		'id' 			=> "{$prefix}header-margin",
		'name'			=> esc_html__( 'Header Wrapper Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 0', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),				
);				