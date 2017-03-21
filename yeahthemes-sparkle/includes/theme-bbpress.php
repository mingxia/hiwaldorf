<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
//add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
if( class_exists('bbPress')):

/**
 * Remove stylesheet for BBP_Private_Replies plugin
 */
global $bbp_private_replies;

if( is_a( $bbp_private_replies, 'BBP_Private_Replies' ))
	remove_action( 'wp_enqueue_scripts', array( $bbp_private_replies, 'register_plugin_styles' ) );
//add_filter ('bbp_no_breadcrumb', '__return_true');

add_action( 'after_setup_theme', 'yt_bbpress_after_setup_theme' );
/**
 * bbPress after theme setup
 */
function yt_bbpress_after_setup_theme(){
	//forum
	add_post_type_support( 'forum', 'thumbnail' );
	//add_post_type_support( 'topic', 'thumbnail' );

}

add_action( 'widgets_init', 'yt_bbpress_widget_init', 10 );
/**
 * bbPress register sidebar
 */
function yt_bbpress_widget_init(){

	register_sidebar(
		array(
			'name' 				=> __('Forum Main Sidebar', 'wpthms'),
			'id' 				=> 'f-main-sidebar',
			'before_title' 		=> apply_filters( 'yt_sidebar_before_title', '<h3 class="widget-title">' ),
			'after_title'		=> apply_filters( 'yt_sidebar_after_title', '</h3>' ),
			'before_widget' 	=> apply_filters( 'yt_sidebar_before_widget', '<aside id="%1$s" class="widget %2$s">' ),
			'after_widget' 		=> apply_filters( 'yt_sidebar_after_widget', '</aside>' )
		)
	);

	register_sidebar(
		array(
			'name' 				=> __('Forum Sub Sidebar', 'wpthms'),
			'id' 				=> 'f-sub-sidebar',
			'before_title' 		=> apply_filters( 'yt_sidebar_before_title', '<h3 class="widget-title">' ),
			'after_title'		=> apply_filters( 'yt_sidebar_after_title', '</h3>' ),
			'before_widget' 	=> apply_filters( 'yt_sidebar_before_widget', '<aside id="%1$s" class="widget %2$s">' ),
			'after_widget' 		=> apply_filters( 'yt_sidebar_after_widget', '</aside>' )
		)
	);	
}

add_filter( 'yt_theme_dynamic_sidebars_default', 'yt_bbpress_sidebar_secondary' );
/**
 * bbPress custom sidebar
 */
function yt_bbpress_sidebar_secondary($sidebar){
	if( yt_is_bbpress() && 'main-sidebar' == $sidebar)
		return 'f-main-sidebar';

	if( yt_is_bbpress() && 'sub-sidebar' == $sidebar)
		return 'f-sub-sidebar';

	return $sidebar;
}

/**
 * Sticky label
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_bbpress_sticky_topic_label')) {
	add_action( 'bbp_theme_before_topic_title', 'yt_bbpress_sticky_topic_label' );
	function yt_bbpress_sticky_topic_label() {
		
		global $post;
		
		$output_status = '';
		$topic_id = bbp_get_topic_id();
		
		if(bbp_is_topic_sticky($topic_id)){
			$output_status = '<span class="label '.(bbp_is_topic_super_sticky($topic_id) ? ' label-primary super-sticky bbp-super-sticky-label' : 'sticky label-primary bbp-sticky-label' ).' margin-right-10" title="">'.__('Sticky','wpthms').'</span>';
			
		}
		
		echo $output_status;
		
	}
}

add_action( 'bbp_theme_before_forum_title', 'yt_bbpress_forum_thumbnail' );
/**
 * Forum archive topic thumbnail
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_bbpress_forum_thumbnail')) {

	function yt_bbpress_forum_thumbnail() {
		
		global $post;
		
		$output = '';
		$post_id = bbp_get_forum_id();
		
		$thumbnail = has_post_thumbnail($post_id) ? get_the_post_thumbnail($post_id, 'thumbnail', array('class'=> 'yt-bbp-forum-thumnnail')) : '';
		
		$output = $thumbnail ? '<a href="' . esc_url( get_permalink( $post_id ) ) . '" class="yt-bbp-post-thumbnail" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . $thumbnail .'</a>' : '';
		
		echo $output;
		
	}
}

add_action( 'bbp_theme_before_topic_title', 'yt_bbpress_topic_thumbnail' );
/**
 * Forum archive topic thumbnail
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_bbpress_topic_thumbnail')) {
	function yt_bbpress_topic_thumbnail() {
		
		global $post;
		
		$output = '';
		$post_id = bbp_get_topic_id();
		
		$thumbnail = has_post_thumbnail($post_id) ? get_the_post_thumbnail($post_id, 'thumbnail', array('class'=> 'yt-bbp-topic-thumnnail')) : '';
		
		$output = $thumbnail ? '<a href="' . esc_url( get_permalink( $post_id ) ) . '" class="yt-bbp-post-thumbnail" title="' . esc_attr( get_the_title( $post_id ) ) . '">' . $thumbnail .'</a>' : '';
		
		echo $output;
		
		
	}
}

add_action( 'bbp_theme_before_reply_content', 'yt_bbpress_reply_thumbnail' );
/**
 * Topic thumbnail for single topic
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_bbpress_reply_thumbnail')) {
	function yt_bbpress_reply_thumbnail() {
		
		global $post;

		static $yt_first_reply = true;
		
		$output = '';
		$post_id = bbp_get_topic_id();
		
		if(has_post_thumbnail($post_id)){
			
			$output = '<div class="bbp-topic-thumbnail margin-bottom-30" title="' . esc_attr( get_the_title( $post_id ) ).'">' . get_the_post_thumbnail( $post_id, 'large' ) . '</div>';
		}
		if( $yt_first_reply )
			echo $output;
		
		$yt_first_reply = false;
		
	}
}


endif;// class_exists('bbPress')