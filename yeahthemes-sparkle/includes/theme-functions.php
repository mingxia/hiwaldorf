<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
/**
 * Theme functions
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	Yeah Includes
 */

/**
 * Get current layout
 *
 * @param string $default Optional. Default layout. eg put default layout get_theme_mod( 'layout', 'default' )
 * @param string $post_id Optional. Retrieve by post id
 * @return string
 * @since 2.0.0
 */
if( !function_exists( 'yt_get_current_layout') ):
	function yt_get_current_layout( $default = 'default', $post_id = 0 ){

		$layout = $default;	
		
		if( !intval( $post_id ) ){

			if( !empty( $GLOBALS['post'] )){
				$post = $GLOBALS['post'];
				$post_id = $post->ID;
			}else{
				$post_id = 0;
			}
		}

		if( is_search() || is_404() )
			$post_id = 0;
		
		if( is_home() && $blogid = get_option( 'page_for_posts' ) )
			$post_id = $blogid;

		// default filter for changing current layout
		$layout = apply_filters( 'yt_get_current_layout', $layout );

		// Default supported layouts by page templates
		if( ( $template = get_post_meta( $post_id, 'yt_page_sidebar_layout', true ) ) ){

			// Allow custom page templates to filter their layouts
			$layout = apply_filters( 'yt_get_current_page_layout', $template );
			
		}
		if( empty( $layout ) ){
			$layout = $default;
		}
		// Higher priority to filter the current layout before returning
		return apply_filters( 'yt_before_return_current_layout', $layout );
	}
endif;

add_filter( 'yt_sidebar_array', 'yt_site_sidebars' );
/**
 * Sidebars Initialization
 */

if( !function_exists( 'yt_site_sidebars') ) {
	
	function yt_site_sidebars( $sidebars ){
		
		/**
		 * Retrieve Options data
		 */
		$yt_data = yt_get_options();
		
		/* =Sub sidebar */
		$sidebars['sub-sidebar'] = __('Sub Sidebar','wpthms');
		
		/* =Footer sidebar */		
		/* get the number of column from theme option */
		$footer_columns = isset( $yt_data['footer_columns'] ) && $yt_data['footer_columns'] ? $yt_data['footer_columns'] : 3; 
		$number_of_sidebar = '';
		
		/* if is numeric number*/
		if( is_numeric( $footer_columns ) ){
			$number_of_sidebar = $footer_columns;
		}
		/* else if is string */
		else{
			
			$footer_col_array = explode('_', $footer_columns );


			foreach ($footer_col_array as $k => $v ) {
				if( strpos($v, "clear") !== false || strpos($v, "hr") !== false )
					unset($footer_col_array[$k]);
			}

			//print_r($footer_col_array); die();

			$number_of_sidebar = count( ( array ) $footer_col_array );
			
		}
		
		for( $i = 1; $i <= $number_of_sidebar; $i++){
			
			$sidebars['footer-widget-' . $i] = sprintf( __('Footer Widget %s','wpthms'), $i );
			
		}
		
		$sidebars = apply_filters( 'yt_site_sidebars', $sidebars );
		
		return $sidebars;
		
	}
}


if( !function_exists( 'yt_site_post_meta_description') ) {

	function yt_site_post_meta_description(){
		
		get_template_part( 'includes/templates/post-meta-desc-default' );
		
	}
	
	
}

/**
 * Override Default gallery shortcode
 */
add_filter( 'post_gallery', 'yt_site_gallery_shortcode', 99999 , 2);

