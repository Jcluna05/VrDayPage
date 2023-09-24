<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}level-width",
		'name'			=> esc_html__( 'Level Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set a width in (%, px, vw). Use the vw unit to enter a percentage related to the viewport width. Ex 50vw would make the width equal to half the window width.', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'general',
		'std'			=> '350px'
	),
	array(
		'id' 			=> "{$prefix}level-mobile-centered",
		'name' 			=> esc_html__( 'Level Mobile Centered', 'slick-menu' ),
		'desc' 			=> esc_html__( 'Force level content to be centered on mobile breakpoint', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0"
	),	
	array(
		'id' 			=> "{$prefix}level-valign",
		'name'			=> esc_html__( 'Inner Level Vertical Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'top'	=>	esc_html__('Top', 'slick-menu'),
			'middle'	=>	esc_html__('Middle', 'slick-menu'),
			'bottom'	=>	esc_html__('Bottom', 'slick-menu')
		),
		'accordion'		=> 'general',
		'std'			=> 'middle'
	),				
	array(
		'id' 			=> "{$prefix}level-disabled-scroll",
		'name' 			=> esc_html__( 'Level Disable Scroll', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0"
	),	

	array(
		'id' 			=> "{$prefix}level-show-scrollbar",
		'name' 			=> esc_html__( 'Level Show Scroll Bar', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0",
		'visible'		=> array("{$prefix}level-disabled-scroll", '0')
	),	
	
	array(
		'id' 			=> "{$prefix}level-scroll-to-current",
		'name' 			=> esc_html__( 'Level Auto Scroll To Current Item', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> "0",
		'visible'		=> array("{$prefix}level-disabled-scroll", '0')
	),	
				
	// SPACING	
	array(
		'id' 			=> "{$prefix}level-padding",
		'name'			=> esc_html__( 'Level Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '40px 0'
	)
);