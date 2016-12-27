<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 **********************************************************************************************************
 * 										WARNING: DO NOT EDIT THIS FILE
 **********************************************************************************************************
 *
 * Theme General functions
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	Yeah Includes
 */
 
/*
 * Simple Offline mode
 *
 * @since 1.0
 * @framework 1.0
 */
add_action( 'get_header', 'yt_theme_offline_mode' );

if ( ! function_exists( 'yt_theme_offline_mode' ) ) {
	
	function yt_theme_offline_mode() {
		
		if( !yt_get_options( 'offline_mode' ) ){
			return;
		}
		else{
			if ( ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) && !is_admin() ) {
				add_filter( 'yt_die_handler', 'yt_theme_offline_mode_handler' );
				wp_die();
			}
		}
	}
}

if ( ! function_exists( 'yt_theme_offline_mode_handler' ) ) {

	function yt_theme_offline_mode_handler() {
		return 'yt_theme_offline_mode_handler_template';
	}
}

if ( ! function_exists( 'yt_theme_offline_mode_handler_template' ) ) {
	
	function yt_theme_offline_mode_handler_template( $message, $title = '' ) {
		/**
		 * Retrieve Options data
		 */
		$yt_data = yt_get_options();
		
		if( !$yt_data['offline_mode'] )
			return;
	
		$logo = get_template_directory_uri().'/images/logo.png';
		
		$logo 		= !empty( $yt_data['login_logo'] ) ? yt_clean_url( $yt_data['login_logo'] ) : $logo;
		$heading 	= $yt_data['offline_heading'];
		$msg 		= $yt_data['offline_about_msg'];
		$meta_d 	= $yt_data['offline_meta_description'];
		
		
		$site_title = get_bloginfo( 'name' );
	 
		/*
		 * Add the site description for the home/front page.
		 */
		$site_description = get_bloginfo( 'description', 'display' );
			
		
		$title = ( $heading ? $heading . ' - ' : '' ) . $site_title . ' | ' . $site_description;
		$meta_d = $meta_d ? $meta_d : $site_description;
?>
<!DOCTYPE html>
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0" />
		<meta name="description" content="<?php echo esc_attr( $meta_d );?>">
		<?php echo sprintf( '<%1$s>%2$s</%1$s>', 'title', esc_html( $title ) );  ?>
		<link href="http<?php echo esc_attr(( is_ssl() ? 's' : '' ));?>://fonts.googleapis.com/css?family=Dancing+Script" rel="stylesheet" type="text/css">
		<?php ob_start();?>
		<style type="text/css">
			
			html,body{
				height:100%;
				padding:0 10px;
				margin:0;
			}
			body{
				font-family:Arial, Helvetica, sans-serif;
				line-height:24px;
				text-align:center;
				<?php
					echo 'color:' . ( !empty( $yt_data['offline_text_color'] ) ? esc_attr( $yt_data['offline_text_color'] ) : '#777' ) . ';';
					echo yt_output_option( 'background_options', $yt_data['offline_bg_options'] );
				?>
			}
			a{
				<?php
					echo 'color:' . ( !empty( $yt_data['offline_link_color'] ) ? esc_attr( $yt_data['offline_link_color'] ) : '#2a6496' ) . ';';
				?>
			}
				
			h1{
				margin-bottom:20px !important;
				border:none !important;
				line-height: 50px;
				font-family: 'Dancing Script';
				font-weight: 400;
				font-size: 40px;
				<?php echo !$yt_data['offline_text_color'] ? 'color:#000;' : '';?>
			}
			h1 + p,
			#countdown_section #timer .countdown-section,
			#footer{
				opacity:.8;
				filter:alpha(opacity=80);
			}
			#error-page p:first-child{
				display:none
			}
			.clear{
				height:30px;
				width:100%;
				clear:both;
			}
			#logo{
				margin-top:50px;
				max-height: 100px;
				width: auto;
				max-width: 100%;
			}
			#countdown_section{
				width:480px\9;
				max-width:100%;
				margin:0 auto;
				text-align:center;
				overflow:hidden;
			}
			#countdown_section h3 { 
				font-size:14px; 
				border:none; 
				margin-bottom: 20px; 
			}
			#countdown_section h3 span { 
				background: none;
			}
			#countdown_section #timer {
				text-align: center;
				margin-bottom:30px;
			}
			#countdown_section #timer span.countdown-section { 
				width: 96px;
				display:inline-block;
				margin:12px;
				float:left\9;
			}
			#countdown_section #timer span.countdown-amount {
				font-weight:bold; 
				font-size:36px; 
				color:#fff !important; 
				text-shadow: 0 -1px 0 rgba(0,0,0,0.3), 0 -2px 0 rgba(0,0,0,0.3), 0 -3px 0 rgba(0,0,0,0.3); 
				line-height: 80px; 
				height:80px;
				width:80px;
				margin-left:8px;
				margin-right:8px;
				border-radius:100px;
				-moz-border-radius:100px;
				-webkit-border-radius:100px;
				background: #222;
				display:block;
				opacity:.9;
				filter:alpha(opacity=90);
			}
			#countdown_section #timer .countdown-period { 
				font-size:10px; 
				display: block; 
				line-height: 15px; 
				font-style: normal; 
				text-transform: uppercase; 
				text-align:center;
				margin-top: 15px;
			}
			
			.countdown-sp{
				display:block;
				text-align:center;
				position:relative;
				max-width:500px;
				margin:50px auto 20px;
			}
			.countdown-sp span{
				position:relative;
				top:-13px;
				padding-left:15px;
				padding-right:15px
			}
			.countdown-sp span:before,
			.countdown-sp span:after{
				position:absolute;
				content:'';
				border-top:1px solid #EEE;
				border-top:1px solid rgba(0,0,0,.1);
				width:100%;
				top:50%;
			}
			.countdown-sp span:before{
				right:100%;
			}
			.countdown-sp span:after{
				left:100%;
			}
			#footer{
				font-size:10px;
				text-transform:uppercase;
			}
			
		</style>
		<?php 
		$css_output = ob_get_contents();
		ob_end_clean();

		$css_output = str_replace(array("\r", "\n", "\t"), "", $css_output);

		echo $css_output;
		?>

        <?php if( $yt_data['offline_countdown'] === 'show' && !empty( $yt_data['offline_launch_date'] ) ):
			$date = explode( '/', $yt_data['offline_launch_date'] );
			$time = explode( ':', $yt_data['offline_launch_time'] );
		?>
        <script type="text/javascript" src="<?php echo esc_url( site_url() . '/wp-includes/js/jquery/jquery.js' ); ?>"></script>
        <script type="text/javascript" src="<?php echo esc_url( wpthms_FRAMEWORK_URI . 'js/jquery.plugin.min.js' ); ?>"></script>
        <script type="text/javascript" src="<?php echo esc_url( wpthms_FRAMEWORK_URI . 'js/jquery.countdown.min.js' ); ?>"></script>
       
        <script type="text/javascript">
        /* <![CDATA[ */
		(function($) {
			$(window).on('load', function(){
		
				$('.countdown-timer').countdown( { until: new Date(<?php echo esc_js( $date[2] ) . ',' . esc_js( $date[0] ) . ' - 1,' . esc_js( $date[1] ) . ', ' . esc_js( $time[0] ) . ', ' . esc_js( $time[1] ); ?> ), format: 'DHMS' } );
			});
		})(jQuery);
		/* ]]> */
		</script>
        <?php 
        	
    	endif;?>
    </head>
    <body id="error-page">
		<?php echo '<center><img src="' . esc_url( yt_photon_url( $logo ) ) . '" id="logo"><h1>' . esc_html( $heading ) . '</h1><p>' . $msg . '</p></center>';?>
		
		<?php if( $yt_data['offline_countdown'] === 'show' && !empty( $yt_data['offline_launch_date'] ) ){ ?>
		<div id="countdown_section">
			<span class="countdown-sp"><span><?php esc_html_e( 'Site will launch in', 'wpthms' );?></span></span>
			<div id="timer" class="countdown-timer"></div>
		</div>
		<?php }?>
		
		<div class="clear"></div>
		<div id="footer"><?php echo $yt_data['offline_footer'];?></div>

    </body>
