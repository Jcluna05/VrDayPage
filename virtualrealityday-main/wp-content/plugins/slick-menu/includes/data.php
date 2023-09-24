<?php

function slick_menu_data_get_posts($args = array())
{

    $default = array(
        'posts_per_page'   => 15,
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'any',
        'post_status'      => 'publish',
        'suppress_filters' => true
    );

    $args = array_merge($default, $args);

    $results = get_posts( $args );

    $response = array();

    foreach ($results as $result) {
        $response[ intval($result->ID) ] = $result->post_title;
    }

    return $response;
}

function sm_mb_select_post_callback()
{
    $query = sanitize_text_field($_GET['s']);

    $results = slick_menu_data_get_posts(array(
        's' => $query
    ));

    $response = array(
        'results' => array()
    );

    foreach ($results as $key => $value) {
        $response['results'][] = array('id' => $key, 'text' => $value);
    }

    wp_send_json($response);
}
add_action('wp_ajax_sm_mb_select_post_callback', 'sm_mb_select_post_callback');


function sm_mb_select_initial_options_callback( $meta )
{

    $results = slick_menu_data_get_posts(array(
        'include' => $meta
    ));

    $options = array();

    foreach ($results as $key => $value) {
        $options[] = (object)array('value' => $key, 'label' => $value);
    }

    return $options;
}

function slick_menu_data_get_effects($type = null, $valuesOnly = false) {
	
	$selection = array();
	
	$effects = array(
		"sm-effect-0" => esc_html__("Fade in", 'slick-menu'),
		"sm-effect-1" => esc_html__("Slide in", 'slick-menu'),
		"sm-effect-2" => esc_html__("Reveal", 'slick-menu'),
		"sm-effect-3" => esc_html__("Push", 'slick-menu'),
		"sm-effect-4" => esc_html__("Slide along", 'slick-menu'),
		"sm-effect-5" => esc_html__("Reverse slide out", 'slick-menu'),
		"sm-effect-6" => esc_html__("Rotate pusher", 'slick-menu'),
		"sm-effect-7" => esc_html__("3D rotate in", 'slick-menu'),
		"sm-effect-8" => esc_html__("3D rotate out", 'slick-menu'),
		"sm-effect-9" => esc_html__("Scale down pusher", 'slick-menu'),
		"sm-effect-10" => esc_html__("Scale Up", 'slick-menu'),
		"sm-effect-11" => esc_html__("Scale &amp; rotate pusher", 'slick-menu'),
		"sm-effect-12" => esc_html__("Open door", 'slick-menu'),
		"sm-effect-13" => esc_html__("Fall down", 'slick-menu'),
		"sm-effect-14" => esc_html__("Delayed 3D Rotate", 'slick-menu'),
		"sm-effect-15" => esc_html__("Scale in", 'slick-menu'),
		"sm-effect-16" => esc_html__("Scale out", 'slick-menu'),
	);	

	
	if($type === 'push') {
	
		$selection = array(
			'sm-effect-3',
			'sm-effect-6',
			'sm-effect-7',
			'sm-effect-8',
			'sm-effect-14'
		);

	}else if($type === 'mobile') {
		
		$selection = array(
			'sm-effect-0',
			'sm-effect-1',
			'sm-effect-2',
			'sm-effect-4',
			'sm-effect-15',
			'sm-effect-16',
		);
	}
	
	if(!empty($selection)) {	
			
		$selection_effects = array();	
		foreach($effects as $key => $effect) {

			if(in_array($key, $selection)) {
				$selection_effects[$key] = $effect;
			}
		}
		$effects = $selection_effects;
	}
	
	if($valuesOnly) {
		$effects = array_keys($effects);
	}
	
	return $effects;	
}

