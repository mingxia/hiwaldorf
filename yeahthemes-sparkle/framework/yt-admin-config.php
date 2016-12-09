<?php
 // This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Admin Config
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */


/**
 * Check the current post for the existence of a short code
 *
 * @return      void
 *
 * @access      public
 * @since       1.0
 *
 **/
add_action( 'widgets_init', 'yt_widgets_init' );

if( !function_exists( 'yt_widgets_init' ) ) {
	
	function yt_widgets_init() {
		
		$sidebars = apply_filters( 'yt_sidebar_array', array(
		
			'main-sidebar' => esc_html__('Main Sidebar','wpthms'),
			
		));
		
		//print_r($sidebars);
		
		if( empty( $sidebars ))
			return;
			
		foreach( ( array ) $sidebars as $sidebar_id => $sidebar_name ){
			
			register_sidebar(
				array(
					'name' 				=> $sidebar_name,
					'id' 				=> $sidebar_id,
					'before_title' 		=> apply_filters( 'yt_sidebar_before_title', '<h3 class="widget-title">' ),
					'after_title'		=> apply_filters( 'yt_sidebar_after_title', '</h3>' ),
					'before_widget' 	=> apply_filters( 'yt_sidebar_before_widget', '<aside id="%1$s" class="widget %2$s">' ),
					'after_widget' 		=> apply_filters( 'yt_sidebar_after_widget', '</aside>' )
				)
			);	
			
		}
	}
}

add_filter( 'attachment_link', 'yt_enhanced_image_navigation', 10, 2 );
/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
if ( ! function_exists( 'yt_enhanced_image_navigation' ) ) {
	function yt_enhanced_image_navigation( $url, $id ) {
		if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
			return $url;
	
		$image = get_post( $id );
		if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
			$url .= '#content';
	
		return $url;
	}

}

add_filter( 'upload_mimes', 'yt_restrict_mime_types' );
/**
 * Restrict mime types
 */
if ( ! function_exists( 'yt_restrict_mime_types' ) ) {
	function yt_restrict_mime_types( $existing_mimes ) {
		// add your extension to the array
		
		$mime_type = apply_filters( 'yt_upload_mime_types', array(
			'svg' => 'image/svg+xml',
			'eot' => 'application/vnd.ms-fontobject',
			'woff' => 'application/x-font-woff',
			'ttf' => 'font/truetype',
			'css' => 'text/css',
		) );
		
		$existing_mimes = array_merge( $existing_mimes, $mime_type );
		
		return $existing_mimes;
	}
}