function yt_site_gallery_shortcode( $output, $attr) {

	if( !yt_get_options('allow_overwrite_default_gallery'))
		return $output;
	
	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr['ids'] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr['orderby'] ) )
			$attr['orderby'] = 'post__in';
		$attr['include'] = $attr['ids'];
	}

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => 'li',
		'icontag'    => 'span',
		'captiontag' => 'p',
		'columns'    => 3,
		'size'       => 'large',
		'include'    => '',
		'exclude'    => '',
		'link'       => ''
	), $attr, 'gallery'));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= apply_filters( 'yt_gallery_wp_get_attachment_link', wp_get_attachment_link($att_id, $size, true), $id ) . "\n";
		return $output;
	}

	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$icontag = tag_escape($icontag);
	$valid_tags = wp_kses_allowed_html( 'post' );
	if ( ! isset( $valid_tags[ $itemtag ] ) )
		$itemtag = 'div';
	if ( ! isset( $valid_tags[ $captiontag ] ) )
		$captiontag = 'span';
	if ( ! isset( $valid_tags[ $icontag ] ) )
		$icontag = 'span';

	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>";
	$size_class = sanitize_html_class( $size );
	$slider_settings = apply_filters( 'yt_wp_default_gallery_settings', array(
		'selector' => '.slides > li',
		'controlNav' => false,
		'pausePlay' => true,
	), 'slider');
	$gallery_div = "<div id='$selector' data-settings='" . esc_attr( json_encode( $slider_settings ) ) . "' class='yeahslider gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
	$output .= '<ul class="slides">';
	$i = 0;
	
	foreach ( $attachments as $id => $attachment ) {
		if ( ! empty( $link ) && 'file' === $link )
			$image_output = apply_filters( 'yt_gallery_wp_get_attachment_link', wp_get_attachment_link( $id, $size, false, false ), $id );
		elseif ( ! empty( $link ) && 'none' === $link )
			$image_output = wp_get_attachment_image( $id, $size, false );
		else
			$image_output = apply_filters( 'yt_gallery_wp_get_attachment_link', wp_get_attachment_link( $id, $size, true, false ), $id );

		$image_meta  = wp_get_attachment_metadata( $id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) )
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

		$output .= "<{$itemtag} class='gallery-item" . ( $captiontag && trim($attachment->post_excerpt) ? ' wp-caption' : '') . "'>";
		$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
		if ( $captiontag && trim($attachment->post_excerpt) ) {
			$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</{$captiontag}>";
		}
		$output .= "</{$itemtag}>";
	}

	$output .= "
			</ul>
		</div>\n";

	return $output;
}


//add_filter( 'jp_carousel_force_enable', function(){return true;});
//add_filter( 'yt_gallery_wp_get_attachment_link', 'yt_site_add_data_to_gallery_images' ,10, 2);
 
