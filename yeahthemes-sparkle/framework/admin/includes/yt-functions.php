<?php 

/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;



//add_action('ytto_after_update_options', 'yt_unzip');
function yt_unzip( $data){
	
	$upload_dir = wp_upload_dir();
	
	$yeah_uploads_dir = trailingslashit( $upload_dir['basedir'] ) . 'wpthms_uploads';
	
	
	
	/*Getting Credentials*/
	$url = wp_nonce_url('admin.php?page=yt-theme-options', 'yt_options_ajax_nonce' );

	if (false === ($creds = request_filesystem_credentials($url, '', false, false, null) ) ) {
		return; // stop processing here
	}
	
	/*Initializing WP_Filesystem_Base*/
	if( ! WP_Filesystem($creds) ){
		
		request_filesystem_credentials($url, '', true, false, null);
		return;
		
	}
	
	global $wp_filesystem;
	
	$file = get_attached_file( 148);

	unzip_file( $file, $yeah_uploads_dir);
	
	///exit('hâhhahhaha');
	$font_list = $wp_filesystem->dirlist( $yeah_uploads_dir);
	//print_r($font_list);
	
	foreach( $font_list as $font_dir => $v ){
		
		//Remove the whole icon font dir
		//$wp_filesystem->rmdir( trailingslashit( $yeah_uploads_dir ) . $font_dir, true );
	}
	
	
	
		/*$response   	= wp_remote_fopen($mdf_icon_font_url );
		
	$xml = simplexml_load_string($response);
			
	$font_attr = $xml->defs->font->attributes();
	
	$glyphs = $xml->defs->font->children();
	$ssss = array();
	foreach($glyphs as $item => $glyph)
	{
		if($item == 'glyph')
		{
			$attributes = $glyph->attributes();
			$unicode	=  (string) $attributes['unicode'];
			$class		=  (string) $attributes['class'];
			
			if($class != 'hidden')
			{
				$unicode_key = trim(json_encode($unicode),'\\\"');
				
				if($item == 'glyph' && !empty($unicode_key) && trim($unicode_key) != "")
				{	
					$ssss[$unicode_key] = $unicode_key;
				}
			}
		}
	}*/
	
	
}

/**
 * Load Files
 *
 * @since 1.0.0
 */
/**
 * Get overwritable file, 
 * if childtheme was used, only overwrite if file exist, else use parent's
 *
 * @access public
 * @since 1.0
 */
if( !function_exists( 'yt_get_overwritable_directory_uri' ) ) {
	function yt_get_overwritable_directory_uri( $file ){

		if ( is_child_theme() && file_exists( get_stylesheet_directory() . $file ) ) {
			$location = get_stylesheet_directory_uri() . $file;

		//else return parent file
		}else{
			$location = get_template_directory_uri() . $file;
		}

		return $location;

	}
}
/**
 * Get overwritable dir, 
 * if childtheme was used, only overwrite if file exist, else use parent's
 *
 * @access public
 * @return directory
 * @since 1.0
 */
