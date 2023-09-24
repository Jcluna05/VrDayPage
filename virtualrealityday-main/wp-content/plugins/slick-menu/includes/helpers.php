<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Gets the ID of the post, even if it's not inside the loop.
 *
 * @uses WP_Query
 * @uses get_queried_object()
 * @extends get_the_ID()
 * @see get_the_ID()
 *
 * @return int
 */
function slick_menu_get_post_id() {
    if ( in_the_loop() ) {
        $post_id = get_the_ID();
    } else {
        /** @var $wp_query wp_query */
        global $wp_query;
        $post_id = $wp_query->get_queried_object_id();
    }
    return $post_id;
}

/**
 * Returns all child nav_menu_items under a specific parent
 *
 * @param int the parent nav_menu_item ID
 * @param array nav_menu_items
 * @param bool gives all children or direct children only
 * @return array returns filtered array of nav_menu_items
 */
function slick_menu_get_menu_item_children($parent_id, $nav_menu_items, $depth = true)
{
    $nav_menu_item_list = array();
    foreach ((array) $nav_menu_items as $nav_menu_item) {
        if ($nav_menu_item->menu_item_parent == $parent_id) {
            $nav_menu_item_list[] = $nav_menu_item;
            if ($depth) {
                if ($children = slick_menu_get_menu_item_children($nav_menu_item->ID, $nav_menu_items))
                    $nav_menu_item_list = array_merge($nav_menu_item_list, $children);
            }
        }
    }
    return $nav_menu_item_list;
}


function slick_menu_get_menu_item_parent_id($item_id) {
	
	return intval(get_post_meta($item_id, '_menu_item_menu_item_parent', true));
}


function slick_menu_css_value($attr, $value) {
	
	if(!empty($value)) {
		
		echo $attr.': '.$value.';';
	}
}


function slick_menu_remove_all_metaboxes($type, $keep = array()) {
	
	global $wp_meta_boxes;
	
	$keeping = array();
	
	if(empty($wp_meta_boxes[$type]['side']['core'])) {
		return false;
	}
	
	$keeping[$type]['side']['core'] = $wp_meta_boxes[$type]['side']['core'];

	$locations = array('normal','side','advanced');
	$priorities = array('low','high');
	
	if(!empty($keep)) {
		foreach($keep as $keep_id) {

			foreach($locations as $location) {
				
				foreach($priorities as $priority) {
					
					if(empty($wp_meta_boxes[$type][$location][$priority][$keep_id]["id"])) {
						continue;
					}
					if($keep_id === $wp_meta_boxes[$type][$location][$priority][$keep_id]["id"]) {
						$keeping[$type][$location][$priority][$keep_id] = $wp_meta_boxes[$type][$location][$priority][$keep_id];
					}
					
				}	
			}
		}	
	}

	$wp_meta_boxes[$type] = $keeping[$type];
} 


function slick_menu_class_string($classes = array()) {
	
	return implode(" ", array_unique($classes));
}

function slick_menu_data_string($data = array()) {
	
	$data_string = '';
	foreach($data as $key => $value) {
		$data_string .= ' data-'.$key.'="'.esc_attr($value).'"';
	}
	
	return $data_string;
}

function slick_menu_array_value($array, $key) {
	
	if(isset($array[$key])) {
		return $array[$key];
	}
	
	return "";
}

function slick_menu_is_empty($var, $includeZeroSring = false) {
	
	if(!is_array($var)) {
		
		$empty = (is_null($var) || ($var === 'inherit') || ($var === '') || (is_array($var) && count($var) === 0));
		if($includeZeroSring && $var == '0') {
			$empty = true;
		}
		
		return $empty;
	
	}else{
	
		foreach($var as $key => $val) {

			$empty = slick_menu_is_empty($val, true);
			if(!$empty) {
				return false;
			}
		}
		
		return true;
	}	
}


