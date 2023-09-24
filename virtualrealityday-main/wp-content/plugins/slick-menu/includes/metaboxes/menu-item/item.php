<?php

return $this->get_menu_items_fields(
	'level-menu-items', 
	array(
		"{$prefix}menu-items-hide-label",
		"{$prefix}menu-items-arrow-icon",
		"{$prefix}menu-items-hidden"
	), 
	array("menu-items", "menu-item"),
	array("Menu Items", "Menu Item"),
	array(
		
		array(
			'fields' => array(
				array(
					'id' 			=> "{$prefix}menu-item-column",
					'name'			=> esc_html__( 'Menu Item Column', 'slick-menu' ),
					'desc'			=> esc_html__( 'Select a column layout used to display this item.', 'slick-menu' ),
					'type'			=> 'select_group',
					'options'		=> slick_menu_data_get_columns(null, false, true),
					'accordion'		=> 'general',
					'std'			=> ''
				)
			),
			'type' => "last"
		),
		array(
			'fields' => $this->include_metabox_fields('menu-item', 'item-icon'),
			'type' => "before",
			'key' => "{$prefix}menu-items-icon-size"
		),
		array(
			'fields' => array(
				array(
					'id' 			=> "{$prefix}menu-item-thumb",
					'name'			=> esc_html__( 'Menu Item Thumbnail', 'slick-menu' ),
					'type'			=> 'image_advanced',
					'max_file_uploads' => 1,
                    'class'			=> 'sm-rwmb-small-preview',
					'accordion'		=> 'image'
				)				
			),
			'type' => "before",
			'key' => "{$prefix}menu-items-thumb-position"
		),	
	)	
);