function slick_menu_data_get_easings($valuesOnly = false) {
	
	$easings = array(
		
		'linear' => 'linear',
		'ease' => 'ease',
		'ease-in' => 'ease-in',
		'ease-out' => 'ease-out',
		'ease-in-out' => 'ease-in-out',
		
		'cubic-bezier(0.550, 0.085, 0.680, 0.530)' => 'easeInQuad',
		'cubic-bezier(0.550, 0.055, 0.675, 0.190)' => 'easeInCubic',
		'cubic-bezier(0.895, 0.030, 0.685, 0.220)' => 'easeInQuart',
		'cubic-bezier(0.755, 0.050, 0.855, 0.060)' => 'easeInQuint',
		'cubic-bezier(0.470, 0.000, 0.745, 0.715)' => 'easeInSine',
		'cubic-bezier(0.950, 0.050, 0.795, 0.035)' => 'easeInExpo',
		'cubic-bezier(0.600, 0.040, 0.980, 0.335)' => 'easeInCirc',
		'cubic-bezier(0.600, -0.280, 0.735, 0.045)' => 'easeInBack',
		
		'cubic-bezier(0.250, 0.460, 0.450, 0.940)' => 'easeOutQuad',
		'cubic-bezier(0.215, 0.610, 0.355, 1.000)' => 'easeOutCubic',
		'cubic-bezier(0.165, 0.840, 0.440, 1.000)' => 'easeOutQuart',
		'cubic-bezier(0.230, 1.000, 0.320, 1.000)' => 'easeOutQuint',
		'cubic-bezier(0.390, 0.575, 0.565, 1.000)' => 'easeOutSine',
		'cubic-bezier(0.190, 1.000, 0.220, 1.000)' => 'easeOutExpo',
		'cubic-bezier(0.075, 0.820, 0.165, 1.000)' => 'easeOutCirc',
		'cubic-bezier(0.175, 0.885, 0.320, 1.275)' => 'easeOutBack',
		
		'cubic-bezier(0.455, 0.030, 0.515, 0.955)' => 'easeInOutQuad',
		'cubic-bezier(0.645, 0.045, 0.355, 1.000)' => 'easeInOutCubic',
		'cubic-bezier(0.770, 0.000, 0.175, 1.000)' => 'easeInOutQuart',
		'cubic-bezier(0.860, 0.000, 0.070, 1.000)' => 'easeInOutQuint',
		'cubic-bezier(0.445, 0.050, 0.550, 0.950)' => 'easeInOutSine',
		'cubic-bezier(1.000, 0.000, 0.000, 1.000)' => 'easeInOutExpo',
		'cubic-bezier(0.785, 0.135, 0.150, 0.860)' => 'easeInOutCirc',
		'cubic-bezier(0.680, -0.550, 0.265, 1.550)' => 'easeInOutBack'
	);	
	
	if($valuesOnly) {
		$easings = array_keys($easings);
	}
		
	return $easings;

}

function slick_menu_data_get_filters($valuesOnly = false, $disableFilter = false) {
	
	$filters = array(

		'sm-filter-brightness' => 'Brightness',
		'sm-filter-grayscale' => 'Grayscale',
		'sm-filter-sepia' => 'Sepia',
		'sm-filter-saturate' => 'Saturate',
		'sm-filter-invert' => 'Invert',
		'sm-filter-opacity' => 'Opacity',
		'sm-filter-contrast' => 'Contrast',
		'sm-filter-blur' => 'Blur',
		'sm-filter-tint' => 'Tint',
		'sm-filter-inkwell' => 'Inkwell',
		'sm-filter-hue-rotate-90' => 'Hue Rotate 90deg',
		'sm-filter-hue-rotate-180' => 'Hue Rotate 180deg',
		'sm-filter-hue-rotate-270' => 'Hue Rotate 270deg',
		'sm-filter-hue-rotate-360' => 'Hue Rotate 360deg',
		'sm-filter-half-transparent' => 'Half Transparent',
		'sm-filter-transparent' => 'Fully Transparent'

	);	
	
	if($disableFilter) {
		
		$filters = array_merge(
			array('sm-filter-none' => 'None'), 
			$filters
		);
	}
	
	if($valuesOnly) {
		$filters = array_keys($filters);
	}
		
	return $filters;

}

function slick_menu_data_get_align($leftRightOnly = false, $valuesOnly = false) {
	
	$align = array(
		'left'		=>	esc_html__('Left', 'slick-menu'),
		'center'	=>	esc_html__('Center', 'slick-menu'),
		'right'		=>	esc_html__('Right', 'slick-menu'),
		'justify'	=>	esc_html__('Justify', 'slick-menu')
	);
	
	if($leftRightOnly) {
		unset($align["center"]);
		unset($align["justify"]);
	}

	if($valuesOnly) {
		$align = array_keys($align);
	}
		
	return $align;
}