function slick_menu_array_splice(&$input, $offset, $length, $replacement = array()) {
    $replacement = (array) $replacement;
    $key_indices = array_flip(array_keys($input));
    if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset];
    }
    if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
    }

    $input = array_slice($input, 0, $offset, TRUE)
            + $replacement
            + array_slice($input, $offset + $length, NULL, TRUE); 
}


/**
 * Retrieves the response from the specified URL using one of PHP's outbound request facilities.
 *
 * @params	$url	The URL of the feed to retrieve.
 * @returns			The response from the URL; null if empty.
 */
function slick_menu_remote_get( $url, $params = array(), $unserialize = false, $cache = true, $cacheExpiration = DAY_IN_SECONDS) {
	
	$cache_key = md5('sm-'.$url.serialize($params));
	
	$nocache = !empty($_GET['nocache']) ? true : false;
	
	if ( $nocache || !$cache || false === ( $response = get_transient( $cache_key ) ) ) {
     
     	// this code runs when there is no valid transient set
	 	
	 	if(!empty($params)) {
		 	$url = add_query_arg( $params, $url );
	 	}
	 	$response = null;
	 	
 		// First, we try to use wp_remote_get
		$response = wp_remote_get( $url, array(
            'timeout' => 120,
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
        ));
	
		if( is_wp_error( $response ) || (!empty($response["response"]["code"]) && $response["response"]["code"] === 403 )) {
			
            if(function_exists('curl_init')) {
	
				// And if that doesn't work, then we'll try curl
				$curl = curl_init( $url );
			
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_HEADER, 0 );
				curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0' );
				curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
			
				$response = curl_exec( $curl );
				if( 0 !== curl_errno( $curl ) || 200 !== curl_getinfo( $curl, CURLINFO_HTTP_CODE ) ) {
					
					// If that doesn't work, then we'll try file_get_contents
			        $response = @file_get_contents( $url );
			
				} // end if
				curl_close( $curl );

			}else{
			
			    // If curl is not availaible try file_get_contents
			    $response = file_get_contents( $url );
			        
			}// end if
					
			if( null == $response ) {
				$response = null;
			}	
	
			if(!empty($response) && $unserialize) {
				$response = maybe_unserialize($response);
			}
	
		}else{

	        // Parse remote HTML file
			$response = wp_remote_retrieve_body( $response );
	
	        // Check for error
			if ( !is_wp_error( $response ) && $unserialize) {
				$response = maybe_unserialize($response);
			}
		}

		if(!empty($response)) {
			set_transient( $cache_key, $response, $cacheExpiration );
		}
	}
	
	return $response;

} // end get_response


function slick_menu_remote_get_data($type, $params = array(), $unserialize = false, $cache = true, $cacheExpiration = DAY_IN_SECONDS) {

	$data_url = "http://www.slickmenu.net/data/$type.php"; 
	
	if(!empty($params)) {
		$data_url .= '?'.http_build_query($params);
	}
	
	return slick_menu_remote_get($data_url, $params, $unserialize, $cache, $cacheExpiration);
}


function slick_menu_include_part($id, $vars) {
	
	extract($vars);
	
	return include(Slick_Menu()->plugin_dir().'/includes/menu-parts/'.$id.'.php');
} 

function slick_menu_is_action($action) {
	
	if(!empty($_GET['smaction']) && $_GET['smaction'] == $action) {
		return true;
	}
	return false;
}

function slick_menu_is_ajax_request() {
	
	if(!empty($_GET['sm_ajax'])) {
		return true;
	}
	return false;
}

function slick_menu_is_ajax_action($action) {
	
	if(slick_menu_is_ajax_request() && $_GET['sm_ajax'] == $action) {
		return true;
	}
	return false;
}

function slick_menu_get_ajax_link($action, $params = array()) {
	
	$version_time = Slick_Menu()->pcache->get('ajax_version');
	
	if($version_time === false) {
		
		$version_time = time();
		
		Slick_Menu()->pcache->set('ajax_version', $version_time);
	}	
	
	$url = home_url('/?sm_ajax='.$action.'&t='.$version_time);
	
	return add_query_arg($params, $url);
}