if( !function_exists( 'yt_get_overwritable_directory' ) ) {

	function yt_get_overwritable_directory( $dir ){

		if ( is_child_theme() && file_exists( get_stylesheet_directory() . $dir ) ) {
			$location = get_stylesheet_directory() . $dir;

		//else return parent file
		}else{
			$location = get_template_directory() . $dir;
		}
		return $location;
	}
}
/**
 * Beautify number
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_beautify_number' ) ) {
	function yt_beautify_number($n, $precision = 1) {

		$n_format = $n;
	    if ($n >= "1e9"){ 
			$n_format = number_format( ($n / "1e9"), $precision, ",", "") . "B";
		}elseif ($n >= "1e6"){
			$n_format = number_format( ($n / "1e6"), $precision, ",", "") . "M";
		} else if ($n >= "1e3"){ 
			$n_format = number_format( ($n / "1e3"), $precision, ",", "") . "k";
		}

	    return $n_format;
	}
 }
/**
 * Valid hex
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_valid_hex_color' ) ) {
	
	function yt_valid_hex_color( $val ){
		
		$hex_color = '(([a-fA-F0-9]){3}){1,2}$';

		if ( preg_match( '/^' . $hex_color . '/i', $val ) ) // Value is just 123abc, so prepend #.`
			$val = '#' . $val;
		elseif ( ! preg_match( '/^#' . $hex_color . '/i', $val ) ) // Value doesn't match #123abc, so sanitize to just #.
			$val = '';
		
		return $val;
	}
}
/**
 * Helper function to get attachment id using URL
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_attachment_id_by_url' ) ) {
	function yt_get_attachment_id_by_url( $attachment_url ) {
		
		
		$attachment_id = false;
		
		// If there is no url, return.
		if ( '' == $attachment_url )
			return '';
			
		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();
		
		/* Clean url with query string*/
		$attachment_url = yt_clean_url( $attachment_url );
		
		/*If this is the URL of an auto-generated thumbnail, get the URL of the original image*/
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
		
		/*Make sure the upload path base directory exists in the attachment URL*/
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
			
			global $wpdb;
			$prefix     = $wpdb->prefix;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts WHERE guid RLIKE %s;", $attachment_url ) );
		
			return is_array( $attachment ) && !empty($attachment[0]) ? $attachment[0] : '';
		}
		
	}
}
/**
 * Helper function to clean an URL
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_clean_url' ) ) {
	function yt_clean_url( $url ) {
		// If there is no url, return.
		if ( '' == $url )
			return '';
			
		$url = preg_replace('/\?.*/', '', $url);
		
		return $url;
	  
	}
}
/**
 * Pretty print
 *
 * @access    public
 * @since     1.0
 */
function yt_pretty_print( $array = array(), $return = false ) {
	
	if( $return )
		return '<pre>' . print_r( $array, true ) . '</pre>';
	else
		echo '<pre>' . print_r( $array, true ) . '</pre>';
	
}
/**
 * Helper function to open a file
 *
 * @access    public
 * @since     1.0
 */
function yt_file_open( $handle, $mode ) {
	
	$func = 'f' . 'open';
	return @$func( $handle, $mode );
	
}

/**
 * Helper function to close a file
 *
 * @access    public
 * @since     1.0
 */
function yt_file_close( $handle ) {

	$func = 'f' . 'close';
	return $func( $handle );
  
}

/**
 * Helper function to read to an open file
 *
 * @access    public
 * @since    1.0
 */
function yt_file_read( $handle, $string ) {

	$func = 'f' . 'read';
	return $func( $handle, $string );
  
}

/**
 * Helper function to write to an open file
 *
 * @access    public
 * @since    1.0
 */
function yt_file_write( $handle, $string ) {

	$func = 'f' . 'write';
	return $func( $handle, $string );
  
}

/**
 * Helper function to return encoded strings
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
function yt_encode( $value ) {

	$func = 'base64' . '_encode';
	return $func( $value );
  
}

/**
 * Helper function to return decoded strings
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
function yt_decode( $value ) {

	$func = 'base64' . '_decode';
	return $func( $value );
  
}

/**
 * Helper function for file_get_contents()
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
function yt_file_get_contents( $filename, $use_include_path = false , $context = null, $offset = -1 ) {

	$func = 'file_get' . '_contents';
	return $func( $filename, $use_include_path, $context, $offset );
  
}

/**
 * Helper function for file_put_contents()
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
function yt_file_put_contents( $filename, $use_include_path = false , $context = null ) {

	$func = 'file_put' . '_contents';
	return $func( $filename, $use_include_path, $context);
  
}

/**
 * Get string between
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
function yt_string_between( $s, $l, $r ) {
	$il = strpos( $s, $l, 0) + strlen( $l );
	$ir = strpos( $s, $r, $il );
	return substr( $s, $il, ( $ir - $il ) );
}
/**
 * Custom stripslashes from single value or array.
 *
 * @param       mixed   $input
 * @return      mixed
 *
 * @access      public
 * @since       1.0
 */
