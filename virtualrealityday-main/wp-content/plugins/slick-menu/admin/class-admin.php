<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    Slick_Menu
 * @subpackage Slick_Menu/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Slick_Menu
 * @subpackage Slick_Menu/admin
 * @author     XplodedThemes <helpdesk@xplodedthemes.com>
 */
class Slick_Menu_Admin {

    /**
     * The single instance of Slick_Menu_Nav.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      Slick_Menu    core    Core Class
     */
    private $core;

    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   public
     * @var      Slick_Menu_Welcome    welcome    Welcome Class
     */
    public $welcome;

    /**
     * Menu List class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $menus_list = null;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    Slick_Menu    $core    Plugin core class.
     */
    public function __construct( &$core ) {

        $this->core = $core;

        $this->welcome = $this->init_welcome_page();
    }

    function admin_body_class( $classes ) {

        $screen = get_current_screen();

        if(!empty($screen) && strpos($screen->base, $this->core->plugin_slug()) !== false) {
            $classes .= ' '.$this->core->plugin_slug('admin');
        }

        return $classes;
    }

    public function auto_update ( $update, $item ) {
        // Array of plugin slugs to always auto-update
        $plugins = array ($this->core->plugin()->freemium_slug);

        if ( in_array( $item->slug, $plugins ) ) {
            return true; // Always update plugins in this array
        } else {
            return $update; // Else, use the normal API response to decide whether to update or not
        }
    }

    function init_welcome_page() {

        require_once 'welcome/class-welcome.php';

        $sections = array();

        $sections[] = array(
            'id' => 'menus',
            'title' => esc_html__( 'Menus', 'slick-menu' ),
            'show_menu' => true,
            'content' => array(
                'type' => 'function',
                'function' => array($this, 'menus_page')
            ),
            'callback' => array($this, 'menus_screen_option'),
            'order' => -30
        );

        $sections[] = array(
            'id' => 'js-api',
            'title' => esc_html__( 'JS API', 'slick-menu' ),
            'show_menu' => false,
            'content' => array(
                'type' => 'function',
                'function' => 'slick_menu_remote_get_data',
                'args' => array('js-api')
            ),
            'order' => -20
        );

        $sections[] = array(
            'id' => 'changelog',
            'title' => esc_html__( 'Change Log', 'slick-menu' ),
            'show_menu' => false,
            'content' => array(
                'type' => 'changelog',
                'show_refresh' => true
            )
        );

        if($this->core->plugin_market() !== 'freemius') {

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__( 'Support', 'slick-menu' ),
                'show_menu' => true,
                'external' => 'https://xplodedthemes.com/support'
            );

        }else{

            $sections[] = array(
                'id' => 'support',
                'title' => esc_html__( 'Support', 'slick-menu' ),
                'show_menu' => false,
                'redirect' => $this->core->plugin_url('contact')
            );
        }

        $sections[] = array(
            'id' => 'shop',
            'title' => esc_html__( 'Shop', 'slick-menu' ),
            'show_menu' => false,
            'content' => array(
                'type' => 'url',
                'url' => 'http://xplodedthemes.com/api/products.php?format=html&exclude='.$this->core->plugin_slug(),
                'title' => esc_html__( 'Products you might like', 'slick-menu' ),
                'show_refresh' => true,
            ),
            'order' => 89
        );

        if(!$this->core->fs()->is_paying() && $this->core->plugin_market() === 'freemius') {

            $sections[] = array(
                'id' => 'upgrade',
                'title' => esc_html__( 'Upgrade', 'slick-menu' ),
                'show_menu' => false,
                'featured' => true,
                'redirect' => $this->core->fs()->get_upgrade_url(),
                'order' => 99
            );

        }

        $logo = apply_filters('slickmenu_welcome_logo', esc_url( $this->core->assets_url.'images/logo.png' ), $this->core);
        $description = apply_filters('slickmenu_welcome_description', esc_html__('Slick Menu is more than just a menu plugin. It can be used to create unlimited multi level push menus or content sidebars with rich content, multiple style options and animation effects. Every menu level is customizable featuring background colors, images, overlays, patterns, videos, custom fonts and much more.'), $this->core);
        $sections = apply_filters('slickmenu_welcome_sections', $sections, $this->core);

        return new Slick_Menu_Welcome($this->core, $sections, $logo, $description);
    }

    public function menus_screen_option() {

        $this->menus_list = new Slick_Menu_List($this->core);
    }

    public function menus_page() {

        ?>
        <div class="wrap slick-menu-wrap">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">

                    <?php if($this->core->fs()->can_use_premium_code__premium_only()) : ?>

						<?php if(function_exists('Slick_Menu_Import_Export')): ?>
                        	<a href="<?php echo esc_url($this->core->plugin_url('import-export'));?>" class="page-title-action"><?php echo esc_html__('Import Menu', 'slick-menu');?></a>
                        	<?php endif; ?>
                        	<a href="<?php echo esc_url(admin_url('/nav-menus.php?action=edit&menu=0'));?>" class="page-title-action"><?php echo esc_html__('Add New', 'slick-menu');?></a>
						<a href="<?php echo esc_url($this->core->plugin_url('settings'));?>" class="page-title-action"><?php echo esc_html__('Global Settings', 'slick-menu');?></a>
						<form method="post">
						    <?php
						    $this->menus_list->prepare_items();
						    $this->menus_list->display(); ?>
						</form>

                    <?php else: ?>

                        	<?php $this->render_license_needed(); ?>

                    <?php endif; ?>

                </div>
            </div>
            <br class="clear">
        </div>
        <?php
    }

    public function render_license_needed() {

        ?>
        <div class="sm-license-required">
            <?php
            echo sprintf(
                    wp_kses_post(__('Please <a href="%s">Upgrade</a> or <a href="%s">Activate</a> your license to have access to the menus.', 'slick-menu')),
                    esc_url($this->core->fs()->get_upgrade_url()),
                    esc_url($this->core->fs()->get_account_url( false, array( 'activate_license' => 'true' ) ))
                );
            ?>
        </div>
        <?php
    }

    /**
     * Main Slick_Menu_Admin Instance
     *
     * Ensures only one instance of Slick_Menu_Admin is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Slick_Menu()
     * @return Main Slick_Menu_Admin instance
     */
    public static function instance ( $parent ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $parent );
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup () {
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->plugin_version() );
    } // End __wakeup()

}
