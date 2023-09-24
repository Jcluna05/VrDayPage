<?php

return array(

	SM_RWMB_EXTEND_Groups::background(array(
		'id' 			=> "{$prefix}level-bg",
		'name' 		=> '',
        'toggle'		=> false,
	    'merge' 		=> array(
	    	array(
				'id' 			=> "apply-sublevels",
				'name'			=> esc_html__( 'Apply background to sub levels', 'slick-menu' ),
				'type'			=> 'checkbox',
				'std'			=> 0
			),
		),
		'accordion'		=> 'bg-image',
		'std' => array(
			'color' => '#244c75',
			'apply-sublevels' => 0
		)
    )),	

	SM_RWMB_EXTEND_Groups::pattern(array(
		'id' 			=> "{$prefix}level-pattern",
		'name' 		=> '',
		'toggle'		=> false,
		'merge' 		=> array(
	    	array(
				'id' 			=> "apply-sublevels",
				'name'			=> esc_html__( 'Apply pattern to sub levels', 'slick-menu' ),
				'type'			=> 'checkbox',
				'std'			=> 0
			),
		),
		'accordion'		=> 'bg-pattern',
		'std' => array(
			'opacity' => 0.5
		)
	)),

	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}level-overlay",
		'name' 		=> '',
		'toggle'		=> false,
		'merge' 		=> array(
	    	array(
				'id' 			=> "apply-sublevels",
				'name'			=> esc_html__( 'Apply overlay to sub levels', 'slick-menu' ),
				'type'			=> 'checkbox',
				'std'			=> 0
			),
		),
		'accordion'		=> 'bg-overlay',
		'std' => array(
			'type' => 'color'
		)
	)),

	SM_RWMB_EXTEND_Groups::video(array(
		'id' 			=> "{$prefix}level-video",
		'name' 		=> '',
		'toggle'		=> false,
		'accordion'		=> 'bg-video',
		'std'			=> array()
	))
);