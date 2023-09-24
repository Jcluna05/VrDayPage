<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class SM_RWMB_Image_Upload_Field extends SM_RWMB_Image_Advanced_Field
{
	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();
		SM_RWMB_File_Upload_Field::admin_enqueue_scripts();
		wp_enqueue_script( 'sm-rwmb-image-upload', SM_RWMB_JS_URL . 'image-upload.js', array( 'sm-rwmb-file-upload', 'sm-rwmb-image-advanced' ), SM_RWMB_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates()
	{
		parent::print_templates();
		SM_RWMB_File_Upload_Field::print_templates();
	}
}
