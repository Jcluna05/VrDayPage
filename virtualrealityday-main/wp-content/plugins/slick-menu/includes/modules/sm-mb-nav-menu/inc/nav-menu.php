<?php
/**
 * The main class of the plugin which handles show, edit, save meta boxes for nav menu
 * @package    Meta Box
 * @subpackage MB Nav Menu
 * @author     Georges Haddad <prismosoft@gmail.com>
 */

/**
 * Class for handling meta boxes for nav menu
 */
class SM_MB_Nav_Menu
{

	/**
	 * @var array Settings page arguments.
	 */
	public $args;
	
	public $mb_nav_menu_boxes = array();
	public $meta_boxes = array();
	public $saving = false;

	function __construct() {

		if(!is_admin()) {
			return false;
		}
		
		$this->script_suffix = defined( 'SM_SCRIPT_DEBUG' ) && SM_SCRIPT_DEBUG ? '' : '.min';

		add_filter( 'sm_rwmb_meta_boxes', array( $this, 'meta_boxes_filter'), 999 );
				
		add_action( 'sm_mb_nav_menu_ready', array( $this, 'register'), 1 );

		add_action( 'current_screen', array( $this, 'menu_not_found_check'), 1 );

		add_action( 'wp_ajax_sm_mb_nav_menu_load', array( $this, 'menu_load' ), 10 );
		add_action( 'wp_ajax_nopriv_sm_mb_nav_menu_load', array( $this, 'menu_load' ), 10 );
		
		add_action( 'wp_ajax_sm_mb_nav_menu_save', array( $this, 'menu_save' ), 10 );
		add_action( 'wp_ajax_nopriv_sm_mb_nav_menu_save', array( $this, 'menu_save' ), 10 );

		add_action( 'wp_ajax_sm_mb_nav_menu_activation', array( $this, 'ajax_menu_activation' ), 10 );
		add_action( 'wp_ajax_nopriv_sm_mb_nav_menu_activation', array( $this, 'ajax_menu_activation' ), 10 );

		add_action( 'wp_ajax_sm_mb_nav_menu_metabox', array( $this, 'ajax_global_metabox_render' ), 10 );
		add_action( 'wp_ajax_nopriv_sm_mb_nav_menu_metabox', array( $this, 'ajax_global_metabox_render' ), 10 );

		add_filter('sm_rwmb_end_html', array( $this, 'menu_reset_button'), 10, 3);
	}


	/**
	 * Redirect if menu does not exist
	 */
	function menu_not_found_check()
	{
			
		if(!is_customize_preview() && $this->is_edit_screen() && !empty($_GET['menu'])) {
			$menu =  intval( $_GET['menu'] );
			if(!is_nav_menu($menu)) {
				wp_redirect(admin_url('nav-menus.php'));
				exit;	
			}
		}

	}

