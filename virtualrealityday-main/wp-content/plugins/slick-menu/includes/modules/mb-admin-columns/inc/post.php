<?php
/**
 * Class that manage post admin columns.
 */

/**
 * Post admin columns class
 */
class SM_MB_Admin_Columns_Post
{
	/**
	 * Post type
	 * @var string
	 */
	private $post_type;

	/**
	 * List of fields for the post type.
	 * @var array
	 */
	private $fields;

	/**
	 * Constructor.
	 * @param string $post_type Post type
	 * @param array  $fields    List of fields
	 */
	public function __construct( $post_type, $fields )
	{
		$this->post_type = $post_type;
		$this->fields    = $fields;

		// Actions to show post columns can be executed via normal page request or via Ajax when quick edit
		// Priority 20 allows us to overwrite WooCommerce settings
		$priority = 20;
		add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'columns' ), $priority );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'show' ), $priority, 2 );
		add_filter( "manage_edit-{$this->post_type}_sortable_columns", array( $this, 'sortable_columns' ), $priority );

		// Edit post row actions
		//if ( method_exists( $this, 'row_actions' ) )
		//	add_filter( 'post_row_actions', array( $this, 'row_actions' ), $priority, 2 );

		// Set primary column, @since WordPress 4.3
		//if ( method_exists( $this, 'primary_column' ) )
		//	add_filter( 'list_table_primary_column', array( $this, 'primary_column' ), $priority, 2 );

		// Other actions need to run only in Management page
		add_action( 'load-edit.php', array( $this, 'execute' ) );
	}

	/**
	 * Actions need to run only in Management page.
	 */
	public function execute()
	{
		if ( ! $this->is_screen() )
		{
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'pre_get_posts', array( $this, 'filter' ) );
	}

	/**
	 * Enqueue admin styles.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'sm-mb-admin-columns', SM_MB_ADMIN_COLUMNS_URL . 'css/admin-columns.css' );
	}

	/**
	 * Get list of columns.
	 * @param array $columns Default WordPress columns
	 * @return array
	 */
	public function columns( $columns )
	{
		foreach ( $this->fields as $field )
		{
			$config = $field['admin_columns'];

			// Just show this column
			if ( true === $config )
			{
				$this->add_column( $columns, $field['id'], $field['name'] );
				continue;
			}

			// If position is specified
			if ( is_string( $config ) )
			{
				$config = strtolower( $config );
				list( $position, $target ) = array_map( 'trim', explode( ' ', $config . ' ' ) );
				$this->add_column( $columns, $field['id'], $field['name'], $position, $target );
			}

			// If an array of configuration is specified
			if ( is_array( $config ) )
			{
				$config = wp_parse_args( $config, array(
					'position' => '',
					'title'    => $field['name'],
				) );
				list( $position, $target ) = array_map( 'trim', explode( ' ', $config['position'] . ' ' ) );
				$this->add_column( $columns, $field['id'], $config['title'], $position, $target );
			}
		}
		return $columns;
	}

	/**
	 * Show column content.
	 *
	 * @param string $column  Column ID
	 * @param int    $post_id Post ID
	 */
	public function show( $column, $post_id )
	{
		if ( false === ( $field = $this->find_field( $column ) ) )
		{
			return;
		}

		$config = array(
			'before' => '',
			'after'  => '',
		);
		if ( is_array( $field['admin_columns'] ) )
		{
			$config = wp_parse_args( $field['admin_columns'], $config );
		}
		
		$value = sm_rwmb_meta($field['id'], '', $post_id);
		$label = sm_rwmb_the_value( $field['id'], '', $post_id, false );
		
		printf(
			'<div class="sm-mb-admin-columns sm-mb-admin-columns-%s" id="sm-mb-admin-columns-%s">%s%s%s</div>',
			$field['type'],
			$field['id'],
			str_replace(array('{value}', '{label}'), array($value, $label), $config['before']),
			$label,
			str_replace(array('{value}', '{label}'), array($value, $label), $config['after'])
		);
	}

	/**
	 * Make columns sortable
	 * @param array $columns
	 * @return array
	 */
	public function sortable_columns( $columns )
	{
		foreach ( $this->fields as $field )
		{
			if ( is_array( $field['admin_columns'] ) && ! empty( $field['admin_columns']['sort'] ) )
			{
				$columns[$field['id']] = $field['id'];
			}
		}
		return $columns;
	}

	/**
	 * Sort by meta value
	 * @param WP_Query $query
	 */
	public function filter( $query )
	{
		if ( ! isset( $_GET['orderby'] ) || false === ( $field = $this->find_field( $_GET['orderby'] ) ) )
		{
			return;
		}
		$query->set( 'orderby', in_array( $field['type'], array( 'number', 'slider', 'range' ) ) ? 'meta_value_num' : 'meta_value' );
		$query->set( 'meta_key', $_GET['orderby'] );
	}

	/**
	 * Check if we in right page in admin area.
	 * @return bool
	 */
	private function is_screen()
	{
		if ( ! is_admin() )
			return false;

		$screen = get_current_screen();
		return 'edit' == $screen->base && $this->post_type == $screen->post_type;
	}

	/**
	 * Add a new column
	 * @param array  $columns  Array of columns
	 * @param string $id       New column ID
	 * @param string $title    New column title
	 * @param string $position New column position. Empty to not specify the position. Could be 'before', 'after' or 'replace'
	 * @param string $target   The target column. Used with combination with $position
	 */
	private function add_column( &$columns, $id, $title, $position = '', $target = '' )
	{
		// Just add new column
		if ( ! $position )
		{
			$columns[$id] = $title;
			return;
		}

		// Add new column in a specific position
		$new = array();
		switch ( $position )
		{
			// Replace
			case 'replace':
				foreach ( $columns as $key => $value )
				{
					if ( $key == $target )
					{
						$new[$id] = $title;
					}
					else
					{
						$new[$key] = $value;
					}
				}
				break;
			case 'before':
				foreach ( $columns as $key => $value )
				{
					if ( $key == $target )
					{
						$new[$id] = $title;
					}
					$new[$key] = $value;
				}
				break;
			case 'after':
				foreach ( $columns as $key => $value )
				{
					$new[$key] = $value;
					if ( $key == $target )
					{
						$new[$id] = $title;
					}
				}
				break;
			default:
				return;
		}
		$columns = $new;
	}

	/**
	 * Find field by ID
	 * @param string $field_id
	 * @return array|bool False if not found. Array of field parameters if found.
	 */
	private function find_field( $field_id )
	{
		foreach ( $this->fields as $field )
		{
			if ( $field_id == $field['id'] )
			{
				return $field;
			}
		}
		return false;
	}
}
