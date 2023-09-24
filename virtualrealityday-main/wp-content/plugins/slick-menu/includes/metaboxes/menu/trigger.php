<?php

$info  = '<div class="sm-trigger-info">';
$info .= '<div class="sm-rwmb-label"><label for="slick-menu-menu-trigger-class">'.esc_html__( 'There is more than one way to trigger a menu.', 'slick-menu' ).'</label></div>';
$info .= '<div class="sm-rwmb-field">';
$info .= '	<ol>';
$info .='		<li>'.sprintf(esc_html__( 'Hamburger Trigger - Assign a Hamburger to any of your Slick Menus within the %s. Note that only 2 menus can be assigned to be triggered via a Hamburger Trigger. (Top Left or Top Right)', 'slick-menu' ), '<a target="_blank" href="'.$this->plugin_url('settings').'#tab-hamburgers">Global Settings</a>').'</li>';
$info .='		<li>'.esc_html__( 'Menu to Menu trigger - Enable a trigger within any of your other theme menus', 'slick-menu' ).'</li>';
$info .='		<li>'.esc_html__( 'Custom Trigger - Insert the trigger class below to any of your html elements and it will become a trigger for this menu. ', 'slick-menu' ).'</li>';
$info .='		<li>'.esc_html__( 'Via JS API - Use the API to open / close or toggle a menu', 'slick-menu' ).'</li>';
$info .= '	</ol>';
$info .= '</div>';
$info .= '</div>';

return array(
	array(
		'id' 		=> "{$prefix}menu-trigger-info",
		'type' 		=> 'custom-html',
		'std' 		=> $info,
	),
	array(
		'id' 		=> "{$prefix}menu-trigger-class",
		'name' 		=> esc_html__( 'Trigger Class', 'slick-menu' ),
		'desc' 		=> esc_html__( 'Adding this class to any link or button will make it act as a toggle and trigger for this menu.', 'slick-menu' ),
		'type' 		=> 'custom-html',
		'std' 		=> $this->get_trigger_selector(null, false)
	),
	array(
		'id' 		=> "{$prefix}menu-trigger-custom-selector",
		'name' 		=> esc_html__( 'Trigger Custom Selector(s)', 'slick-menu' ),
		'desc' 		=> esc_html__( 'Are you using a theme that already has a push menu trigger ? Insert the trigger selector here to override it\'s behaviour and act as a slick menu trigger instead. You can insert many selectors separated by a comma. Note: Within the customizer, this setting requires a page reload to take effect.', 'slick-menu' ),
		'type' 		=> 'text',
		'std' 		=> ''
	),	
	array(
		'id' 		=> "{$prefix}menu-api-toggle",
		'name' 		=> esc_html__( 'API Toggle JS Function', 'slick-menu' ),
		'desc' 		=> esc_html__( 'This javascript function will act as a trigger and toggle the menu.', 'slick-menu' ),
		'type' 		=> 'custom-html',
		'std' 		=> $this->get_api_function(null, 'toggle', false)
	),
	array(
		'id' 		=> "{$prefix}menu-api-open",
		'name' 		=> esc_html__( 'API Open JS Function', 'slick-menu' ),
		'desc' 		=> esc_html__( 'This javascript function will act as a trigger and open the menu.', 'slick-menu' ),
		'type' 		=> 'custom-html',
		'std' 		=> $this->get_api_function(null, 'open', false)
	),
	array(
		'id' 		=> "{$prefix}menu-api-open",
		'name' 		=> esc_html__( 'API Close JS Function', 'slick-menu' ),
		'desc' 		=> esc_html__( 'This javascript function will act as a trigger close the menu.', 'slick-menu' ),
		'type' 		=> 'custom-html',
		'std' 		=> $this->get_api_function(null, 'close', false)
	),
	array(
		'id' 		=> "{$prefix}menu-api-full",
		'name' 		=> esc_html__( 'Full API Docs', 'slick-menu' ),
		'type' 		=> 'custom-html',
		'std' 		=> '<a target="_blank" href="'.esc_url($this->backend()->welcome->get_section_url('api')).'">'.esc_html__('API Documentation', 'slick-menu').'</a>'
	),
);