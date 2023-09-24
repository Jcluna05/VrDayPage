<?php
	
if ( ! defined( 'ABSPATH' ) ) exit;


if(!defined('XT_REPO_URL')) {
	define('XT_REPO_URL', 'https://repo.xplodedthemes.com');
}

class Slick_Menu_Extensions {

    /**
     * The single instance of Slick_Menu_Nav.
     * @var 	object
     * @access  private
     * @since 	1.0.0
     */
    private static $_instance = null;

    /**
     * Parent class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      Slick_Menu    parent    Parent Class
     */
    private $parent;

	public $parent_id;
	public $parent_name;
	public $menu_parent;
	public $menu_slug;
	public $path;
	public $url;
	public $is_theme;

	public function __construct ($instance, $apiOnly = false) {
		
		$this->parent = $instance;
		$this->parent_name = $instance->plugin_name();
		$this->parent_id = $instance->plugin_slug();
		$this->menu_parent = $instance->plugin_slug();
		$this->menu_slug = $instance->plugin_slug('extensions');
		$this->path = $instance->plugin_dir().'/includes/extensions';
		$this->url = $instance->url.'/includes/extensions';
		$this->is_theme = false;

        if(!apply_filters('slick_menu_extensions_enabled', '__return_true')) {
            return $this;
        }

        if($apiOnly) {
			return $this;
		}
		
		require_once('tgmpa.class.php');
		
		$this->tgmp()->parent_slug = $this->menu_parent;
		$this->tgmp()->menu = $this->menu_slug;
				
		if($this->is_admin_page()) {
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'), 100);
			add_filter('admin_body_class', array($this, 'admin_body_class' ));
		}
			
		add_action('slick_menu_tgmpa_register', array($this, 'register_required_plugins'));
        add_filter('slick_menu_tgmpa_admin_menu_args', array($this, 'admin_menu_args'), 10, 1);
        add_filter('slickmenu_welcome_sections', array($this, 'welcome_section_extensions'), 1, 1);
        add_action('admin_footer', array($this, 'update_notice'), 10);
		add_action('admin_notices', array($this, 'new_extension_notice' ), 10);

		return $this;
	}

	public function enqueue_admin_styles() {
	
        wp_enqueue_style($this->parent_id.'-extensions', $this->url.'/css/extensions.css', false, '', 'all' );
	
	}

	public function admin_body_class( $classes ) {
		
		$classes .= ' xt-extensions';
		$classes .= ' '.$this->menu_slug;
		
	    return $classes;
	}


	/*
	 * Register recommended plugins for this theme.
	 */
	
	public function register_required_plugins ()
	{

		$plugins = $this->get_plugins();
		
		$config = array(
			'domain'       => 'slick-menu',
			'has_notices'  => true, // Show admin notices or not
			'is_automatic' => true // Automatically activate plugins after installation or not
		);
	
		slick_menu_tgmpa($plugins, $config);
	
	}

	public function tgmp() {
		
		if(empty($GLOBALS['slick_menu_tgmpa'])) {
			$GLOBALS['slick_menu_tgmpa'] = SM_TGM_Plugin_Activation::get_instance();
		}
		
		return $GLOBALS['slick_menu_tgmpa'];
	}

	public function admin_menu_args() {

        $args['parent_slug'] = null;
        $args['page_title'] = null;
        $args['menu_title'] = null;
        $args['menu_slug'] = null;
        $args['capability'] = null;
        $args['function'] = null;

        return $args;
    }

    public function welcome_section_extensions($sections) {

        $sections[] = array(
            'id' => 'extensions',
            'title' => esc_html__('Extensions', 'slick-menu'),
            'show_menu' => true,
            'action_link' => true,
            'content' => array(
                'title' => $this->parent_name.' ' .esc_html__('Extensions', 'slick-menu'),
                'type' => 'function',
                'function' => array($this, 'addons_page_extensions'),
            ),
            'order' => -10
        );

        return $sections;
    }

