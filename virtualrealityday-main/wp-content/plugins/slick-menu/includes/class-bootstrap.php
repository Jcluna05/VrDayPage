<?php

/**
 * Fired during plugin activation
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Slick_Menu
 * @subpackage Slick_Menu/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Slick_Menu
 * @subpackage Slick_Menu/includes
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class Slick_Menu_Bootstrap
{
    public static  $_instance ;
    public  $plugin ;
    public  $sdk ;
    /**
     * Boot SDK
     *
     * @since    1.0.0
     * @param $plugin
     * @return mixed
     */
    public function __construct( $plugin )
    {
        $this->plugin = $plugin;
        
        if ( !isset( $this->sdk ) ) {
            
            if ( $this->plugin->market !== 'freemius' ) {
                $this->sdk = $this->local__premium_only();
            }
            // Signal that SDK was initiated.
            do_action( 'xt_slickmenu_fs_loaded' );
            $this->include_files();
        }
        
        return $this->sdk;
    }
    
    
    /**
     * Boot Local SDK
     *
     * @since    1.0.0
     * @return mixed
     */
    public function local__premium_only()
    {
        // Include License Checker
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/license/start.php';
        return xt_slickmenu_license_init( $this->plugin );
    }
    
    
    /**
     * Plugin main icon
     *
     * @return string Plugin icon
     */
    public function plugin_icon()
    {
        return dirname( $this->plugin->file ) . '/admin/assets/images/icon.png';
    }
    
    /**
     * Include plugin files
     */
    public function include_files()
    {
        // Load Framework
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/slick-menu-icons/slick-menu-icons.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box/meta-box.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-group/meta-box-group.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-extend/meta-box-extend.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/sm-mb-nav-menu/sm-mb-nav-menu.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/sm-mb-settings-page/sm-mb-settings-page.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'admin/class-admin.php';
        
        if ( is_admin() ) {
            //require_once( plugin_dir_path( $this->plugin->file ) . 'includes/modules/sm-mb-admin-columns/sm-mb-admin-columns.php' );
            require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-tabs/meta-box-tabs.php';
            require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-accordions/meta-box-accordions.php';
            require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-columns/meta-box-columns.php';
            require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-conditional-logic/meta-box-conditional-logic.php';
            require_once plugin_dir_path( $this->plugin->file ) . 'includes/modules/meta-box-include-exclude/meta-box-include-exclude.php';
        }
        
        // Load plugin function files
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/data.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/helpers.php';
        // Load plugin class files
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-core.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-migration.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-language.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-cache.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-pcache.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-nav.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-settings.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-output.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-styles-output.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-menu-list.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/class-walker.php';
        // Load plugin libraries
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/lib/class-styles.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/lib/class-image.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/lib/class-post-type.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/lib/class-extension.php';
        require_once plugin_dir_path( $this->plugin->file ) . 'includes/extensions/extensions.class.php';
    }
    
    /**
     * Main Slick_Menu_Bootstrap Instance
     *
     * Ensures only one instance of Slick_Menu_Bootstrap is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Slick_Menu_Bootstrap()
     * @return Slick_Menu_Bootstrap instance
     */
    public static function boot( $plugin )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $plugin );
        }
        return self::$_instance;
    }

}