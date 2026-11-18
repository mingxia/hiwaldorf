<?php
/*
Plugin Name: YT Impressive Like Button (HiWaldorf)
Plugin URI: https://github.com/mingxia/hiwaldorf/tree/master/hiwaldorf-yt-plugins/yt-impressive-like
Description: Original wpthms Sparkle companion plugin; maintained by HiWaldorf for existing sites.
Version: 2026.11.0
Author: wpthms (original), HiWaldorf (maintenance)
Author URI: https://hiwaldorf.com/
 Update URI: https://github.com/mingxia/hiwaldorf
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

define( 'YT_IMPRESSIVE_LIKE_VER', '2026.11.0' );
define( 'YT_IMPRESSIVE_LIKE_URI', plugin_dir_url( __FILE__ ) );
define( 'YT_IMPRESSIVE_LIKE_DIR', plugin_dir_path( __FILE__ ) );
define( 'YT_IMPRESSIVE_LIKE_PLUGIN', __FILE__ );
define( 'YT_IMPRESSIVE_LIKE_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );
	
	
add_action( 'plugins_loaded', 'yt_impressive_like_text_domain' );	
add_action('wp_enqueue_scripts', 'yt_impressive_like_scripts'); 
add_action('wp_ajax_nopriv_yt_yeah_i_like_it', 'yt_impressive_like_ajax_likes');  
add_action('wp_ajax_yt_yeah_i_like_it', 'yt_impressive_like_ajax_likes');



/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
function yt_impressive_like_text_domain() {
  load_plugin_textdomain( 'yt-impressive-like', false, YT_IMPRESSIVE_LIKE_DIRNAME . '/langs/' ); 
}

function yt_impressive_like_scripts(){
	
	wp_enqueue_style( 'yt-impressive-like-style', 	YT_IMPRESSIVE_LIKE_URI . 'css/yt-impressive-like.css' );
	wp_enqueue_script('yt-impressive-like-js', 		YT_IMPRESSIVE_LIKE_URI . 'js/yt-impressive-like.js', array('jquery'), '1.0', true );  
	wp_localize_script('yt-impressive-like-js', 'ytImpressiveLikeVars', array(  
		'ajax_url' => admin_url('admin-ajax.php'),  
		'nonce' => wp_create_nonce('yt-impressive-like-nonce'),
		'symbol' => apply_filters('yt_impressive_like_symbol', '&hearts;'),
		'number' => apply_filters('yt_impressive_like_symbol_number', 10),
		'like' => __('Like', 'yt-impressive-like'),
		'likes' => __('Likes', 'yt-impressive-like'),
		'unlike' => __('Unlike', 'yt-impressive-like')
	));
			
}

/**
 * Ajax like
 */
function yt_impressive_like_ajax_likes(){
	// Check for nonce security
	$nonce = $_POST['nonce'];
 
	if ( ! wp_verify_nonce( $nonce, 'yt-impressive-like-nonce' ) )
		die();
	
	$rating_type = $_POST['rating_type'];
	
	if(isset($_POST['post_like']))
	{
		// Retrieve user IP address
		$ip = $_SERVER['REMOTE_ADDR'];
		$post_id = $_POST['post_id'];
		
		// Get voters'IPs for the current post
		$liked_ip = get_post_meta($post_id, apply_filters( 'yt_impressive_liked_ip_meta_key', '_liked_ip' ), true);

		if(!is_array($liked_ip))
			$liked_ip = array();
		
		// Get votes count for the current post
		$meta_count_meta = get_post_meta($post_id, apply_filters( 'yt_impressive_like_count_meta_key', '_likes' ) );
		$meta_count = isset( $meta_count_meta[0] ) ? $meta_count_meta[0] : 0;
		

		// if action == like
		if( 'like' == $rating_type){
			
			$liked_ip[$ip] = time();
			// Save IP and increase votes count
			update_post_meta($post_id, apply_filters( 'yt_impressive_liked_ip_meta_key', '_liked_ip' ), $liked_ip);
			update_post_meta($post_id, apply_filters( 'yt_impressive_like_count_meta_key', '_likes' ), ++$meta_count);
		
		//Unlike
		}else{
			
			unset($liked_ip[$ip]);
			
			update_post_meta($post_id, apply_filters( 'yt_impressive_liked_ip_meta_key', '_liked_ip' ), $liked_ip);
			update_post_meta($post_id, apply_filters( 'yt_impressive_like_count_meta_key', '_likes' ), --$meta_count);
			
		}
			echo $meta_count;

		// Use has already voted ?
		/*if(!yt_impressive_like_liked($post_id))
		{
			$liked_ip[$ip] = time();

			// Save IP and increase votes count
			update_post_meta($post_id, '_liked_ip', $liked_ip);
			update_post_meta($post_id, '_likes', ++$meta_count);
			
			// Display count (ie jQuery return value)
			echo $meta_count;
		}
		else
			echo 'already';*/
	}
	die();
}

