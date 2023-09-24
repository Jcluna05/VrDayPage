<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Slick_Menu_List extends WP_List_Table {

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public static $parent = null;
	
	
	/** Class constructor */
	public function __construct($parent) {

		self::$parent = &$parent;
		
		parent::__construct( array(
			'singular' => __( 'Slick Menu', 'slick-menu' ), //singular name of the listed records
			'plural'   => __( 'Slick Menus', 'slick-menu' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		));

    }

	/**
	 * Retrieve menus data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_menus() {
		
		$args = array();
		
		if(!empty($_GET['order'])) {
			$args['order'] = $_GET['order'];
		}
		
		if(!empty($_GET['orderby'])) {
			$args['orderby'] = $_GET['orderby'];
		}

		$result = self::$parent->get_menus($args);

		return $result;
	}


	/**
	 * Delete a menu record.
	 *
	 * @param int $id menu ID
	 */
	public static function delete_menu( $id ) {
		
		return self::$parent->delete_menu($id);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {

		$count = count(self::$parent->get_menus());

		return $count;
	}


	/** Text displayed when no menu data is available */
	public function no_items() {
		_e( 'No menus available.', 'slick-menu' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		
		switch ( $column_name ) {
			
			case 'menu-ajax':
			case 'menu-always-visible':
			
				return (bool)self::$parent->get_menu_option($column_name, $item->term_id) ? esc_html__("Yes", "slick-menu") : esc_html__("No", "slick-menu");
				break;
				
			case 'menu-position':
			case 'menu-animation-type':
			case 'level-animation-type':
			
				return self::$parent->get_menu_option_choice_label($column_name, $item->term_id);		
				break;
				
			default:
			
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item->term_id
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'slick_menu_delete_menu' );

		$title = '<strong>' . $item->name . '&nbsp;-&nbsp;'.$item->term_id.'</strong>';
		$nav_menu_url = self::$parent->get_menu_url($item->term_id);
		$nav_menu_customizer_url = self::$parent->get_menu_customizer_url($item->term_id);

		$actions = array(
			'edit' => sprintf( 
				'<a href="%s">%s</a>', 
				$nav_menu_url, 
				esc_html__( 'Edit', 'slick-menu' )
			),
			'customizer' => sprintf( 
				'<a href="%s">%s</a>', 
				$nav_menu_customizer_url, 
				esc_html__( 'Edit In Customizer', 'slick-menu' ) 
			),
			'delete' => sprintf( 
				'<a href="?page=%s&action=%s&menu=%s&_wpnonce=%s">%s</a>', 
				esc_attr( $_REQUEST['page'] ), 
				'delete', 
				absint( $item->term_id ), 
				$delete_nonce, 
				esc_html__( 'Delete', 'slick-menu' )
			 )
		);
		
		$actions = apply_filters('slick_menu_manage_list_actions', $actions, absint( $item->term_id ));

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'name'    => esc_html__( 'Name', 'slick-menu' ),
			'menu-ajax' => esc_html__( 'Ajax Enabled', 'slick-menu' ),
			'menu-position' => esc_html__( 'Position', 'slick-menu' ),
			'menu-always-visible' => esc_html__( 'Always Visible', 'slick-menu' ),
			'menu-animation-type'    => esc_html__( 'Menu Animation Type', 'slick-menu' ),
			'level-animation-type'    => esc_html__( 'Levels Animation Type', 'slick-menu' )
		);

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		
		$sortable_columns = array(
			'name' => array( 'name', true )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		
		$actions = array(
			'bulk-delete' => 'Delete'
		);

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = -1;
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );

		$this->items = self::get_menus();
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'slick_menu_delete_menu' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				
				if(!self::delete_menu( absint( $_GET['menu'] ) )) {
					self::$parent->pcache->flush();
				}
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_menu( $id );
			}
			
			self::$parent->pcache->flush();

		}
	}

}
