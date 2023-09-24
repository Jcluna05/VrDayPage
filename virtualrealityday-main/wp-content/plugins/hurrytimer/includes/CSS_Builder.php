<?php

namespace Hurrytimer;

use Hurrytimer\Traits\Singleton;
use \MatthiasMullie\Minify;

class CSS_Builder
{
    use Singleton;

    public function get_build_url()
    {
        $hash = $this->get_current_build_hash();

        $file = $this->get_build_path( $hash );

        if ( !file_exists( $file ) ) {
            $hash = $this->build();
        }

        $upload_dir = wp_get_upload_dir();

        return set_url_scheme( trailingslashit( $upload_dir[ 'baseurl' ] ) . $this->get_build_relative_path( $hash ) );
    }

    private function get_current_build_hash()
    {
        return get_option( 'hurrytimer_css_build_hash' );
    }

    public function delete_build( $hash )
    {
        $file = $this->get_build_path( $hash );

        @unlink( $file );
    }

    /**
     * Re/Build CSS file
     */
    public function build()
    {
        ob_start();
        include HURRYT_DIR . 'assets/css/main.css';
        include __DIR__ . '/css_template.php';

        $css = ob_get_clean();

        $this->delete_build( $this->get_current_build_hash() );

        $hash = substr( md5( time() ), 0, 16 );

        $file = $this->get_build_path( $hash );

        if ( filter_var( apply_filters( 'hurryt_minify_css', false ), FILTER_VALIDATE_BOOLEAN ) ) {
            $minifier = new Minify\CSS();
            $minifier->add( $css );
            $minifier->minify( $file );

        } else {
            file_put_contents( $file, $css );
        }

        update_option( 'hurrytimer_css_build_hash', $hash );

        return $hash;
    }

    private function get_build_relative_path( $hash )
    {
        $uploads_dir = wp_get_upload_dir();

        $relative_dir = implode( DIRECTORY_SEPARATOR, [ 'hurrytimer', 'css' ] );

        $path = trailingslashit( $uploads_dir[ 'basedir' ] ) . $relative_dir;

        if ( !file_exists( $path ) ) {
            mkdir( $path, 0777, true );
        }

        return apply_filters( 'hurryt_css_path', trailingslashit( $relative_dir ) . $hash . '.css' );
    }

    public function get_build_path( $hash )
    {
        $upload_dir = wp_get_upload_dir();

        return trailingslashit( $upload_dir[ 'basedir' ] ) . $this->get_build_relative_path( $hash );

    }

}