	public function addons_page_extensions() { 
    ?>

    <header class="xt-addons-header">
        <p class="xt-addons-desc">Extend the functionality of <?php echo $this->parent_name;?> to suit your needs via the plugins available below.</p>

        <?php
        $plugin_table = new SM_TGMPA_List_Table;

        // Return early if processing a plugin installation action.
        if ( ( ( 'slick-menu-tgmpa-bulk-install' === $plugin_table->current_action() || 'slick-menu-tgmpa-bulk-update' === $plugin_table->current_action() ) && $plugin_table->process_bulk_actions() ) || $this->tgmp()->do_plugin_install() ) {
            return;
        }

        // Force refresh of available plugin information so we'll know about manual updates/deletes.
        wp_clean_plugins_cache( false );
        ?>

        <?php $plugin_table->prepare_items(); ?>


        <?php
        $messages = '';
        if ( ! empty( $this->tgmp()->message ) && is_string( $this->tgmp()->message ) ) {
            $messages =  wp_kses_post( $this->tgmp()->message );
        }
        ?>
        <div class="xt-extensions-messages"><?php echo $messages;?></div>

    </header>


    <form id="slick-menu-tgmpa-plugins" action="" method="post">
    <input type="hidden" name="slick-menu-tgmpa-page" value="<?php echo esc_attr( $this->tgmp()->menu ); ?>" />
    <input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
    <?php wp_nonce_field( 'bulk-' . $plugin_table->_args['plural'] ); ?>

    <?php
    $plugins = $plugin_table->items;
    $update_plugins = array();
    $active_plugins = array();
    $inactive_plugins = array();
    $uninstalled_plugins = array();


    foreach ( $plugins as $plugin ) {

        if ( $this->plugin_exists( $plugin['file_path'] ) ) {


            if($this->tgmp()->does_plugin_require_update($plugin['slug'])) {

                $plugin['status'] = 'update-needed';
                $plugin['status_msg'] = 'Update Needed';
                $update_plugins[] = $plugin;

            }else{

                if ( is_plugin_active( $plugin['file_path'] ) ) {

                    $plugin['status'] = 'active';
                    $plugin['status_msg'] = 'Active';
                    $active_plugins[] = $plugin;

                } else  {

                    $plugin['status'] = 'inactive';
                    $plugin['status_msg'] = 'Inactive';
                    $inactive_plugins[] = $plugin;
                }

            }

        }else{

            $plugin['status'] = 'not-installed';
            $plugin['status_msg'] = 'Not Installed';
            $uninstalled_plugins[] = $plugin;

        }

    }

    ?>
    <div class="xt-addons-extensions-list-wrap">

        <div class="xt-addons-bulk-actions">
            <label class="screen-reader-text" for="cb-select-all"><?php echo esc_html__( 'Select All', 'slick-menu' );?></label>
            <input id="cb-select-all" type="checkbox">
            <select name="action">

                <option value="-1"><?php echo esc_html__( 'Bulk Actions', 'slick-menu' );?></option>

                <?php foreach($plugin_table->get_bulk_actions() as $key => $action):?>

                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($action);?></option>

                <?php endforeach; ?>

            </select>
            <input type="submit" id="doaction" class="button action" value="<?php echo esc_html__( 'Apply', 'slick-menu' );?>">
            <input type="button" id="refresh-plugins" onclick="location.href='<?php echo $this->tgmp()->get_slick_menu_tgmpa_url();?>&xt-action=force_plugins';" class="button action" value="<?php echo esc_html__( 'Refresh List', 'slick-menu' );?>">
        </div>

        <?php
        $plugin_table->views();

        $this->plugin_list(esc_html__('Active Extensions', 'slick-menu'), $active_plugins, $plugin_table);
        $this->plugin_list(esc_html__('Inactive Extensions', 'slick-menu'), $inactive_plugins, $plugin_table);
        $this->plugin_list(esc_html__('Update Extensions', 'slick-menu'), $update_plugins, $plugin_table);
        $this->plugin_list(esc_html__('Uninstalled Extensions', 'slick-menu'), $uninstalled_plugins, $plugin_table);
        ?>
        </div>
    </form>
    <script>
    jQuery(document).ready(function($) {

        $('.notice, .error, #message').each(function() {
            $(this).appendTo('.xt-extensions-messages').fadeIn();
        })

        $('.xt-addons-bulk-actions #cb-select-all').on('click', function() {

            $('.xt-addons-extensions-list .checkbox-wrap input').trigger('click');

        });

        $('.xt-addons-extensions-list-wrap .subsubsub').appendTo('.xt-addons-bulk-actions');

    });
    </script>

	<?php }
		
