<?php

return $this->get_menu_items_fields(
	$id, 
	array(
		"{$prefix}menu-items-hide-label",
		"{$prefix}menu-items-arrow-icon"
	), 
	array("menu-items", "submenu-items")	
);

