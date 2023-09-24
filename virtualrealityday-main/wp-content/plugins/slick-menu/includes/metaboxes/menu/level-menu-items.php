<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}menu-items-hidden",
		'name'			=> esc_html__( 'Hidden', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0'
	),

	array(
		'id' 			=> "{$prefix}menu-items-hide-label",
		'name'			=> esc_html__( 'Hide Label', 'slick-menu' ),
		'desc'			=> esc_html__( 'Applicable only if a menu item icon is set', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '0'
	),	

	array(
		'id' 			=> "{$prefix}menu-items-label-visibility",
		'name'			=> esc_html__( 'Hide Label Visibility', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'0'					=>	esc_html__('Always Visible', 'slick-menu'),
			'show-on-hover'		=>	esc_html__('Visible On Hover', 'slick-menu'),
			'hide-on-hover'		=>	esc_html__('Hidden On Hover', 'slick-menu')
		),
		'accordion'		=> 'general',
		'std'			=> '0',
		'visible'		=> array("{$prefix}menu-items-hide-label", '!=', '1')
	),
	
	array(
		'id' 			=> "{$prefix}menu-items-fullwidth",
		'name'			=> esc_html__( 'Fullwidth', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'general',
		'std'			=> '1'
	),	
	array(
		'id' 			=> "{$prefix}menu-items-height",
		'name'			=> esc_html__( 'Height (optional)', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set a height in (%, px, vh). Useful to build grids. Use the vh unit to enter a percentage related to the viewport height. Ex 50vh would make the height equal to half the window height.', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'general',
		'std'			=> ''
	),
	
	// FONT
	array(
		'id' 			=> "{$prefix}menu-items-font-family",
		'name'			=> esc_html__( 'Font Family', 'slick-menu' ),
		'type'			=> 'googlefonts',
		'accordion'		=> 'font',
		'std'			=> 'Lato:400'
	),	
	
	array(
		'id' 			=> "{$prefix}menu-items-font-size",
		'name'			=> esc_html__( 'Font Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '20px'
	),
	array(
		'id' 			=> "{$prefix}menu-items-line-height",
		'name'			=> esc_html__( 'Line Height', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter line height in (px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'font',
		'std'			=> '25px'
	),	
	array(
		'id' 			=> "{$prefix}menu-items-text-transform",
		'name'			=> esc_html__( 'Text Transform', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_text_transform(),
		'accordion'		=> 'font',
		'std'			=> 'none'
	),
	array(
		'id' 			=> "{$prefix}menu-items-text-align",
		'name'			=> esc_html__( 'Text Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_align(),
		'accordion'		=> 'font',
		'std'			=> 'center'
	),
	array(
		'id' 			=> "{$prefix}menu-items-vertical-align",
		'name'			=> esc_html__( 'Vertical Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_vpositions(),
		'accordion'		=> 'font',
		'std'			=> 'top'
	),
		
	// COLORS
	array(
		'id' 			=> "{$prefix}menu-items-font-color",
		'name'			=> esc_html__( 'Font Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(255, 255, 255, 0.9)'
	),
	array(
		'id' 			=> "{$prefix}menu-items-bg-color",
		'name'			=> esc_html__( 'Background Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}menu-items-active-font-color",
		'name'			=> esc_html__( 'Active Text Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> '#ffffff'
	),
	array(
		'id' 			=> "{$prefix}menu-items-active-bg-color",
		'name'			=> esc_html__( 'Active Background Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(0,0,0,0.1)'
	),	
	array(
		'id' 			=> "{$prefix}menu-items-hover-font-color",
		'name'			=> esc_html__( 'Hover Text Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> '#ffffff'
	),
	array(
		'id' 			=> "{$prefix}menu-items-hover-bg-color",
		'name'			=> esc_html__( 'Hover Background Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'colors',
		'std'			=> 'rgba(0,0,0,0.1)'
	),	

	// SPACING
	array(
		'id' 			=> "{$prefix}menu-items-padding",
		'name'			=> esc_html__( 'Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> '15px 50px'
	),	
	array(
		'id' 			=> "{$prefix}menu-items-margin",
		'name'			=> esc_html__( 'Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),
	
	// BORDER
	SM_RWMB_EXTEND_Groups::boxBorder(array(
		'id' 			=> "{$prefix}menu-items-border",
		'name'			=> '',
		'toggle'		=> false,
		'accordion'		=> 'border',
		'std' 			=> ''
	)),		
	 	
	 	
	// ICONS	 	
	array(
		'id' 			=> "{$prefix}menu-items-icon-size",
		'name'			=> esc_html__( 'Icon Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> '25px'
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-line-height",
		'name'			=> esc_html__( 'Icon Line Height', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-width",
		'name'			=> esc_html__( 'Icon Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter width in (px). Sometimes icons have irregular sizes, you might need to adjust the icon width to make sure that menu item titles are aligned properly', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'icons',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-halign",
		'name'			=> esc_html__( 'Icon Honrizontal Align', 'slick-menu' ),
		'desc'			=> esc_html__( 'Only useful if you have set an icon width', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'icons',
		'std'			=> 'left',
		'visible'		=> array("{$prefix}menu-items-icon-width", '!=', '')
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-valign",
		'name'			=> esc_html__( 'Icon Vertical Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_vpositions(),
		'accordion'		=> 'icons',
		'std'			=> 'middle'
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-color",
		'name'			=> esc_html__( 'Icon Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'icons',
		'std'			=> 'rgba(255, 255, 255, 0.9)'
	),
	array(
		'id' 			=> "{$prefix}menu-items-icon-hover-color",
		'name'			=> esc_html__( 'Icon Hover Color', 'slick-menu' ),
		'desc'			=> '',
		'type'			=> 'rgba',
		'accordion'		=> 'icons',
		'std'			=> '#ffffff'
	),
	
	// ARROWS
	array(
		'id' 			=> "{$prefix}menu-items-arrow-icon",
		'name'			=> esc_html__( 'Arrow Icon', 'slick-menu' ),
		'desc'			=> esc_html__( 'Set an arrow icon', 'slick-menu' ),
		'type'			=> 'icon_picker',
		'accordion'		=> 'arrows',
		'std'			=> array(
			'type' 		=> 'fa',
			'icon' 		=> 'fa-angle-right'
		)
	),	
	array(
		'id' 			=> "{$prefix}menu-items-arrow-hide",
		'name'			=> esc_html__( 'Arrow Hide', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'accordion'		=> 'arrows',
		'std'			=> '0'
	),
	array(
		'id' 			=> "{$prefix}menu-items-arrow-position",
		'name'			=> esc_html__( 'Arrow Position', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_hpositions(true),
		'accordion'		=> 'arrows',
		'std'			=> 'right'
	),
	array(
		'id' 			=> "{$prefix}menu-items-arrow-size",
		'name'			=> esc_html__( 'Arrow Size', 'slick-menu' ),
		'desc'			=> esc_html__( 'Enter size in (%, px)', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'arrows',
		'std'			=> '22px'
	),
	array(
		'id' 			=> "{$prefix}menu-items-arrow-hoffset",
		'name'			=> esc_html__( 'Arrow Horizontal Offset', 'slick-menu' ),
        'type' 			=> 'slider',
		'suffix'		=> 'px',
		'js_options' 	=> array(
			'min'  => -50,
			'max'  	=> 50,
			'step' 	=> 1,
		),
		'accordion'		=> 'arrows',
        'std'			=> 0
    ),
	array(
		'id' 			=> "{$prefix}menu-items-arrow-voffset",
		'name'			=> esc_html__( 'Arrow Vertical Offset', 'slick-menu' ),
        'type' 			=> 'slider',
        'suffix'		=> 'px',
		'js_options' 	=> array(
			'min'  => -50,
			'max'  	=> 50,
			'step' 	=> 1,
		),
		'accordion'		=> 'arrows',
        'std'			=> 0
    ),
	array(
		'id' 			=> "{$prefix}menu-items-arrow-color",
		'name'			=> esc_html__( 'Arrow Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'arrows',
		'std'			=> 'rgba(255,255,255,0.6)'
	),
	array(
		'id' 			=> "{$prefix}menu-items-hover-arrow-color",
		'name'			=> esc_html__( 'Hover Arrow Color', 'slick-menu' ),
		'type'			=> 'rgba',
		'accordion'		=> 'arrows',
		'std'			=> 'rgba(255,255,255,0.6)'
	),

	// THUMB
	
	array(
		'id' 			=> "{$prefix}menu-items-thumb-general-settings",
		'name'			=> esc_html__( 'Image General', 'slick-menu' ),
		'type'			=> 'heading',
		'accordion'		=> 'image',
	),
	
	array(
		'id' 			=> "{$prefix}menu-items-thumb-position",
		'name'			=> esc_html__( 'Thumbnail Position', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> array(
			'above'		=>	esc_html__('Above Title', 'slick-menu'),
			'below'		=>	esc_html__('Below Title', 'slick-menu'),
			'behind'	=>	esc_html__('Behind Title', 'slick-menu'),
			'replace'	=>	esc_html__('Replace Title', 'slick-menu'),
		),
		'std' 			=> 'above',
		'accordion'		=> 'image',
	),
	array(
		'id' 			=> "{$prefix}menu-items-thumb-bg-repeat",
		'name'			=> esc_html__( 'Thumbnail Background Repeat ', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options' => array(
	        'no-repeat' => esc_html__('No Repeat', 'slick-menu'),
	        'repeat' => esc_html__('Repeat All', 'slick-menu'),
	        'repeat-x' => esc_html__('Repeat Horizontally', 'slick-menu'),
	        'repeat-y' => esc_html__('Repeat Vertically', 'slick-menu')
	    ),
		'std' 			=> 'no-repeat',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	),	
	array(
		'id' 			=> "{$prefix}menu-items-thumb-bg-size",
		'name'			=> esc_html__( 'Thumbnail Background Size ', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options' => array(
            'cover' => esc_html__('Cover', 'slick-menu'),
            'contain' => esc_html__('Contain', 'slick-menu')
        ),
		'std' 			=> 'cover',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	),	
	array(
		'id' 			=> "{$prefix}menu-items-thumb-bg-position",
		'name'			=> esc_html__( 'Thumbnail Background Position ', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
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
		'std' 			=> 'center center',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	),	

	array(
		'id' 			=> "{$prefix}menu-items-thumb-size-settings",
		'name'			=> esc_html__( 'Image Size', 'slick-menu' ),
		'type'			=> 'heading',
		'accordion'		=> 'image',
	),
			
	array(
		'id' 			=> "{$prefix}menu-items-thumb-size",
		'name'			=> esc_html__( 'Thumbnail Size', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> Slick_Menu_Image::get_image_sizes_options(),
		'std' 			=> 'medium',
		'accordion'		=> 'image',
	),
	array(
		'id' 			=> "{$prefix}menu-items-thumb-width",
		'name'			=> esc_html__( 'Thumbnail Width', 'slick-menu' ),
		'type'			=> 'number',
		'std' 			=> '',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-size", 'custom')
		)
	),
	array(
		'id' 			=> "{$prefix}menu-items-thumb-height",
		'name'			=> esc_html__( 'Thumbnail Height', 'slick-menu' ),
		'type'			=> 'number',
		'std' 			=> '',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-size", 'custom')
		)
	),
	array(
		'id' 			=> "{$prefix}menu-items-thumb-crop",
		'name'			=> esc_html__( 'Thumbnail Crop', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std' 			=> '0',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-size", 'custom')
		)
	),
	array(
		'id' 			=> "{$prefix}menu-items-thumb-stretch",
		'name'			=> esc_html__( 'Thumbnail Stretch', 'slick-menu' ),
		'desc'			=> esc_html__( 'This will stretch the image to fit it\'s container', 'slick-menu' ),
		'type'			=> 'radio',
		'options'		=> slick_menu_data_get_true_false(),
		'std' 			=> '0',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", '!=', 'behind')
		)
	),

	array(
		'id' 			=> "{$prefix}menu-items-thumb-spacing-settings",
		'name'			=> esc_html__( 'Image Spacing', 'slick-menu' ),
		'type'			=> 'heading',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'not in', array('behind', 'replace')),
		)
	),
			
	array(
		'id' 			=> "{$prefix}menu-items-thumb-margin",
		'name'			=> esc_html__( 'Thumbnail Margin', 'slick-menu' ),
		'desc'			=> esc_html__( 'Margin in (%, px). [top right bottom left] Ex: 20px or 20px auto', 'slick-menu' ),
		'type'			=> 'text',
		'std' 			=> '10px auto',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'not in', array('behind', 'replace')),
		)
	),

	array(
		'id' 			=> "{$prefix}menu-items-thumb-overlay-settings",
		'name'			=> esc_html__( 'Image Overlay', 'slick-menu' ),
		'type'			=> 'heading',
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	),
	
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}menu-items-thumb-bg-overlay",
		'name'			=> esc_html__( 'Thumbnail Background Overlay ', 'slick-menu' ),
		'toggle'		=> false,
		'std' 			=> array(
			'type' => 'color'
		),
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	)), 
	
	SM_RWMB_EXTEND_Groups::overlay(array(
		'id' 			=> "{$prefix}menu-items-thumb-hover-bg-overlay",
		'name'			=> esc_html__( 'Thumbnail Hover Background Overlay ', 'slick-menu' ),
		'toggle'		=> false,
		'std' 			=> array(
			'type' => 'color'
		),
		'accordion'		=> 'image',
		'visible'		=> array(
			array("{$prefix}menu-items-thumb-position", 'behind')
		)
	)), 
	
	array(
		'id' 			=> "{$prefix}menu-items-thumb-filters-settings",
		'name'			=> esc_html__( 'Image Filters', 'slick-menu' ),
		'type'			=> 'heading',
		'accordion'		=> 'image',
	),
	
    array(
		'id' 			=> "{$prefix}menu-items-thumb-filter",
		'name'			=> esc_html__( 'Thumbnail Filter', 'slick-menu' ),
		'desc'			=> esc_html__( 'Apply a filter to the thumbnail', 'slick-menu' ),
		'type'			=> 'select',
		'placeholder'	=> esc_html__( 'Select Filter', 'slick-menu' ),
		'options'		=> slick_menu_data_get_filters(),
		'std'			=> '',
		'accordion'	=>	'image'
	),	
    array(
		'id' 			=> "{$prefix}menu-items-thumb-hover-filter",
		'name'			=> esc_html__( 'Thumbnail Hover Filter', 'slick-menu' ),
		'desc'			=> esc_html__( 'Apply a filter to the thumbnail on hover', 'slick-menu' ),
		'type'			=> 'select',
		'placeholder'	=> esc_html__( 'Select Filter', 'slick-menu' ),
		'options'		=> slick_menu_data_get_filters(false, true),
		'std'			=> '',
		'accordion'	=>	'image'
	),	
							
	// ANIMATIONS
	array(
		'id' 			=> "{$prefix}menu-items-animation",
		'name'			=> esc_html__( 'Animation', 'slick-menu' ),
		'type'			=> 'select_group',
		'class'			=> 'sm-mb-animate-field',
		'options'		=> slick_menu_data_get_animations(),
		'accordion'		=> 'animations',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}menu-items-hover-animation",
		'name'			=> esc_html__( 'Hover Animation', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_hover_animations(),
		'accordion'		=> 'animations',
		'std'			=> 'sm-hover-normal'
	),


	SM_RWMB_EXTEND_Groups::boxShadow(array(
		'id' 			=> "{$prefix}menu-items-shadow",
		'name'			=> esc_html__( 'Shadow', 'slick-menu' ),
		'toggle'		=> true,
		'accordion'		=> 'shadow',
		'std' 			=> ''
	)),
	SM_RWMB_EXTEND_Groups::boxShadow(array(
		'id' 			=> "{$prefix}menu-items-hover-shadow",
		'name'			=> esc_html__( 'Hover Shadow', 'slick-menu' ),
		'toggle'		=> true,
		'accordion'		=> 'shadow',
		'std' 			=> ''
	)),

	SM_RWMB_EXTEND_Groups::transforms(array(
		'id' 			=> "{$prefix}menu-items-inactive-transforms",
		'name'			=> esc_html__( 'Transforms before item is visible', 'slick-menu' ),
		'toggle'		=> true,
		'accordion'		=> 'transforms',
		'std' 			=> '',
		'exclude'		=> array(
			'delay',
			'duration'
		)
	)),				
	SM_RWMB_EXTEND_Groups::transforms(array(
		'id' 			=> "{$prefix}menu-items-transforms",
		'name'			=> esc_html__( 'Transforms after item is visible', 'slick-menu' ),
		'toggle'		=> true,
		'accordion'		=> 'transforms',
		'std' 			=> ''
	)),	
	SM_RWMB_EXTEND_Groups::transforms(array(
		'id' 			=> "{$prefix}menu-items-hover-transforms",
		'name'			=> esc_html__( 'Transforms on item Hover', 'slick-menu' ),
		'toggle'		=> true,
		'accordion'		=> 'transforms',
		'std' 			=> ''
	))	
			
);

