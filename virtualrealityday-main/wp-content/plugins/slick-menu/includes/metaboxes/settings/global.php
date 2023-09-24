<?php

return array(
	
	array(
		'id' 		=> "{$prefix}menu-container",
		'name' 		=> esc_html__( 'Menu Container', 'slick-menu' ),
		'desc' 		=> esc_html__( 'Menu Container DOM Selector. Default Value: body', 'slick-menu' ),
		'type' 		=> 'text',
		'std' 		=> 'body'
	),
	array(
		'id' 		=> "{$prefix}mobile-breakpoint",
		'name' 		=> esc_html__( 'Menu Mobile Breakpoint', 'slick-menu' ),
		'desc' 		=> esc_html__( 'Breakpoint Width in px', 'slick-menu' ),
		'type' 		=> 'text',
		'std' 		=> '500px'
	),
	array(
		'id' 		=> "{$prefix}google-fonts-api-key",
		'name' 		=> esc_html__( 'Google Fonts API Key', 'slick-menu' ),
		'desc' 		=> esc_html__( 'Used to load google fonts', 'slick-menu' ),
		'type' 		=> 'text',
		'std' 		=> ''
	),
    array(
		'id' 			=> "{$prefix}internal-dynamic-styles",
		'name'			=> esc_html__( 'Dynamic Styles Internal CSS', 'slick-menu' ),
		'desc'			=> esc_html__( 'This will output all dynamic styles within each page instead of using an external link.', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> "0"
	),		
    array(
		'id' 			=> "{$prefix}onloaded-fadein",
		'name'			=> esc_html__( 'Fade In Website once Slick Menus are fully loaded', 'slick-menu' ),
		'desc'			=> esc_html__( 'Better visual experience making sure everything is in place before viewing the site. Turn off this if you are getting a blank screen due to a theme conflict.', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> "0"
	),		
);