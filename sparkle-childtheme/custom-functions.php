<?php

/**********************************************************************************************************
 *        __   _______    _    _   _ _____ _   _ _____ __  __ _____ ____         ____ ___  __  __
 *        \ \ / / ____|  / \  | | | |_   _| | | | ____|  \/  | ____/ ___|       / ___/ _ \|  \/  |
 *         \ V /|  _|   / _ \ | |_| | | | | |_| |  _| | |\/| |  _| \___ \      | |  | | | | |\/| |
 *          | | | |___ / ___ \|  _  | | | |  _  | |___| |  | | |___ ___) |  _  | |__| |_| | |  | |
 *          |_| |_____/_/   \_\_| |_| |_| |_| |_|_____|_|  |_|_____|____/  (_)  \____\___/|_|  |_|
 *
 *				custom functions for the theme
 **********************************************************************************************************/
function remove_eazyest_gallery_breadcrumb() {
  remove_action('eazyest_gallery_before_attachment',     'ezg_breadcrumb', 5);
  remove_action('eazyest_gallery_before_folder_content', 'ezg_breadcrumb', 5);
}

add_action( 'eazyest_gallery_ready', 'remove_eazyest_gallery_breadcrumb', 1 );

function needRemoveP() {
	remove_filter('the_excerpt', 'wpautop');
}
add_filter( 'wp_image_editors', 'change_graphic_lib' );
function change_graphic_lib($array) {
  return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
}

// 最新功能：添加支持优酷视频嵌入及手机观看

wp_embed_unregister_handler('youku');

function wp_iframe_handler_youku( $matches, $attr, $url, $rawattr ) {

    $iframe = '<div style="padding-top: 83.3333%;" class="fluid-width-video-wrapper"><iframe src="http://player.youku.com/embed/'. esc_attr($matches[1]) .'" frameborder=0 allowfullscreen></iframe></div>';

	return apply_filters( 'iframe_youku', $iframe, $matches, $attr, $url, $ramattr );
}

wp_embed_register_handler( 'youku_iframe', '#http://v.youku.com/v_show/id_(.*?).html#i', 'wp_iframe_handler_youku' );

/**
 * 在 WordPress 编辑器添加“下一页”按钮
 * http://www.wpdaxue.com/add-next-page-button-wordpress-post-editor.html
 */
add_filter('mce_buttons','wpdaxue_add_next_page_button');
function wpdaxue_add_next_page_button($mce_buttons) {
	$pos = array_search('wp_more',$mce_buttons,true);
	if ($pos !== false) {
		$tmp_buttons = array_slice($mce_buttons, 0, $pos+1);
		$tmp_buttons[] = 'wp_page';
		$mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos+1));
	}
	return $mce_buttons;
}

// 添加语言包到子主题

function my_child_theme_locale() {
    load_theme_textdomain( 'wpthms', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'my_child_theme_locale' );

//后台面板增加数据统计
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
  
function my_custom_dashboard_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('custom_help_widget', '核心统计', 'custom_dashboard_help');
}
//WordPress获取网站浏览量总数
function all_view() /*调用的函数名*/
{
	global $wpdb;
	$count=0;
	$views= $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key='_post_views'");

	foreach($views as $key=>$value)
	{
		$meta_value=$value->meta_value;
		if($meta_value!=' ')
		{
			$count+=(int)$meta_value;
		}
	}
	return $count;
}

function view_5000() /*调用的函数名*/
{
	global $wpdb;
	$count=0;
	$num= $wpdb->get_var("SELECT count(*) FROM $wpdb->postmeta WHERE meta_key='_post_views' and meta_value>5000");
	return $num;
}

function custom_dashboard_help() {
	echo "全站页面总访问".all_view()."次<br>"; 
	echo "访问次数超过5000的文章/页面有".view_5000()."个<br>"; 

}

?>
