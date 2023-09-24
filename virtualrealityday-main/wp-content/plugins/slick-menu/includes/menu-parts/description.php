<?php
 
// SM DESCRIPTION

$return = "";

if($description_enabled && !empty($description)) {
	
	$return  = Slick_Menu()->do_output_action('before_description', $menu_id, $item_id);
	
	$return .= '<div class="'.esc_attr($description_classes).'">';
	$return .= '	<div class="'.esc_attr($description_wrap_classes).'"'.$description_animation_data.'>';
		
	$return .= 		$description;
	
	$return .= '	</div>';					
	$return .= '</div>';
	
	$return .= Slick_Menu()->do_output_action('after_description', $menu_id, $item_id);

}

return $return;