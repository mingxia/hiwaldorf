<?php
 // This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Admin Functions
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */

/*
 * Dynamically genetate css using less css
 * 
 * @since 1.1
 * @return string
 */
if( !function_exists( 'yt_lesscss_compiler' )) {

	function yt_lesscss_compiler( $lesscss, $compress = false ){

		$compiled_css = '';

		$dir_uri = get_template_directory_uri();

		$remote_compilier = apply_filters( 'yt_lesscss_compiler_remote_mode', false ) ;

		if( !$remote_compilier ){

			if( !class_exists( 'Less_Parser' ) )
				require_once(  'extended/less-parser/Less.php' );
			
			try{
				$parser = new Less_Parser( array( 'compress' => $compress ) );

				$parser->parse( $lesscss );

				$compiled_css = $parser->getCss();
			}catch( Exception $e){
				$error_message = $e->getMessage();
				$compiled_css .= $error_message;
			}
		}

		// Allow filtering
		$compiled_css = apply_filters( 'yt_lesscss_compiler_before_return_compiled_css', $compiled_css, $lesscss );

		return $compiled_css;

		// wp_add_inline_style( 'yt-custom-styles', $custom_css );

	}
}


/**
 * Set default term for custom post types
 *
 * @access public
 * @since 1.0
 */
add_action( 'save_post', 'yt_set_default_object_terms', 100, 2 );

if( !function_exists( 'yt_set_default_object_terms' ) ) {
	
	function yt_set_default_object_terms( $post_id, $post ) {
		
		$defaults = apply_filters( 'yt_default_object_terms_array' , array() );
		
		if( empty( $defaults ) ) 
			return;
		/* 
		$defaults = array(
			'portfolio-type' => array( 'uncategorized' => __('Uncategorized','wpthms'))
		);
		*/
		if ( 'publish' === $post->post_status ) {
			
			$taxonomies = get_object_taxonomies( $post->post_type );
			
			foreach ( (array) $taxonomies as $taxonomy ) {
				
				$terms = wp_get_post_terms( $post_id, $taxonomy );
				
				if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
					
					wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
					
				}
			}
		}
	}
}
/**
 * Get Post format meta
 *
 * @return array
 * @access public
 * @since 1.0
 */
if( !function_exists( 'yt_get_post_formats_meta' ) ) {
	function yt_get_post_formats_meta(  $post_id ){
		
		if( empty( $post_id ) && !is_numeric( $post_id ) ){
			return;
		}
		if ( !current_theme_supports( 'post-formats' ) ) {
			return;
		}
		/*Allow filtering for theme migration*/ 
		$ouput = apply_filters( 'yt_post_formats_meta_data', array(
			'_thumbnail_id' 				=> get_post_meta( $post_id, '_thumbnail_id', true ),
			'_format_image' 				=> get_post_meta( $post_id, '_format_image', true ),
			'_format_url' 					=> get_post_meta( $post_id, '_format_url', true ),
			'_format_gallery' 				=> get_post_meta( $post_id, '_format_gallery', true ),
			'_format_audio_embed'			=> get_post_meta( $post_id, '_format_audio_embed', true ),
			'_format_video_embed' 			=> get_post_meta( $post_id, '_format_video_embed', true ),
			'_format_quote_source_name' 	=> get_post_meta( $post_id, '_format_quote_source_name', true ),
			'_format_quote_source_url' 		=> get_post_meta( $post_id, '_format_quote_source_url', true ),
			'_format_link_url' 				=> get_post_meta( $post_id, '_format_link_url', true )
		), $post_id );
		
		return $ouput;
		
	}
}