if ( ! function_exists( 'yt_site_add_data_to_gallery_images' ) ) {
	function yt_site_add_data_to_gallery_images( $html, $attachment_id ) {
		
		//if ( $this->first_run ) // not in a gallery
			//return $html;

		$attachment_id   = intval( $attachment_id );
		$orig_file       = wp_get_attachment_image_src( $attachment_id, 'full' );
		$orig_file       = isset( $orig_file[0] ) ? $orig_file[0] : wp_get_attachment_url( $attachment_id );
		$meta            = wp_get_attachment_metadata( $attachment_id );
		$size            = isset( $meta['width'] ) ? intval( $meta['width'] ) . ',' . intval( $meta['height'] ) : '';
		$img_meta        = ( ! empty( $meta['image_meta'] ) ) ? (array) $meta['image_meta'] : array();
		$comments_opened = intval( comments_open( $attachment_id ) );

		/*
		 * Note: Cannot generate a filename from the width and height wp_get_attachment_image_src() returns because
		 * it takes the $content_width global variable themes can set in consideration, therefore returning sizes
		 * which when used to generate a filename will likely result in a 404 on the image.
		 * $content_width has no filter we could temporarily de-register, run wp_get_attachment_image_src(), then
		 * re-register. So using returned file URL instead, which we can define the sizes from through filename
		 * parsing in the JS, as this is a failsafe file reference.
		 *
		 * EG with Twenty Eleven activated:
		 * array(4) { [0]=> string(82) "http://vanillawpinstall.blah/wp-content/uploads/2012/06/IMG_3534-1024x764.jpg" [1]=> int(584) [2]=> int(435) [3]=> bool(true) }
		 *
		 * EG with Twenty Ten activated:
		 * array(4) { [0]=> string(82) "http://vanillawpinstall.blah/wp-content/uploads/2012/06/IMG_3534-1024x764.jpg" [1]=> int(640) [2]=> int(477) [3]=> bool(true) }
		 */

		$medium_file_info = wp_get_attachment_image_src( $attachment_id, 'medium' );
		$medium_file      = isset( $medium_file_info[0] ) ? $medium_file_info[0] : '';

		$large_file_info  = wp_get_attachment_image_src( $attachment_id, 'large' );
		$large_file       = isset( $large_file_info[0] ) ? $large_file_info[0] : '';

		$attachment       = get_post( $attachment_id );
		$attachment_title = wptexturize( $attachment->post_title );
		$attachment_desc  = wpautop( wptexturize( $attachment->post_content ) );

		// Not yet providing geo-data, need to "fuzzify" for privacy
		if ( ! empty( $img_meta ) ) {
			foreach ( $img_meta as $k => $v ) {
				if ( 'latitude' == $k || 'longitude' == $k )
					unset( $img_meta[$k] );
			}
		}

		$img_meta = json_encode( array_map( 'strval', $img_meta ) );

		$html = str_replace(
			'<img ',
			sprintf(
				'<img data-attachment-id="%1$d" data-orig-file="%2$s" data-orig-size="%3$s" data-comments-opened="%4$s" data-image-meta="%5$s" data-image-title="%6$s" data-image-description="%7$s" data-medium-file="%8$s" data-large-file="%9$s" ',
				$attachment_id,
				esc_attr( $orig_file ),
				$size,
				$comments_opened,
				esc_attr( $img_meta ),
				esc_attr( $attachment_title ),
				esc_attr( $attachment_desc ),
				esc_attr( $medium_file ),
				esc_attr( $large_file )
			),
			$html
		);

		$html = apply_filters( 'jp_carousel_add_data_to_images', $html, $attachment_id );

		return $html;
	}
}

/**
 * YT_Walker_Nav_Mega_Menu_By_Category copied from YT_Walker_Nav_Menu
 * Mega menu news by Category with icon (description)
 *
 * @package Includes
 * @since 1.0.0
 */
class YT_Walker_Nav_Mega_Menu_By_Category extends YT_Walker_Nav_Menu{
	
	var $megamenu_checker = false;
	var $widget_checker = false;
	var $description_type;
	
	function __construct(){
		
		$this->description_type = apply_filters( 'yt_walker_nav_menu_description_style', 'description' );
		
	}
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$extra_class = 'sub-menu';
		if( $this->megamenu_checker && 0 == $depth )
			$extra_class = 'row';
		
		
		/*if( $depth == 1 && $this->megamenu_checker )
			$extra_class = '';
*/			
		$indent = str_repeat("\t", $depth);
		
		$sub_menu_wrapper = "\n$indent<ul class=\"$extra_class\">\n";
		
		
		if( $depth == 1 && $this->widget_checker ){
			$sub_menu_wrapper = '';
		}
		