if ( ! function_exists( 'yt_stripslashes' ) ) {

	function yt_stripslashes( $input ) {
	
		if ( is_array( $input ) ) {
		
			foreach( $input as &$val ) {
			
				if ( is_array( $val ) ) {
				
					$val = yt_stripslashes( $val );
					
				} else {
					
					$val = stripslashes( trim( $val ) );
					
				}
				
			}
			
		} else {
			
			$input = stripslashes( trim( $input ) );
		
		}
		
		return $input;
	}
}
/**
 * Get embed html from url
 *
 * @return    string
 *
 * @access    public 
 * @since     1.0
 */
if ( ! function_exists( 'yt_get_embed' ) ) {
	function yt_get_embed( $url, $default = '' ){
		
		global $wp_embed;

		$media_types = array_merge( wp_get_audio_extensions(), wp_get_video_extensions(), array( 'jpg', 'jpeg', 'png', 'gif', 'svg') );
		$type = wp_check_filetype( yt_clean_url( $url ), wp_get_mime_types() );

		//var_dump( $type );die();
		
		$embed_html = '';

		/*if is direct link*/
		if ( !empty( $type['ext'] ) && in_array( strtolower( $type['ext'] ), $media_types ) ){
			$embed_html = do_shortcode( $wp_embed->autoembed( yt_clean_url( $url ) ) );

		/*Oembed link*/
		}else{
			$embed_html = wp_oembed_get( $url );
		}

		//$embed = (wp_oembed_get($url) !==false) ? @wp_oembed_get( $url ) : '';

		return !empty( $embed_html ) ? $embed_html : sprintf( '<span>%s</span>', esc_html__( 'Embed HTML not available.', 'wpthms' ) );
		
	}
}
/**
 * Helper function to Get embed html from url using ajax
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
if ( ! function_exists( 'yt_wp_ajax_get_embed' ) ) {
	function yt_wp_ajax_get_embed() {
	
		$url = isset( $_GET['url'] ) ? $_GET['url'] : '';
		wp_send_json_success( yt_get_embed( $url ) );
	  
	}
}

/**
 * Helper function to Get embed html from url using ajax
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_get_ob_content')) {
	
	function yt_get_ob_content( $function, $args ) {
		ob_start();
			if( is_string( $function ) && function_exists( $function ) && is_callable( $function ) ){

				if( is_array( $args ))
					call_user_func_array( $function, $args );
				else
					call_user_func( $function, $args );	
			}
				
			$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}

/**
 * strpos() with arrays.
 *
 * @param $array, $needles
 * @return boolean
 *
 * @access public
 * @since 1.0
 */
if ( ! function_exists( 'yt_strpos_array' ) ) {

	function yt_strpos_array( $haystack, $needles = array() ) {
	
		foreach( $needles as $needle ) {
			$pos = strpos( $haystack, $needle );
			if ( $pos !== false ) {
				return true;
			}
		}
		
		return false;
	}

}
/**
 * yt_rip_tags()
 * Similar to strip_tags() function but this will add the space between remove tags
 */ 
if( !function_exists( 'yt_rip_tags' ) ) {
	
	function yt_rip_tags( $string ) { 
		/*
		 *  remove HTML TAG
		 */
		$string = preg_replace ( '/<[^>]*>/', ' ', $string ); 
		
		/*
		 *  remove control characters
		 */
		$string = str_replace( "\r", '', $string );    // --- replace with empty space
		$string = str_replace( "\n", ' ', $string );   // --- replace with space
		$string = str_replace( "\t", ' ', $string );   // --- replace with space
		
		/*
		 *  remove multiple spaces
		 */
		$string = trim( preg_replace( '/ {2,}/', ' ', $string ) );
		
		return $string; 
	
	}
}
/**
 * check if array key exist
 *
 * @param $array, $needed
 * @return boolean
 *
 * @access public
 * @since 1.0
 */