/**
 * Check if reader has liked
 */
function yt_impressive_like_liked($post_id){  
	$time_before_revote = apply_filters('yt_impressive_like_expire_time_for_revote', 120); // = 2 hours  
  
	// Retrieve post votes IPs  
	$liked_ip = get_post_meta($post_id, apply_filters( 'yt_impressive_liked_ip_meta_key', '_liked_ip' ), true);  
	
	  
	if(!is_array($liked_ip))  
		$liked_ip = array();  
		  
	// Retrieve current user IP  
	$ip = $_SERVER['REMOTE_ADDR'];  
	  
	// If user has already voted  
	if(in_array($ip, array_keys($liked_ip)))  
	{  
		$time = $liked_ip[$ip];  
		$now = time();  

		//echo round(($now - $time) / 60);
		  
		// Compare between current time and vote time  
		if(round(($now - $time) / 60) > $time_before_revote)  
			return false;  
			  
		return true;  
	}  
	  
	return false;  
} 

/**
 * Output like button
 */
function yt_impressive_like_display( $post_id = '', $allow_unlike = true, $extra_class = '' ){  
  	
	if( empty( $post_id ) && isset( $GLOBALS['post'] ) ){
		$post = $GLOBALS['post'];
		$post_id = $post->ID;
	}

	if ( empty( $post_id ) )
		return '';

	$likes_count_meta = get_post_meta($post_id, apply_filters( 'yt_impressive_like_count_meta_key', '_likes' ) );
	$likes_count = isset( $likes_count_meta[0] ) ? $likes_count_meta[0] : 0;
	
		
	$symbol = apply_filters('yt_impressive_like_symbol', '&hearts;');
	
	$class = '';
	$class .= ' yt-impressive-unliked';
	$action = 'like';
	$label = __('Like', 'yt-impressive-like');
	
	if( yt_impressive_like_liked( $post_id ) ){
		$class = ' yt-impressive-already-liked';
		$action = 'unlike';
		$label = __('Unlike', 'yt-impressive-like');
	}
		
	if( $allow_unlike ){
		$class .= ' yt-impressive-allow-unlike';
	}

	$class .= ' ' . $extra_class;
	
	$output = '<span class="yt-impressive-like-button' . $class . '" data-post-id=' . $post_id . ' data-count="' . $likes_count . '" data-action="' . $action . '">';
		
		//Before the button
		$output .= $symbol ? '<span class="yt-il-symbol">' . $symbol . '</span>' : '';
		
		$output .= '<span class="yt-il-counter">
			<span class="yt-il-counter-number">' . $likes_count . '</span>&nbsp;<span class="yt-il-counter-desc">' . ( $likes_count > 1 ? __('Likes', 'yt-impressive-like') : __('Like', 'yt-impressive-like') ) . '</span>
		</span>';
		
		
		$output .= '<a class="yt-il-like-action" href="#like">' . $label . '</a>';
		
		
		
	$output .= '</span>';
	
	return $output;  
}