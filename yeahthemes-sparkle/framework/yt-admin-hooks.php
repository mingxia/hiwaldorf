<?php

// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Action hooks and Wrapper classes
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @package 	WordPress
 * @subpackage 	Yeah Framework
 * @since		Version 1.0
 */

/* TABLE OF CONTENTS
 *
 * - =Site wrapper
 * - 
 * - yt_before_wrapper()
 * - yt_after_wrapper()
 * -
 * - yt_wrapper_start()
 * - yt_wrapper_end()
 * -
 * - =Header
 * - 
 * - yt_before_header()
 * - yt_inside_header()
 * - yt_after_header()
 * - yt_header_start()
 * - yt_header_end()
 * -
 * - =Primary
 * - 
 * - yt_before_primary()
 * - yt_after_primary()
 * - yt_primary_start()
 * - yt_primary_end()
 * -
 * - =Secondary
 * - 
 * - yt_before_secondary()
 * - yt_after_secondary()
 * - yt_secondary_start()
 * - yt_secondary_end()
 * -
 * - =Tertiary
 * - 
 * - yt_before_tertiary()
 * - yt_after_tertiary()
 * - yt_tertiary_start()
 * - yt_tertiary_end()
 * -
 * - =Loop
 * - 
 * - yt_before_loop()
 * - yt_after_loop()
 * -
 * - =Footer
 * - 
 * - yt_before_footer()
 * - yt_inside_footer()
 * - yt_after_header()
 * - yt_footer_start()
 * - yt_footer_end()
 * - 
 * - =Search form
 * - 
 * - yt_before_search_form()
 * - yt_after_search_form()
 * - yt_search_form_start()
 * - yt_search_form_end()
 */

 
/**
 *Core Action hooks
 *
 **/

/*
 * Wrapper
 */
if ( ! function_exists( 'yt_before_wrapper' ) ) {
	function yt_before_wrapper() { yt_do_atomic('yt_before_wrapper'); }
}
if ( ! function_exists( 'yt_after_wrapper' ) ) {
	function yt_after_wrapper() { yt_do_atomic('yt_after_wrapper'); }
}

if ( ! function_exists( 'yt_wrapper_start' ) ) {
	function yt_wrapper_start() { yt_do_atomic('yt_wrapper_start'); }
}
if ( ! function_exists( 'yt_wrapper_end' ) ) {
	function yt_wrapper_end() { yt_do_atomic('yt_wrapper_end'); }
}


 
/*
 * Header
 */
if ( ! function_exists( 'yt_before_header' ) ) {
	function yt_before_header() { yt_do_atomic('yt_before_header'); }
}
if ( ! function_exists( 'yt_inside_header' ) ) {
	function yt_inside_header() { yt_do_atomic('yt_inside_header'); }
}
if ( ! function_exists( 'yt_after_header' ) ) {
	function yt_after_header() { yt_do_atomic('yt_after_header'); }
}
if ( ! function_exists( 'yt_header_start' ) ) {
	function yt_header_start() { yt_do_atomic('yt_header_start'); }
}
if ( ! function_exists( 'yt_header_end' ) ) {
	function yt_header_end() { yt_do_atomic('yt_header_end'); }
}

/*
 * Main
 */

if ( ! function_exists( 'yt_main_start' ) ) {
	function yt_main_start() { yt_do_atomic('yt_main_start'); }
}
if ( ! function_exists( 'yt_main_end' ) ) {
	function yt_main_end() { yt_do_atomic('yt_main_end'); }
}

/*
 * Primary content
 */
if ( ! function_exists( 'yt_before_primary' ) ) {
	function yt_before_primary() { yt_do_atomic('yt_before_primary'); }
}
if ( ! function_exists( 'yt_after_primary' ) ) {
	function yt_after_primary() { yt_do_atomic('yt_after_primary'); }
}
if ( ! function_exists( 'yt_primary_start' ) ) {
	function yt_primary_start() { yt_do_atomic('yt_primary_start'); }
}
if ( ! function_exists( 'yt_primary_end' ) ) {
	function yt_primary_end() { yt_do_atomic('yt_primary_end'); }
}

/*
 * Secondary sidebar column
 */
if ( ! function_exists( 'yt_before_secondary' ) ) {
	function yt_before_secondary() { yt_do_atomic('yt_before_secondary'); }
}
if ( ! function_exists( 'yt_after_secondary' ) ) {
	function yt_after_secondary() { yt_do_atomic('yt_after_secondary'); }
}
if ( ! function_exists( 'yt_secondary_start' ) ) {
	function yt_secondary_start() { yt_do_atomic('yt_secondary_start'); }
}
if ( ! function_exists( 'yt_secondary_end' ) ) {
	function yt_secondary_end() { yt_do_atomic('yt_secondary_end'); }
}

