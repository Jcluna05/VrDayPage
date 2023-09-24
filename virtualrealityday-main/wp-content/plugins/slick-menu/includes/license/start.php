<?php

if ( ! class_exists( 'Slick_Menu_License_Manager' ) ) {

    require_once plugin_dir_path( __FILE__ ) . 'includes/class-license-manager.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/plugin-update-checker/plugin-update-checker.php';
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-plugin-updater.php';

    /**
     * @param array <string,string> $module Plugin or Theme details.
     *
     * @return Slick_Menu_License_Manager
     * @throws Exception
     */
    function xt_slickmenu_license_init( $plugin ) {

        $license_manager = Slick_Menu_License_Manager::instance( $plugin );

        if(is_admin()) {

            new Slick_Menu_Plugin_Updater($plugin->file, $plugin->premium_slug, $plugin->market, $license_manager->license);
        }

        return $license_manager;
    }

}