</html>
<?php
		die();
	}
}
/*
 * Login style
 *
 * @since 1.0
 * @framework 1.0
 */
add_action( 'login_head', 'yt_theme_admin_login' );

if( !function_exists( 'yt_theme_admin_login' ) ) {
	
	function yt_theme_admin_login() { 
		/**
		 * Retrieve Options data
		 */
		$yt_data = yt_get_options();
		
		$logo 			= $yt_data['login_logo'] ? $yt_data['login_logo'] : wpthms_INCLUDES_URI . 'images/login-logo.png';
		$color 			= $yt_data['login_link_color'] ? 'color:' . $yt_data['login_link_color'] . '!important;' : '';
		
		ob_start();
		?>
		<style type="text/css">
			
			*{
				-webkit-transition:all linear .2s;
				-moz-transition:all linear .2s;
				transition:all linear .2s;	
				tap-highlight-color: transparent;  
				-o-tap-highlight-color: transparent;  
				-moz-tap-highlight-color: transparent;  
				-webkit-tap-highlight-color: transparent;
			}
			textarea:focus, input[type="text"]:focus, input[type="password"]:focus{
				border-color: #7CB5D8;
				-webkit-box-shadow:0px 0px 3px #90C4E4 inset;
				box-shadow:0px 0px 3px #90C4E4 inset;
				-moz-box-shadow:0px 0px 3px #90C4E4 inset;
				
			}
			body.login{
				display:table;
				width:100%;
				<?php
					echo yt_output_option( 'background_options', $yt_data['login_bg_options'] );
				?>
			}
			.login #nav a, .login #backtoblog a {
				<?php echo $color;?>
			}
			#login {
				padding-top: 30px;
			}
			.login form{
				box-shadow: 0 0 0 1px rgba(0,0,0,0.05), 0 1px 5px rgba(0,0,0,0.08), 0 0 0 8px rgba(0, 0, 0, 0.02), 0 0 10px rgba(0, 0, 0, 0.05);
				-moz-box-shadow: 0 0 0 1px rgba(0,0,0,0.05), 0 1px 5px rgba(0,0,0,0.08), 0 0 0 8px rgba(0, 0, 0, 0.02), 0 0 10px rgba(0, 0, 0, 0.05);
				-webkit-box-shadow: 0 0 0 1px rgba(0,0,0,0.05), 0 1px 5px rgba(0,0,0,0.08), 0 0 0 8px rgba(0, 0, 0, 0.02), 0 0 10px rgba(0, 0, 0, 0.05);
				border-color:#FFF;
				z-index:5;
				position:relative !important;
				opacity: .95;
			}
			.login #nav, .login #backtoblog{
				text-shadow:none
			}
			
			.login form .input, .login input[type="text"] {
				margin-top: 5px;
				margin-right: 6px;
				margin-bottom: 13px;	
			}
			h1 a { 
				background-image:url(<?php echo esc_url( yt_photon_url( $logo ) );?>) !important;
				background-size: contain !important;
				background-position:center center !important;
				height:100px !important; width:auto !important; 
			}
        </style>
        <?php
		$css_output = ob_get_contents();
		ob_end_clean();

		$css_output = str_replace(array("\r", "\n", "\t"), "", $css_output);
		echo $css_output;
	}
}
/*
 * Adding first and last class to wp_nav_menu
 *
 * @since 1.0
 * @framework 1.0
 */
