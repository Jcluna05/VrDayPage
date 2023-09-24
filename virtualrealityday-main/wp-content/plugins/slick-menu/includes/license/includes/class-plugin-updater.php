<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Slick_Menu_Plugin_Updater {

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */

	protected $slug;
	protected $market;
	protected $license;

	protected $update_url = "https://repo.xplodedthemes.com/updates.php?action=get_metadata&slug={slug}&market={market}";
	
	public function __construct ( $plugin_file, $plugin_slug, $market, $license ) {

        $this->file = $plugin_file;
	    $this->slug = $plugin_slug;
        $this->market = $market;
        $this->license = $license;

		$this->update_url = str_replace(
			array(
				'{slug}',
				'{market}'
			),
			array(
                $this->slug,
                $this->market,
			),
			$this->update_url
		);
		
		$updateChecker = Puc_v4_Factory::buildUpdateChecker(
		   	$this->update_url,
            $this->file,
            $this->slug
		);
		
		$updateChecker->addQueryArgFilter(array($this, 'filter_update_checker'));
	}

	public function filter_update_checker($queryArgs) {

	    if ( !empty($this->license) && $this->license->getLocalLicense() !== false) {

		    $product = $this->license->getLocalLicense()->license;
		     
	        $queryArgs['purchase_code'] = $product->purchase_code;
	        $queryArgs['product_id'] = $product->product_id;
	        $queryArgs['domain'] = $product->domain;
	        
	    }else{
		    
		    $queryArgs['purchase_code'] = '';
	        $queryArgs['product_id'] = '';
	        $queryArgs['domain'] = '';
	    }
	    
	    return $queryArgs;
	}

}
