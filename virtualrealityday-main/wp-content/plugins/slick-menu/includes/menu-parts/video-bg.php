<?PHP

// SM VIDEO BG

$return = "";	
if(!empty($bgvideo) && !empty($bgvideo["id"])) {
	
	$return .= '<div class="sm-level-bgvideo" data-id="'.esc_attr($bgvideo["id"]).'"';
	
	if(!empty($bgvideo["audio"])) {
		$return .=  ' data-audio="'.esc_attr($bgvideo["audio"]).'"';
	}
	if(!empty($bgvideo["quality"])) {
		$return .=  ' data-quality="'.esc_attr($bgvideo["quality"]).'"';
	}
	if(!empty($bgvideo["scale"])) {
		$return .=  ' data-scale="'.esc_attr($bgvideo["scale"]).'"';
	}
	if(!empty($bgvideo["start"])) {
		$return .=  ' data-start="'.esc_attr($bgvideo["start"]).'"';
	}
	if(!empty($bgvideo["end"])) {
		$return .=  ' data-end="'.esc_attr($bgvideo["end"]).'"';
	}	
	if(!empty($bgvideo["repeat"])) {
		$return .=  ' data-repeat="'.esc_attr($bgvideo["repeat"]).'"';
	}
	if(!empty($bgvideo["nopause"])) {
		$return .=  ' data-nopause="'.esc_attr($bgvideo["nopause"]).'"';
	}
	if(!empty($bgvideo["spinner"])) {
		$return .=  ' data-spinner="'.esc_attr($bgvideo["spinner"]).'"';
	}
	if(!empty($bgvideo["behind-overlay"])) {
		$return .=  ' data-behind-overlay="'.esc_attr($bgvideo["behind-overlay"]).'"';
	}
	if(!empty($bgvideo["mobile"])) {
		$return .=  ' data-mobile="'.esc_attr($bgvideo["mobile"]).'"';
	}
	
	$return .= '></div>';		
}

return $return;