<?php

return array(
		
	SM_RWMB_EXTEND_Groups::background(array(
	    'id' 		=> "{$prefix}wrapper-bg",
		'name' 		=> esc_html__( 'Wrapper Background', 'slick-menu' ),
		'desc' 		=> esc_html__( 'The background will only be visible underneath the content when selecting a 3D menu effect that has a perspective viewing', 'slick-menu' ),
	    'toggle'	=> true,
	    'std'		=> ''
	), false),
	
	SM_RWMB_EXTEND_Groups::pattern(array(
		'id' 			=> "{$prefix}wrapper-pattern",
		'name'			=> esc_html__( 'Wrapper Background Pattern', 'slick-menu' ),
		'toggle'		=> true,
		'std' => array(
			'opacity' => 0.5
		)
	), false),  	
	
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 		=> "{$prefix}wrapper-overlay",
		'name'		=> esc_html__( 'Wrapper Background Overlay', 'slick-menu' ),
		'toggle'	=> true,
		'std' => array(
			'type' => 'color'
		)
	), false),
	
	SM_RWMB_EXTEND_Groups::background(array(
	    'id' 		=> "{$prefix}content-bg",
		'name' 		=> esc_html__( 'Content Background', 'slick-menu' ),
		'desc' 		=> esc_html__( 'If you have set a Wrapper Background, you need to also set the Content Background', 'slick-menu' ),
	    'toggle'	=> true,
	    'std'		=> ''
	), false),	
);