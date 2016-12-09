<?php
 // This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Admin Init
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */

/**
 * Global Object
 *
 * for use in frontend and backend
 * @return void
 *
 **/

add_action ( 'wp_print_scripts', 'yt_wpthms_global_object', 1 );

if( !function_exists( 'yt_wpthms_global_object' ) ) {
	function yt_wpthms_global_object(){
		if(!is_admin())
			return;
		global $post, $wp_query;
		
		$post_id = 0;
		
		if( !$wp_query->is_404 && !$wp_query->is_search && !$wp_query->is_archive ){
			$post_id = isset( $post->ID ) ? $post->ID : 0;
		}
		
		if( 'posts' === get_option( 'show_on_front' ) ){
			$post_id = 0;
		}
		
		if( $wp_query->is_home && get_option( 'page_for_posts' ) ){
			$post_id = get_option( 'page_for_posts' );	
		}

		$base_url = set_url_scheme( home_url( '/' ) );

		$ajaxurl = add_query_arg( array( 'yt_ajaxify' => 1 ), $base_url );
		
		$wpthms = array();
		$wpthms['_vars']['currentPostID'] = esc_js( $post_id );
		$wpthms['_vars']['ajaxurl'] = esc_url( $ajaxurl );
		$wpthms['_vars']['nonce'] = esc_js( wp_create_nonce('wpthms_frontend_nonce') );
		
		$wpthms = apply_filters('yt_wpthms_global_object', $wpthms );
		
		$output = 'var wpthms = {}';
		
		
		$output = 'var wpthms = ' . json_encode( $wpthms ) . ';';
	
		echo '<script type="text/javascript">/* <![CDATA[ */' . $output  .'/* ]]> */</script>' . "\n";
	}
}

/**
 * Load functions
 *
 * @since 1.0
 * @framework 1.0
 */		
require_once( 'yt-admin-hooks.php' ); 
require_once( 'yt-admin-config.php' ); 
require_once( 'yt-admin-helpers.php' ); 
require_once( 'yt-admin-functions.php' ); 
require_once( 'front-end/bootstrap-templates.php' ); 
require_once( 'classes/class.wpthms-walkers.php' );

/**
 * Load Widgets
 *
 * @since 1.0
 * @framework 1.0
 */
 
require_once( 'widgets/yt-smart-widget.class.php' );
require_once( 'widgets/widget-ads-125.php' );
require_once( 'widgets/widget-ads-full.php' );
require_once( 'widgets/widget-flickr.php' );
require_once( 'widgets/widget-facebook-likebox.php' );
require_once( 'widgets/widget-categorys-descendants.php' );
require_once( 'widgets/widget-childpages.php' );
require_once( 'widgets/widget-twitter-profile.php' );
require_once( 'widgets/widget-twitter-timelines.php' );
require_once( 'widgets/widget-mailchimp.php' );
require_once( 'widgets/widget-smart-tabby-widget.php' );
//require_once( 'widgets/widget-test.php' );


add_action( 'widgets_init', 'yt_init_framework_widgets' );
/**
 * Register Widget
 *
 * @since 1.0
 * @framework 1.0
 */
function yt_init_framework_widgets() {
	$widget_list = apply_filters( 'yt_framework_widgets_init', array(
		'YT_Ads125_Widget',
		'YT_AdsFull_Widget',
		'YT_Flickr_Widget',
		'YT_Facebook_Likebox_Widget',
		'YT_Category_Descendants_Widget',
		'YT_Childpages_Widget',
		'YT_Twitter_Profiles_Widget',
		'YT_Twitter_Timelines_Widget',
		'YT_Smart_Tabbed_Widget',
		'YT_Mailchimp_Subscription_Form_Widget',
		//'YT_Test_Widget'
	));
	
	if( empty( $widget_list ) )
		return;
		
	foreach( $widget_list as $widget ){
		register_widget( $widget );
	}
}