/*
 * Tertiary sidebar column
 */
if ( ! function_exists( 'yt_before_tertiary' ) ) {
	function yt_before_tertiary() { yt_do_atomic('yt_before_tertiary'); }
}
if ( ! function_exists( 'yt_after_tertiary' ) ) {
	function yt_after_tertiary() { yt_do_atomic('yt_after_tertiary'); }
}
if ( ! function_exists( 'yt_tertiary_start' ) ) {
	function yt_tertiary_start() { yt_do_atomic('yt_tertiary_start'); }
}
if ( ! function_exists( 'yt_tertiary_end' ) ) {
	function yt_tertiary_end() { yt_do_atomic('yt_tertiary_end'); }
}
/*
 * Search form
 */
if ( ! function_exists( 'yt_before_search_form' ) ) {
	function yt_before_search_form() { yt_do_atomic('yt_before_search_form'); }
}
if ( ! function_exists( 'yt_after_search_form' ) ) {
	function yt_after_search_form() { yt_do_atomic('yt_after_search_form'); }
}
if ( ! function_exists( 'yt_search_form_start' ) ) {
	function yt_search_form_start() { yt_do_atomic('yt_search_form_start'); }
}
if ( ! function_exists( 'yt_search_form_end' ) ) {
	function yt_search_form_end() { yt_do_atomic('yt_search_form_end'); }
}

/*
 * The main Loop
 */
if ( ! function_exists( 'yt_before_loop' ) ) {
	function yt_before_loop() { yt_do_atomic('yt_before_loop'); }
}
if ( ! function_exists( 'yt_after_loop' ) ) {
	function yt_after_loop() { yt_do_atomic('yt_after_loop'); }
}
if ( ! function_exists( 'yt_loop_start' ) ) {
	function yt_loop_start() { yt_do_atomic('yt_loop_start'); }
}
if ( ! function_exists( 'yt_loop_end' ) ) {
	function yt_loop_end() { yt_do_atomic('yt_loop_end'); }
}

/*
 * Footer
 */
if ( ! function_exists( 'yt_before_footer' ) ) {
	function yt_before_footer() { yt_do_atomic('yt_before_footer'); }
}
if ( ! function_exists( 'yt_inside_footer' ) ) {
	function yt_inside_footer() { yt_do_atomic('yt_inside_footer'); }
}
if ( ! function_exists( 'yt_after_footer' ) ) {
	function yt_after_footer() { yt_do_atomic('yt_after_footer'); }
}
if ( ! function_exists( 'yt_footer_start' ) ) {
	function yt_footer_start() { yt_do_atomic('yt_footer_start'); }
}
if ( ! function_exists( 'yt_footer_end' ) ) {
	function yt_footer_end() { yt_do_atomic('yt_footer_end'); }
}


add_action( 'template_redirect', 'yt_ajax_handler'  );
/**
 * Alternatively Better Ajax Handler
 * Our own Ajax response, avoiding calling admin-ajax
 *
 * @since 1.0
 */
if ( ! function_exists( 'yt_ajax_handler' ) ) {
	function yt_ajax_handler() {

		if( empty( $_REQUEST['yt_ajaxify'] ) )
			return;

		if( empty( $_REQUEST['action'] ) )
			return;

		define( 'DOING_AJAX', true );

		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );

		send_nosniff_header();
		nocache_headers();

		if ( is_user_logged_in() ) {
			/**
			 * Fires authenticated AJAX actions for logged-in users.
			 */
			do_action( 'yt_ajax_' . $_REQUEST['action'] );
		} else {
			/**0....
			 * Fires non-authenticated AJAX actions for logged-out users.
			 */
			do_action( 'yt_ajax_nopriv_' . $_REQUEST['action'] );
		}

		die( '0' );
	}
}

/**
 *Dynamic Class Filters
 *
 **/
 
/*
 * Section classes
 */
if ( ! function_exists( 'yt_section_classes' ) ) {
	function yt_section_classes( $class = '', $pos = '' ) {
		
		/*Separates classes with a single space*/
		$output = '';
		
		if(yt_get_section_classes($class, $pos)){
			
			$output .= 'class="' . esc_attr( join( ' ', yt_get_section_classes( $class, $pos ) ) ) . '"';
			
		}
		
		echo $output;
	}
}

/*
 * Get Section classes
 */
