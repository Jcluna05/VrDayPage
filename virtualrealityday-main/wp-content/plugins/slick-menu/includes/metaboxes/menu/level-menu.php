<?php

return array(

	// GENERAL
	array(
		'id' 			=> "{$prefix}level-menu-width",
		'name'			=> esc_html__( 'Nav Menu Max Width', 'slick-menu' ),
		'desc'			=> esc_html__( 'Width in (%, px). Ex: 50% or 200px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'general',
		'std'			=> ''
	),
	array(
		'id' 			=> "{$prefix}level-menu-align",
		'name'			=> esc_html__( 'Nav Menu Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'center'
	),
	
	array(
		'id' 			=> "{$prefix}level-menu-columns",
		'name'			=> esc_html__( 'Nav Menu Items Columns', 'slick-menu' ),
		'desc'			=> esc_html__( 'Select a default column layout used to display the items. This can be overridden within individual items.', 'slick-menu' ),
		'type'			=> 'select',
		'options'		=> slick_menu_data_get_columns(null, true),
		'accordion'		=> 'general',
		'std'			=> '1-1'
	),			
	array(
		'id' 			=> "{$prefix}level-menu-column-align",
		'name'			=> esc_html__( 'Nav Menu Items Column Align', 'slick-menu' ),
		'type'			=> 'radio',
		'inline'		=> false,
		'options'		=> slick_menu_data_get_hpositions(),
		'accordion'		=> 'general',
		'std'			=> 'center'
	),

	
	// SPACING
	array(
		'id' 			=> "{$prefix}level-menu-padding",
		'name'			=> esc_html__( 'Nav Menu Padding', 'slick-menu' ),
		'desc'			=> esc_html__( 'Padding in (%, px). [top right bottom left] Ex: 20px or 20px 10px', 'slick-menu' ),
		'type'			=> 'text',
		'accordion'		=> 'spacing',
		'std'			=> ''
	),				
);

