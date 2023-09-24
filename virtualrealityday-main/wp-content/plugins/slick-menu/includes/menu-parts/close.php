<?php

// SM CLOSE LINK

$return = "";
if(empty($close_link_hidden)) {

	$return .= Slick_Menu()->do_output_action('before_close_link', $menu_id, $item_id);	
	
	$return .= '<div class="'.esc_attr($close_classes).'">';
	$return .= '	<span'.$close_animation_data.'>';
	$return .= '		<a href="#" title="'.esc_html__('Close', 'slick-menu').'">'.$close_link_icon.'</a>';
	$return .= '	</span>';
	$return .= '</div>';
	
	$return .= Slick_Menu()->do_output_action('after_close_link', $menu_id, $item_id);	
}

return $return;