if ( ! function_exists( 'yt_get_section_classes' ) ) {
	function yt_get_section_classes($class = '', $pos = '') {
		
		$classes = array();

		$class = (array) $class;
		
		if ( ! empty( $class ) ) {
			if ( !is_array( $class ) )
				$class = preg_split( '#\s+#', $class );
				
			$classes = array_merge( $classes, $class );
		} else {
				// Ensure that we always coerce class to being an array.
				$class = array();
		}
		
		$classes = array_map( 'esc_attr', $classes );
		
		return apply_filters( 'yt_section_class', $classes, $pos );
	}
}


/**
 * Default body class
 *
 **/
 
/* Add specific CSS class by filter */

add_filter('body_class','yt_default_body_class');
add_filter('yt_admin_body_class','yt_default_body_class');

if( !function_exists( 'yt_default_body_class' ) ) {

	function yt_default_body_class($classes) {
		global $post;
		
		$classes[] = 'yeah-framework'; 
		if( !class_exists( 'YT_Browser_Detector' ) ){
			
			require_once( wpthms_FRAMEWORK_DIR . 'classes/class.browser-detector.php' );
		
		}

		//Retrieve Browser name
		$browser = YT_Browser_Detector::detect();
		$classes[] = $browser['name'] . '-browser';
		
		$classes[] = $browser['platform'] . '-platform';
		
		//Retrieve device type
		if( !class_exists( 'Mobile_Detect' ) ){
			
			require_once( wpthms_FRAMEWORK_DIR . 'extended/class.mobile-detect.php' );
		
		}
		
		$detect = new Mobile_Detect();
		
		if($detect->isMobile()){
			$classes[] = 'handheld';
			
			$classes[] = $detect->isTablet() ? 'tablet' : 'phone';
			
			if($detect->isIphone()) { $classes[] = 'iphone'; };
			if($detect->isIpad()) { $classes[] = 'ipad'; };
			if($detect->isBlackBerry()) { $classes[] = 'blackberry'; };
			if($detect->isKindle()) { $classes[] = 'kindle'; };
			if($detect->isOpera()) { $classes[] = 'opera'; };
		
		}else{
			$classes[] = 'desktop';
		}		
		
		//device OS
		if($detect->isiOS()) { $classes[] = 'ios'; };
		if($detect->isAndroidOS()) { $classes[] = 'android'; };
		
		
		if( !is_admin() ){
			
			
			$frontend_body_class = array();
			
			// Check user logged in?
			if( ! is_user_logged_in() ) {
				$frontend_body_class[] = 'not-logged-in';
				$frontend_body_class[] = 'yeah-framework';
			}
			
			// Check is multi-author
			if ( is_multi_author() ) {
				$frontend_body_class[] = 'group-blog';
			}
			
			$classes = array_unique( wp_parse_args( $classes, $frontend_body_class ) );
			$classes = apply_filters( 'yt_frontend_body_class', $classes );
			
			//print_r($classes);
			
		}
		
		//echo implode(', ',$classes);
		// return the $classes array
		return apply_filters( 'yt_body_class', $classes );
	}
}

	
add_filter( 'admin_body_class', 'yt_admin_body_class' );
	

if( !function_exists( 'yt_admin_body_class' ) ) {

	function yt_admin_body_class( $classes ) {
		
		$admin_body_class = apply_filters( 'yt_admin_body_class', array() );
		 
		$admin_body_class = array_unique( $admin_body_class );
		
		$classes .= ' ' . join( ' ', $admin_body_class );
		
		return $classes;
	}
}
/**
 * Atomic actions/filters
 *
 **/
 
if ( ! function_exists( 'yt_do_atomic' ) ) {
/**
 * yt_do_atomic()
 * 
 * Adds contextual action hooks to the theme.  This allows users to easily add context-based content 
 * without having to know how to use WordPress conditional tags.  The theme handles the logic.
 *
 * An example of a basic hook would be 'yt_head'.  The yt_do_atomic() function extends that to 
 * give extra hooks such as 'yt_head_home', 'yt_head_singular', and 'yt_head_singular-page'.
 *
 * Major props to Ptah Dunbar for the do_atomic() function.
 * @link http://ptahdunbar.com/wordpress/smarter-hooks-context-sensitive-hooks
 *
 * @since 3.9.0
 * @uses yt_get_query_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 */
	function yt_do_atomic( $tag = '', $args = '' ) {
		if ( !$tag ) return false;
	
		/* Do actions on the basic hook. */
		do_action( $tag, $args );
		/* Loop through context array and fire actions on a contextual scale. */
		foreach ( (array) yt_get_query_context() as $context )
			do_action( "{$tag}_{$context}", $args );		
	} // End yt_do_atomic()
}

