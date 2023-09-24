<?php

// SM NAV END

$return  = '</ul>';
$return .= Slick_Menu()->do_output_action('after_nav_menu', $menu_id, $item_id);

return $return;