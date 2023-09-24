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
class SM_MB_Nav_Menu_Meta_Box extends SM_RW_Meta_Box
{
	public static $id;
	/**
	 * @var string Current nav menu ID.
	 */
	public $menu_id;
	
	/**
	 * @var string Current nav menu item ID.
	 */	
	public $item_id;
	
	public $depth;
	
	public $target_item;

	public $nav_menu_meta_box;
	
	public $parent;

	/**
	 * Constructor
	 * Call parent constructor and add specific hooks
	 *
	 * @param array $meta_box
	 */
	public function __construct( $meta_box, $parent)
	{
		// Run script only in admin area
		if ( ! is_admin() )
			return;


		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'sm_mb_nav_menu_load' && !empty($_REQUEST['menu_id'])) {
			$this->menu_id =  intval( $_REQUEST['menu_id'] );
			$this->item_id = intval( $_REQUEST['item_id'] );
			$this->depth = intval( $_REQUEST['depth'] );
			$this->target_item = !empty($this->item_id);
		}


		add_filter( 'sm_rwmb_normalize_wysiwyg_field', array($this, 'normalize_special_fields'), 10, 1);

		$meta_box           = self::normalize( $meta_box );
		$meta_box['fields'] = self::normalize_fields( $meta_box['fields'] );

		$this->parent = $parent;
		$this->meta_box = $meta_box;
		$this->fields   = &$this->meta_box['fields'];

		parent::__construct($meta_box);

		// Add additional actions for fields
/*
		$fields = self::get_fields( $this->fields );
		foreach ( $fields as $field )
		{
			call_user_func( array( self::get_class_name( $field ), 'add_actions' ) );
		}
*/

		// Add meta box
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Hide meta box if it's set 'default_hidden'
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hide' ), 10, 2 );

		if ( empty($meta_box['nav_menus'] ))
		{
			return false;
		}
		
		
		self::$id = $meta_box['id'];
		
		$this->fields   = $this->meta_box['fields'];
		
		
		// Prevent adding meta box to post
		$this->meta_box['post_types'] = $this->meta_box['pages'] = array();
		remove_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		remove_action( 'save_post_post', array( $this, 'save_post' ) );

		add_filter( 'sm_rwmb_field_meta', array( $this, 'field_meta' ), 10, 2 );
		
			// Enqueue common styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );	
		
		add_filter( 'sm_rwmb_show', array( 'SM_MB_Nav_Menu_Include_Exclude', 'check' ), 10, 2 );
	}


	public function normalize_special_fields($field) {
		
		if(empty($field['special-field'])) {
			
			$append_id = '-'.$this->menu_id;
			
			if($this->target_item) {
				$append_id .= '-'.$this->item_id;
			}
			$field['special-field'] = true;
			$field['id'] = $field['id'].$append_id;
		}
		
		return $field;
	}
	
	
	public function show_fields() {

		
		if(!$this->is_visible())
			return;

		?>
		

		<div class="sm-mb-metabox-holder <?php echo esc_attr($this->meta_box['id']);?>"  data-metabox="<?php echo esc_attr($this->meta_box['id']);?>">
			<div class="sm-mb-metabox-title"><span><?php echo esc_attr($this->meta_box['title']);?></span></div>
			<?php
				
				/**
				 * Allow plugins/themes to inject HTML before nav menu' fields
				 *
				 * @param object $item  Menu item data object.
				 * @param int    $depth Nav menu depth.
				 * @param array  $args  Menu item args.
				 * @param int    $id    Nav menu ID.
				 *
				 */
				do_action( 'sm_mb_nav_menu_before_fields', $this->item_id, $this->depth, $this->menu_id );
	
				self::show();
	
				/**
				 * Allow plugins/themes to inject HTML after nav menu' fields
				 *
				 * @param object $item  Menu item data object.
				 * @param int    $depth Nav menu depth.
				 * @param array  $args  Menu item args.
				 * @param int    $id    Nav menu ID.
				 *
				 */
				do_action( 'sm_mb_nav_menu_after_fields', $this->item_id, $this->depth, $this->menu_id );
			?>
			
		</div>

        <?php 

	}
	
		
	/**
	 * Get field meta value
	 *
	 * @param mixed $meta  Meta value
	 * @param array $field Field parameters
	 *
	 * @return mixed
	 */
	public function field_meta( $meta, $field )
	{	

		$saved = $this->is_saved();
		
		$key = $field['id'];
		if($field['type'] == 'wysiwyg') {
			$key = $field['field_name'];
		}
		$key = sanitize_text_field( $key );
		
		if($this->target_item) {
			$cache_key = $this->item_id . '-'.$key;
		}else{
			$cache_key = $this->menu_id . '-'.$key;
		}
		
		$meta = wp_cache_get( $cache_key );
		if ( false === $meta ) {
		

			$single = $field['clone'] || ! $field['multiple'] || (in_array($field['type'], array('autocomplete', 'select_advanced', 'image_advanced')));

			if($this->target_item) {
				$meta   = get_post_meta( $this->item_id, $key, $single );
			}else{
				$meta   = get_term_meta( $this->menu_id, $key, $single );
			}

			// Escape attributes
			$meta = call_user_func( array( SM_RW_Meta_Box::get_class_name( $field ), 'esc_meta' ), $meta );
	
			// Make sure meta value is an array for clonable and multiple fields
			if ( $field['clone'] || $field['multiple'] )
			{
				if ( empty( $meta ) || ! is_array( $meta ) )
				{
					/**
					 * Note: if field is clonable, $meta must be an array with values
					 * so that the foreach loop in self::show() runs properly
					 * @see self::show()
					 */
					$meta = $field['clone'] ? array( '' ) : array();
				}
			}
	
			if(slick_menu_is_empty($meta) && !empty($field['options']) && !empty($field['wysiwyg'])) {
				$meta = 'inherit';
			}

			wp_cache_set( $cache_key, $meta );
		} 	
		return $meta;
	}
	
	public function is_visible() {

		// Allow users to show/hide meta box
		// 1st action applies to all meta boxes
		// 2nd action applies to only current meta box

		$show = true;
		$show = apply_filters( 'sm_rwmb_show', $show, $this->meta_box );
		$show = apply_filters( "sm_rwmb_show_{$this->meta_box['id']}", $show, $this->meta_box );

		return $show;
	
	}

	
	/**
	 * Check if meta box is saved before.
	 * This helps saving empty value in meta fields (for text box, check box, etc.) and set the correct default values.
	 *
	 * @return bool
	 */
	public function is_saved()
	{

		foreach ( $this->fields as $field )
		{
			if ( empty( $field['id'] ) )
			{
				continue;
			}
			
			if($this->target_item) {
				$value = get_post_meta( $this->item_id, $field['id'], ! $field['multiple'] );
			}else{
				$value = get_term_meta( $this->menu_id, $field['id'], ! $field['multiple'] );
			}
			
		
			if (
				( ! $field['multiple'] && '' !== $value )
				|| ( $field['multiple'] && array() !== $value )
			)
			{
				return true;
			}
		}

		return false;
	}
	

	/**
	 * Check if we're on the right edit screen.
	 *
	 * @param WP_Screen $screen Screen object. Optional. Use current screen object by default.
	 *
	 * @return bool
	 */
	function is_edit_screen( $screen = null )
	{
        return Slick_Menu()->is_edit_screen($screen);
	}

	public function find_field( $field_id )
	{
		foreach ( $this->fields as $field )
		{
			if ( ! empty( $field['id'] ) && $field_id == $field['id'] )
			{
				return $field;
			}
		}
		return false;
	}
		
}