	public function plugin_list($title, $plugins, &$plugin_table) {
		
		if(empty($plugins))
			return false;
			
		add_thickbox();
			
		$id = sanitize_title($title);	
		?>
		
		<h4><?php echo esc_html($title);?></h4>
		<ul class="xt-addons-extensions-list cf <?php echo esc_attr($id);?>" id="xt-addons-extensions-list">
		<?php

		foreach ( $plugins as $plugin ) :
	
			$plugin_data = $this->get_plugin($plugin['slug']);
			
			if($this->tgmp()->is_plugin_installed($plugin['slug'])) {
				$extra_data = get_plugin_data( WP_PLUGIN_DIR.'/'.$plugin['file_path'], false, true );
			}
			
			$actions_data = $this->plugin_actions($plugin);
			$actions_urls = $actions_data['urls'];
			$actions_links = $actions_data['links'];
			$actions_ids = $actions_data['ids'];
			
			$status         = $plugin['status'];
			$status_message = $plugin['status_msg'];
	        ?>
	
	        <li data-slug="<?php echo esc_attr($plugin['slug']);?>" data-actions="<?php echo htmlentities(json_encode($actions_ids), ENT_QUOTES, 'UTF-8'); ?>" class="xt-addons-extension-wrap <?php echo esc_attr($status); ?>" id="<?php echo esc_attr($plugin['slug']); ?>">
		        <div class="xt-addons-extension">
					<?php if($this->is_new_extension($plugin_data)): ?>
						<span class="xt-new-extension">New</span>
					<?php endif; ?>
		          	<div class="top cf">
						<img src="<?php echo XT_REPO_URL . '/logos/'.$plugin['slug'].'.png'; ?>" class="img">
						<div class="info">
							<h4 class="title"><?php echo esc_html($plugin['name']); ?></h4>
							
							<?php if($this->tgmp()->is_plugin_installed($plugin['slug']) && !empty($extra_data['Description'])) : ?>
							<p class="author"><cite>By <?php echo esc_html($extra_data['Author']); ?></cite></p>
							<p class="desc"><?php echo esc_html($extra_data['Description']); ?></p>
							<?php endif; ?>
							
							<div class="statuses">
								<?php if(!empty($plugin['installed_version'])): ?>
								<span class="version status">v.<?php echo esc_html($plugin['installed_version']); ?></span> 
								<?php endif; ?>
								<span class="status <?php echo esc_attr($status); ?>"><?php echo esc_html($status_message); ?></span>
							</div>
	
		            	</div>
		          </div>
		          <div class="bottom cf">

                        <span class="checkbox-wrap"><input type="checkbox" name="plugin[]" value="<?php echo esc_attr($plugin['slug']);?>"></span>
                        <?php
                        echo implode('', $actions_links);
                        ?>
			      </div>
		        </div>
	        </li>
	
	      <?php endforeach; ?>
	
		</ul>
	
		<?php 
			
		if($this->is_action('activate')) {
			
			if(!empty($_GET['slug'])) {
				
				$slug = $_GET['slug'];
				$plugin = $this->get_plugin($slug);
				$actions = $this->plugin_actions($plugin, 'urls');
				die('<meta http-equiv="refresh" content="0; url='.$actions['activate'].'">');
				exit;
			}
		}	
		
		if($this->is_action('install')) {
			
			if(!empty($_GET['slug'])) {
				
				$slug = $_GET['slug'];
				$plugin = $this->get_plugin($slug);
				$actions = $this->plugin_actions($plugin, 'urls');
				die('<meta http-equiv="refresh" content="0; url='.$actions['install'].'">');
			}
		}	
    
	}	
	
