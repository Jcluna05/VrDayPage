<?php

return array(

 	SM_RWMB_EXTEND_Groups::background(array(
	    'id' 		=> "{$prefix}content-bg",
		'name' 		=> '',
		'desc' 		=> esc_html__( 'This will override the global content background. If you have set a Wrapper Background, you need to also set the Content Background', 'slick-menu' ),
	    'toggle'	=> false,
	    'accordion'	=>	'bg-image',
	    'std' 			=> ''
	)), 

	SM_RWMB_EXTEND_Groups::boxShadow(array(
		'id' 			=> "{$prefix}content-shadow",
		'name' 		=> '',
		'desc'			=> esc_html__( 'Apply a box shadow to the site content when the menu is active', 'slick-menu' ),
		'toggle'		=> false,
		'accordion'	=>	'shadow',
		'std' 			=> ''
	)),
		 
    array(
		'id' 			=> "{$prefix}content-filter",
		'name'			=> esc_html__( 'Content Filter Effect', 'slick-menu' ),
		'desc'			=> esc_html__( 'Apply a filter to the site content when the menu is active', 'slick-menu' ),
		'type'			=> 'select',
		'placeholder'	=> esc_html__( 'Select Filter', 'slick-menu' ),
		'options'		=> slick_menu_data_get_filters(),
		'std'			=> 'sm-filter-brightness',
		'accordion'	=>	'filter',
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	)	 
);