if ( ! function_exists( 'yt_array_keys_exists' ) ) {

	function yt_array_keys_exists( $array, $keys ) {
	
		foreach($keys as $k) {
			if ( isset( $array[$k] ) ) {
				return true;
			}
		}
		
		return false;
	}

}
/**
 * Add prefix to string
 *
 * @param $array, $needed
 * @return boolean
 *
 * @access public
 * @since 1.0
 */
if ( ! function_exists( 'yt_string_prefix' ) ) {

	function yt_string_prefix( &$string, $prefix = '' ) {
		
		return $prefix . $string;
	}

}
/**
 * Add suffix to string
 *
 * @param $array, $needed
 * @return boolean
 *
 * @access public
 * @since 1.0
 */
if ( ! function_exists( 'yt_string_suffix' ) ) {

	function yt_string_suffix( &$string, $suffix = '' ) {
		
		return $string . $suffix;
	}

}
/**
 * Clean string
 *
 * @param     string   string to clean
 * @param     seperator  
 * @return    string
 *
 * @access    public
 * @since     1.0
 */

if( !function_exists( 'yt_clean_string' ) ) {
	
	function yt_clean_string( $string, $replace = ' ', $seperator = '-' ){
		
		return preg_replace( '/[^a-zA-Z0-9-_]/', '', strtolower( trim( str_replace( $replace, $seperator, $string ) ) ) );
	}
}

/**
 * Hex to RGB
 *
 * @access public
 * @since 1.0
 */
if( !function_exists( 'yt_hex2rgb' ) ) {
	function yt_hex2rgb ( $hexColor ){
		if( preg_match( '/^#?([a-h0-9]{2})([a-h0-9]{2})([a-h0-9]{2})$/i', $hexColor, $matches ) )
		{
			return array(
				'red' => hexdec( $matches[ 1 ] ),
				'green' => hexdec( $matches[ 2 ] ),
				'blue' => hexdec( $matches[ 3 ] )
			);
		}
		else{
			return array( 0, 0, 0 );
		}
	}
}

