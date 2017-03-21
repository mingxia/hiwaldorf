<?php
/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;

/**
 * @title:			wpthms Options Framework Init
 * @description:	wpthms Options Framework Initialization
 * @version: 		1.0
 * @author:		wpthms
 * @author URI:	http://wpthms.com
 * @license:		(c) wpthms
 *
 * @package:		wpthms
 * @subpackage:	framework/admin
 * @since 1.0
 */

/**
 * Definitions
 *
 * @since 1.0
 */
$themedata;
$themedata = wp_get_theme();
$theme_stylesheet = get_option( 'stylesheet' );
$theme_dir_slug = preg_replace( '/[^a-zA-Z0-9-_]/', '', strtolower( trim( str_replace( '-', '_', $theme_stylesheet  ) ) ) );

//echo preg_replace( '/[^a-zA-Z0-9-_]/', '', strtolower( trim( str_replace( '_', '-', 'fhdjsafh32423+++++_____++++__---d-as-d=-das_'  ) ) ) );
define( 'wpthms_FRAMEWORK_URI', 		wpthms_URI . 'framework/' );
define( 'wpthms_FRAMEWORK_DIR', 		wpthms_DIR . 'framework/' );
define( 'wpthms_FRAMEWORK_VERSION', '1.0.4' );
define( 'THEMENAME', $themedata->Name );
define( 'THEMESLUG', preg_replace( '/[^a-zA-Z0-9-_]/', '', strtolower( trim( str_replace( '_', '-', $themedata->Name  ) ) ) )  );
define( 'THEMEVERSION', $themedata->Version  );
define( 'THEMEAUTHOR', $themedata->Author );
		
define( 'wpthms_WPML_OPTIONS_CONTEXT', ucfirst( THEMENAME ) . ' Theme Options' );
global $sitepress;	

/*Define theme option key constant*/
if( defined( 'ICL_LANGUAGE_CODE' ) && is_a( $sitepress, 'SitePress') && ICL_LANGUAGE_CODE !== $sitepress->get_default_language() ) {
	
	define( 'wpthms_THEME_OPTIONS', 'yeahthemes_theme_options_' . $theme_dir_slug . '_' . ICL_LANGUAGE_CODE );
	
}else{
	
	define( 'wpthms_THEME_OPTIONS', 'yeahthemes_theme_options_' . $theme_dir_slug );
	
}

/**
 * Load Files
 *
 * @since 1.0.0
 */			
require_once( 'includes/yt-conditional-tags.php');
require_once( 'includes/yt-functions-plugins.php');
require_once( 'includes/yt-functions.php');
require_once( 'includes/yt-functions-options.php');
require_once( 'includes/yt-functions-admin.php');

/**
 * Include functions for admin side and when isset wp_customize
 */
if( is_admin() || isset( $GLOBALS['wp_customize'] ) ){
	require_once( 'includes/yt-functions-field-types.php');
	require_once( 'includes/yt-options-settings.api.php');
	require_once( 'includes/yt-theme-customize.api.php');
	require_once( 'includes/yt-meta-box-api.php');
	require_once( 'includes/yt-default-metaboxes.php');
}
/**
 * Initialize Theme Options, Framework Settings, Theme customizer
 */
add_action( 'init', 'yt_admin_options_init', 3 );

if( !function_exists( 'yt_admin_options_init' ) ) {

	function yt_admin_options_init(){

		global $yt_options;

		if ( ( is_admin() || isset( $GLOBALS['wp_customize'] ) ) && class_exists( 'YT_Options_Framework' ) ) {
			
			$themename = wp_get_theme()->get('Name');
			
			
		
			//$yt_settings_page = new YT_Options_Framework();
			
			$GLOBALS['yt_theme_options'] = new YT_Options_Framework_Theme_Options();
			
			/*option key that store the option data
			 * 
			 * The name of the constant. Only Alphanumeric and underscore (_) are allowed 
			 * Recommended to use with prefix
			 */
			$option_name = wpthms_THEME_OPTIONS;
			
			/*option array you want to generate*/
			$option_fields = $yt_options;
			
			/**
			 * option type
			 *
			 * if you use for theme options, set it to 'theme_mod', 
			 * the option data will be store in theme mod using you theme_mods_$theme_slug as key
			 * this is very important for wp customize
			 */
			
			$admin_menu = array(
				/**
				 * Administration Menus location
				 * http://codex.wordpress.org/Adding_Administration_Menus
				 */									
				'page_title' 	=> __('wpthms Theme options','wpthms'),		//$page_title
				'menu_title' 	=> THEMENAME, 				//$menu_title
				'capability' 	=> 'edit_theme_options',								//$capability
				'menu_slug' 	=> 'yt-theme-options',								//$menu_slug
				'icon_url'		=> null,
				'position'	=> '50.6'
			);
			
			$config = array(
				'prefix' => 'ytto_',
				'heading' => $themename,
				'option_name' => $option_name,
				'option_fields' => $yt_options,
				'admin_bar' => true,
				'admin_bar_title' => __('Theme Options', 'wpthms'),
				'menu_function' => 'add_menu_page',
				'menu_args' => $admin_menu 
			);
			
			$GLOBALS['yt_theme_options']->init( $config );
			
		}
		
		/**
		 * Init Theme customizer
		 */
		if ( class_exists( 'YT_Theme_Customize' ) && isset( $GLOBALS['wp_customize'] ) && apply_filters( 'yt_support_theme_customizer', true ) ) {
			
			
			$GLOBALS['yt_theme_customize'] = new YT_Theme_Customize();
			
			$GLOBALS['yt_theme_customize']->init( wpthms_THEME_OPTIONS, $yt_options );
		
		}
		
	}

}

