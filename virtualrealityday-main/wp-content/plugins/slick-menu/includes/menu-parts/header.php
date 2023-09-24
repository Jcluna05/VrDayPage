<?php
	
// SM HEADER
	
$return  = Slick_Menu()->do_output_action('before_header', $menu_id, $item_id);
$return .= '<div class="'.esc_attr($header_classes).'">';

	$return .= Slick_Menu()->do_output_action('header_begin', $menu_id, $item_id);

		// SM LOGO
		if((!empty($logo) || !empty($logo_avatar)) && empty($logo_hidden)) {
			
			$logo_animation_class = $options['logo-animation'];
			$logo_animation_data = '';
			if(!empty($logo_animation_class)) {
				$logo_animation_data = ' class="sm-animated" data-animation="sm-'.esc_attr($logo_animation_class).'"';
			}
			
			$return .= Slick_Menu()->do_output_action('before_logo', $menu_id, $item_id);
			
			$return .= '<div class="sm-logo">';
			
			if(!empty($logo_url)) {
				$return .= '<a href="'.esc_url($logo_url).'">';
			}
			
			if(!empty($logo_avatar)) {
				
				$return .= str_replace('<img', '<img'.$logo_animation_data, $logo_avatar);
				
			}else{
				
				$logo_data = wp_get_attachment_image_src($logo[0], 'full');
				$return .= '	<img'.$logo_animation_data.' alt="logo" width="'.esc_attr($logo_data[1]).'" height="'.esc_attr($logo_data[2]).'" src="'.esc_url($logo_data[0]).'">';
			}
			
			if(!empty($logo_url)) {
				$return .= '</a>';
			}
			
			$return .= '</div>';
			
			$return .= Slick_Menu()->do_output_action('after_logo', $menu_id, $item_id);
		}


		// SM SEARCH
		if(empty($item_id) && !empty($options['search-enabled'])) {
			
			$search_animation_class = $options['search-animation'];
			$search_animation_data = '';
			$search_class = 'sm-search';
			if(!empty($search_animation_class)) {
				$search_animation_data = ' data-animation="sm-'.esc_attr($search_animation_class).'"';
				$search_class .= ' sm-animated';
			}
			
			$placeholder = "";
			if(!empty($options['search-show-placeholder'])) {
				$placeholder = esc_attr__( 'Search &hellip;', 'slick-menu' );
			}
			
			$searchForm = '<form role="search" method="get" class="sm-search-form" action="' . esc_url( home_url( '/' ) ) . '">
                <label>
                    <span class="screen-reader-text">' . __( 'Search for:', 'slick-menu' ) . '</span>
                    <input type="search" class="sm-search-field" placeholder="'.esc_attr($placeholder).'" value="' . get_search_query() . '" name="s" />
                </label>
                <button type="submit" class="sm-search-submit">
                	<span class="fa fa-search" aria-hidden="true"></span>
					<span class="screen-reader-text">'. esc_attr__( 'Search', 'slick-menu' ) .'</span>
                </button>
            </form>';
	            
			$return .= Slick_Menu()->do_output_action('before_search', $menu_id);
			$return .= '<div class="'.esc_attr($search_class).'"'.$search_animation_data.'>';
				$return .= Slick_Menu()->do_output_action('search_start', $menu_id);
				$return .= Slick_Menu()->apply_filters('search_form', $searchForm, $menu_id);
				$return .= Slick_Menu()->do_output_action('search_end', $menu_id);
			$return .= '</div>';
			$return .= Slick_Menu()->do_output_action('after_search', $menu_id);
		}
		
	$return .= 	Slick_Menu()->do_output_action('header_end', $menu_id, $item_id);
	
$return .= '</div>';
$return .= Slick_Menu()->do_output_action('after_header', $menu_id, $item_id);	

return $return;