	/**
	 * Register settings pages
	 */
	function register()
	{

		$nav_menus_args = apply_filters( 'sm_mb_nav_menus', array() );
		$this->args = $this->normalize( $nav_menus_args );
		
		if(!empty($this->mb_nav_menu_boxes) && empty($this->meta_boxes)) {
			foreach (  $this->mb_nav_menu_boxes as $meta_box )
			{
				$this->meta_boxes[] = new SM_MB_Nav_Menu_Meta_Box( $meta_box, $this);
				
			}
		}
		
		
		// Add hooks
		if(is_customize_preview()) {
			
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'enqueue_scripts' ));
			
		}else{
			
			add_action( "admin_print_styles", array( $this, 'enqueue_scripts' ) );
			add_action( 'load-nav-menus.php', array( $this, 'add_help_tabs' ) );
			add_action( 'admin_head-nav-menus.php', array($this, 'global_metabox'));

		}	

	}


	/**
	 * Normalize settings page arguments
	 * @param array $args Settings page arguments
	 * @return array
	 */
	public function normalize( $args )
	{
		$args = wp_parse_args( $args, array(
			'id' 		=> '',
			'title' 	=> 'Custom Settings',
			'help_tabs' => array(),
		));

		return $args;
	}


	
	function global_metabox() {
		
		add_meta_box( 
			$this->args['id'].'-global', 
			$this->args['title'], 
			array($this, 'global_metabox_render'), 
			'nav-menus', 
			'side', 
			'high'
		);
	}

	/**
	 * Displays a menu metabox 
	 *
	 * @param string $object Not used.
	 * @param array $args Parameters and arguments. If you passed custom params to add_meta_box(), 
	 * they will be in $args['args']
	 */
	function global_metabox_render( $object, $args ) {
		
		global $nav_menu_selected_id;
		$this->global_metabox_content($nav_menu_selected_id);
	
	}


	function global_metabox_content($menu_id) {
	 
	 	$not_default_lang = null;
	 	$instance = Slick_Menu();
	 	if($instance->lang->enabled()) {
	
			$default_language = $instance->lang->get_default_language();
			$current_language = $instance->lang->get_current_language();
			if($default_language != $current_language) {
				$not_default_lang = true;
			}		
		}
					
		if(!empty($menu_id)) {
			
			$enabled = get_term_meta($menu_id, apply_filters('sm_mb_nav_menu_enabled_key', 'sm-mb-nav-menu-enabled'), true);

			if($instance->lang->enabled()) {

				if($default_language != $current_language) {
					$menu_id_source = apply_filters('wpml_object_id', $menu_id, 'nav_menu', true, $default_language);
					
					if($menu_id != $menu_id_source) {
						$source_enabled = get_term_meta($menu_id_source, apply_filters('sm_mb_nav_menu_enabled_key', 'sm-mb-nav-menu-enabled'), true);
						if($source_enabled !== $enabled) {
							$this->menu_activation($menu_id, $source_enabled === "yes");
							$enabled = $source_enabled;
						}
					}
				}
			}
			
		}else{
			$enabled = false;
		}
		?>
		<div id="sm-mb-nav-menu-metabox-div" class="sm-mb-nav-menu-metabox-div">
			<div class="sm-mb-nav-menu-metabox-title"><?php echo esc_html__('Slick Menu', 'slick-menu'); ?></div>
			<div class="sm-mb-nav-menu-metabox-content">
				<?php if($enabled === "yes"): ?>
					
					<div class="sm-mb-row">
						<div class="sm-mb-col sm-mb-col-3">
							<a class="sm-mb-button sm-mb-button-primary sm-mb-has-icon sm-mb-nav-menu-launch" data-id="<?php echo esc_attr($menu_id);?>">
								<span class="sm-spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></span>
								<?php echo esc_html__('Menu Settings', 'sm-mb-nav-menu');?>
							</a>
						</div>
						<?php if(empty($not_default_lang)): ?>
						<div class="sm-mb-col sm-mb-col-2">
							<a class="sm-mb-button sm-mb-has-icon sm-mb-nav-menu-disable" data-id="<?php echo esc_attr($menu_id);?>"><?php echo esc_html__('Disable', 'sm-mb-nav-menu');?></a>
						</div>
						<?php endif; ?>
					</div>
					<?php
						$alwaysVisible = (bool)Slick_Menu()->get_menu_option('menu-always-visible', $menu_id);
						$previewVisibleClass = '';
						if(!$alwaysVisible) {
							$previewVisibleClass = ' visible';
						}
					?>
					<div class="sm-mb-row sm-mb-preview-row">	
						<div class="sm-mb-col sm-mb-col-5">
							<a class="sm-mb-button sm-mb-has-icon sm-mb-nav-menu-preview<?php echo esc_attr($previewVisibleClass); ?>" data-id="<?php echo esc_attr($menu_id);?>"><?php echo esc_html__('Toggle Preview', 'sm-mb-nav-menu');?></a>
						</div>
					</div>
					
				<?php else: ?>
					
					<div class="sm-mb-row">
						<div class="sm-mb-col sm-mb-col-5">
							<?php if(empty($not_default_lang)): ?>
								<?php if(!empty($menu_id)): ?>
								<p><a class="sm-mb-button sm-mb-has-icon sm-mb-nav-menu-enable" data-id="<?php echo esc_attr($menu_id);?>"><?php echo esc_html__('Enable', 'sm-mb-nav-menu');?></a></p>
								<p class="description"><?php echo esc_html__('The current menu will automatically become a Slick Menu', 'slick-menu');?></p>
								<?php else: ?>
								<p class="description"><?php echo esc_html__('First create a menu, you will then be able to activate it as a Slick Menu!', 'slick-menu');?></p>
								<?php endif; ?>
							<?php else: ?>
								<p class="description"><?php echo esc_html__('To enable Slick Menu, you need to do so on the menu default language.', 'slick-menu');?></p>
							<?php endif; ?>
						</div>
					</div>
					
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	function ajax_global_metabox_render() {
		
	    $nonce = $_POST['wpnonce'];
	    if ( ! wp_verify_nonce( $nonce, 'sm_mb_nav_menu_metabox' ) ) {
		    
	       	die( 'invalid nonce' );
	       	
	    } else {
		    
			$menu_id = intval($_POST['id']);
			$this->global_metabox_content($menu_id);

	    }
	    die();
	}
		
	function ajax_menu_activation() {
		
	    $nonce = $_POST['wpnonce'];
	    if ( ! wp_verify_nonce( $nonce, 'sm_mb_nav_menu_activation' ) ) {
		    
	       	die( 'invalid nonce' );
	       	
	    } else {

			$menu_id = intval($_POST['id']);
			$enabled = filter_var($_POST['flag'], FILTER_VALIDATE_BOOLEAN);
			
			$this->menu_activation($menu_id, $enabled);
			
			$this->global_metabox_content($menu_id);
	        
	    }
	    die();
	}
	
	function menu_activation($menu_id, $enabled) {
		
		$key = apply_filters('sm_mb_nav_menu_enabled_key', 'sm-mb-nav-menu-enabled');
			
		if($enabled) {
			update_term_meta($menu_id, $key, 'yes');
		}else{
			delete_term_meta($menu_id, $key);
		}

        do_action('sm_mb_nav_menu_activation', $menu_id, $enabled);
	}
	

	/**
	 * Filter meta boxes to prevent them to be added to posts
	 * @param array $meta_boxes
	 * @return array
	 */
	
	 
	function meta_boxes_filter( $meta_boxes )
	{
		$this->mb_nav_menu_boxes = array();
		foreach ( $meta_boxes as $k => $meta_box )
		{
			if ( isset( $meta_box['nav_menus'] ) )
			{
				unset( $meta_box['post_types'], $meta_box['pages'] );
				$this->mb_nav_menu_boxes[$k] = $meta_box;
	
				// Prevent adding meta box to post
				unset( $meta_boxes[$k] );
			}
		}
		
		do_action('sm_mb_nav_menu_ready');
	
		return $meta_boxes;
	}


	function menu_load() {

	    $nonce = $_POST['wpnonce'];
	    if ( ! wp_verify_nonce( $nonce, 'sm_mb_nav_menu_load' ) ) {
	       	die( 'invalid nonce' );
	    }
		    
		if ( empty( $this->meta_boxes ) || ! is_array(  $this->meta_boxes ) ) {
			die( 'No meta boxes found' );
		}	
		
			
		$menu_id =  intval( $_REQUEST['menu_id'] );
		$item_id = intval( $_REQUEST['item_id'] );
		$depth = intval( $_REQUEST['depth'] );
		$target_item = !empty($item_id);
		$customizer = (bool)( $_REQUEST['customizer'] );
		$mp_enabled = get_term_meta($menu_id, apply_filters('sm_mb_nav_menu_enabled_key', 'sm-mb-nav-menu-enabled'), true);

		?>
		<form method="post" action="" enctype="multipart/form-data" id="sm-mb-nav-menu-metaboxes">
			<div class="sm-mb-metabox-wrap">

                <?php if(Slick_Menu()->fs()->can_use_premium_code__premium_only()) : ?>

                    <?php
                    foreach (  $this->meta_boxes as $meta_box )
                    {
                        $meta_box->show_fields();
                    }
                    ?>

                    <input type="hidden" name="action" value="sm_mb_nav_menu_save">
                    <input type="hidden" name="wpnonce" value="<?php echo wp_create_nonce('sm_mb_nav_menu_save');?>">
                    <input type="hidden" name="menu_id" value="<?php echo esc_attr($menu_id);?>">
                    <input type="hidden" name="item_id" value="<?php echo esc_attr($item_id);?>">
                    <input type="hidden" name="depth" value="<?php echo esc_attr($depth);?>">
                    <input type="hidden" name="mp_enabled" value="<?php echo ($mp_enabled == "yes") ? "1" : "0";?>">

                <?php else: ?>

                    <?php Slick_Menu()->backend()->render_license_needed(); ?>

                <?php endif; ?>

            </div>
			<div id="submit" class="submit">
				<a href="#" class="sm-mb-button sm-mb-nav-menu-back">
					<?php echo esc_html__( 'Close', 'sm-mb-nav-menu-item' );?>
				</a>
                <?php if(Slick_Menu()->fs()->can_use_premium_code__premium_only()) : ?>
				    <?php if($customizer): ?>
                    <a href="#" class="sm-mb-button sm-mb-nav-menu-preview">
                        <?php echo esc_html__( 'Preview', 'sm-mb-nav-menu-item' );?>
                    </a>
                    <?php endif; ?>
                    <a href="#" class="sm-mb-button sm-mb-nav-menu-reset">
                        <?php echo esc_html__( 'Reset', 'sm-mb-nav-menu-item' );?>
                    </a>
                    <a href="#" class="sm-mb-button sm-mb-button-primary sm-mb-nav-menu-submit" data-saving="<?php echo esc_attr__( 'Saving...', 'sm-mb-nav-menu-item' );?>" data-saved="<?php echo esc_attr__( 'Saved!', 'sm-mb-nav-menu-item' );?>" data-submitting="<?php echo esc_attr__( 'Save', 'sm-mb-nav-menu-item' );?>">
                        <?php echo esc_html__( 'Save', 'sm-mb-nav-menu-item' );?>
                    </a>
                    <span class="spinner"></span>
                <?php endif; ?>
			</div>
		</form>
		<div class="sm-mb-nav-menu-loading"></div>
		<?php

	    die();
	
	}



	/**
	 * Save menu custom fields
	 *
	 * @access      public
	 * @since       1.0 
	 * @return      void
	*/

	function menu_save() {
	
		$nonce = $_POST['wpnonce'];
	    if ( ! wp_verify_nonce( $nonce, 'sm_mb_nav_menu_save' ) ) {
	       	die( 'invalid nonce' );
	    }
		    	
		if(empty($_POST)) {
			die( 'Nothing to save' );
		}

		if ( empty( $this->meta_boxes ) || ! is_array(  $this->meta_boxes ) ) {
			die( 'No meta boxes found' );
		}

						
		$data = $_POST;
		
		$menu_id =  intval( $data['menu_id'] );
		$item_id = intval( $data['item_id'] );
		$depth = intval( $data['depth'] );
		$target_item = !empty($item_id);
		
		foreach ( $this->meta_boxes as $metabox ) {

			foreach ( $metabox->fields as $field )
			{
				$name = $field['field_name'];
				$name = str_replace("[]", "", $name);
				
				$value = isset( $data[$name] ) ? $data[$name] : ( $field['multiple'] ? array() : '' );
				$value = wp_unslash( $value );
				$value = call_user_func( array( SM_MB_Nav_Menu_Meta_Box::get_class_name( $field ), 'value' ), $value, '', 0, $field );
			
				if($value === 'inherit') {
					if($target_item) {
						delete_post_meta( $item_id, $name );
					}else{
						delete_term_meta( $menu_id, $name );
					}
				}else{
					if($target_item) {
						update_post_meta( $item_id, $name, $value );
					}else{
						update_term_meta( $menu_id, $name, $value );
					}
			    }
			}
		}

		do_action('sm_mb_nav_menu_saved', $menu_id, $item_id, $target_item, $data, $this->args, $this->meta_boxes);
		
		die(json_encode($data));
		   
	}
	
	function menu_reset_button($end, $field, $meta) {
		
		if(empty($_REQUEST['action']) || empty($_REQUEST['menu_id'])) {
			return $end;	
		}	

		$excluded = array('custom-html', 'divider', 'heading', 'button', 'empty');
		
		if(in_array($field['type'], $excluded) || !empty($field['no-reset']) || empty($field["name"]))
			return $end;
			
		$is_menu_item = !empty($_REQUEST['item_id']);
		
		$std = '';
		$type = '';
		
		if($field['type'] != 'group') {
			
			$reset_class = "slick-menu-reset-field";
			
			if(isset($field['std']) && $field['std'] !== '' && !$is_menu_item) {
				
				$std = $field['std'];
				$type = 'string';
				
				if(is_array($std)) {
					
					$std = json_encode($std);
					$std = htmlentities($std, ENT_QUOTES, 'UTF-8');
					$type = 'object';
				}
				
			}else{
				
				if(!empty($meta) && is_array($meta)) {
					
					$std = array();
					foreach($meta as $key => $val) {
						$std[$key] = '';
					}
					
					$std = json_encode($std);
					$std = htmlentities($std, ENT_QUOTES, 'UTF-8');
					$type = 'object';
				}
				
				if($field['type'] == 'autocomplete') {
					$type = 'string';
					$std = '';
				}
			}
			
		}else{
			
			$reset_class = "slick-menu-reset-group";
		}		
		
		$end .= '<div class="clearfix"></div><div class="slick-menu-sm-rwmb-reset"><a id="reset-'.esc_attr($field['id']).'" title="'.esc_html__('Reset to default value', 'slick-menu').'" data-id="'.esc_attr($field['id']).'" data-std="'.$std.'" data-type="'.esc_attr($type).'" class="'.esc_attr($reset_class).' hide-if-no-js" href="#"><i class="dashicons dashicons-image-rotate"></i></a></div>';
		
		return $end;
	}


	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts()
	{
        global $wp_version;

        $screen = get_current_screen();
        $is_customizer = is_customize_preview();
        $rtl_suffix = is_rtl() ? '-rtl' : '';


        // For meta boxes
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'wp-lists' );
        wp_enqueue_script( 'postbox' );

        wp_register_style( 'sm-mb-nav-menu', SM_MB_NAV_MENU_URL . 'css/style'.$rtl_suffix.$this->script_suffix.'.css', array());
        wp_enqueue_style( 'sm-mb-nav-menu');

        wp_register_script( 'sm-mb-nav-menu', SM_MB_NAV_MENU_URL . 'js/sm-mb-nav-menu'.$this->script_suffix.'.js', array( 'jquery' ));
        wp_enqueue_script( 'sm-mb-nav-menu' );

        wp_register_script( 'sm-mb-nav-menu-script', SM_MB_NAV_MENU_URL . 'js/script'.$this->script_suffix.'.js', array( 'jquery', 'sm-mb-nav-menu' ));
        wp_localize_script( 'sm-mb-nav-menu-script', 'SM_MB_NAV_MENU_VARS', array(
            'activation_nonce' => wp_create_nonce('sm_mb_nav_menu_activation'),
            'editor_load_nonce' => wp_create_nonce('sm_mb_nav_menu_load'),
            'metabox_nonce' => wp_create_nonce('sm_mb_nav_menu_metabox'),
            'title' => $this->args["title"],
            'customizer' => is_customize_preview(),
            'wp_version' => str_replace(".", "-", $wp_version),
            'doing_ajax' =>  defined( 'DOING_AJAX' ) && DOING_AJAX
        ));

        if((!empty($screen) && $screen->id === 'nav-menus') || !empty($is_customizer)) {
            wp_enqueue_script( 'sm-mb-nav-menu-script' );
        }

	}


	/**
	 * Add help tabs
	 */
	public function add_help_tabs()
	{
		if ( ! $this->args['help_tabs'] || ! is_array( $this->args['help_tabs'] ) )
		{
			return;
		}
	
		$screen = get_current_screen();
		foreach ( $this->args['help_tabs'] as $k => $help_tab )
		{
			// Auto generate help tab ID if missed.
			if ( empty( $help_tab['id'] ) )
			{
				$help_tab['id'] = "{$this->args['id']}-help-tab-$k";
			}
			$screen->add_help_tab( $help_tab );
		}
	}


	function is_edit_screen($screen = null)
	{
		return Slick_Menu()->is_edit_screen($screen);
	}
	
}
