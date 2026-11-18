<?php 

/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;

/**************************************************************
 * Compatibilities for Third Party Plugins
 ***************************************************************/

/**
 * JetPack Photon URL
 * 
 * @access public
 * @since 1.0.2
 */
if( !function_exists( 'yt_photon_url' ) ) {
	function yt_photon_url( $image_url, $args = array(), $scheme = null ) {
		if( class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'photon' ) && function_exists( 'jetpack_photon_url' ))
			return jetpack_photon_url( $image_url, $args = array(), $scheme = null );

		return $image_url;

	}
}

/**
 * Helper function to unregister a WPML string
 *
 * @param     context
 * @param     id  
 *
 * @access    public
 * @since     1.0
 */
if ( ! function_exists( 'yt_wpml_unregister_string' ) ) {
	function yt_wpml_unregister_string( $context, $id ) {
	
		if ( function_exists( 'icl_unregister_string' ) ) {
		  
			icl_unregister_string( $context, $id );
		  
		}
	  
	}
}
/**
 * Helper function to register a WPML string
 *
 * @param     context
 * @param     id  
 * @value     id  
 *
 * @access    public
 * @since     1.0
 */
if ( ! function_exists( 'yt_wpml_register_string' ) ) {
	function yt_wpml_register_string( $context, $id, $value ) {
	
		if ( function_exists( 'icl_register_string' ) ) {
		
			icl_register_string( $context, $id, $value );
		
		}
	  
	}
}
/**
 * Helper function to return a translated string
 *
 * @param     context
 * @param     id  
 * @value     id  
 *
 * @access    public
 * @since     1.1
 */
if ( ! function_exists( 'yt_wpml_icl_t' ) ) {
	function yt_wpml_icl_t( $context, $id , $value) {
	
		if ( function_exists( 'icl_t' ) ) {
		
			return icl_t( $context, $id, $value );
		
		}

		return $value;
	  
	}
}