/**
 * Get list of Supported post formats
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_supported_post_formats' ) ) {
	function yt_get_supported_post_formats(){
		$supported_post_formats = array();
		$post_formats = get_theme_support( 'post-formats' );
					
		if ( is_array( $post_formats[0] ) ) {
			$supported_post_formats = array_merge( $supported_post_formats, $post_formats[0] );
			
		}
		array_unshift( $supported_post_formats, "standard" );

		return $supported_post_formats;
	}
}
/**
 * Get list of post
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_post_list' ) ) {
	
	function yt_get_post_list( $post_type_array = array(), $description = '', $posts_per_page = 30 ){
		$description = '' == $description && 30 == $posts_per_page ? __('30 Latest Posts','wpthms') : '';
		$result = array();
		if( $description )
			$result[] = $description;
		
		if( !empty( $post_type_array ) ){;
		
			$items = get_posts( array (
				'post_type'	=> $post_type_array,
				'posts_per_page' => $posts_per_page,
				'suppress_filters' => false
			));
			if( !empty( $items ) ){
				foreach( $items as $item ){
					$result[$item->ID] = $item->post_title . ' - (' . $item->post_type . ')';	
				}
			}
		}
		
		return $result;
	}
}
/**
 * Helper function to get postformat using post id
 *
 * @return    string
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists('yt_get_post_format')) {
	function yt_get_post_format( $id = '' ) {
		
		$format = get_post_format( $id );
		
		if ( false === $format )
			$format = 'standard';
			
			
		return $format;			
	}
}
/**
 * Get available category
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_category_list' ) ) {
	
	function yt_get_category_list( $args = array(), $description = ''){
		$categories = array(); 
		if( $description )
			$categories[] = $description;
		$categories_obj = get_categories( $args );
		
		if( !empty( $categories_obj ) ){
			foreach ( $categories_obj as $category ) {
				$categories[$category->cat_ID] = $category->cat_name;
			}
		}
		
		return $categories;
		
	}
}


/**
 * Get get parent categories from post id
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_parent_categories' ) ) {
	
	function yt_get_parent_categories( $post_id = null ){
		
		$post_id = $post_id ? $post_id : $GLOBALS['post']->ID;

		if( empty( $post_id ))
			return array();

		$cats = get_the_category( $post_id );

		//print_r($cats);

		$parent_cats = array();
		foreach( $cats as $cat){
			if( $cat->category_parent ){
				$cat_parent_obj = get_category( $cat->category_parent );
				$parent_cats[$cat_parent_obj->cat_ID] = $cat_parent_obj->name;
			}
		}

		return $parent_cats;
	}
}

/**
 * Get get sibling categories that are direct descendants from post's categories,
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_sibling_categories' ) ) {
	
	function yt_get_sibling_categories( $post_id = null, $post_cats = array() ){
		
		
		if( empty( $post_cats )){
			$post_id = $post_id ? $post_id : $GLOBALS['post']->ID;

			if( empty( $post_id ))
				return array();

			$parent_cats = yt_get_parent_categories( $post_id );
			$parent_cats = !empty( $parent_cats ) ? array_keys( $parent_cats ) : array();
		}else{

			// get parent categories from provided category
			foreach ($post_cats as $post_cat ) {
				$post_cat_obj = get_category( $post_cat );
				
				if( $post_cat_obj->category_parent ){
					$parent_cats[] = $post_cat_obj->category_parent;
				}
			}

			
		}
		$child_cat_ids = array();

		if( !empty( $parent_cats ) ){
			
			foreach ( $parent_cats as $pcat_id ) {
				$child_cat_objects = get_categories( "parent=$pcat_id" );
				if( $child_cat_objects ){
					foreach ($child_cat_objects as $child_cat) {
						$child_cat_ids[$child_cat->cat_ID] = $child_cat->cat_name;
					}
				}
			}
		}
		//print_r($child_cat_ids);

		return $child_cat_ids;
	}
}

/**
 * Get available Tag
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_tag_list' ) ) {
	
	function yt_get_tag_list( $args = '', $description = ''){
		if( empty( $args ) )
			$args = 'number=30&orderby=count';
		$tags = array(); 
		if( $description )
			$tags[] = $description;
		$tags_obj = get_tags( $args );
		if( !empty( $tags_obj ) ){
			foreach ( $tags_obj as $tag ) {
				$tags[$tag->term_id] = $tag->name;
			}
		}
		
		return $tags;
		
	}
}
/**
 * Get registered sidebars
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_registered_sidebars' ) ) {
	
	function yt_get_registered_sidebars(){
		global $wp_registered_sidebars;
		
		$sidebars = array();
		foreach($wp_registered_sidebars as $sidebar_id => $sidebar){
			$sidebars[$sidebar_id] = $sidebar['name'];
		}
		
		return $sidebars;
		
	}
}


/**
 * Get available category
 *
 * @return    array
 *
 * @access    public
 * @since     1.0
 */
if( !function_exists( 'yt_get_page_list' ) ) {
	
	function yt_get_page_list(){
		$pages = array();  
		$pages_obj = get_pages('sort_column=post_parent,menu_order');
		
		foreach ( $pages_obj as $page ) {
			$pages[$page->ID] = $page->post_title;
		}
		
		return $pages;
		
	}
}


/**
 * Options ouput
 * 
 * @type: type of option
 * @option_data: data (mixed)
 * 
 * @access public
 * @since 1.0
 */

