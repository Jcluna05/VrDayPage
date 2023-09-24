<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class SM_RWMB_File_Upload_Field extends SM_RWMB_Media_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'sm-rwmb-upload', SM_RWMB_CSS_URL . 'upload.css', array( 'sm-rwmb-media' ), SM_RWMB_VER );
		wp_enqueue_script( 'sm-rwmb-file-upload', SM_RWMB_JS_URL . 'file-upload.js', array( 'sm-rwmb-media' ), SM_RWMB_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates()
	{
		parent::print_templates();
		require_once SM_RWMB_INC_DIR . 'templates/upload.php';
	}
}
