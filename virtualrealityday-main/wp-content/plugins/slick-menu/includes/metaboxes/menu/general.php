<?php

return array(

    array(
        'id'      => "{$prefix}exclude-pages",
        'name'    => esc_html__( 'Disable on selected pages', 'slick-menu' ),
        'desc' 	  => esc_html__( 'This will completely remove the menu from the source code on selected pages. The menu will not be loaded even if set as "Always Visible". Note: within the customizer, this option requires a full page reload to take effect after saving.', 'slick-menu' ),
        'type'    => 'select_advanced',
        'options' => array('' => ''),
        'initial_options_callback' => 'sm_mb_select_initial_options_callback',
        'multiple' => true,
        'js_options' => array (
            'ajax' => array(
                'url' => '/wp-admin/admin-ajax.php?action=sm_mb_select_post_callback',
                'dataType' => 'json',
                'type' => 'get',
                'delay' => '250'
            ),
            'minimumInputLength' => 3,
            'allowClear' => true,
            'placeholder' => 'Select Page(s)'
        ),
        'std' 			=> array()
    ),
	array(
		'id' 			=> "{$prefix}mobile-breakpoint",
		'name' 			=> esc_html__( 'Menu Mobile Breakpoint', 'slick-menu' ),
		'desc' 			=> esc_html__( 'Breakpoint Width in px (Leave empty to inherit Global Settings)', 'slick-menu' ),
		'type' 			=> 'text',
		'std' 			=> ''
	),			
    array(
		'id' 			=> "{$prefix}menu-ajax",
		'name'			=> esc_html__( 'Load menu on demand using ajax', 'slick-menu' ),
		'desc'			=> esc_html__( 'Recommended if you have created a lot of slick menus for a faster Page load. If disabled, the menu will be auto loaded on each page', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> "0"
	),			
	array(
		'id' 			=> "{$prefix}menu-position",
		'name'			=> esc_html__( 'Menu Position', 'slick-menu' ),
		'desc' 			=> esc_html__( 'Select a menu position', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(true),
		'std'			=> 'right'
	),	
    array(
		'id' 			=> "{$prefix}menu-open-active-level",
		'name'			=> esc_html__( 'Activate Current Menu Item on Menu Open', 'slick-menu' ),
		'desc'			=> esc_html__( 'Automatically open current menu item level once menu is open', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> "1"
	),	
    array(
		'id' 			=> "{$prefix}menu-always-visible",
		'name'			=> esc_html__( 'Always visible', 'slick-menu' ),
		'desc'			=> esc_html__( 'You can only have 1 menu set as "always visible", by enabling this option, any other visible menus will automatically be hidden.', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std'			=> '0'
	),	
	SM_RWMB_EXTEND_Groups::boxShadow(array(
		'id' 			=> "{$prefix}menu-shadow",
		'name'			=> esc_html__( 'Menu Shadow', 'slick-menu' ),
		'desc' 			=> esc_html__( 'Apply a shadow to menu levels', 'slick-menu' ),
		'toggle'		=> true,
		'std' 			=> ''
	)),			
	array(
		'id' 			=> "{$prefix}level-animation-type",
		'name'			=> esc_html__( 'Levels Animation Type', 'slick-menu' ),
		'desc'			=> esc_html__( 'Select a level animation type', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> array(
			'cover'		=>	esc_html__('Covering', 'slick-menu'),
			'overlap'	=>	esc_html__('Overlapping', 'slick-menu')
		),
		'attributes'	=> array(
			'onchange' => "

				$ = jQuery;
				var field = $(this);
				var field_wrap = field.closest('.sm-rwmb-field');
				var effects_list = field_wrap.next().find('select');
				var value = field.val();
				var effects = field.data(value);
				
				effects_list.empty();
				$.each(effects, function( key, label ) {
				  	effects_list.append('<option value=\"'+key+'\">'+label+'</option>');
				});

			",
			'data-overlap' =>  htmlentities(json_encode(slick_menu_data_get_effects('push')), ENT_QUOTES, 'UTF-8'),
			'data-cover' => htmlentities(json_encode(slick_menu_data_get_effects()), ENT_QUOTES, 'UTF-8')
		),			
		'std'			=> 'cover',
	),		
	array(
		'id' 			=> "{$prefix}menu-animation-type",
		'name'			=> esc_html__( 'Menu Animation Type', 'slick-menu' ),
		'desc'			=> esc_html__( 'Select a menu animation type', 'slick-menu' ),
		'type'			=> 'select',
		'options'		=> slick_menu_data_get_effects(),
		'std'			=> 'sm-effect-1',
		'visible'		=> array(
			array("{$prefix}menu-always-visible", '!=', '1')
		)
	),

	array(
		'id' 			=> "{$prefix}menu-mobile-animation-type",
		'name'			=> esc_html__( 'Menu Animation Type for Mobiles Devices', 'slick-menu' ),
		'desc'			=> esc_html__( 'Select a menu animation type that will be used for mobile devices.', 'slick-menu' ),
		'type'			=> 'select',
		'options'		=> slick_menu_data_get_effects('mobile'),
		'std'			=> 'sm-effect-2',
		'visible'		=> array(
			array("{$prefix}menu-always-visible", '!=', '1')
		)
	),
	
	
	// Menu Open Animation	
	array(
		'id' 			=> "{$prefix}menu-open-duration",
		'name'			=> esc_html__( 'Menu Opening Animation Duration', 'slick-menu' ),
		'type' 	=> 'slider',
		'suffix' => 'ms',
		'js_options' => array(
			'suffix' => 'ms',
			'min'  => 0,
			'max'  => 2000,
			'step' => 10,
		),
        'std'	=> 500,
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	),	
	array(
		'id' 			=> "{$prefix}menu-open-easing",
		'name'			=> esc_html__( 'Menu Opening Animation Easing', 'slick-menu' ),
		'type'			=> 'select',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_easings(),
		'std'			=> 'ease-out',
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	),
	
	// Menu Close Animation	
	array(
		'id' 			=> "{$prefix}menu-close-duration",
		'name'			=> esc_html__( 'Menu Closing Animation Duration', 'slick-menu' ),
		'type' 	=> 'slider',
		'suffix' => 'ms',
		'js_options' => array(
			'min'  => 0,
			'max'  => 2000,
			'step' => 10,
		),
        'std'	=> 500,
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	),
	array(
		'id' 			=> "{$prefix}menu-close-easing",
		'name'			=> esc_html__( 'Menu Closing Animation Easing', 'slick-menu' ),
		'type'			=> 'select',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_easings(),
		'std'			=> 'ease-in',
		'visible'		=> array("{$prefix}menu-always-visible", '!=', '1')
	),

		
	array(
		'id' 			=> "{$prefix}menu-overlap-font-family",
		'name'			=> esc_html__( 'Menu Overlap Title Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'std'			=> 'Lato:400',
		'visible'		=> array("{$prefix}level-animation-type", 'overlap')
	),
	array(
		'id' 			=> "{$prefix}menu-overlap-title-color",
		'name'			=> esc_html__( 'Menu Overlap Title Color', 'slick-menu' ),
		'desc'			=> esc_html__( 'Applicable for overlapping Levels', 'slick-menu' ),
		'type'			=> 'rgba',
		'std'			=> '#ffffff',
		'visible'		=> array("{$prefix}level-animation-type", 'overlap')
	),
	array(
		'id' 			=> "{$prefix}menu-overlap-icon-color",
		'name'			=> esc_html__( 'Menu Overlap Icon Color', 'slick-menu' ),
		'desc'			=> esc_html__( 'Applicable for overlapping Levels', 'slick-menu' ),
		'type'			=> 'rgba',
		'std'			=> '#ffffff',
		'visible'		=> array("{$prefix}level-animation-type", 'overlap')
	),
	array(
		'id' 			=> "{$prefix}menu-overlap-bg-color",
		'name'			=> esc_html__( 'Menu Overlap Background Color', 'slick-menu' ),
		'desc'			=> esc_html__( 'Applicable for overlapping Levels', 'slick-menu' ),
		'type'			=> 'rgba',
		'std'			=> 'rgba(0, 0, 0, 0.3)',
		'visible'		=> array("{$prefix}level-animation-type", 'overlap')
	)		
);