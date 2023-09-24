<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Taxonomy {

	/**
	 * The name for the taxonomy.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $taxonomy;

	/**
	 * The plural name for the taxonomy terms.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $plural;

	/**
	 * The singular name for the taxonomy terms.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $single;

	/**
	 * The array of post types to which this taxonomy applies.
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $post_types;

  /**
	 * The array of taxonomy arguments
	 * @var 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public $taxonomy_args;

	public function __construct ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $tax_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		// Post type name and labels
		$this->taxonomy = $taxonomy;
		$this->plural = $plural;
		$this->single = $single;
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}
		$this->post_types = $post_types;
		$this->taxonomy_args = $tax_args;

		// Register taxonomy
		add_action('init', array( $this, 'register_taxonomy' ) );
	}

	/**
	 * Register new taxonomy
	 * @return void
	 */
	public function register_taxonomy () {

        $labels = array(
            'name' => $this->plural,
            'singular_name' => $this->single,
            'menu_name' => $this->plural,
            'all_items' => sprintf( esc_html__( 'All %s' , 'slick-menu' ), $this->plural ),
            'edit_item' => sprintf( esc_html__( 'Edit %s' , 'slick-menu' ), $this->single ),
            'view_item' => sprintf( esc_html__( 'View %s' , 'slick-menu' ), $this->single ),
            'update_item' => sprintf( esc_html__( 'Update %s' , 'slick-menu' ), $this->single ),
            'add_new_item' => sprintf( esc_html__( 'Add New %s' , 'slick-menu' ), $this->single ),
            'new_item_name' => sprintf( esc_html__( 'New %s Name' , 'slick-menu' ), $this->single ),
            'parent_item' => sprintf( esc_html__( 'Parent %s' , 'slick-menu' ), $this->single ),
            'parent_item_colon' => sprintf( esc_html__( 'Parent %s:' , 'slick-menu' ), $this->single ),
            'search_items' =>  sprintf( esc_html__( 'Search %s' , 'slick-menu' ), $this->plural ),
            'popular_items' =>  sprintf( esc_html__( 'Popular %s' , 'slick-menu' ), $this->plural ),
            'separate_items_with_commas' =>  sprintf( esc_html__( 'Separate %s with commas' , 'slick-menu' ), $this->plural ),
            'add_or_remove_items' =>  sprintf( esc_html__( 'Add or remove %s' , 'slick-menu' ), $this->plural ),
            'choose_from_most_used' =>  sprintf( esc_html__( 'Choose from the most used %s' , 'slick-menu' ), $this->plural ),
            'not_found' =>  sprintf( esc_html__( 'No %s found' , 'slick-menu' ), $this->plural ),
        );

        $args = array(
        	'label' => $this->plural,
        	'labels' => apply_filters( $this->taxonomy . '_labels', $labels ),
        	'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'meta_box_cb' => null,
            'show_admin_column' => true,
            'update_count_callback' => '',
            'query_var' => $this->taxonomy,
            'rewrite' => true,
            'sort' => '',
        );

        $args = array_merge($args, $this->taxonomy_args);

        register_taxonomy( $this->taxonomy, $this->post_types, apply_filters( $this->taxonomy . '_register_args', $args, $this->taxonomy, $this->post_types ) );
    }

}