add_filter( 'wp_nav_menu_objects', 'yt_theme_first_and_last_menu_item_class');

if ( ! function_exists( 'yt_theme_first_and_last_menu_item_class' ) ) {
	
	function yt_theme_first_and_last_menu_item_class( $items ) {
		
		if( empty( $items ) )
			return $items;
		
		$items[1]->classes[] = 'first-child';
		
		$temp = array();
		foreach($items as $item){
			if( $item->menu_item_parent == 0 ){
				$temp[] = $item->menu_order;
			}
		}
		$items[max( $temp )]->classes[] = 'last-child';
		
		/*echo count($items);
		print_r($items);*/
		return $items;
	}
}
/**
 * Filters twitter oauth api settings for twitter widgets
 *
 * @since 1.0
 * @framework 1.0
 */
add_filter( 'yt_third_party_api_keys', 'yt_third_party_api_settings' );

if( !function_exists('yt_third_party_api_settings')) {

	function yt_third_party_api_settings( $apis ){

		$apis['twitter'] = array(
			'oauth_access_token' 		=> yt_get_options( 'twitter_access_token' ),
			'oauth_access_token_secret' => yt_get_options( 'twitter_access_token_secret' ),
			'consumer_key' 				=> yt_get_options( 'twitter_consumer_key' ),
			'consumer_secret' 			=> yt_get_options( 'twitter_consumer_secret' )
		);

		$apis['google'] 				= yt_get_options( 'google_api' );
		$apis['mailchimp'] 				= yt_get_options( 'mailchimp_api' );

		return $apis;
	}
}

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since 1.0
 * @framework 1.0
 */

add_filter( 'wp_title', 'yt_theme_wp_title', 10, 2 );