if( !function_exists( 'yt_parse_google_fonts' ) ) {
	function yt_parse_google_fonts(){


		$webfonts = get_template_directory() . '/framework/extended/';
		$webfonts = trailingslashit( $webfonts );
		$webfonts = $webfonts . 'google-webfonts.php';
			
		$fonts_raw = '';
		$fonts_data = array();
		$transient_name = 'yt_parsed_webfonts';
		$fonts_data = get_transient( $transient_name );
		
		/*if no webfont transient found, parsed new*/
		if ( empty(  $fonts_data ) ) {
			
			// $response = '';

			// try {
			// 	$response = @yt_file_get_contents( $webfonts, true );
			// } catch (Exception $e) {
			// 	return $e->getMessage();
			// }
			if( !function_exists( 'yt_google_webfonts_data') )
				include_once $webfonts;
			
			if( function_exists( 'yt_google_webfonts_data' ) )
				$fonts_raw = yt_google_webfonts_data() ;

			if( $fonts_raw && yt_is_json( $fonts_raw ) ){
				
				$fonts_data = json_decode( $fonts_raw );
				$fonts_data = !empty( $fonts_data->items ) ? $fonts_data->items : array();
				
				foreach ( $fonts_data as $font ){
					if( isset( $font->kind ) )
						unset( $font->kind );
					if( isset( $font->lastModified ) )
						unset( $font->lastModified );
					if( isset( $font->files ) )
						unset( $font->files );
					if( isset( $font->version ) )
						unset( $font->version );
					/*if( isset( $font->subsets ) )
						unset( $font->subsets );*/							
				}
				set_transient( $transient_name, $fonts_data, 4 * WEEK_IN_SECONDS );
			}
		}
		
		return $fonts_data;
	}
}
/**
 * Options ouput
 * 
 * @type: type of option
 * @option_data: data (mixed)
 * 
 * @access public
 * @since 1.0
 */

if( !function_exists( 'yt_used_typography' ) ) {
	function yt_used_typography( $data = array(), $type = 'googlefont' ){
		
		if(empty( $data ) )
			return array();
			
		if( !in_array( $type, array( 'googlefont', 'customfont' ) ))
			return array();
		
		$used = array();
		
		foreach( $data as $k => $v ){
			$variants = array();
			
			if( !empty( $v['face'] ) && strpos( $v['face'], $type . '-' ) !== false && $type === $type ){
				
				$name =  yt_string_between( $v['face'] , $type . '-', ':' );
				if( isset( $v['weight'] ) && !in_array( $v['weight'] , array( 'inherit' ) ) ){
					$variants[] = $v['weight'];
				}
				if( isset( $v['style'] ) && 'italic' == $v['style'] ){
					$variants[] = 'italic';
				}
				
				if( !isset( $used[$name] ) ){
					if( !empty( $variants ) ){
						$used[$name][] = join('', $variants);
					}else{
						$used[$name] = array();
					}
				}
				elseif( isset( $used[$name] ) && !in_array( join('', $variants), $used[$name] ) ){
					$used[$name][] = join('', $variants);
				}
			}
		}
		$used = apply_filters( 'yt_used_typography', $used );
		
		return $used;
	}
}
/**
 * Options ouput
 * 
 * @type: type of option
 * @option_data: data (mixed)
 * 
 * @access public
 * @since 1.0
 */