		$output .= $sub_menu_wrapper;
	}
	

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$sub_menu_wrapper = "$indent</ul>\n";
		
		if( $depth == 1 && $this->widget_checker ){
			$sub_menu_wrapper = '';
			$this->widget_checker = false;
		}
		$output .= $sub_menu_wrapper;
		
		/**
		 * Increase level of submenu
		 * @since 1.0.1
		 */
		if( $depth > 3 ){
			$this->megamenu_checker = false;
		}
	}
	

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$data_cat = '';
		//print_r($item);
		/*Assigns data-dats*/
		if( $depth == 0 ){
			
			/*Append Parent menu item object_id to data-cats*/
			if( $item->object == 'category' ){
				//$data_cat .= $item->object_id . ','; 
			}
			
			if( !empty( $item->data_cat ) ){
				$data_cat .= $item->data_cat;
			}
			
			/*Check if is Default mega menu*/
			if( !empty( $item->mega_menu ) && 'default' == $item->mega_menu ){
				$this->megamenu_checker = true;
			}
		
			if( empty( $item->mega_menu ) ){
				$this->megamenu_checker = false;
			}

		}
		
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		
		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		
		
		if( $depth == 1 && $this->megamenu_checker ){
			$classes = array();
			$classes[] = $item->mega_menu_columns;
		}
		
		if( $depth == 2 && $this->megamenu_checker ){
			//$classes = array();
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		
		

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : $item->title;
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['description']   = ! empty( $item->description ) ? $item->description : '';

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		//Attributes for news megamenu parent
		$data_cat_output = '';
		$data_atts = '';
		if( $depth == 0 && !empty( $item->mega_menu ) && 'news' == $item->mega_menu && $data_cat ){
			$data_cat_output = ' data-cats="' . esc_attr( $data_cat ) . '"';
			$data_atts = ' data-atts="' . esc_attr( json_encode( $atts ) ) . '"';
		}

		$menu_item_wrapper = $indent . '<li' . $id . $value . $class_names . $data_cat_output . $data_atts .'>';

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) && 'description' !== $attr) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}
		
		//print_r($item);

		$item_output = $args->before;
		$item_output .= !empty( $atts['href'] ) ? '<a'. $attributes .'>' : '';
		
		$submenus = array();
		//Menu parent sign
		if( 'indicator' == $this->description_type ){
			$submenus = get_posts( array( 
				'post_type' => 'nav_menu_item', 
				'numberposts' => 1, 
				'meta_query' => array( 
					array( 
						'key' => '_menu_item_menu_item_parent', 
						'value' => $item->ID, 
						'fields' => 'ids' ) 
					) 
				) 
			);
		}
		/*Menu indicator*/
		$description = wp_parse_args( 
			apply_filters( 'yt_walker_nav_menu_description', array(), $atts ),
			array(
				'before' => '',
				'after' => '',
				'parent' => '',
				'children' => '',
			)
		);
		/*Menu indicator position*/
		$position = apply_filters( 'yt_walker_nav_menu_description_position' ,'before', $submenus, $depth );
		
		/*Prepend*/
		if( $position == 'after' ){
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}
		
		if( 'indicator' == $this->description_type ){
			$item_output .= ! empty( $submenus ) ? ( 0 == $depth ? $description['parent'] : $description['children'] ) : '';
		}else{
			$item_output .= $description['before'] . ( 0 == $depth ? $description['parent'] : $description['children'] ) . $description['after'];
		}
		
		/*Append*/
		if( $position == 'before' ){
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		}
		
		
       $item_output .= !empty( $atts['href'] ) ? '</a>' : '';
	   /*Begin Mega Menu container*/
	   if( $depth == 0 && !empty( $item->mega_menu ) ){
			$item_output .= '<div class="full-width-wrapper mega-menu-container">
				<div class="container">';
				
			$item_output .= apply_filters( 'yt_mega_menu_content', '', $item->mega_menu, $data_cat, $depth, $atts );
	   }
		$item_output .= $args->after;
		
		
		
		$depth_title = apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		if( $depth == 1 && $this->megamenu_checker && empty( $atts['href'] ) ){
			$depth_title = '<span>' . apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args ) . '</span>';
		}
		if( $depth == 1 && $this->megamenu_checker && !empty( $item->sidebar ) ){
			
			$depth_title = yt_get_ob_content( 'dynamic_sidebar', $item->sidebar );
			$this->widget_checker = true;
		
		}else{
			$this->widget_checker = false;
		}
		/*Remove child if its parent is a sidebar*/
		if( $depth == 2 && $this->widget_checker ){
			$menu_item_wrapper = '';
			$depth_title = '';
		}
		
		$output .= $menu_item_wrapper . $depth_title;
	}
	
	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		
		$data_cat = '';
		if( $depth == 0 ){
			/*End Mega Menu container*/
			if(!empty( $item->mega_menu )/* && !empty( $item->data_cat ) */){
				
				//do_action( 'yt_mega_menu_end', $item->mega_menu, $data_cat, $depth );
				
				$output .= '</div>
					</div>';
			}
		}
		$menu_item_wrapper = "</li>\n";
		
		if( $depth == 2 && $this->widget_checker ){
			$menu_item_wrapper = '';
		}
		
		$output .= $menu_item_wrapper;
	}
}

