<?php

return $this->get_menu_items_fields(
	$id, 
	null,
	null,
	null,
	array(
	
		array(
			'fields' => array(
				array(
					'id' 			=> "{$prefix}level-menu-columns",
					'name'			=> esc_html__( 'Menu Items Columns', 'slick-menu' ),
					'desc'			=> esc_html__( 'Select a default column layout used to display the items. This can be overridden within individual items.', 'slick-menu' ),
					'type'			=> 'select',
					'options'		=> slick_menu_data_get_columns(null, true),
					'accordion'		=> 'general',
					'std'			=> ''
				),
			),
			'type' => "replace",
			'key' => "{$prefix}level-menu-columns"
		),		
	)	
);