if ( ! function_exists( 'yt_apply_atomic' ) ) {
/**
 * yt_apply_atomic()
 * 
 * Adds contextual filter hooks to the theme.  This allows users to easily filter context-based content 
 * without having to know how to use WordPress conditional tags. The theme handles the logic.
 *
 * An example of a basic hook would be 'yt_entry_meta'.  The yt_apply_atomic() function extends 
 * that to give extra hooks such as 'yt_entry_meta_home', 'yt_entry_meta_singular' and 'yt_entry_meta_singular-page'.
 *
 * @uses yt_get_query_context() Gets the context of the current page.
 * @param string $tag Usually the location of the hook but defines what the base hook is.
 * @param mixed $value The value to be filtered.
 * @return mixed $value The value after it has been filtered.
 */
	function yt_apply_atomic( $tag = '', $value = '' ) {
		if ( ! $tag ) return false;
		/* Get theme prefix. */
		$pre = 'yt';
		/* Apply filters on the basic hook. */
		$value = apply_filters( "{$pre}_{$tag}", $value );
		/* Loop through context array and apply filters on a contextual scale. */
		foreach ( (array)yt_get_query_context() as $context )
			$value = apply_filters( "{$pre}_{$context}_{$tag}", $value );
		/* Return the final value once all filters have been applied. */
		return $value;
	} // End yt_apply_atomic()
}

if ( ! function_exists( 'yt_get_query_context' ) ) {
/**
 * yt_get_query_context()
 *
 * Retrieve the context of the queried template.
 *
 * @return array $query_context
 */
	function yt_get_query_context() {
		global $wp_query, $query_context;
		
		/* If $query_context->context has been set, don't run through the conditionals again. Just return the variable. */
		if ( is_object( $query_context ) && isset( $query_context->context ) && is_array( $query_context->context ) ) {
			return $query_context->context;
		}
	
		unset( $query_context );
		$query_context = new stdClass();
		$query_context->context = array();
	
		/* Front page of the site. */
		if ( is_front_page() ) {
			$query_context->context[] = 'home';
		}
	
		/* Blog page. */
		if ( is_home() && ! is_front_page() ) {
			$query_context->context[] = 'blog';
	
		/* Singular views. */
		} elseif ( is_singular() ) {
			$query_context->context[] = 'singular';
			if( !empty( $wp_query->post->post_type ) )
				$query_context->context[] = "singular_{$wp_query->post->post_type}";
		
			/* Page Templates. */
			if ( is_page_template() ) {
				$to_skip = array( 'page', 'post' );
			
				$page_template = basename( get_page_template() );
				$page_template = str_replace( '.php', '', $page_template );
				$page_template = str_replace( '.', '_', $page_template );
			
				if ( $page_template && ! in_array( $page_template, $to_skip ) ) {
					$query_context->context[] = $page_template;
				}
			}
			
			if( !empty( $wp_query->post->post_type ) )
				$query_context->context[] = "singular_{$wp_query->post->post_type}_{$wp_query->post->ID}";
		}
	
		/* Archive views. */
		elseif ( is_archive() ) {
			$query_context->context[] = 'archive';
	
			/* Taxonomy archives. */
			if ( is_tax() || is_category() || is_tag() ) {
				$term = $wp_query->get_queried_object();
				$query_context->context[] = 'taxonomy';
				$query_context->context[] = $term->taxonomy;
				$query_context->context[] = "{$term->taxonomy}_" . sanitize_html_class( $term->slug, $term->term_id );
			}
	
			/* User/author archives. */
			elseif ( is_author() ) {
				$query_context->context[] = 'user';
				$query_context->context[] = 'user_' . sanitize_html_class( get_the_author_meta( 'user_nicename', get_query_var( 'author' ) ), $wp_query->get_queried_object_id() );
			}
	
			/* Time/Date archives. */
			else {
				if ( is_date() ) {
					$query_context->context[] = 'date';
					if ( is_year() )
						$query_context->context[] = 'year';
					if ( is_month() )
						$query_context->context[] = 'month';
					if ( get_query_var( 'w' ) )
						$query_context->context[] = 'week';
					if ( is_day() )
						$query_context->context[] = 'day';
				}
				if ( is_time() ) {
					$query_context->context[] = 'time';
					if ( get_query_var( 'hour' ) )
						$query_context->context[] = 'hour';
					if ( get_query_var( 'minute' ) )
						$query_context->context[] = 'minute';
				}
			}
		}
	
		/* Search results. */
		elseif ( is_search() ) {
			$query_context->context[] = 'search';
		/* Error 404 pages. */
		} elseif ( is_404() ) {
			$query_context->context[] = 'error_404';
		}
		
		return $query_context->context;
	} // End yt_get_query_context()
}