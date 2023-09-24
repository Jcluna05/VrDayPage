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
					'id' 			=> "{$prefix}level-show-empty",
					'name'			=> esc_html__( 'Show Empty Sub Level', 'slick-menu' ),
					'desc'			=> esc_html__( 'Show sub level even if the menu item has no sub items. Useful to show the level background image or video without having to add sub menu items', 'slick-menu' ),
					'type'			=> 'radio',
					'accordion'		=> 'general',
					'options'		=> slick_menu_data_get_true_false(),
				),
			),
			'type' => "first"
		)		
	)
);
