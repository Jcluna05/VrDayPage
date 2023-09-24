<?php
/**
 * Validation module.
 * @package Meta Box
 */

/**
 * Validation class.
 */
class SM_RWMB_Validation
{
	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct()
	{
		add_action( 'sm_rwmb_after', array( $this, 'rules' ) );
		add_action( 'sm_rwmb_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 * @param SM_RW_Meta_Box $object Meta Box object
	 */
	public function rules( SM_RW_Meta_Box $object )
	{
		if ( ! empty( $object->meta_box['validation'] ) )
		{
			echo '<script type="text/html" class="sm-rwmb-validation-rules" data-rules="' . esc_attr( json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 */
	public function enqueue()
	{
		wp_enqueue_script( 'jquery-validate', SM_RWMB_JS_URL . 'jquery.validate.min.js', array( 'jquery' ), SM_RWMB_VER, true );
		wp_enqueue_script( 'sm-rwmb-validate', SM_RWMB_JS_URL . 'validate.js', array( 'jquery-validate' ), SM_RWMB_VER, true );
		/**
		 * Prevent loading localized string twice.
		 * @link https://github.com/rilwis/meta-box/issues/850
		 */
		$wp_scripts = wp_scripts();
		if ( ! $wp_scripts->get_data( 'sm-rwmb-validate', 'data' ) )
		{
			wp_localize_script( 'sm-rwmb-validate', 'rwmbValidate', array(
				'summaryMessage' => __( 'Please correct the errors highlighted below and try again.', 'meta-box' ),
			) );
		}
	}
}