/**
 * Extended from YT_Walker_Nav_Menu_Fields to add custom css for menu level
 *
 * @package Includes
 * @since 1.0.0
 */

if( !class_exists( 'YT_Nav_Menu_Custom_Fields') )
	require_once( wpthms_FRAMEWORK_DIR . 'classes/class.navmenu-custom-fields.php' );

class YT_Site_Nav_Menu_Custom_Fields extends YT_Nav_Menu_Custom_Fields{
	
	public function admin_print_scripts(){
		parent::admin_print_scripts();
		global $pagenow, $typenow;
	
		if ( empty( $typenow ) && !empty( $_GET['post'] ) ) {
			$post = get_post( $_GET['post'] );
			$typenow = $post->post_type;
		}
		
		if ( (is_admin() && $pagenow == 'nav-menus.php' ) ) {
			$css = '<style type="text/css">
				/*Custom css for menu nav*/
				.yt-custom-menu-fields-wrapper span.description{
					display:block;
				}
				.field-mega_menu,
				.field-mega_menu_columns,
				.field-sidebar {
					display: none;
				}
				.menu-item-depth-0 .field-mega_menu,
				.menu-item-depth-1 .field-mega_menu_columns,
				.menu-item-depth-1 .field-sidebar {
					display: block;
				}
				.yt-custom-menu-fields-wrapper select{
					width:190px;
					clear:right;
				}
			</style>';
 			$css = str_replace(array("\r", "\n", "\t"), "", $css);
			printf( $css . "\n" );
		}
	}
}

add_action( 'init', 'yt_site_admin_mega_menu_settings' );
/**
 * Init custom field for nav menu item
 *
 * @package Includes
 * @since 1.0.0
 */

if( !function_exists( 'yt_site_admin_mega_menu_settings') ) {
	
	function yt_site_admin_mega_menu_settings(){
		
		new YT_Site_Nav_Menu_Custom_Fields(array(
			array( 
				'name' => __( 'Mega menu', 'wpthms'),
				'id' => 'mega_menu',
				'desc' => __( 'Enable Mega menu and choose style (Default or News by category).', 'wpthms'),
				'type' => 'select',
				'options' => array(
					'' => __( 'Off', 'wpthms'),
					'default' => __( 'Default', 'wpthms'),
					'news' => __( 'News by category', 'wpthms'),
				),
				
			),
			array( 
				'name' => __( 'Mega menu Columns', 'wpthms'),
				'id' => 'mega_menu_columns',
				'desc' => __( 'Choose column size (Only apply to Default Mega menu)', 'wpthms'),
				'type' => 'select',
				'std' => 'yt-col-1-5',
				'options' => array(
					// 'col-md-1' => 'col-md-1',
					// 'col-md-2' => 'col-md-2',
					// 'col-md-3' => 'col-md-3',
					// 'col-md-4' => 'col-md-4',
					// 'col-md-5' => 'col-md-5',
					// 'col-md-6' => 'col-md-6',
					// 'col-md-7' => 'col-md-7',
					// 'col-md-8' => 'col-md-8',
					// 'col-md-9' => 'col-md-9',
					// 'col-md-10' => 'col-md-10',
					// 'col-md-11' => 'col-md-11',
					// 'col-md-12' => 'col-md-12',
					'yt-col-1-2' => __( '1/2', 'wpthms'),
					'yt-col-1-3' => __( '1/3', 'wpthms'),
					'yt-col-1-4' => __( '1/4', 'wpthms'),
					'yt-col-1-5' => __( '1/5', 'wpthms'),
					'yt-col-1-6' => __( '1/6', 'wpthms'),
					'yt-col-2-3' => __( '2/3', 'wpthms'),
					'yt-col-2-5' => __( '2/5', 'wpthms'),
					'yt-col-3-4' => __( '3/4', 'wpthms'),
					'yt-col-3-5' => __( '3/5', 'wpthms'),
					'yt-col-4-5' => __( '4/5', 'wpthms'),
					'yt-col-5-6' => __( '5/6', 'wpthms'),
				),
			),
			array( 
				'name' => __( 'Display this menu as a sidebar', 'wpthms'),
				'id' => 'sidebar',
				'desc' => ''/*__( 'Add your sidebar to megamenu ( Only apply to Default mega menu)', 'wpthms')*/,
				'type' => 'select',
				'options' => array_merge( array( '' => __('Select a sidebar', 'wpthms')), yt_get_registered_sidebars() )
			)
			
		), 'YT_Walker_Nav_Menu_Edit');
	}
}

