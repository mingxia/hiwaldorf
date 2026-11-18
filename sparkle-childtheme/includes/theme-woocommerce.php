<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

add_action( 'after_setup_theme', 'yt_wc_after_setup_theme' );

function yt_wc_after_setup_theme() {

	add_theme_support( 'woocommerce' );

}

/***************************************************************************************/
/* WooCommerce Overrides */
/***************************************************************************************/
add_filter( 'woocommerce_enqueue_styles', 'yt_wc_dequeue_styles' );
/**
 * Remove each style one by one
 */
function yt_wc_dequeue_styles( $enqueue_styles ) {
	unset( $enqueue_styles['woocommerce-general'] );	// Remove the gloss
	unset( $enqueue_styles['woocommerce-layout'] );		// Remove the layout
	unset( $enqueue_styles['woocommerce-smallscreen'] );	// Remove the smallscreen optimisation
	return $enqueue_styles;
}

add_action( 'yt_theme_scripts_before_enqueue_styles', 'yt_wc_default_style' );
/**
 * Load default framework css
 */
function yt_wc_default_style(){

	$css_dir = get_template_directory_uri() . '/css/';
	$js_dir = get_template_directory_uri() . '/js/';

	// Dequeue Increment plugin
	wp_dequeue_style( 'wcqi-css' );

	wp_enqueue_style('woocommerce-layout'			, $css_dir . 'woocommerce-layout.css');
	wp_enqueue_style('woocommerce-smallscreen'		, $css_dir . 'woocommerce-smallscreen.css', 'woocommerce-layout', '1.0' );
	wp_enqueue_style('woocommerce-general'			, $css_dir . 'woocommerce.css');
		
}
/*************************************************************************************
 * LAYOUT
 *************************************************************************************/
/**
 * 将迷你购物车修改为App下载
 *
 * @since 1.0
 */

add_filter( 'wp_nav_menu_items', 'yt_wc_custom_menu_item', 15, 2 );

if ( ! function_exists( 'yt_wc_custom_menu_item' ) ) {

	function yt_wc_custom_menu_item ( $items, $args ) {

		if( $args->theme_location == 'primary' && 'hide' !== yt_get_options( 'wc_main_menu_mini_cart' ) ){

			$items .= sprintf( '<li class="menu-item menu-item-type-custom menu-item-shopping-cart default-dropdown sub-menu-left pull-right">
				<a href="http://hiwaldorf.com/app/" target="_blank" title="客户端下载"><i class="fa fa-mobile" aria-hidden="true"></i>APP</a>
			</li>'
			);
		}

		return $items;
	}
}