function slick_menu_data_get_hpositions($leftRightOnly = false, $valuesOnly = false) {
	
	$positions = array(
		'left'		=>	esc_html__('Left', 'slick-menu'),
		'center'	=>	esc_html__('Center', 'slick-menu'),
		'right'		=>	esc_html__('Right', 'slick-menu')
	);
	
	if($leftRightOnly) {
		unset($positions["center"]);
	}

	if($valuesOnly) {
		$positions = array_keys($positions);
	}
		
	return $positions;
}

function slick_menu_data_get_vpositions($topBottomOnly = false, $valuesOnly = false) {
	
	$positions = array(
		'top'		=>	esc_html__('Top', 'slick-menu'),
		'middle'	=>	esc_html__('Middle', 'slick-menu'),
		'bottom'	=>	esc_html__('Bottom', 'slick-menu')
	);
	
	if($topBottomOnly) {
		unset($positions["middle"]);
	}

	if($valuesOnly) {
		$positions = array_keys($positions);
	}
		
	return $positions;
}

function slick_menu_data_get_text_transform($valuesOnly = false) {
	

	$transforms = array(
		'none'			=>	esc_html__('None', 'slick-menu'),
		'capitalize'	=>	esc_html__('Capitalize', 'slick-menu'),
		'uppercase'		=>	esc_html__('Uppercase', 'slick-menu'),
		'lowercase'		=>	esc_html__('Lowercase', 'slick-menu'),
		'initial'		=>	esc_html__('Initial', 'slick-menu'),
		'inherit'		=>	esc_html__('Inherit', 'slick-menu')
	);

	if($valuesOnly) {
		$transforms = array_keys($transforms);
	}
		
	return $transforms;	
}

function slick_menu_data_get_true_false($labels = array()) {

	$true_false = array(
		"0"	=>	esc_html__('No', 'slick-menu'),
		"1"	=>	esc_html__('Yes', 'slick-menu')
	);	
	
	if(!empty($labels) && count($labels) === 2) {
		$true_false = array(
			"0"	=>	$labels[0],
			"1"	=>	$labels[1]
		);			
	}
	
	return $true_false;
}


function slick_menu_data_get_hover_animations($valuesOnly = false) {

	$animations = array(
		'sm-hover-normal'	=> esc_html__( 'Normal', 'slick-menu' ),
		'sm-hover-right'	=> esc_html__( 'Slide Right', 'slick-menu' ),
		'sm-hover-left'		=> esc_html__( 'Slide Left', 'slick-menu' ),
		'sm-hover-top'		=> esc_html__( 'Slide Up', 'slick-menu' ),
		'sm-hover-bottom'	=> esc_html__( 'Slide Down', 'slick-menu' ),
		'sm-hover-zoom'	=> esc_html__( 'Zoom', 'slick-menu' )
	);

	if($valuesOnly) {
		$animations = array_keys($animations);
	}	

	return $animations;
}