if( !function_exists('yt_theme_wp_title')) {
	
	function yt_theme_wp_title( $title, $sep ) {
		global $page, $paged;
	
		if ( is_feed() )
			return $title;
	
		// Add the blog name
		$title .= get_bloginfo( 'name' );
	
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title .= " $sep $site_description";
	
		// Add a page number if necessary:
		if ( $paged >= 2 || $page >= 2 )
			$title .= " $sep " . sprintf( __( 'Page %s', 'wpthms' ), max( $paged, $page ) );
	
		return $title;
	}
}
/*
 * Dynamic Sidebar
 *
 * @since 1.0
 * @framework 1.0
 */

if( !function_exists( 'yt_theme_dynamic_sidebars') ) {
	
	function yt_theme_dynamic_sidebars( $meta_key, $default ){
		if( !$meta_key && !$default ) 
			return;
		
		$conditional = is_page() || ( is_home() && get_option( 'page_for_posts' ) ) || ( yt_is_woocommerce() && wc_get_page_id('shop') ) ;

		/* If is page OR is page for posts ( must set the page for posts in Reading)*/
		if( apply_filters( 'yt_theme_dynamic_sidebars_conditional', $conditional ) ){
			if( !empty( $GLOBALS['post'] )){
				$post = $GLOBALS['post'];
				$post_id = $post->ID;
			}else{
				$post_id = 0;
			}
			
			if( is_home() && get_option( 'page_for_posts' ) )
				$post_id = get_option( 'page_for_posts' );

			if( yt_is_woocommerce() && wc_get_page_id('shop') )
				$post_id = wc_get_page_id('shop');


			$post_id = apply_filters( 'yt_theme_dynamic_sidebars_post_id', $post_id );

			/*Retrieve page layout from meta key*/ 
			$sidebar = get_post_meta( $post_id, $meta_key, true );

			if( 'none' !== $sidebar ){
				dynamic_sidebar( $sidebar );
			}

			if( empty( $sidebar ) )
				dynamic_sidebar( $default );
			
		}else{
			dynamic_sidebar( apply_filters( 'yt_theme_dynamic_sidebars_default', $default ) );
		}
	}

}
/*
 * After updating thene options
 *
 * @since 1.0
 * @framework 1.0
 */
add_action('ytto_after_option_data_saved', 'yt_theme_after_updated_options', 1);

if( !function_exists( 'yt_theme_after_updated_options') ) {
	
	function yt_theme_after_updated_options( $options_data ){

		
		$transient_list = apply_filters( 'yt_refresh_transient_list_after_updating_theme_options', array(

			'yt_mailchimp_widget_list',
			
			'yt_theme_gfonts',
			'yt_theme_cfonts',
			'yt_gfonts_checker',
			'yt_cfonts_checker',
		) );
		
		foreach( $transient_list as $transient ){
			delete_transient( $transient );	
		}
	}
}
/*
 * Reupdate theme options after saving from customizer
 *
 * @since 1.0
 * @framework 1.0
 */
add_action( 'yt_customize_after_saving', 'yt_reupdate_options_customize_after_saving' );

if( !function_exists( 'yt_reupdate_options_customize_after_saving' ) ) {
	
	function yt_reupdate_options_customize_after_saving( $options_data ){
		
		$GLOBALS['yt_theme_options']->set_options( wpthms_THEME_OPTIONS, $options_data );
		
	}
}
/*
 * Filtering Theme option fontface variable
 *
 * @since 1.0
 * @framework 1.0
 */

add_filter( 'yt_option_vars_fontfaces', 'yt_theme_filtering_fontfaces' );