	public function plugin_exists( $plugin ) {
	
	  if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
	    return true;
	  } else {
	    return false;
	  }
	
	}
		
	public function plugin_active( $plugin ) {
	
	  if ( is_plugin_active( WP_PLUGIN_DIR . '/' . $plugin ) ) {
	    return true;
	  } else {
	    return false;
	  }
	
	}
	
	public function plugin_actions($item, $type = null) {
				
		$actions      = array();
		$action_links = array();
		$action_urls = array();
		$action_ids = array();
	
		// Display the 'Install' action link if the plugin is not yet available.
		if ( ! $this->tgmp()->is_plugin_installed( $item['slug'] ) ) {
			
			$actions['install'] = esc_html__( 'Install', 'slick-menu' );
			
		} else {
			
			// Display the 'Update' action link if an update is available and WP complies with plugin minimum.
			if ( false !== $this->tgmp()->does_plugin_have_update( $item['slug'] ) && $this->tgmp()->can_plugin_update( $item['slug'] ) ) {
				$actions['update'] = esc_html__( 'Update to v.', 'slick-menu' ).$item['available_version'];
			}else{
	
				// Display the 'Activate' action link, but only if the plugin meets the minimum version.
				if ( $this->tgmp()->can_plugin_activate( $item['slug'] ) ) {
					$actions['activate'] = esc_html__( 'Activate', 'slick-menu' );
				}else{
					$actions['deactivate'] = esc_html__( 'Deactivate', 'slick-menu' );
				}
			}	
		}
	
		// Create the actual links.
		foreach ( $actions as $action => $text ) {
			
			$class = 'button';
			
			if($action == 'install' || $action == 'update') {
				$class = 'button button-primary '.$action;
			}
			
			$nonce_url = wp_nonce_url(
				add_query_arg(
					array(
						'plugin'           => urlencode( $item['slug'] ),
						'slick-menu-tgmpa-' . $action => $action . '-plugin',
					),
					$this->tgmp()->get_slick_menu_tgmpa_url()
				),
				'slick-menu-tgmpa-' . $action,
				'slick-menu-tgmpa-nonce'
			);
			
			$action_ids[$action] = $action;
			$action_urls[ $action ] = $nonce_url;
			$action_links[ $action ] = sprintf(
				'<a class="'.esc_attr($class).'" href="%1$s">' . esc_html( $text ) . '</a>',
				esc_url( $nonce_url )
			);
		}
		
		$actions = array(
			'links' => array_filter( $action_links ),
			'urls' => array_filter( $action_urls ),
			'ids' => array_filter( $action_ids )
		);
		
		
		if(!empty($type) && !empty($actions[$type])) {
			return $actions[$type];
		}
		
		return $actions;
			
	}

	public function get_plugins() {
		
		$cache_key = $this->parent_id.'_plugins';
		
		if(defined('XT_REPO_DEBUG') && XT_REPO_DEBUG) {
			$cache_key .= '-debug';
		}
	
		if ( false === ( $plugins = get_site_transient( $cache_key ) ) || $this->is_action('force_plugins')) {
			
			$plugins = wp_cache_get( $cache_key );
	
		 	if($plugins === false) {
			 	
				if($this->is_theme) {
					$key = 'theme';
				}else{
					$key = 'p';
				}
				
				$token = md5('xt-'.$this->parent_id);
				$url = XT_REPO_URL.'/?'.$key.'='.$this->parent_id.'&token='.$token.'&market=envato';
				
				if(defined('XT_REPO_DEBUG') && XT_REPO_DEBUG) {
					$url .= '&debug=1';
				}

				$response = wp_remote_get($url);
				if ( !is_wp_error( $response ) ) {
					
					$plugins = wp_remote_retrieve_body($response);
					if(!empty($plugins)) {
						set_site_transient( $cache_key, $plugins, DAY_IN_SECONDS );	
						delete_site_transient($this->parent_id.'_plugins_updates');
						wp_cache_set( $cache_key, $plugins );
					}	
				}
			}	
		}
		$plugins = maybe_unserialize($plugins);
		
		if(!is_array($plugins)) {
			delete_site_transient( $cache_key );
			$plugins = array();
		}

		return $plugins;
	}

	public function get_plugin($slug) {
		
		$plugins = $this->get_plugins();
			
		foreach($plugins as $plugin) {
			
			if($plugin['slug'] == $slug) {
				return $plugin;
			}
		}
		
		return false;
	}

	public function is_action($action) {
		
		return is_admin() && !empty($_GET['xt-action']) && $_GET['xt-action'] == $action;
	}
	
	public function is_admin_page() {

		return $this->tgmp()->is_slick_menu_tgmpa_page();
	}

    public function update_notice() {
	
		$cache_key = $this->parent_id.'_plugins_updates';
		
		if(!empty($_GET['plugin'])) {
			delete_site_transient($cache_key);
			delete_site_transient($this->parent_id.'_plugins');
			return false;
		}
			
		if ( false === ( $update_count = get_site_transient( $cache_key ) ) || !empty($_GET['plugin_status']) || $this->is_action('force-plugins')) {
			
			$update_count = 0;
			
			foreach ( $this->tgmp()->plugins as $slug => $plugin ) {
				if ( $this->tgmp()->is_plugin_active( $slug ) && false === $this->tgmp()->does_plugin_have_update( $slug ) ) {
					continue;
				}
				
				if ( $this->tgmp()->is_plugin_installed( $slug ) ) {
	
					if ( $this->tgmp()->does_plugin_require_update( $slug ) || false !== $this->tgmp()->does_plugin_have_update( $slug ) ) {
	
						if ( current_user_can( 'install_plugins' ) ) {
							
							if ( $this->tgmp()->does_plugin_require_update( $slug ) || ($this->tgmp()->does_plugin_have_update( $slug ) !== false) ) {
								$update_count++;
							}
						}
					}
				}
			}
			set_site_transient( $cache_key, $update_count, 12 * HOUR_IN_SECONDS );	
		}	
		
		if($update_count > 0) {
			?>
			<script>
				(function () {
			
					var menu = document.querySelector('li.toplevel_page_slick-menu a[href="admin.php?page=<?php echo $this->menu_slug;?>"]');
					var menu_parent = menu.parentElement.parentElement.parentElement.querySelector('a .wp-menu-name');
					
					var menus = [
						menu,
						menu_parent
					];
					
					for (var i = 0 ; i < menus.length ; i++) {
						
						var _menu = menus[i];
					
						var pending = _menu.querySelector('.pending-count');
						if(pending) {
							return false;
						}
						
						var span = document.createElement("span"); 
						span.setAttribute('class', "awaiting-mod count-<?php echo $update_count; ?>");
						
						var inner_span = document.createElement("span"); 
						inner_span.setAttribute('class', "pending-count");
						
						var count = document.createTextNode(<?php echo $update_count; ?>);
						var space = document.createTextNode(' ');
						
						inner_span.appendChild(count);
						span.appendChild(inner_span);
						_menu.appendChild(space);
						_menu.appendChild(span);
					
					}

				}());
			</script>
			<?php

		}	
	}	

	public function new_extension_notice() {
		
		if(!empty($_GET['xt_ext_dismiss_notice'])) {
			
			$notice_id = esc_attr($_GET['xt_ext_dismiss_notice']);
			$this->update_dismissed_notices($notice_id);
		}
		
		foreach ( $this->tgmp()->plugins as $slug => $plugin ) {
			
			$notice_id = 'new_'.$plugin['slug'];
	
			if($this->is_new_extension($plugin) && !$this->is_dismissed_notices($notice_id)):?>
			
			<div class="xt-notice notice notice-warning is-dismissible">
		        <p>
			    <?php 
				echo sprintf(
					__('New <strong>Slick Menu Extension</strong> now available: <strong><a href="%1$s">%2$s</a></strong>', 'slick-menu'), 
					$this->tgmp()->get_slick_menu_tgmpa_url().'&xt_ext_dismiss_notice='.$notice_id,
					$plugin['name']
				);
				?>
				</p>
		    </div>
	    
			<?php
			endif;
		}
	}
	
	public function get_dismissed_notices() {
		
		return get_site_option('xt-extensions-dismissed-notices', array(), false);
	}
	
	public function is_dismissed_notices($notice_id) {
		
		$notices = $this->get_dismissed_notices();
		return !empty($notices[$notice_id]);
	}
	
	public function update_dismissed_notices($notice_id) {
		
		$notices = $this->get_dismissed_notices();
		$notices[$notice_id] = $notice_id;
		
		return update_site_option('xt-extensions-dismissed-notices', $notices);
	}
		
	public function is_new_extension($plugin) {

		if(empty($plugin['date'])){
			return false;
		}
		
		$date = $plugin['date'];
		if(strtotime($date) >= strtotime('-30 days')) {
			return true;
		}
		
		return false;
	}
	
	public function is_slickmenu_site() {
		
		if(defined('SM_DEBUG_EXTENSIONS') && SM_DEBUG_EXTENSIONS) {
			return false;
		}
		return strpos($_SERVER["HTTP_HOST"], 'slickmenu.net') !== false || strpos($_SERVER["HTTP_HOST"], 'slickmenu.test') !== false;
	}

    /**
     * Main Slick_Menu_Extensions Instance
     *
     * Ensures only one instance of Slick_Menu_Extensions is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Slick_Menu()
     * @return Slick_Menu_Extensions instance
     */
    public static function instance ( $parent, $apiOnly = false ) {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $parent, $apiOnly );
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
		