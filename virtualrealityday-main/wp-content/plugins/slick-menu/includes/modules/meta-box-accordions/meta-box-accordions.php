<?php
/**
 * Plugin Name: Meta Box Accordions
 * Plugin URI: https://meaccordionox.io/plugins/meta-box-accordions/
 * Description: Create accordions for meta boxes easily. Support 3 WordPress-native accordion styles.
 * Version: 0.1.7
 * Author: Rilwis
 * Author URI: https://www.deluxeblogtips.com
 * License: GPL2+
 */

// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class
 * @package    Meta Box
 * @subpackage Meta Box Accordions
 * @author     Georges Haddad <prismosoft@gmail.com>
 */
class SM_MB_Accordions
{
	/**
	 * Indicate that the instance of the class is working on a meta box that has accordions or not
	 * It will be set 'true' BEFORE meta box is display and 'false' AFTER
	 *
	 * @var bool
	 */
	public $active = false;

	/**
	 * Store all output of fields
	 * This is used to put fields in correct <div> for accordions
	 * The fields' output will be get via filter 'sm_rwmb_outer_html'
	 *
	 * @var array
	 */
	public $fields_output = array();

	/**
	 * Add hooks to meta box
	 */
	public function __construct()
	{
		add_action( 'sm_rwmb_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'sm_rwmb_before', array( $this, 'opening_div' ), 1 ); // 1 = display first, before accordion nav
		add_action( 'sm_rwmb_after', array( $this, 'closing_div' ), 100 ); // 100 = display last, after accordion panels

		add_action( 'sm_rwmb_after', array( $this, 'show_panels' ) );

		add_filter( 'sm_rwmb_outer_html', array( $this, 'capture_fields' ), 10, 2 );
	}

	/**
	 * Enqueue scripts and styles for accordions
	 */
	public function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'sm-rwmb-accordions', plugins_url( 'accordions.css', __FILE__ ) );
		wp_enqueue_script( 'sm-rwmb-accordions', plugins_url( 'accordions.js', __FILE__ ), array( 'jquery' ), '0.1', true );
	}

	/**
	 * Display opening div for accordions for meta box
	 *
	 * @param SM_RW_Meta_Box $obj Meta Box object
	 */
	public function opening_div( SM_RW_Meta_Box $obj )
	{
		if ( empty( $obj->meta_box['accordions'] ) )
			return;

		$class = 'sm-rwmb-accordions';
		if ( isset( $obj->meta_box['accordion_style'] ) && 'default' != $obj->meta_box['accordion_style'] )
			$class .= ' sm-rwmb-accordions-' . $obj->meta_box['accordion_style'];

		if ( isset( $obj->meta_box['accordion_wrapper'] ) && false == $obj->meta_box['accordion_wrapper'] )
			$class .= ' sm-rwmb-accordions-no-wrapper';

		echo '<div class="' . $class . '">';

		// Set 'true' to let us know that we're working on a meta box that has accordions
		$this->active = true;
	}

	/**
	 * Display closing div for accordions for meta box
	 */
	public function closing_div()
	{
		if ( ! $this->active )
			return;

		echo '</div>';

		// Reset to initial state to be ready for other meta boxes
		$this->active        = false;
		$this->fields_output = array();
	}


	/**
	 * Display accordion navigation for meta box
	 * Note that: this public function is hooked to 'sm_rwmb_after', when all fields are outputted
	 * (and captured by 'capture_fields' public function)
	 */
	public function show_panels(SM_RW_Meta_Box $obj)
	{
		if ( ! $this->active )
			return;


		$accordions = $obj->meta_box['accordions'];
		$i = 0;
		echo '<div class="sm-rwmb-accordion-panels">';
		foreach ( $this->fields_output as $accordion => $fields )
		{
			$accordion_data = $accordions[$accordion];
			
			if ( is_string( $accordion_data ) )
			{
				$accordion_data = array( 'label' => $accordion_data );
			}
			$accordion_data = wp_parse_args( $accordion_data, array(
				'icon'  => '',
				'label' => '',
			) );
			
			// If icon is URL to image
			if ( filter_var( $accordion_data['icon'], FILTER_VALIDATE_URL ) )
			{
				$icon = '<img src="' . $accordion_data['icon'] . '">';
			}
			// If icon is icon font
			else
			{
				// If icon is dashicon, auto add class 'dashicons' for users
				if ( false !== strpos( $accordion_data['icon'], 'dashicons' ) )
				{
					$accordion_data['icon'] .= ' dashicons';
				}
				// Remove duplicate classes
				$accordion_data['icon'] = array_filter( array_map( 'trim', explode( ' ', $accordion_data['icon'] ) ) );
				$accordion_data['icon'] = implode( ' ', array_unique( $accordion_data['icon'] ) );

				$icon = $accordion_data['icon'] ? '<i class="' . $accordion_data['icon'] . '"></i>' : '';
			}

			$class = "sm-rwmb-accordion-title sm-rwmb-accordion-$accordion";
			if ( ! $i )
				$class .= ' sm-rwmb-accordion-active';

			printf(
				'<div class="%s" data-panel="%s"><a href="#">%s%s</a></div>',
				$class,
				$accordion,
				$icon,
				$accordion_data['label']
			);
				
			echo '<div class="sm-rwmb-accordion-panel sm-rwmb-accordion-panel-' . $accordion . '">';
			echo implode( '', $fields );
			echo '</div>';
			
			$i++;
		}
		echo '</div>';
	}

	/**
	 * Save field output into class variable to output later
	 *
	 * @param string $output Field output
	 * @param array  $field  Field configuration
	 *
	 * @return string
	 */
	public function capture_fields( $output, $field )
	{
		// If meta box doesn't have accordions, do nothing
		if ( ! $this->active || ! isset( $field['accordion'] ) )
			return $output;

		$accordion = $field['accordion'];

		if ( ! isset( $this->fields_output[$accordion] ) )
			$this->fields_output[$accordion] = array();
		$this->fields_output[$accordion][] = $output;

		// Return empty string to let Meta Box plugin echoes nothing
		return '';
	}
}

if ( is_admin() )
{
	new SM_MB_Accordions;
}
