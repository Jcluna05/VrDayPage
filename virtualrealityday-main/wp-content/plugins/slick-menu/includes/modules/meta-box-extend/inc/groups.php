<?php
	
if ( defined( 'ABSPATH' ) && ! class_exists( 'SM_RWMB_EXTEND_Groups' ) )
{

	class SM_RWMB_EXTEND_Groups {

		static function background($group, $singleColumn = true, $std = true) {
			
			$fields = array(
                'image' => array(
                    'id' 	=> 'image',
					'name' 	=> esc_html__('Background Image', 'slick-menu'),
                    'type' 	=> 'image_advanced',
                    'max_file_uploads' => 1,
                    'class'	=> 'sm-rwmb-small-preview',
                    'columns' => 3,
                    'std'	=> ''
                ),
                'color' => array(
                    'id' 	=> 'color',
                    'name' 	=> esc_html__('Background Color', 'slick-menu'),
                    'type' 	=> 'rgba',
                    'columns' => 9,
                    'std'	=> ''
                ),
                'empty-0' => array(
                    'type' 	=> 'empty',
                    'columns' => 3,
                    'std'	=> ''
                ),
                'repeat' => array(
                    'id' 	=> 'repeat',
                    'name' 	=> esc_html__('Background Repeat', 'slick-menu'),
                    'type' 	=> 'select',
                    'placeholder' => ' --- ',
                    'options' => array(
	                    'no-repeat' => esc_html__('No Repeat', 'slick-menu'),
	                    'repeat' => esc_html__('Repeat All', 'slick-menu'),
	                    'repeat-x' => esc_html__('Repeat Horizontally', 'slick-menu'),
	                    'repeat-y' => esc_html__('Repeat Vertically', 'slick-menu')
                    ),
                    'columns' => 9,
                    'std'	=> ''
                ),
                'empty-1' => array(
                    'type' 	=> 'empty',
                    'columns' => 3,
                    'std'	=> ''
                ),
                'size' => array(
                    'id' 	=> 'size',
                    'name' 	=> esc_html__('Background Size', 'slick-menu'),
                    'type' 	=> 'select',
                    'placeholder' => ' --- ',
                    'options' => array(
	                    'cover' => esc_html__('Cover', 'slick-menu'),
	                    'contain' => esc_html__('Contain', 'slick-menu')
                    ),
                    'columns' => 9,
                    'std'	=> ''
                ),
                'empty-2' => array(
                    'type' 	=> 'empty',
                    'columns' => 3,
                    'std'	=> ''
                ),
                'position' => array(
                    'id' 	=> 'position',
                    'name' 	=> esc_html__('Background Position', 'slick-menu'),
                    'type' 	=> 'select',
                    'placeholder' => ' --- ',
                    'options' => array(
	                    'left top' => esc_html__('Left Top', 'slick-menu'),
	                    'left center' => esc_html__('Left Center', 'slick-menu'),
	                    'left bottom' => esc_html__('Left Bottom', 'slick-menu'),
	                    'center top' => esc_html__('Center Top', 'slick-menu'),
	                    'center center' => esc_html__('Center Center', 'slick-menu'),
	                    'center bottom' => esc_html__('Center Bottom', 'slick-menu'),
	                    'right top' => esc_html__('Right Top', 'slick-menu'),
	                    'right center' => esc_html__('Right Center', 'slick-menu'),
	                    'right bottom' => esc_html__('Right Bottom', 'slick-menu'),
                    ),
                    'columns' => 9,
                    'std'	=> ''
                ),
            );
            
            
            if($singleColumn) {
	            
	            foreach($fields as $key => $field) {
		            
		            if($key == 'image') {
			            $fields[$key]['class'] = str_replace('sm-rwmb-small-preview', '', $fields[$key]['class']);
		            }
		            
		            if(!empty($field['columns'])) {
		            	unset($fields[$key]['columns']);
		            }
		            
		            if($field['type'] == 'empty') {
			            unset($fields[$key]);
		            }
	            }
	            
            }
            
            if(empty($std)) {
				self::removeStd($fields);
            }
            
            
            return self::getGroup($group, $fields);
		}


		static function video($group, $std = true) {

			$fields = array(
                'id' => array(
                    'id' 	=> 'id',
					'name' 	=> esc_html__('Youtube Video ID', 'slick-menu'),
					'desc'  => esc_html__('Ex: LSmgKRx5pBo', 'slick-menu'),
                    'type' 	=> 'text',
                    'std'	=> ''
                ),
                'quality' => array(
	                'id' 	=> 'quality',
                    'name' 	=> esc_html__('Quality', 'slick-menu'),
	              	'desc' => esc_html__('We recommend that you set the parameter value to default, which instructs YouTube to select the most appropriate playback quality, which will vary for different users, videos, systems and other playback conditions.', 'slick-menu'),
	              	'type' 	=> 'select',
                    'placeholder' => ' --- ',
	              	'options' => array(
		              	'default' => 'Default', 
		              	'small' => 'Small', 
		              	'medium' => 'Medium', 
		              	'large' => 'Large', 
		              	'hd720' => 'HD 720', 
		              	'hd1080' => 'HD 1080', 
		              	'highres' => 'High Resolution'
	              	),
                    'std'	=> 'default'  
                ),
                'opacity' => array(
                    'id' 	=> 'opacity',
                    'name' 	=> esc_html__('Opacity', 'slick-menu'),
                    'desc' => esc_html__('Set video opacity, a lower value makes the video more transparent', 'slick-menu'),
                    'type' 	=> 'slider',
					'js_options' => array(
						 'min'  => 0.01,
						'max'  => 1,
						'step' => 0.01,
					),
                    'std'	=> 1
                ),
                'scale' => array(
                    'id' 	=> 'scale',
                    'name' 	=> esc_html__('Scale Video', 'slick-menu'),
                    'desc' => esc_html__('Scaling the video is useful to hide top and bottom black borders in some videos', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' 		=> 'x',
					'js_options' => array(
						'min'  => 1,
						'max'  => 2,
						'step' => 0.1,
					),
                    'std'	=> 1
                ),
                'start' => array(
                    'id' 	=> 'start',
                    'name' 	=> esc_html__('Start Video At', 'slick-menu'),
                    'desc' => esc_html__('Second in which video should begin playing at', 'slick-menu'),
                    'type' 	=> 'text',
                    'std'	=> ''
                ),
                'end' => array(
                    'id' 	=> 'end',
                    'name' 	=> esc_html__('End Video At', 'slick-menu'),
                    'desc' => esc_html__('Second in which video should end playing at', 'slick-menu'),
                    'type' 	=> 'text',
                    'std'	=> ''
                ),
                'nopause' => array(
                    'id' 	=> 'nopause',
                    'name' 	=> esc_html__('Never Pause', 'slick-menu'),
                    'desc' => esc_html__('By default, the video will be paused and hidden on inactive menu levels and will be un-paused once the level is active again', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),                
                'repeat' => array(
                    'id' 	=> 'repeat',
                    'name' 	=> esc_html__('Repeat', 'slick-menu'),
                    'desc' => esc_html__('Loop video indefinitely', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 1
                ),
                'audio' => array(
                    'id' 	=> 'audio',
                    'name' 	=> esc_html__('Play Audio', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),
                'spinner' => array(
                    'id' 	=> 'spinner',
                    'name' 	=> esc_html__('Show Loading Animation', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),
                'behind-overlay' => array(
                    'id' 	=> 'behind-overlay',
                    'name' 	=> esc_html__('Place behind background overlay', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),
                'mobile' => array(
                    'id' 	=> 'mobile',
                    'name' 	=> esc_html__('Mobile Video Embed Fallback', 'slick-menu'),
                    'desc' => esc_html__('Video backgrounds are not supported on mobile. Enable this fallback option to make the video available on mobile as a regular embed video player.'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),

            );
               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}
	
		static function overlay($group, $std = true) {

			$group_id = $group['id'];
			
			$fields = array(
				'type' => array(
                    'id' 	=> 'type',
					'name' 	=> esc_html__('Type', 'slick-menu'),
                    'type' 	=> 'radio',
                    'inline'=> false,
	              	'options' => array(
		              	'color' => 'Solid Color', 
		              	'gradient' => 'Gradient'
	              	),
                    'std'	=> 'color'  
                ),
                'color' => array(
                    'id' 	=> 'color',
                    'name' 	=> esc_html__('Color', 'slick-menu'),
                    'type' 	=> 'rgba',
                    'visible' => array("{$group_id}[type]", 'color'),
                    'std'	=> ''
                ),
                'direction' => array(
                    'id' 	=> 'direction',
					'name' 	=> esc_html__('Direction', 'slick-menu'),
                    'type' 	=> 'radio',
                    'inline'=> false,
	              	'options' => array(
		              	'to right' => 'To Right', 
		              	'to left' => 'To Left', 
		              	'to top' => 'To Top', 
		              	'to bottom' => 'To Bottom'
	              	),
	              	'visible' => array("{$group_id}[type]", 'gradient'),
                    'std'	=> 'to right'  
                ),
                'color_start' => array(
                    'id' 	=> 'color_start',
                    'name' 	=> esc_html__('Color Start', 'slick-menu'),
                    'type' 	=> 'rgba',
                    'visible' => array("{$group_id}[type]", 'gradient'),
                    'std'	=> ''
                ),
                'color_end' => array(
                    'id' 	=> 'color_end',
                    'name' 	=> esc_html__('Color End', 'slick-menu'),
                    'type' 	=> 'rgba',
                    'visible' => array("{$group_id}[type]", 'gradient'),
                    'std'	=> ''
                ),
                'uigradients' => array(
	                'id'	=> 'uigradients',
	                'type'	=> 'custom-html',
                    'visible' => array("{$group_id}[type]", 'gradient'),
	                'std'	=> 'Looking for nice gradient colors? Grab some colors from <a target="_blank" href="http://uigradients.com/">uigradients.com</a>'
                )
            );
               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}

		static function pattern($group, $std = true) {

			$fields = array(
                'pattern' => array(
                    'id' 	=> 'pattern',
					'name' 	=> esc_html__('Pattern', 'slick-menu'),
                    'type' 	=> 'image-bg-select',
	              	'options' => slick_menu_get_patterns(),
                    'std'	=> ''  
                ),
                'opacity' => array(
                    'id' 	=> 'opacity',
                    'name' 	=> esc_html__('Opacity', 'slick-menu'),
                    'desc' => esc_html__('Set pattern opacity, a lower value makes the pattern more transparent', 'slick-menu'),
                    'type' 	=> 'slider',
					'js_options' => array(
						 'min'  => 0.01,
						'max'  => 1,
						'step' => 0.01,
					),
                    'std'	=> 0.5
                ),
            );

               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}		


		static function boxShadow($group, $std = true) {

			$fields = array(
                
                'hshadow' => array(
                    'id' 	=> 'hshadow',
                    'name' 	=> esc_html__('Horizontal Shadow', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => -60,
						'max'  => 60,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'vshadow' => array(
                    'id' 	=> 'vshadow',
                    'name' 	=> esc_html__('Vertical Shadow', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => -60,
						'max'  => 60,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'blur' => array(
                    'id' 	=> 'blur',
                    'name' 	=> esc_html__('Shadow Distance', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 60,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'spread' => array(
                    'id' 	=> 'spread',
                    'name' 	=> esc_html__('Shadow Spread Size', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 60,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'color' => array(
                    'id' 	=> 'color',
					'name' 	=> esc_html__('Shadow Color', 'slick-menu'),
                    'type' 	=> 'rgba',
                    'std'	=> ''  
                ),
            );

               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}	

		static function boxBorder($group, $std = true) {

			$fields = array(
                
                'top-width' => array(
                    'id' 	=> 'top-width',
                    'name' 	=> esc_html__('Border Top Width', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'right-width' => array(
                    'id' 	=> 'right-width',
                    'name' 	=> esc_html__('Border Right Width', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'bottom-width' => array(
                    'id' 	=> 'bottom-width',
                    'name' 	=> esc_html__('Border Bottom Width', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'left-width' => array(
                    'id' 	=> 'left-width',
                    'name' 	=> esc_html__('Border Left Width', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
                    'std'	=> 0
                ),
                'radius' => array(
                    'id' 	=> 'radius',
                    'name' 	=> esc_html__('Border Radius', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
                    'std'	=> 0
                ),
				'style' => array(
					'id' 			=> "style",
					'name'			=> esc_html__( 'Border Style', 'slick-menu' ),
					'type'			=> 'radio',
					'inline'		=> false,
					'options'		=> array(
						'none'		=>	esc_html__('None', 'slick-menu'),
						'solid'		=>	esc_html__('Solid', 'slick-menu'),
						'dotted'	=>	esc_html__('Dotted', 'slick-menu'),
						'dashed'	=>	esc_html__('Dashed', 'slick-menu'),
						'double'	=>	esc_html__('Double', 'slick-menu')
					),
					'std'			=> 'none'
				),
				'color' => array(
					'id' 			=> "color",
					'name'			=> esc_html__( 'Border Color', 'slick-menu' ),
					'type'			=> 'rgba',
					'std'			=> ''
				)
            );

               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}	
		

		static function transforms($group, $std = true) {
			
			$group_id = $group["id"];
			
			$fields = array(
                
                'enabled' => array(
                    'id' 	=> 'enabled',
                    'name' 	=> '',
                    'desc' 	=> esc_html__('Enable Transforms', 'slick-menu'),
                    'type' 	=> 'checkbox',
                    'std'	=> 0
                ),
                 'perspective' => array(
                    'id' 	=> 'perspective',
                    'name' 	=> esc_html__('Perspective', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'vw',
					'js_options' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),  
                'perspective-origin' => array(
                    'id' 	=> 'perspective-origin',
                    'name' 	=> esc_html__('Perspective Origin', 'slick-menu'),
                    'type' 	=> 'select',
                    'options' => array(
	                    'left top' => esc_html__('Left Top', 'slick-menu'),
	                    'left center' => esc_html__('Left Center', 'slick-menu'),
	                    'left bottom' => esc_html__('Left Bottom', 'slick-menu'),
	                    'center top' => esc_html__('Center Top', 'slick-menu'),
	                    'center center' => esc_html__('Center Center', 'slick-menu'),
	                    'center bottom' => esc_html__('Center Bottom', 'slick-menu'),
	                    'right top' => esc_html__('Right Top', 'slick-menu'),
	                    'right center' => esc_html__('Right Center', 'slick-menu'),
	                    'right bottom' => esc_html__('Right Bottom', 'slick-menu'),
                    ),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 'center center'
                ), 
                'delay' => array(
                    'id' 	=> 'delay',
                    'name' 	=> esc_html__('Transition Delay', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'ms',
					'js_options' => array(
						'min'  => 0,
						'max'  => 3000,
						'step' => 10,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),              
                'duration' => array(
                    'id' 	=> 'duration',
                    'name' 	=> esc_html__('Transition Duration', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'ms',
					'js_options' => array(
						'min'  => 100,
						'max'  => 3000,
						'step' => 10,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 400
                ),
                'scale-x' => array(
                    'id' 	=> 'scale-x',
                    'name' 	=> esc_html__('Scale X', 'slick-menu'),
                    'type' 	=> 'slider',
                    'prefix' => 'x',
					'js_options' => array(
						'min'  => 0,
						'max'  => 2,
						'step' => 0.01,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 1
                ),
                'scale-y' => array(
                    'id' 	=> 'scale-y',
                    'name' 	=> esc_html__('Scale Y', 'slick-menu'),
                    'type' 	=> 'slider',
                    'prefix' => 'x',
					'js_options' => array(
						'min'  => 0,
						'max'  => 2,
						'step' => 0.01,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 1
                ),
                'translate-x' => array(
                    'id' 	=> 'translate-x',
                    'name' 	=> esc_html__('Offset X', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'translate-y' => array(
                    'id' 	=> 'translate-y',
                    'name' 	=> esc_html__('Offset Y', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'rotate' => array(
                    'id' 	=> 'rotate',
                    'name' 	=> esc_html__('Rotate 2D', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'deg',
					'js_options' => array(
						'min'  => -180,
						'max'  => 180,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'rotate-x' => array(
                    'id' 	=> 'rotate-x',
                    'name' 	=> esc_html__('Rotate X', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'deg',
					'js_options' => array(
						'min'  => -90,
						'max'  => 90,
						'step' => 5,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'rotate-y' => array(
                    'id' 	=> 'rotate-y',
                    'name' 	=> esc_html__('Rotate Y', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'deg',
					'js_options' => array(
						'min'  => -90,
						'max'  => 90,
						'step' => 5,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'rotate-z' => array(
                    'id' 	=> 'rotate-z',
                    'name' 	=> esc_html__('Rotate Z', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'deg',
					'js_options' => array(
						'min'  => -90,
						'max'  => 90,
						'step' => 5,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'skew' => array(
                    'id' 	=> 'skew',
                    'name' 	=> esc_html__('Skew', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'deg',
					'js_options' => array(
						'min'  => -25,
						'max'  => 25,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                ),
                'radius' => array(
                    'id' 	=> 'radius',
                    'name' 	=> esc_html__('Radius', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'visible' => array("{$group_id}[enabled]", '1'),
                    'std'	=> 0
                )
            );

               
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}	
		
				
		static function hamburgerTrigger($group, $std = true) {
	
			$group_id = $group['id'];
			$group['class'] = 'sm-rwmb-hamburger-group';
			
			$menus = array();
			if(is_admin()) {
				$menus = Slick_Menu()->get_menus_dropdown_options();
			}
		
			$fields = array(
                
                'menu'	=>	array(
					'id' 		=> "menu",
					'name' 		=> esc_html__( 'Select a Slick Menu to trigger', 'slick-menu' ),
					'type' 		=> 'select',
					'placeholder'	=> esc_html__( 'Select Menu', 'slick-menu' ),
					'options' 	=> $menus,
					'std' 		=> ''
				),
				
		    	'hamburger' => array(
					'id' 			=> "hamburger",
					'name'			=> esc_html__( 'Select Hamburger', 'slick-menu' ),
					'type'			=> 'hamburger-select',
					'std'			=> '',
					'visible' => array("{$group_id}[menu]", '!=', ''),
				),
				'reverse' => array(
					'id' 			=> "reverse",
					'name'			=> esc_html__( 'Reverse Animation', 'slick-menu' ),
					'desc'			=> esc_html__( 'By default, hamburger animation is clockwise, select reverse to make it counterclockwise', 'slick-menu' ),
					'type'			=> 'checkbox',
					'class'			=> 'sm-padded',
					'std'			=> 0,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', 'sm-hamburger--squeeze')
					)	
				),
				'visibility' => array(
					'id' 			=> "visibility",
					'name'			=> esc_html__( 'Visibility', 'slick-menu' ),
					'type' 			=> 'radio',
					'inline'		=> false,
				    'options' => array(
						'show-on-all' 				=> esc_attr__( 'Show on all', 'slick-menu' ),
						'show-on-mobile-only' 		=> esc_attr__( 'Show on mobile only', 'slick-menu' ),
						'show-on-desktop-only' 		=> esc_attr__( 'Show on desktop only', 'slick-menu' )
                    ),
					'std'	=> 'show-on-all',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', '')
					)	
				),
				'label' => array(
					'id' 			=> "label",
					'name'			=> esc_html__( 'Label', 'slick-menu' ),
					'desc'			=> esc_html__( 'Add a hamburger label.', 'slick-menu' ),
					'type'			=> 'checkbox',
					'class'			=> 'sm-hamburger-label',
					'std'			=> 0,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', '')
					)		
				),
				'label-text' => array(
					'id' 			=> "label-text",
					'name'			=> esc_html__( 'Label Text', 'slick-menu' ),
					'desc'			=> esc_html__( 'Insert label text eg: Menu', 'slick-menu' ),
					'type'			=> 'text',
					'class'			=> 'sm-hamburger-label',
					'std'			=> '',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
						array("{$group_id}[label]", '!=', '0')
					)	
				),
				'label-position' => array(
					'id' 			=> "label-position",
					'name'			=> esc_html__( 'Label Position', 'slick-menu' ),
					'type' 			=> 'radio',
					'class'			=> 'sm-hamburger-label',
					'inline'		=> false,
                    'options' => array(
	                    'above' => esc_html__('Above Hamburger', 'slick-menu'),
	                    'before' => esc_html__('Before Hamburger', 'slick-menu'),
	                    'below' => esc_html__('Below Hamburger', 'slick-menu'),
	                    'after' => esc_html__('After Hamburger', 'slick-menu')
                    ),
					'std'	=> 'below',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
						array("{$group_id}[label]", '!=', '0')
					)	
				),
				'label-size' => array(
					'id' 			=> "label-size",
					'name'			=> esc_html__( 'Label Size', 'slick-menu' ),
					'type' 			=> 'slider',
					'class'			=> 'sm-hamburger-label',
                    'suffix' 		=> 'px',
					'js_options' => array(
						'min'  => 12,
						'max'  => 100,
						'step' => 1,
					),
                    'std'	=> 18,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
						array("{$group_id}[label]", '!=', '0'),
					)	
				),
				'label-distance' => array(
					'id' 			=> "label-distance",
					'name'			=> esc_html__( 'Label Distance', 'slick-menu' ),
					'desc'			=> esc_html__( 'Distance between the Label and the Hamburger', 'slick-menu' ),
					'type' 	=> 'slider',
					'class'			=> 'sm-hamburger-label',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					),
                    'std'	=> 10,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
						array("{$group_id}[label]", '!=', '0'),
					)	
				),
				'margin-x' => array(
                    'id' 	=> 'margin-x',
                    'name' 	=> esc_html__('Horizontal Margin (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
                    'std'	=> 15,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),	
                'margin-y' => array(
                    'id' 	=> 'margin-y',
                    'name' 	=> esc_html__('Vertical Margin (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					),
                    'std'	=> 15,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),
				'bar-width' => array(
                    'id' 	=> 'bar-width',
                    'name' 	=> esc_html__('Bar Width (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 5,
						'max'  => 200,
						'step' => 1,
					),
                    'std'	=> 40,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),
                'bar-height' => array(
                    'id' 	=> 'bar-height',
                    'name' 	=> esc_html__('Bar Height (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					),
                    'std'	=> 2,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),	
                'bar-spacing' => array(
                    'id' 	=> 'bar-spacing',
                    'name' 	=> esc_html__('Bar Spacing (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
                    'std'	=> 6,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),	
                'bar-radius' => array(
                    'id' 	=> 'bar-radius',
                    'name' 	=> esc_html__('Bar Radius (px)', 'slick-menu'),
                    'type' 	=> 'slider',
                    'suffix' => 'px',
					'js_options' => array(
						'min'  => 0,
						'max'  => 8,
						'step' => 1,
					),
                    'std'	=> 3,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),
                'hover-opacity' => array(
                    'id' 	=> 'hover-opacity',
                    'name' 	=> esc_html__('Hover Opacity', 'slick-menu'),
                    'type' 	=> 'slider',
					'js_options' => array(
						'min'  => 0.01,
						'max'  => 1,
						'step' => 0.01,
					),
                    'std'	=> 0.7,
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
                ),
				'color' => array(
					'id' 			=> "color",
					'name'			=> esc_html__( 'Color', 'slick-menu' ),
					'type'			=> 'rgba',
					'std'			=> '',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
				),
				'hover-color' => array(
					'id' 			=> "hover-color",
					'name'			=> esc_html__( 'Hover Color', 'slick-menu' ),
					'type'			=> 'rgba',
					'std'			=> '',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
				),
				'active-color' => array(
					'id' 			=> "active-color",
					'name'			=> esc_html__( 'Active Color', 'slick-menu' ),
					'type'			=> 'rgba',
					'std'			=> '',
					'visible' => array(
						array("{$group_id}[menu]", '!=', ''),
						array("{$group_id}[hamburger]", '!=', ''),
					)
				)
            );

         
            if(empty($std)) {
				self::removeStd($fields);
            }
                     
            return self::getGroup($group, $fields);
		}	
		
						
		static function getGroup($group, $fields) {

			if(!empty($group['merge'])) {
				$fields = array_merge_recursive($fields, $group['merge']);
				unset($group['merge']);
			}
			
			if(!empty($group['exclude'])) {
				foreach($fields as $key => $field) {
					if(in_array($field['id'], $group['exclude']))  {
						unset($fields[$key]);
					}	
				}
				unset($group['exclude']);
			}
			
			if(empty($group['class'])) {
				$group['class'] = $group['id'];
			}else{
				$group['class'] .= ' '.$group['id'];
			}
			
			if(!empty($group['std']) && is_array($group['std'])) {
				
				foreach($fields as $key => $field) {
					if(!empty($group['std'][$field['id']])) {
						$fields[$key]['std'] = $group['std'][$field['id']];
					}	
				}
			}	
		
			$group['type'] = 'group';
			$group['fields'] = $fields;
			
			return $group;
		}
						
		static function removeStd(&$fields) {
	
		    foreach($fields as $key => $field) {
	            if(isset($fields[$key]["std"])) {
	            	unset($fields[$key]["std"]);
	            }
	        }
		}
		
	}

}

