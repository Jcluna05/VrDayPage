<?php
 
// SM TITLE

$return  = Slick_Menu()->do_output_action('before_title', $menu_id, $item_id);

$return .= '<div class="'.esc_attr($title_classes).'">';
$return .= '	<span class="'.esc_attr($title_wrap_classes).'"'.$title_animation_data.'>';

	if(!empty($item_id)) {
		
		if($show_title_icon) {
			$return .= 	$menu_item_icon;
		}
		
		$title = $item->title;
		if(!empty($title_override)) {
			$title = $title_override;
		}
		
		$return .= 	' <span>'.esc_attr($title) . '</span>';
		
	}else{
		
		if($show_title_icon) {
			$return .= 	$global_icon;
		}
		
		$title = $nav_menu->name;
		if(!empty($title_override)) {
			$title = $title_override;
		}
		
		$return .= 	' <span>'.$title.'</span>';
		
	}

	// SM SUB TITLE
	if(!empty($subtitle_enabled) && !empty($subtitle)) {
		
		$return .= Slick_Menu()->do_output_action('before_subtitle', $menu_id, $item_id);
		
		$return .= '<span class="'.esc_attr($subtitle_classes).'">';
		$return .= '	<span class="'.esc_attr($subtitle_wrap_classes).'"'.$subtitle_animation_data.'>';
		$return .= '		<span>'.$subtitle.'</span>';
		$return .= '	</span>';
		$return .= '</span>';
		
		$return .= Slick_Menu()->do_output_action('after_subtitle', $menu_id, $item_id);
	}
		
$return .= '	</span>';					
$return .= '</div>';

$return .= Slick_Menu()->do_output_action('after_title', $menu_id, $item_id);

return $return;