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
					'id' 			=> "{$prefix}logo-hidden",
					'name'			=> esc_html__( 'Logo Hidden', 'slick-menu' ),
					'type'			=> 'radio',
					'accordion'		=> 'general',
					'options'		=> slick_menu_data_get_true_false(),
				),
			),
			'type' => "first"
		)
	)		
);

