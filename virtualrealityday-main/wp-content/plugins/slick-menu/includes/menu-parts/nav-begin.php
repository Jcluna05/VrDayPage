<?php

// SM NAV BEGIN

$return  = Slick_Menu()->do_output_action('before_nav_menu', $menu_id, $item_id);
$return .= '<ul class="'.esc_attr($nav_classes).'">';

return $return;