function slick_menu_data_get_columns($exclude = null, $groupsOnly = false) {
	
	$columns = array(	
	    array(
		  	"text" => esc_html__("Grid of 1 column", "slick-menu"),
		    "type" => "1",
		  	"children" => array(
	            array(
	                "id" => "1-1",
	                "text" => "1 of 1"
	            )
	        )   
	    ),
	    array(
		    "text" => esc_html__("Grid of 2 columns", "slick-menu"),
		    "type" => "2",
		    "children" => array(
				        
				array(
				    "id" => "1-2",
				    "text" => "1 of 2"
				),
				array(
				    "id" => "2-2",
				    "text" => "2 of 2"
				),
			)
		),
		array(
		    "text" => esc_html__("Grid of 3 columns", "slick-menu"),
		    "type" => "3",
		    "children" => array(
			    		
				array(
				    "id" => "1-3",
				    "text" => "1 of 3"
				),
				array(
				    "id" => "2-3",
				    "text" => "2 of 3"
				),
				array(
				    "id" => "3-3",
				    "text" => "3 of 3"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 4 columns", "slick-menu"),
		    "type" => "4",
		    "children" => array(
			    		
				array(
				    "id" => "1-4",
				    "text" => "1 of 4"
				),
				array(
				    "id" => "2-4",
				    "text" => "2 of 4"
				),
				array(
				    "id" => "3-4",
				    "text" => "3 of 4"
				),
				array(
				    "id" => "4-4",
				    "text" => "4 of 4"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 5 columns", "slick-menu"),
		    "type" => "5",
		    "children" => array(	
			    	
				array(
				    "id" => "1-5",
				    "text" => "1 of 5"
				),
				array(
				    "id" => "2-5",
				    "text" => "2 of 5"
				),
				array(
				    "id" => "3-5",
				    "text" => "3 of 5"
				),
				array(
				    "id" => "4-5",
				    "text" => "4 of 5"
				),
				array(
				    "id" => "5-5",
				    "text" => "5 of 5"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 6 columns", "slick-menu"),
		    "type" => "6",
		    "children" => array(	
				
				array(
				    "id" => "1-6",
				    "text" => "1 of 6"
				),
				array(
				    "id" => "2-6",
				    "text" => "2 of 6"
				),
				array(
				    "id" => "3-6",
				    "text" => "3 of 6"
				),
				array(
				    "id" => "4-6",
				    "text" => "4 of 6"
				),
				array(
				    "id" => "5-6",
				    "text" => "5 of 6"
				),
				array(
				    "id" => "6-6",
				    "text" => "6 of 6"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 7 columns", "slick-menu"),
		    "type" => "7",
		    "children" => array(
				
				array(
				    "id" => "1-7",
				    "text" => "1 of 7"
				),
				array(
				    "id" => "2-7",
				    "text" => "2 of 7"
				),
				array(
				    "id" => "3-7",
				    "text" => "3 of 7"
				),
				array(
				    "id" => "4-7",
				    "text" => "4 of 7"
				),
				array(
				    "id" => "5-7",
				    "text" => "5 of 7"
				),
				array(
				    "id" => "6-7",
				    "text" => "6 of 7"
				),
				array(
				    "id" => "7-7",
				    "text" => "7 of 7"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 8 columns", "slick-menu"),
		    "type" => "8",
		    "children" => array(
				
				array(
				    "id" => "1-8",
				    "text" => "1 of 8"
				),
				array(
				    "id" => "2-8",
				    "text" => "2 of 8"
				),
				array(
				    "id" => "3-8",
				    "text" => "3 of 8"
				),
				array(
				    "id" => "4-8",
				    "text" => "4 of 8"
				),
				array(
				    "id" => "5-8",
				    "text" => "5 of 8"
				),
				array(
				    "id" => "6-8",
				    "text" => "6 of 8"
				),
				array(
				    "id" => "7-8",
				    "text" => "7 of 8"
				),
				array(
				    "id" => "8-8",
				    "text" => "8 of 8"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 9 columns", "slick-menu"),
		    "type" => "9",
		    "children" => array(
				
				array(
				    "id" => "1-9",
				    "text" => "1 of 9"
				),
				array(
				    "id" => "2-9",
				    "text" => "2 of 9"
				),
				array(
				    "id" => "3-9",
				    "text" => "3 of 9"
				),
				array(
				    "id" => "4-9",
				    "text" => "4 of 9"
				),
				array(
				    "id" => "5-9",
				    "text" => "5 of 9"
				),
				array(
				    "id" => "6-9",
				    "text" => "6 of 9"
				),
				array(
				    "id" => "7-9",
				    "text" => "7 of 9"
				),
				array(
				    "id" => "8-9",
				    "text" => "8 of 9"
				),
				array(
				    "id" => "9-9",
				    "text" => "9 of 9"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 10 columns", "slick-menu"),
		    "type" => "10",
		    "children" => array(
				
				array(
				    "id" => "1-10",
				    "text" => "1 of 10"
				),
				array(
				    "id" => "2-10",
				    "text" => "2 of 10"
				),
				array(
				    "id" => "3-10",
				    "text" => "3 of 10"
				),
				array(
				    "id" => "4-10",
				    "text" => "4 of 10"
				),
				array(
				    "id" => "5-10",
				    "text" => "5 of 10"
				),
				array(
				    "id" => "6-10",
				    "text" => "6 of 10"
				),
				array(
				    "id" => "7-10",
				    "text" => "7 of 10"
				),
				array(
				    "id" => "8-10",
				    "text" => "8 of 10"
				),
				array(
				    "id" => "9-10",
				    "text" => "9 of 10"
				),
				array(
				    "id" => "10-10",
				    "text" => "10 of 10"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 11 columns", "slick-menu"),
		    "type" => "11",
		    "children" => array(
				
				array(
				    "id" => "1-11",
				    "text" => "1 of 11"
				),
				array(
				    "id" => "2-11",
				    "text" => "2 of 11"
				),
				array(
				    "id" => "3-11",
				    "text" => "3 of 11"
				),
				array(
				    "id" => "4-11",
				    "text" => "4 of 11"
				),
				array(
				    "id" => "5-11",
				    "text" => "5 of 11"
				),
				array(
				    "id" => "6-11",
				    "text" => "6 of 11"
				),
				array(
				    "id" => "7-11",
				    "text" => "7 of 11"
				),
				array(
				    "id" => "8-11",
				    "text" => "8 of 11"
				),
				array(
				    "id" => "9-11",
				    "text" => "9 of 11"
				),
				array(
				    "id" => "10-11",
				    "text" => "10 of 11"
				),
				array(
				    "id" => "11-11",
				    "text" => "11 of 11"
				)
			)
		),
		array(
		    "text" => esc_html__("Grid of 12 columns", "slick-menu"),
		    "type" => "12",
		    "children" => array(
				
				array(
				    "id" => "1-12",
				    "text" => "1 of 12"
				),
				array(
				    "id" => "2-12",
				    "text" => "2 of 12"
				),
				array(
				    "id" => "3-12",
				    "text" => "3 of 12"
				),
				array(
				    "id" => "4-12",
				    "text" => "4 of 12"
				),
				array(
				    "id" => "5-12",
				    "text" => "5 of 12"
				),
				array(
				    "id" => "6-12",
				    "text" => "6 of 12"
				),
				array(
				    "id" => "7-12",
				    "text" => "7 of 12"
				),
				array(
				    "id" => "8-12",
				    "text" => "8 of 12"
				),
				array(
				    "id" => "9-12",
				    "text" => "9 of 12"
				),
				array(
				    "id" => "10-12",
				    "text" => "10 of 12"
				),
				array(
				    "id" => "11-12",
				    "text" => "11 of 12"
				),
				array(
				    "id" => "12-12",
				    "text" => "12 of 12"
				)
			)
		)	
	);		
	
	if(!empty($exclude)) {
		
		foreach($columns as $key => $value) {
			
			if(!empty($value["type"]) && in_array($value["type"], $exclude)) {
		    	unset($columns[$key]);
		    }	
		    
		};
	}

	if($groupsOnly) {
		
		$groups = array();
		foreach($columns as $key => $value) {
			
			$column = !empty($value["type"]) ? $value["type"] : "";
			if(!empty($column)) {
				$groups['1-'.$column] = sprintf(esc_html__('Display items in %s columns', 'slick-menu'), $column);
			}else{
				$groups[""] = sprintf(esc_html__('Inherit Parent or Main Settings', 'slick-menu'), $column);
			}
		}
		
		$columns = $groups;
	}
	
	return $columns;	
}

function slick_menu_data_get_animations($exclude = array("out")) {
	
	$animations = array(
	    array(
		  	"text" => "",
		  	"children" => array(
	            array(
	                "id" => "",
	                "text" => ""
	            )
	        )   
	    ),
	    array(
	        "text" => "Attention Seekers",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "bounce",
	                "text" => "bounce"
	            ),
	            array(
	                "id" => "flash",
	                "text" => "flash"
	            ),
	            array(
	                "id" => "pulse",
	                "text" => "pulse"
	            ),
	            array(
	                "id" => "rubberBand",
	                "text" => "rubberBand"
	            ),
	            array(
	                "id" => "shake",
	                "text" => "shake"
	            ),
	            array(
	                "id" => "swing",
	                "text" => "swing"
	            ),
	            array(
	                "id" => "tada",
	                "text" => "tada"
	            ),
	            array(
	                "id" => "wobble",
	                "text" => "wobble"
	            ),
	            array(
	                "id" => "jello",
	                "text" => "jello"
	            )
	        )
	    ),
	    array(
	        "text" => "Bouncing Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "bounceIn",
	                "text" => "bounceIn"
	            ),
	            array(
	                "id" => "bounceInDown",
	                "text" => "bounceInDown"
	            ),
	            array(
	                "id" => "bounceInLeft",
	                "text" => "bounceInLeft"
	            ),
	            array(
	                "id" => "bounceInRight",
	                "text" => "bounceInRight"
	            ),
	            array(
	                "id" => "bounceInUp",
	                "text" => "bounceInUp"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Bouncing Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "bounceOut",
	                "text" => "bounceOut"
	            ),
	            array(
	                "id" => "bounceOutDown",
	                "text" => "bounceOutDown"
	            ),
	            array(
	                "id" => "bounceOutLeft",
	                "text" => "bounceOutLeft"
	            ),
	            array(
	                "id" => "bounceOutRight",
	                "text" => "bounceOutRight"
	            ),
	            array(
	                "id" => "bounceOutUp",
	                "text" => "bounceOutUp"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Fading Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "fadeIn",
	                "text" => "fadeIn"
	            ),
	            array(
	                "id" => "fadeInDown",
	                "text" => "fadeInDown"
	            ),
	            array(
	                "id" => "fadeInDownBig",
	                "text" => "fadeInDownBig"
	            ),
	            array(
	                "id" => "fadeInLeft",
	                "text" => "fadeInLeft"
	            ),
	            array(
	                "id" => "fadeInLeftBig",
	                "text" => "fadeInLeftBig"
	            ),
	            array(
	                "id" => "fadeInRight",
	                "text" => "fadeInRight"
	            ),
	            array(
	                "id" => "fadeInRightBig",
	                "text" => "fadeInRightBig"
	            ),
	            array(
	                "id" => "fadeInUp",
	                "text" => "fadeInUp"
	            ),
	            array(
	                "id" => "fadeInUpBig",
	                "text" => "fadeInUpBig"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Fading Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "fadeOut",
	                "text" => "fadeOut"
	            ),
	            array(
	                "id" => "fadeOutDown",
	                "text" => "fadeOutDown"
	            ),
	            array(
	                "id" => "fadeOutDownBig",
	                "text" => "fadeOutDownBig"
	            ),
	            array(
	                "id" => "fadeOutLeft",
	                "text" => "fadeOutLeft"
	            ),
	            array(
	                "id" => "fadeOutLeftBig",
	                "text" => "fadeOutLeftBig"
	            ),
	            array(
	                "id" => "fadeOutRight",
	                "text" => "fadeOutRight"
	            ),
	            array(
	                "id" => "fadeOutRightBig",
	                "text" => "fadeOutRightBig"
	            ),
	            array(
	                "id" => "fadeOutUp",
	                "text" => "fadeOutUp"
	            ),
	            array(
	                "id" => "fadeOutUpBig",
	                "text" => "fadeOutUpBig"
	            )
	        )
	    ),
	    array(
	        "text" => "Flippers Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "flip",
	                "text" => "flip"
	            ),
	            array(
	                "id" => "flipInX",
	                "text" => "flipInX"
	            ),
	            array(
	                "id" => "flipInY",
	                "text" => "flipInY"
	            )
	        )
	    ),
	    array(
	        "text" => "Flippers Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "flipOutX",
	                "text" => "flipOutX"
	            ),
	            array(
	                "id" => "flipOutY",
	                "text" => "flipOutY"
	            )
	        )
	    ),
	    array(
	        "text" => "Lightspeed Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "lightSpeedIn",
	                "text" => "lightSpeedIn"
	            )
	        )
	    ),
	    array(
	        "text" => "Lightspeed Exits",
	        "type" => "out",
	        "children" => array(

	            array(
	                "id" => "lightSpeedOut",
	                "text" => "lightSpeedOut"
	            )
	        )
	    ),
	    array(
	        "text" => "Rotating Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "rotateIn",
	                "text" => "rotateIn"
	            ),
	            array(
	                "id" => "rotateInDownLeft",
	                "text" => "rotateInDownLeft"
	            ),
	            array(
	                "id" => "rotateInDownRight",
	                "text" => "rotateInDownRight"
	            ),
	            array(
	                "id" => "rotateInUpLeft",
	                "text" => "rotateInUpLeft"
	            ),
	            array(
	                "id" => "rotateInUpRight",
	                "text" => "rotateInUpRight"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Rotating Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "rotateOut",
	                "text" => "rotateOut"
	            ),
	            array(
	                "id" => "rotateOutDownLeft",
	                "text" => "rotateOutDownLeft"
	            ),
	            array(
	                "id" => "rotateOutDownRight",
	                "text" => "rotateOutDownRight"
	            ),
	            array(
	                "id" => "rotateOutUpLeft",
	                "text" => "rotateOutUpLeft"
	            ),
	            array(
	                "id" => "rotateOutUpRight",
	                "text" => "rotateOutUpRight"
	            )
	        )
	    ),
	    array(
	        "text" => "Sliding Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "slideInUp",
	                "text" => "slideInUp"
	            ),
	            array(
	                "id" => "slideInDown",
	                "text" => "slideInDown"
	            ),
	            array(
	                "id" => "slideInLeft",
	                "text" => "slideInLeft"
	            ),
	            array(
	                "id" => "slideInRight",
	                "text" => "slideInRight"
	            )
	        )
	    ),
	    array(
	        "text" => "Sliding Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "slideOutUp",
	                "text" => "slideOutUp"
	            ),
	            array(
	                "id" => "slideOutDown",
	                "text" => "slideOutDown"
	            ),
	            array(
	                "id" => "slideOutLeft",
	                "text" => "slideOutLeft"
	            ),
	            array(
	                "id" => "slideOutRight",
	                "text" => "slideOutRight"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Zoom Entrances",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "zoomIn",
	                "text" => "zoomIn"
	            ),
	            array(
	                "id" => "zoomInDown",
	                "text" => "zoomInDown"
	            ),
	            array(
	                "id" => "zoomInLeft",
	                "text" => "zoomInLeft"
	            ),
	            array(
	                "id" => "zoomInRight",
	                "text" => "zoomInRight"
	            ),
	            array(
	                "id" => "zoomInUp",
	                "text" => "zoomInUp"
	            )
	        )
	    ),
	    array(
	        "text" => "Zoom Exits",
	        "type" => "out",
	        "children" => array(
	            
	            array(
	                "id" => "zoomOut",
	                "text" => "zoomOut"
	            ),
	            array(
	                "id" => "zoomOutDown",
	                "text" => "zoomOutDown"
	            ),
	            array(
	                "id" => "zoomOutLeft",
	                "text" => "zoomOutLeft"
	            ),
	            array(
	                "id" => "zoomOutRight",
	                "text" => "zoomOutRight"
	            ),
	            array(
	                "id" => "zoomOutUp",
	                "text" => "zoomOutUp"
	            )
	            
	        )
	    ),
	    array(
	        "text" => "Specials",
	        "type" => "in",
	        "children" => array(
	            
	            array(
	                "id" => "hinge",
	                "text" => "hinge"
	            ),
	            array(
	                "id" => "rollIn",
	                "text" => "rollIn"
	            ),
	            array(
	                "id" => "rollOut",
	                "text" => "rollOut"
	            )
	        )
	    )
	);
	
	
	if(!empty($exclude)) {
		
		foreach($animations as $key => $value) {
			
			if(!empty($value["type"]) && in_array($value["type"], $exclude)) {
		    	unset($animations[$key]);
		    }	
		    
		};
	}
	
	return $animations;	
}


function slick_menu_get_patterns() {

	$sm = Slick_Menu();
	
	$patterns_path = $sm->assets_dir.'/images/patterns';
	$patterns_url = $sm->assets_url.'/images/patterns';
	
	$patterns = array();
	$files = glob($patterns_path.'/*.png');

	foreach($files as $file) {
	
		$value = $patterns_url . '/' . basename($file);
		$patterns[$value] = $value;
	}
	
	return $patterns;	
}