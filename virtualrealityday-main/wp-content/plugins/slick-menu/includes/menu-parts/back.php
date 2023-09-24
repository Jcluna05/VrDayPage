<?PHP

// SM BACK

$return = "";

if(empty($back_link_hidden)) {
	
	$return .= Slick_Menu()->do_output_action('before_back_link', $menu_id, $item_id);	
	$return .= '<span class="'.esc_attr($back_classes).'">';
	$return .= '	<a href="#" title="'.esc_html__('Back', 'slick-menu').'">';
	
	if(!$back_link_icon_only) {
		
		if($back_link_icon_position === 'before' || $back_link_icon_position === 'above') {
			
			$return .= $back_link_icon;
		}
		
		$return .= '<span>'.esc_html__('Back', 'slick-menu').'</span>';
		
		if($back_link_icon_position === 'after' || $back_link_icon_position === 'below') {
			
			$return .= $back_link_icon;
		}
			
	}else{
		
		$return .= $back_link_icon;
	}	
	
	$return .= '	</a>';
	$return .= '</span>';
	$return .= Slick_Menu()->do_output_action('after_back_link', $menu_id, $item_id);
}

return $return;