/**
 * Class YT_Options_Framework_Theme_Options
 * @since 1.0.0
 */
if( class_exists( 'YT_Options_Framework' )):

class YT_Options_Framework_Theme_Options extends YT_Options_Framework{
	
	/**
	 * Init
	 */
	public function init( $args = array() ){
		
		parent::init( $args );
		
		$this->extended_action_hooks();
		
	}
	
	/**
	 * Overwite parent theme option links on admin menu bar
	 * @since 1.0.0
	 */
	public function admin_bar_menu_shortcuts() {
		
		$this->admin_bar_add_root_menu( __('( Y )', 'wpthms') , $this->_menu_slug );
		$this->admin_bar_add_sub_menu( __('Documentation', 'wpthms'), apply_filters( 'yt_admin_top_menu_bar_document_url', '' ), 'yt-theme-documentation', $this->_menu_slug );
		$this->admin_bar_add_sub_menu( __('Support forum', 'wpthms'), apply_filters( 'yt_admin_top_menu_bar_support_url', '' ), 'yt-theme-support', $this->_menu_slug );
	}
	/**
	 * Extended action hooks
	 */
	function extended_action_hooks(){
		
		add_action('admin_menu', array( &$this, 'register_submenu_page' ));
		
		/* Redirect to theme options page after activating*/
		global $pagenow;
		
		if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ){
			
			wp_redirect( admin_url( 'admin.php?page=' . $this->_menu_slug ));
			exit;
			
		}
	}
	
	/**
	 * Register menu page
	 */
	function register_submenu_page() {
		
		//add_submenu_page
		
		global $yt_options_page;
		
		$yt_options_page = add_submenu_page( 
			$this->_menu_slug, 
			$this->_heading  . ' ' . $this->_admin_bar_title, 
			__( 'Theme Options', 'wpthms' ), 
			'edit_theme_options', 
			'yt-theme-options', 
			array( &$this, 'load_template')
		); 
		// add_submenu_page( 
		// 	$this->_menu_slug, 
		// 	__('Documentation', 'wpthms'), 
		// 	__('Documentation', 'wpthms'), 
		// 	'manage_options', 
		// 	'yt-theme-doc', 
		// 	array( &$this, 'document_page_callback')
		// ); 


	    global $submenu;

	    $submenu[$this->_menu_slug][] = array( __('Documentation', 'wpthms'), 'manage_options', apply_filters( 'yt_admin_top_menu_bar_document_url', '' ));
	    $submenu[$this->_menu_slug][] = array( __('Support forum', 'wpthms'), 'manage_options', apply_filters( 'yt_admin_top_menu_bar_support_url', '' ));
	
		add_action("load-$yt_options_page", array( &$this, 'screen_options_help' ));
	
	}	
	/**
	 * Support page callback
	 */
	function document_page_callback() {
		
		/*echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>My Custom Submenu Page</h2>';
		echo '</div>';*/
		
	
	}
	/**
	 * Help screen
	 */
	function screen_options_help() {

		global $yt_options_page, $current_screen;
	 
		if ($current_screen->id != $yt_options_page)
			return;


		$help_contents = apply_filters( 'yt_theme_options_screen_help_contents', array() );

		if( !empty( $help_contents ) ){
			foreach ( $help_contents as $tab ) {
				if( is_array( $tab ) && !empty( $tab['id'] ) && !empty( $tab['title'] ) )
					$current_screen->add_help_tab( $tab );
			}
		}

	}
	
}
endif;