add_filter( 'wp_nav_menu_objects', 'yt_site_filter_wp_nav_menu_objects', 10, 2 );
/**
 * Modify wp_nav_menu_objects to add child categories
 *
 * @package Includes
 * @since 1.0.0
 */
if( !function_exists( 'yt_site_filter_wp_nav_menu_objects') ) {
	function yt_site_filter_wp_nav_menu_objects( $sorted_menu_items, $args ){
		
		if($args->theme_location !== 'primary')
			return $sorted_menu_items;
		
		$menu_parent = array();
		$menu_tree = array();
		$menu_items_with_children = array();
		foreach ( $sorted_menu_items as $menu_item ) {
			if( $menu_item->menu_item_parent == 0 ){
				$menu_parent[] = $menu_item->ID;
				
				if( !empty( $menu_item->mega_menu ) ){
					$menu_item->classes[] = 'mega-menu-dropdown';
					$menu_item->classes[] = 'mega-menu-dropdown-' . $menu_item->mega_menu;
				}
					
			}elseif( in_array( $menu_item->menu_item_parent, $menu_parent) && in_array( $menu_item->object , array('post_tag', 'category') ) ){
				$menu_tree[$menu_item->menu_item_parent][] = $menu_item->object_id;
			}

			
			
			if ( $menu_item->menu_item_parent )
				$menu_items_with_children[ $menu_item->menu_item_parent ] = true;
					
		}
		foreach ( $sorted_menu_items as $menu_item ) {
			//echo $menu_item->ID . "\n";
			if( $menu_item->menu_item_parent == 0 ){
				$menu_item->data_cat = !empty( $menu_tree[$menu_item->ID] ) ? join(',', $menu_tree[$menu_item->ID] ) : '';
			}
			if ( empty( $menu_item->mega_menu ) && $menu_items_with_children && isset( $menu_items_with_children[ $menu_item->ID ] ) )
				$menu_item->classes[] = 'default-dropdown';
		}
		
		$new_items = array();
		
		for ( $i = 1; $i < count( $sorted_menu_items ) + 1; $i++ ){
			//is lvl0
			if( empty( $sorted_menu_items[$i]->menu_item_parent ) ){
			   $new_items = array_merge( $new_items, yt_site_filter_wp_nav_menu_objects_helper( $sorted_menu_items[$i], $sorted_menu_items ) );
			}
		} 
		// var_dump($new_items); die();
		if( $args->theme_location == 'primary' )
			return $new_items;
			
		//print_r($x_parent);
		//print_r($x_tree);
		// print_r($sorted_menu_items);
		
		return $sorted_menu_items;
	}
}

if( !function_exists( 'yt_site_filter_wp_nav_menu_objects_helper') ) {
	function yt_site_filter_wp_nav_menu_objects_helper( $parent, $items ){
		$rtn = array();
		$rtn[] = $parent;
		if( !empty( $parent->mega_menu ) && $parent->mega_menu == 'news' ) return $rtn;
		for ( $i=1; $i < count( $items ) + 1; $i++ ){
			if( $items[$i]->menu_item_parent && $items[$i]->menu_item_parent == $parent->ID ){
				$rtn = array_merge( $rtn, yt_site_filter_wp_nav_menu_objects_helper( $items[$i], $items ));
			}
		}
		return $rtn;
	}
}