if( !function_exists( 'yt_theme_filtering_fontfaces') ) {
	function yt_theme_filtering_fontfaces( $fontfaces ){

		//if( !is_admin() )
			//return $fontfaces;
		/* Custom fonts */
		$custom_fonts = array();

		if( yt_get_options( 'customfont_mode' ) == 'enable'){
			$custom_fonts = yt_get_options( 'custom_fontface' );
			
			if( !empty( $custom_fonts ) ){
				$fontfaces['optgroup-label-customfonts'] = sprintf( '========================%s========================', __('Uploaded fonts', 'wpthms' ) );
				$temp = array();
				foreach( $custom_fonts as $font ){
					$font_name = !empty( $font['font_name'] )
						&& !empty( $font['font_eot'] )
						&& !empty( $font['font_woff'] )
						&& !empty( $font['font_ttf'] )
						&& !empty( $font['font_svg'] ) ? $font['font_name'] : '';
					

					if( $font_name ){
						$weight = $font['font_weight'] . ( $font['font_style'] != 'normal' ? $font['font_style'] : '');
						
						if( !isset( $temp[$font_name] ) )
							$temp[$font_name] = array();
						if( !in_array( $weight, $temp[$font_name] ))
							$temp[$font_name][] = $weight ;
					}
				}
				
				if( !empty( $temp ) ){
					ksort( $temp );
					foreach( ( array ) $temp as $font_name => $v ){
						$font_variants = join( ',', $v );
						$fontfaces[ sprintf( 'customfont-%s:%s', $font_name , $font_variants ) ] = sprintf( '%s (%s)', $font_name, $font_variants ) ;	
					}	
				}else{
					unset( $fontfaces['optgroup-label-customfonts'] );
				}
			}
		}
		
		/* Google fonts */
		$google_fonts = array();
		if( yt_get_options( 'googlefont_mode' ) == 'enable'){
			$google_fonts = yt_parse_google_fonts();
			
			//print_r($google_fonts);
			
			if( !empty( $google_fonts ) ){
				$fontfaces['optgroup-label-googlefonts'] = sprintf( '=========================%s=========================', __('Google fonts', 'wpthms' ) );
				foreach( $google_fonts as $font ){
					$font_name = !empty( $font->family ) ? $font->family : '';
					$font_variants = !empty( $font->variants ) ? join( ',', $font->variants ) : '';
					$fontfaces[ sprintf( 'googlefont-%s:%s', str_replace(' ', '+' , $font_name ) , $font_variants) ] = sprintf( '%s (%s)', $font_name, $font_variants ) ;
				}	
			}
		}
		
		return $fontfaces;
	}
}

/**
 * Returns true if a blog has more than 1 category
 *
 * @since 1.0
 * @framework 1.0
 */
if ( ! function_exists( 'yt_categorized_blog' ) ) {
	function yt_categorized_blog() {
		if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
			// Create an array of all the categories that are attached to posts
			$all_the_cool_cats = get_categories( array(
				'hide_empty' => 1,
			) );
	
			// Count the number of categories that are attached to the posts
			$all_the_cool_cats = count( $all_the_cool_cats );
	
			set_transient( 'all_the_cool_cats', $all_the_cool_cats );
		}
	
		if ( '1' != $all_the_cool_cats ) {
			// This blog has more than 1 category so yt_categorized_blog should return true
			return true;
		} else {
			// This blog has only 1 category so yt_categorized_blog should return false
			return false;
		}
	}
}
/**
 * Flush out the transients used in yt_categorized_blog
 *
 * @since 1.0
 * @framework 1.0
 */

add_action( 'edit_category', 'yt_category_transient_flusher' );
add_action( 'save_post',     'yt_category_transient_flusher' );

if ( ! function_exists( 'yt_category_transient_flusher' ) ) {
	function yt_category_transient_flusher() {
		// Like, beat it. Dig?
		delete_transient( 'all_the_cool_cats' );
	}
}
/*
 * Allow mobile devices to use request desktop site function :P
 *
 * @since 1.0
 * @framework 1.0
 */
if( !function_exists('yt_theme_meta_viewport')) {
	
	function yt_theme_meta_viewport() {
		$output = '';
		$body_class = get_body_class();
		$meta_viewport = apply_filters( 'yt_theme_meta_viewport', '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">' );
		
		echo $meta_viewport;
	}
}

/*
 * wp_head content
 *
 * @since 1.0
 * @framework 1.0
 */
add_action( 'wp_head', 'yt_theme_wp_header', 30 );	

if( !function_exists( 'yt_theme_wp_header') ) {
	function yt_theme_wp_header( ){
		if( is_admin() )
			return;

		if( '' !== ( $header_code = yt_get_options( 'header_code' ) ) )
			echo "\n" . $header_code;
	}
}

/*
 * wp_footer content
 *
 * @since 1.0
 * @framework 1.0
 */
add_action( 'wp_footer', 'yt_theme_wp_footer', 30 );	

if( !function_exists( 'yt_theme_wp_footer') ) {
	function yt_theme_wp_footer( ){
		if( is_admin() )
			return;
		
		if( '' !== ( $footer_code = yt_get_options( 'footer_code' ) ) )
			echo "\n" . $footer_code;
	}
}



/*
 * Theme Google fonts
 *
 * @since 1.0
 * @framework 1.0
 */
