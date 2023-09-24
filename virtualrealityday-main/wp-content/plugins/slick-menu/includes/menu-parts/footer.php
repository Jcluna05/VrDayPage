<?php

// SM FOOTER

$return  = Slick_Menu()->do_output_action('before_footer', $menu_id, $item_id);
$return .= '<div class="'.esc_attr($footer_classes).'">';

	$return_inner = Slick_Menu()->do_output_action('footer_begin', $menu_id, $item_id);
	
	if(!empty($footer_text)) {
		$return_inner .= '<div class="'.esc_attr($footer_text_classes).'"'.$footer_animation_data.'>'.$footer_text.'</div>';
	}
	
	$return_inner .= Slick_Menu()->do_output_action('footer_end', $menu_id, $item_id);
	
	$return .= $return_inner;
	
$return .= '</div>';
$return .= Slick_Menu()->do_output_action('after_footer', $menu_id, $item_id);

if(empty($return_inner)) {
	$return = '';
}

return $return;