if( !function_exists( 'yt_site_post_list') ) {

	function yt_site_post_list( $instance = array() ){
		

		include( locate_template('includes/templates/func-post-list.php' ) );

		return join(',', $post_ids);
	}
}

add_action( 'yt_ajax_yt-site-ajax-load-posts-infinitely', 'yt_site_ajax_load_posts_infinitely' );
add_action( 'yt_ajax_nopriv_yt-site-ajax-load-posts-infinitely', 'yt_site_ajax_load_posts_infinitely' );
/**
 * Endless scrolling for Post thumbnail widget via ajax
 * @since 1.0.4
 */
if ( !function_exists( 'yt_site_ajax_load_posts_infinitely') ) {
	# code...

	function yt_site_ajax_load_posts_infinitely(){
		if( empty( $_GET['data'] ) )
			return '';

		$data = stripslashes_deep( $_GET['data'] );
		$data['scroll_infinitely'] = false;
		$data['wrapper'] = false;

		$output = '';
		$ids = array();
		if( function_exists( 'yt_site_post_list' ) && is_callable( 'yt_site_post_list' ) ){
			ob_start();
				$ids = yt_site_post_list( $data );	
				
				$output .= ob_get_contents();
			ob_end_clean();
		}

		$return = array(
			'success' => true,
			'html'	=> $output ? $output : __( 'No more posts', 'wpthms'),
			'offset'		=> intval( $data['offset'] ) + intval($data['number']),
			'all_loaded' => $output ? false : true,
			'ids' => $ids
		);

		//
		wp_send_json( $return );
		
		//print_r( $data );
		die();
	}
}

add_action( 'yt_ajax_yt-site-ajax-search', 'yt_site_ajax_search_response' );
add_action( 'yt_ajax_nopriv_yt-site-ajax-search', 'yt_site_ajax_search_response' );
/*
 * Ajax search
 * @since 1.0.4
 */
if( !function_exists( 'yt_site_ajax_search_response') ){
	function yt_site_ajax_search_response(){
		if( empty( $_GET['s'] ) )
			die();

		$string = $_GET['s'];
		$args = array(
	        's' => $string,
	        //'post_type' => array( 'post' ),
	        'posts_per_page' => 5,
	        'post_status' => 'publish'
	    );

	    $search_query = new WP_Query( apply_filters( 'yt_site_ajax_search_query', $args ) );

	    
	    if ( $search_query->have_posts() ) {
	    	echo '<ul class="list-group secondary-2-primary">';
				while ( $search_query->have_posts() ) : $search_query->the_post();
					
				echo '<li class="list-group-item"><a href="' . get_permalink() . '" title="'.esc_attr( get_the_title() ).'" target="_blank">'.get_the_title().'</a><span class="badge">'.$search_query->post->post_type.'</span>' . ( 'post' == $search_query->post->post_type ? '<time class="display-block" datetime="' . esc_attr( get_the_time('c') ) . '">' . get_the_date() . '</time>' : '') .'</li>';

				endwhile;
	    	echo '</ul>';
	    	echo sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', esc_url( home_url( '/?s=' . urlencode( $string ) ) ), __('View all results', 'wpthms') );
	    }else{
	    	_e('Sorry, no posts matched your criteria.', 'wpthms');
	    }

	    wp_reset_postdata();

	    die();
	    exit();   

	}
}
/*
 * Social Sharing button
 * @since 1.0.4
 */

