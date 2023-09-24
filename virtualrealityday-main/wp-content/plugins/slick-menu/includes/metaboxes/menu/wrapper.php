<?php

return array(
	
	array(
        'id'	=> 'wrapper-msg',
        'type'	=> 'custom-html',
        'std'	=> '<span class="dashicons dashicons-info"></span>'.esc_html__( 'This will override the global wrapper settings. The wrapper is behind the site content. The only way to see the wrapper is by selecting a 3D menu effect that has a perspective viewing which will push the site content away letting you see the wrapper behind it.', 'slick-menu' )
    ),
                
	SM_RWMB_EXTEND_Groups::background(array(
		'id' 			=> "{$prefix}wrapper-bg",
		'name' 			=> '',
        'toggle'		=> false,
        'accordion'		=> 'bg-image',
	    'std' 			=> ''
    )),
    
	SM_RWMB_EXTEND_Groups::pattern(array(
		'id' 			=> "{$prefix}wrapper-pattern",
		'name' 			=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-pattern',
		'std' 			=> array(
			'opacity' => 0.5
		)
	)),  
	   
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}wrapper-overlay",
		'name' 			=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-overlay',
		'std' 			=> array(
			'type' => 'color'
		)
	)), 
	  	
	SM_RWMB_EXTEND_Groups::video(array(
		'id' 			=> "{$prefix}wrapper-video",
		'name' 			=> '',
		'toggle'		=> false,
		'exclude'		=> array('behind-overlay'),
		'accordion'		=> 'bg-video',
		'std'			=> array()
	)),
	
    array(
		'id' 			=> "{$prefix}wrapper-filter",
		'name'			=> esc_html__( 'Wrapper Filter Effect', 'slick-menu' ),
		'desc'			=> esc_html__( 'Apply a filter to the site wrapper', 'slick-menu' ),
		'type'			=> 'select',
		'placeholder'	=> esc_html__( 'Select Filter', 'slick-menu' ),
		'options'		=> slick_menu_data_get_filters(),
		'std'			=> 'sm-filter-brightness',
		'accordion'	=>	'filter',
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	)	
);