if( !function_exists( 'yt_output_option' ) ) {
	function yt_output_option( $type, $data ){
		$output = '';
		
		if( empty( $type ) || empty( $data ) )
			return '';
			
		if( !in_array( 
			$type, 
			apply_filters( 'yt_output_option_types', 
			array( 'background_options', 'background_image', 'typography' ) ) 
		) ){
			return '';
		}
		
		/*Background Options*/
		if( 'background_options' === $type ){


			if( !empty( $data['image'] ) ){
				$output .= "\tbackground-image:url(". esc_url( yt_photon_url( yt_clean_url( $data['image'] ) ) ) .");\n";
				$output .= !empty( $data['repeat'] ) ? "\tbackground-repeat:{$data['repeat']};\n" : '';
				$output .= !empty( $data['position'] ) ? "\tbackground-position:{$data['position']};\n" : '';
				$output .= !empty( $data['attachment'] ) ? "\tbackground-attachment:{$data['attachment']};\n" : '';
				$output .= !empty( $data['size'] ) ? "\tbackground-size:{$data['size']};\n" : '';
			}


			$output .= !empty( $data['color'] )
				? "\tbackground-color:{$data['color']};\n" 
				: '';
		}
		/*Background Image*/
		elseif( 'background_image' === $type ){
			$output .= !empty( $data )
				? "\tbackground-image:url(". esc_url( yt_photon_url( yt_clean_url( $data ) ) ).");\n" 
				: '';
		}
		/*Typography */
		elseif( 'typography' === $type ){
			$fontfamily = !empty( $data['face'] ) ? $data['face'] : '';
			
			if( strpos( $fontfamily, 'googlefont-' ) !== false || strpos( $fontfamily, 'customfont-' ) !== false ){
				$fontfamily = str_replace( '+', ' ', yt_string_between( $fontfamily , 'googlefont-', ':' ) );
			}
			$output .= "\n";
			$output .= !empty( $fontfamily )
				? "\tfont-family:{$fontfamily};\n" 
				: "\tfont-family:sans-serif;\n";
			$output .= !empty( $data['size'] )
				? "\tfont-size:{$data['size']};\n" 
				: '';
			$output .= !empty( $data['weight'] )
				? "\tfont-weight:{$data['weight']};\n" 
				: '';
			$output .= !empty( $data['style'] )
				? "\tfont-style:{$data['style']};\n" 
				: '';
			$output .= !empty( $data['height'] )
				? "\tline-height:{$data['height']};\n" 
				: '';
			$output .= !empty( $data['letterspacing'] )
				? "\tletter-spacing:{$data['letterspacing']};\n" 
				: '';
			$output .= !empty( $data['transform'] )
				? "\ttext-transform:{$data['transform']};\n" 
				: '';
			$output .= !empty( $data['marginbottom'] )
				? "\tmargin-bottom:{$data['marginbottom']};\n" 
				: '';
			$output .= !empty( $data['color'] )
				? "\tcolor:{$data['color']};\n" 
				: '';
		}
		
		return $output;
	
	}
}

/**
 * Get current post id
 * 
 * @access public
 * @since 1.0
 */
function yt_get_post_id(){

	global $post, $wp_query;

	$post_id = isset( $post->ID ) ? $post->ID : 0;

	if( $wp_query->is_home && get_option('page_for_posts' ) )
		$post_id = get_option('page_for_posts' );

	if( yt_is_woocommerce() && wc_get_page_id('shop') )
		$post_id = wc_get_page_id('shop');

	return $post_id;

}
/**
 * Get Post thumbnail caption
 * 
 * @access public
 * @since 1.0
 */
if( !function_exists( 'yt_get_thumbnail_meta' ) ) {

	function yt_get_thumbnail_meta( $thumbnail_id = null, $return = null ) {
		$thumbnail_id = (int) $thumbnail_id;
		if ( $thumbnail_id && wp_attachment_is_image( $thumbnail_id ) ){
			if( null == $return || '' == $return )
				return get_post( $thumbnail_id );
			else
				return get_post( $thumbnail_id )->{$return};

		}else{
			return '';
		}
		
	}
}
/**
 * Alternative function for wp_parse_args to parse nested array
 * 
 * @access public
 * @since 1.0.1
 */
if( !function_exists( 'yt_parse_args_deep' ) ) {
	function yt_parse_args_deep( $args, $default){
	
	    if (!is_array($default) or !is_array($args)) { return $args; }

	    foreach ($args as $k => $v){

	        $default[$k] = yt_parse_args_deep( $v, @$default[$k]);
	    }
	    return $default;

	}
}