function yt_site_social_sharing_buttons( $styles = array(), $_services = array(), $ex_class='', $wrapper = 'div' ){

	$styles = wp_parse_args( $styles, array(
		'style' => 'color',
		'size'	=> 'large'
	) );

	$wrapper = $wrapper ? $wrapper : 'div';
	
	$id = get_the_ID();
	$url = get_permalink( $id);
	$title = get_the_title( $id);
	$thumb = wp_get_attachment_url( get_post_thumbnail_id( $id ) );	

	$attr = 'data-url="' . esc_url( $url ) . '" data-title="' . esc_attr( $title ) . '" data-source="' . esc_url( home_url('/') ) . '"';
	$attr .= $thumb ? ' data-media="' . esc_url( $thumb ) . '"' : '';
	/*apply_filters( 'yt_site_social_sharing_services_styles', array(
		'style' => 'color',
		'size'	=> 'large'
	), 'style2' )*/

	$services = yt_parse_args_deep(
		$_services,
		apply_filters( 'yt_site_social_sharing_services', array(
			
			'facebook' => array(
				'icon' => 'fa fa-facebook-square',
				'title' => __('Share on Facebook', 'wpthms'),
				'show' => true,
				'label' => true,
				'via' => ''
			),
			'twitter' => array(
				'icon' => 'fa fa-twitter',
				'title' => __('Share on Twitter', 'wpthms'),
				'show' => true,
				'label' => true,
				'via' => ''
			),
			'google-plus' => array(
				'icon' => 'fa fa-google-plus',
				'title' => __('Share on Google+', 'wpthms'),
				'show' => false,
				'label' => false,
				'via' => ''
			),
			'linkedin' => array(
				'icon' => 'fa fa-linkedin',
				'title' => __('Share on Linkedin', 'wpthms'),
				'show' => false,
				'label' => false,
				'via' => ''
			),
			'pinterest' => array(
				'icon' => 'fa fa-pinterest',
				'title' => __('Pin this Post', 'wpthms'),
				'show' => false,
				'label' => false,
				'via' => ''
			),
			'tumblr' => array(
				'icon' => 'fa fa-tumblr',
				'title' => __('Share on Tumblr', 'wpthms'),
				'show' => false,
				'label' => false,
				'via' => ''
			),
			'more' => array(
				'icon' => 'fa fa-ellipsis-h',
				'title' => __('More services', 'wpthms'),
				'show' => true,
				'label' => false,
				'via' => ''
			),
		) )
		
	);

	$social_services = array(
			
		'styles' => $styles,
		'services' => $services,
	);

	$social_services_class = array();
	$social_services_class[] = 'social-share-buttons';
	if($ex_class) $social_services_class[] = $ex_class;

	foreach ( (array) $social_services['styles'] as $key => $value) {
		$social_services_class[] = "$key-$value";
	}


	echo '<'.$wrapper.' class="' . join(' ', $social_services_class) . '" ' . $attr . '>';
		$count = 0;
		foreach ((array) $social_services['services'] as $key => $value) {
			$count++;
			$label_class = 'hidden-xs';
			$label_class .= $value['label'] ? '' : ' hidden';
			$label_class = ' class="'. $label_class . '"';

			$span_class = $key;
			$span_class .= $value['show'] ? '' : ' hidden';

			# code...
			echo '<span'. ( !empty( $value['title'] ) ? ' title="'.esc_attr( $value['title'] ).'"' : '' ) .' class="' . esc_attr( $span_class ) . '" ' . ( !empty( $value['via'] ) ? ' data-via="'.esc_attr( $value['via'] ).'"' : '' ) . ' data-service="'.esc_attr( $key ). '" data-show="'.esc_attr( $value['show'] ? 'true' : 'false' ).'">'.( !empty( $value['icon'] ) ? '<i class="'. esc_attr( $value['icon']  ).'"></i>' : '') . ( !empty( $value['title'] ) ? '<label'. $label_class .'>'.$value['title'].'</label>' : '' ) . '</span>';
		}
	echo '</'.$wrapper.'>';
		
}

add_filter( 'nav_menu_css_class', 'yt_main_menu_class', 10, 3 );
/*
 * Adding slug to categor menu
 * @since 1.0.4
 */
function yt_main_menu_class( $classes, $item, $args){
	if( $item->object == 'category'){
		$current_cat = get_category($item->object_id);
		if( !empty( $current_cat->slug ) )
			$classes[] = $current_cat->slug;
	}

	return $classes;
}