if( !function_exists( 'yt_get_theme_googlefonts') ) {

	function yt_get_theme_googlefonts( $type = 'url'){
		$transient_name = 'yt_theme_gfonts';
		$output = get_transient( $transient_name );
		
		// checker must be false to continue, prevent parsing multiple times
		if( false === $output && ( false === get_transient( 'yt_gfonts_checker' ) ) ){
			
			set_transient( 'yt_gfonts_checker', true );
			
			$link = 'http' . ( is_ssl() ? 's' : '' ) . '://fonts.googleapis.com/css';
			$query_args = array();

			$yt_data = yt_get_options();
			$googlefonts = yt_used_typography( $yt_data );

			if( empty( $googlefonts ) )
				return '';
			
			$count = 0;
			$family = '';
			foreach( $googlefonts as $font => $variants ){
				$count++;
				$family .= $font . ( !empty( $variants ) ? ':' . join( ',' ,$variants ) : '' ) . ( $count < count( $googlefonts ) ? '|' : '');
			}
			// echo $family; die();
			/*Subsets*/
			$selected_subsets = array_filter( ( array ) $yt_data['googlefont_subsets'] );
			$subsets = '';

			if( !empty( $selected_subsets ) ){
				
				$query_args['subset'] = join(',' , $selected_subsets );
			}

			if( $family ){
				$query_args['family'] = $family;

				$output = add_query_arg( $query_args, $link );
			}

			set_transient( $transient_name, $output );
			
		}
		

		if( $type === 'import'){
			// $output = '@import url("' . esc_url( $output ) . '");';	
			$output = '@import url("' . $output . '");';	
		}elseif( $type === 'standard'){
			// $output = '<link href="' . esc_url( $output ) . '" rel="stylesheet" type="text/css">';
			$output = '<link href="' . $output . '" rel="stylesheet" type="text/css">';
		}
		
		
		return $output;
		
		
	}
}
/*
 * Theme Custom fonts
 *
 * @since 1.0
 * @framework 1.0
 */
if( !function_exists( 'yt_get_theme_customfonts') ) {
	
	function yt_get_theme_customfonts(){

		$transient_name = 'yt_theme_cfonts';
		$output = get_transient( $transient_name );

		if ( false === $output && ( false === get_transient( 'yt_cfonts_checker' ) ) ) {

			set_transient( 'yt_cfonts_checker', true );

			$pickedfonts = yt_used_typography( yt_get_options(), 'customfont' );
			
			$temp = array(); 
			
			$font_list = yt_get_options( 'custom_fontface' );	

			if( empty( $font_list ) )
				return $output;
			
			foreach( $font_list as $k => $v ){
				$mixed_variants = $v['font_weight'] . ( $v['font_style'] == 'italic' ? $v['font_style'] : ''  );
				
				/*if isset font in picked list, loop it*/
				if( !empty( $v['font_name'] ) 
					&& isset( $pickedfonts[ $v['font_name'] ] ) 
					&& !empty( $v['font_eot'] )
					&& !empty( $v['font_woff'] )
					&& !empty( $v['font_ttf'] )
					&& !empty( $v['font_svg'] )
				){
				
					foreach( $pickedfonts as $font => $variants ){
						if( in_array( $mixed_variants, $variants )){
							//$temp[] = $font_list[$k];
							
							$output .= "@font-face {\n";
								$output .= "\tfont-family: '" . esc_attr( $v['font_name'] ) . "';\n";
								$output .= "\tsrc: url('" . esc_url( yt_clean_url( $v['font_eot'] ) ) . "');\n";
								$output .= "\tsrc: url('" . esc_url( yt_clean_url( $v['font_eot'] ) ) . "?#iefix') format('embedded-opentype'),\n";
									 $output .= "\t\turl('" . esc_url( yt_clean_url( $v['font_woff'] ) ) . "') format('woff'),\n";
									 $output .= "\t\turl('" . esc_url( yt_clean_url( $v['font_ttf'] ) ) . "') format('truetype'),\n";
									 $output .= "\t\turl('" . esc_url( yt_clean_url( $v['font_svg'] ) ) . "') format('svg');\n";
								$output .= "\tfont-weight: " . esc_attr( $v['font_weight'] ) . ";\n";
								$output .= "\tfont-style: " . esc_attr( $v['font_style'] ) . ";\n";
							$output .= "}\n";
							
						}
					}
				}
				//if( isset)
			
			}

			set_transient( $transient_name, $output );
		}

		return $output;
	}
}