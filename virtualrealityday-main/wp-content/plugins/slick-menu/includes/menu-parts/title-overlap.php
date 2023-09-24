<?php
	
// TITLE OVERLAP
	
$return = "";	
if(!empty($levelAnimation) && $levelAnimation === 'overlap') {

	$return .= '<span class="'.esc_attr($title_overlap_classes).'">';
	$return .= '	<span class="'.$title_wrap_classes.'">';
	
	if(!empty($item_id)) {
		$return .= $menu_item_icon.' <span>'.esc_attr($item->title) . '</span>';
	}else{
		$return .= $global_icon.' <span>'.$nav_menu->name.'</span>';
	}
	
	$return .= '	</span>';
	$return .= '</span>';
}
return $return;
										