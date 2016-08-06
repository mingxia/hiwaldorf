<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 **********************************************************************************************************
 * 												DO NOT EDIT THIS FILE
 **********************************************************************************************************
 * Theme Scripts
 *
 * @author		Yeahthemes
 * @copyright	Copyright ( c ) Yeahthemes
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	Yeah Includes
 */

if( !is_admin())
	add_action( 'wp_enqueue_scripts', 'yt_theme_scripts', 30 );

if( !function_exists( 'yt_theme_scripts' ) ) {
	
	function yt_theme_scripts(){
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$themedata = wp_get_theme();
		$version = $themedata->Version;
	
		/*
		 * Custom styles
		 */
		global $sitepress;
		$lang = '';
		if( defined( 'ICL_LANGUAGE_CODE' ) && is_a( $sitepress, 'SitePress') && ICL_LANGUAGE_CODE !== $sitepress->get_default_language() ) {
	
			$lang = '_' . ICL_LANGUAGE_CODE;
			
		}
		if(is_multisite()) {
			$uploads = wp_upload_dir();
			wp_register_style( 'custom-styles', $uploads['baseurl'] . "/custom-styles$lang.css", 'style' );
		} else {
			$custom_style_src = yt_get_overwritable_directory_uri( "/css/custom-styles$lang.css" );
			
			wp_register_style( 'custom-styles', $custom_style_src, 'style' );
		}

		$js_dir = get_template_directory_uri() . '/js/';

		wp_deregister_style( 'bootstrap' );
		wp_register_style( 'bootstrap', 		YEAHTHEMES_FRAMEWORK_URI . "css/bootstrap$suffix.css" );
		wp_deregister_script( 'bootstrap' );
		wp_register_script( 'bootstrap',		YEAHTHEMES_FRAMEWORK_URI . "js/bootstrap$suffix.js", 			array( 'jquery' ), '3.0.3', true);
		
		//Allow child theme overwrite theme style
		wp_deregister_style( 'animate' );
		wp_register_style( 'animate', 							yt_get_overwritable_directory_uri( '/css/animate.css' ), '');
		wp_register_style( 'font-awesome', 						yt_get_overwritable_directory_uri( '/css/font-awesome.css' ), '');
		// wp_deregister_style( 'flexslider' );
		wp_register_style( 'flexslider', 						yt_get_overwritable_directory_uri( '/css/flexslider.css' ), '' );
	
		//Allow child theme overwrite theme style
		// wp_deregister_script( 'flexslider' );
		wp_register_script( 'flexslider', 					$js_dir . "jquery.flexslider$suffix.js", array( 'jquery' ), '2.1', true );
		wp_register_script( 'sharrre',						$js_dir . "jquery.sharrre$suffix.js", array( 'jquery' ), '1.0', true );
		wp_register_script( 'script',						$js_dir . "yt.script$suffix.js", array( 'jquery' ), '1.0', true );
		wp_register_script( 'custom',						$js_dir . "yt.custom$suffix.js", array( 'jquery' ), '1.0', true );
		$theme_info = wp_get_theme();
		$base_url = set_url_scheme( home_url( '/' ) );

		$ajaxurl = add_query_arg( array( 'yt_ajaxify' => 1 ), $base_url );
		wp_localize_script( 'custom', 'Yeahthemes', array(
			'_vars' => array(
				'currentPostID' => esc_js( (isset($GLOBALS['post']->ID) ? $GLOBALS['post']->ID : 0 ) ),
				'ajaxurl'	=> esc_url( $ajaxurl ),
				'nonce'		=> esc_js( wp_create_nonce('yeahthemes_frontend_nonce') )
			),
			'themeVars' => array(

				'nonce' => wp_create_nonce( $theme_info->get( 'Name' )),
				'megaMenu'	=> array(
					'nonce' => wp_create_nonce( THEMESLUG . '_mega_menu' ),
					'ajax' => 'ajax' === yt_get_options( 'main_megamenu_request_type' ) ? true : false,
					'effect' => apply_filters( 'yt_ux_mega_menu_effect', 'fadeIn'),
				),				
				'mobileMenuNonce' => wp_create_nonce( THEMESLUG . '_mobile_menu' ),
				'mobileMenuEffect' => apply_filters( 'yt_ux_mobile_menu_effect', 'zoomOut'),
				'widgetAjaxPostsByCatNonce' => wp_create_nonce( THEMESLUG . '_ajax_posts_by_cat' ),
			)


		) );
	
		if( function_exists( 'yt_get_theme_googlefonts' )){
			$google_fonts = yt_get_theme_googlefonts();
			if( $google_fonts ){
				wp_enqueue_style('yt-google-fonts', $google_fonts );
			}
		}

		do_action( 'yt_theme_scripts_before_enqueue_styles' );

		wp_enqueue_style( 'bootstrap' );
		
		if ( class_exists('WPCOM_Liveblog' ) ) {
			wp_dequeue_style('liveblog');
			wp_enqueue_style('liveblog-custom', yt_get_overwritable_directory_uri( '/css/liveblog.css' ), '');
		}
				
		wp_enqueue_style( 'font-awesome' );
		/**
		 * Loads our default stylesheet.
		 */
		wp_enqueue_style( 'theme-default-style', get_stylesheet_uri(), array(), '2.0.4' );
		
		/**
		 * Dynamic stylesheet.
		 */
		wp_enqueue_style( 'animate' );
		wp_enqueue_style( 'flexslider' );

		do_action( 'yt_theme_scripts_after_enqueue_styles' );
		
		wp_enqueue_script( 'jquery' );
		
		do_action( 'yt_theme_scripts_before_enqueue_scripts' );
		/**
		 * Comment reply
		 */
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		wp_enqueue_script( 'bootstrap' );
		wp_enqueue_script( 'flexslider' );
		wp_enqueue_script( 'script' );
		wp_enqueue_script( 'sharrre' );
		wp_enqueue_script( 'custom' );	
		
		do_action( 'yt_theme_scripts_after_enqueue_scripts' );
	}
}