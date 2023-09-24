<?php

$menus = $this->parent->get_menus();
foreach($menus as $menu) {

    $level_width = $this->parent->get_menu_option('level-width', $menu->term_id);
    if(strpos($level_width, "%") !== false) {
        $level_width = intval($level_width).'vw';
        $this->parent->update_menu_option('level-width', $level_width, $menu->term_id);
    }

    $menu_items_height = $this->parent->get_menu_option('menu-items-height', $menu->term_id);
    if(strpos($menu_items_height, "%") !== false) {
        $menu_items_height = intval($menu_items_height).'vh';
        $this->parent->update_menu_option('menu-items-height', $menu_items_height, $menu->term_id);
    }


    $menu_items = wp_get_nav_menu_items( $menu->term_id, array(
        'orderby' => 'menu_order'
    ));

    foreach( $menu_items as $item ) {

        $level_width = $this->parent->get_menu_item_option('level-width', $item->ID);
        if(strpos($level_width, "%") !== false) {
            $level_width = intval($level_width).'vw';
            $this->parent->update_menu_item_option('level-width', $level_width, $item->ID);
        }

        $menu_items_height = $this->parent->get_menu_option('submenu-items-height', $item->ID);
        if(strpos($menu_items_height, "%") !== false) {
            $menu_items_height = intval($menu_items_height).'vh';
            $this->parent->update_menu_option('submenu-items-height', $menu_items_height, $item->ID);
        }

        $menu_items_height = $this->parent->get_menu_option('menu-item-height', $item->ID);
        if(strpos($menu_items_height, "%") !== false) {
            $menu_items_height = intval($menu_items_height).'vh';
            $this->parent->update_menu_option('menu-item-height', $menu_items_height, $item->ID);
        }

    }
}