<?php 

/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;

/**
 * Check array is associative or sequential
 *
 * @param     array  
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */

if( !function_exists( 'yt_is_assoc' ) ) {
	
	function yt_is_assoc($array) {
		
		return (bool)count( array_filter( array_keys( $array ), 'is_string' ) );
	  
	}
	
}
/**
 * Email validator
 *
 * @param     string  
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */

if( !function_exists( 'yt_is_valid_email' ) ) {
	
	function yt_is_valid_email($email) {
		
		return filter_var( $email, FILTER_VALIDATE_EMAIL);
	  
	}
	
}
/*
 * Is Json string
 *
 * @param     string  
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */
function yt_is_json( $string ) {
	if( function_exists( 'json_last_error' )){
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}else{
		return false;
	}
}
/*
 * Is Post type
 *
 * @param     string  
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */
 
if( !function_exists( 'yt_is_post_type' ) ) {
	function yt_is_post_type( $type ){
		global $wp_query;
		
		if( $type == get_post_type( $wp_query->post->ID ) ) 
			return true;
		
		return false;
	}
}

/**
 * Validation
 *
 * @access public
 * @since 1.0
 */
if ( ! function_exists( 'yt_is_url' ) ) {
	function yt_is_url($url) {
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}
}
if ( ! function_exists( 'yt_is_hexcolor' ) ) {
	function yt_is_hexcolor($color) {
		return preg_match('/^#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/i', $color);
	}
}

if ( ! function_exists( 'yt_is_image' ) ) {
	function yt_is_image($image){
		return preg_match('#^http:\/\/(.*)\.(gif|png|jpg|bmp|jpeg)$#i', $image);
	}
}


/**
 * Is Woocommce
 *
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */
 
if( !function_exists( 'yt_is_woocommerce' ) ) {
	function yt_is_woocommerce(){

		if( class_exists( 'woocommerce' ) && is_woocommerce() )
			return true;
		
		return false;
	}
}
/**
 * Is Woocommce Page
 *
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_is_woocommerce_page' ) ) {
	function yt_is_woocommerce_page(){

		if( class_exists( 'woocommerce' ) ){
			$conditional = is_woocommerce() 
				|| is_product_category()
				|| is_product_tag()
				|| is_cart() 
				|| is_checkout()
				|| is_checkout_pay_page()
				|| is_account_page()
				|| is_order_received_page()
				|| is_add_payment_method_page();
			$conditional = apply_filters( 'yt_is_woocommerce_page_conditional', $conditional );

			if( $conditional )
				return true;
		}
		
		return false;
	}
}

/**
 * Is bbPress
 *
 * @return    bool
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_is_bbpress' ) ) {
	function yt_is_bbpress(){
		if( class_exists( 'bbPress' ) && is_bbpress() )
			return true;
		return false;
	}
}