<?php
/**
 * Plugin Name: YT Shortcode Generator (HiWaldorf)
 * Plugin URI: https://github.com/mingxia/hiwaldorf/tree/master/hiwaldorf-yt-plugins/yt-shortcode-generator
 * Description: Original wpthms Sparkle companion plugin; maintained by HiWaldorf for existing sites.
 * Version: 2026.11.0
 * Author: wpthms (original), HiWaldorf (maintenance)
 * Author URI: https://hiwaldorf.com/
 * Update URI: https://github.com/mingxia/hiwaldorf
 * License: GPL2

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

/*
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; 


define( 'YT_SHORTCODE_VER', '2026.11.0' );
define( 'YT_SHORTCODE_PREFIX', 'yt-' );
define( 'YT_SHORTCODE_DOMAIN', 'yt-shortcode-generator' );
define( 'YT_SHORTCODE_NAME', 'YT Shortcode Generator' );

define( 'YT_SHORTCODE_URI', plugin_dir_url( __FILE__ ) );
define( 'YT_SHORTCODE_DIR', plugin_dir_path( __FILE__ ) );
define( 'YT_SHORTCODE_PLUGIN', __FILE__ );
define( 'YT_SHORTCODE_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );


/*
 * Define yt-shortcodes
 */
//if ( ! defined( 'YT_SHORTCODE' ) ) define( 'YT_SHORTCODE', true );

require_once( 'yt-shortcodes-field-types.php' );

/**
 * YT_Shortcode_Generator class
 */
if( !class_exists( 'YT_Shortcode_Generator' ) ){
	
class YT_Shortcode_Generator {
	/**
	 * Shorcode types
	 *
	 * @since 1.0.0
	 */
	public $_shortcodes = array();
	/**
	 * Shorcode Menu item
	 *
	 * @since 1.0.0
	 */
	private $_shortcode_menu_item = array();
	/**
	 * Shorcode list
	 *
	 * @since 1.0.0
	 */
	private $_shortcode_list = array();

	function __construct() {
		
		
		//add_action('admin_init', array( $this, 'init') );
		$this->load_languages();
		

		// !important, This makesure shortcode vars can be filtered from theme 
		$this->admin_init();

		add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts'), 10 );

		add_action( 'init', array( $this, 'editor_styles'), 15 );

		add_action( 'init', array( $this, 'setup_shortcode'), 30 );

		add_action( 'plugins_loaded', array( $this, 'text_domain') );

		add_action( 'after_setup_theme', array( $this, 'add_shortcodes'), 30 );

	}
	
	function setup_shortcode(){

		if( !is_admin() )
			return;
		
		
		add_action( 'wp_ajax_yt_shortcode_modal_box', array( $this, 'shortcode_action' ), 10 );
		add_action( 'wp_ajax_yt_shortcode_load_type', array( $this, 'load_shortcode' ), 10 );
		add_action( 'wp_ajax_yt_shortcode_live_preview', array( $this, 'shortcode_live_preview' ), 10 );
	}
	/**
	 * Registers TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function admin_init(){
		

		if( !is_admin() ) return;

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts'), 1 );
		add_filter( 'admin_body_class', array( $this, 'shortcode_body_class'), 15 );
		
		add_action( 'media_buttons', array( $this, 'shortcode_button'), 15 );
		add_filter( 'wp_fullscreen_buttons',  array( $this, 'shortcode_button_fullscreen'), 15 );
		
		//add_action( 'wp_ajax_yt_get_shortcode_syntax', array( $this, 'get_shortcode_syntax' ), 10 );
	}


	/**
	 * Add shortcode after theme setup
	 *
	 * @since 1.0.6
	 */
 	function add_shortcodes(){

		require_once( 'yt-shortcodes-functions.php' );
	}
	/**
	 * Assigns class's instances
	 *
	 * @since 1.0.0
	 */
	function get_vars(){

		$this->_shortcodes = $this->shortcode_settings();
		
		$this->_shortcode_menu_items = $this->shortcode_menu();
		
		$this->_shortcode_list = $this->get_shortcode_list();
	}
	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function text_domain() {
		load_plugin_textdomain( YT_SHORTCODE_DOMAIN, false, YT_SHORTCODE_DIRNAME . '/langs/' ); 
	}
	/**
	 * Get shortcode info.
	 *
	 * @since 1.0.0
	 */
	private function shortcode_info(){
		
		return (array) get_plugin_data( YT_SHORTCODE_PLUGIN, true, true );	
		
	}
	
	/**
	 * Load the languages before everything else.
	 *
	 * @return    void
	 *
	 * @access    private
	 * @since     1.0
	 */
	private function load_languages() {
		
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		
	}
	
	/**
	 * Load the text domain.
	 *
	 * @return    void
	 *
	 * @access    private
	 * @since     1.0
	 */
	public function load_textdomain() {
	
		load_plugin_textdomain( YT_SHORTCODE_DOMAIN, false, YT_SHORTCODE_DIR . '/languages/' );
	
	}
	
	/**
	 * Adding admin body class
	 *
	 * @return	void
	 */
	public function shortcode_body_class( $classes ){
		
		
		$admin_body_class = apply_filters( 'yt_shortcode_editor_admin_body_class', array() );
		
		$admin_body_class[] = 'yt-shortcode-editor-plugin';
		
		$classes .= ' ' . join( ' ', $admin_body_class );
		
		return $classes;
	
	}
	
	/**
	 * Adding shortcode generator button next to add media
	 *
	 * @return	void<img src="editor/images/yicon.png" width="20" height="40"  alt=""/>
	 */
	function shortcode_button( $editor_id = 'content' ){
		
		$excluded_post_types = apply_filters( 'yt_shortcode_editor_excluded_post_types', array() );

		global $post, $typenow;

		if( !empty( $excluded_post_types ) && !empty( $typenow ) && in_array( $typenow, $excluded_post_types ) )
			return;
		
		echo apply_filters( 'yt_shortcode_editor_button', '<a href="#yt-shortcode-init-action-button" title="' . __( 'Insert Shortcodes', YT_SHORTCODE_DOMAIN ) . '" class="button yt-shortcode-init-action-button" data-nonce="' . esc_attr( wp_create_nonce('yt_shortcode_init_nonce') ) . '" data-editor="' . esc_attr( $editor_id ) . '">[ <i class="yt-shortcode-icon-wpthms"></i> ]</a>' );		
	
	}
	
	function shortcode_button_fullscreen( $buttons ){

		$excluded_post_types = apply_filters( 'yt_shortcode_editor_excluded_post_types', array() );

		global $post, $typenow;

		if( !empty( $excluded_post_types ) && !empty( $typenow ) && in_array( $typenow, $excluded_post_types ) )
			return $buttons;

		$buttons['yt-shortcode-init-action-button-fullscreen'] = apply_filters( 'yt_shortcode_editor_button_fullscreern', array( 
			'title' => __( 'Insert Shortcodes', YT_SHORTCODE_DOMAIN ), 
			'onclick' => '', 
			'both' => true 
		) );
		
		return $buttons;
		
	}
	
	/**
	 * Shortcode ajax action
	 *
	 * @return	void
	 */
	public function shortcode_action(){
		
		check_ajax_referer( 'yt_shortcode_init_nonce', 'nonce' );

		$this->get_vars();
		
		include_once 'yt-shortcodes-ui.php';
		
		die();
	
	}
	
	public function shortcode_live_preview(){
	
		check_ajax_referer( 'yt_shortcode_nonce', 'nonce' );
		
		$shortcode = $_GET['shortcode'];

		$return = array(
			'html' 		=> yt_shortcode_unautop( $shortcode ),
			'html_code' => esc_attr( yt_shortcode_unautop( $shortcode ) ),
			'shortcode' => $shortcode
		);

		wp_send_json_success( $return );

		die();
		
	}
	
	/**
	 * Load shortcode type
	 *
	 * @return	void
	 */
	public function load_shortcode(){
		
		$this->get_vars();
		
		if( empty( $this->_shortcodes ) || !is_array( $this->_shortcodes ) ) 
			return;
			
		check_ajax_referer( 'yt_shortcode_nonce', 'nonce' );
		
		$shortcode_type = $_GET['type'];
		
		//print_r($this->_shortcode_list);
				
		if( $shortcode_type && in_array( $shortcode_type, ( array ) $this->_shortcode_list ) ){
			
			
			
			if( empty( $this->_shortcodes[$shortcode_type]['options'] ) )
				return;
			
			$shortcode_fields = $this->_shortcodes[$shortcode_type]['options'];
			
			foreach( $shortcode_fields as $field_k => $field_v ){
				
				$class =  ' yt-shortcode-field-type-' . $field_v['type'];
				
				echo '<div class="yt-clear yt-shortcode-field-type-row' . $class . '" data-type="' . $field_v['type'] . '">';
					echo !empty( $field_v['name'] ) ? '<h3>' . $field_v['name'] . '</h3>' : '';
					echo '<div class="yt-shortcode-field-type-control">';
					
						$_args = array(
							'name' 		=> isset( $field_v['name'] ) ? $field_v['name'] : '',
							'type'		=> isset( $field_v['type'] ) ? $field_v['type'] : '',
							'id' 		=> $field_k,
							'std' 		=> isset( $field_v['std'] ) ? $field_v['std'] : '',
							'options' 	=> isset( $field_v['options'] ) ? $field_v['options'] : array(),
							'settings' => isset( $field_v['settings'] ) ? $field_v['settings'] : array()
							
						
						);
						$this->shortcode_create_field( $_args );
					
					
					echo '</div>';
					
					echo '<div class="yt-shortcode-field-type-desc">';
					
						echo !empty( $field_v['desc'] ) ? $field_v['desc'] : '';
					
					echo '</div>';
					
				echo '</div>';
				
			}
			
			
		}
		
		die();
	
	}
	
	public function shortcode_create_field( $args = array() ){
		
		
		if( !class_exists( 'YT_Shortcode_Generator_Field_Types' ) ) 
			return;


		$args = wp_parse_args( $args, array(
			'name' 		=> '',
			'desc' 		=> '',
			'std' 		=> '',
			'type' 		=> '',
			'settings'	=> array(),
			'options'	=> array()
		) );
		
		switch( $args['type'] ){
			
			case 'colorpicker':
				
				echo YT_Shortcode_Generator_Field_Types::colorpicker( $args['id'], $args['std'] );
				
			break;
			
			case 'media':
				
				echo  YT_Shortcode_Generator_Field_Types::media( $args['id'], $args['std'], $args['name'], ( !empty( $args['settings']['media_by'] ) ) ? $args['settings']['media_by'] : 'url' );
				
			break;
			
			case 'select':
			
				echo YT_Shortcode_Generator_Field_Types::select( $args['id'], $args['std'], $args['options'], 'parent', ( !empty( $args['settings']['multiple'] ) ? true : false ) );
			
			break;
			
			case 'select_alt':
			
				echo YT_Shortcode_Generator_Field_Types::select_alt( $args['id'], $args['std'], $args['options'], 'parent', ( !empty( $args['settings']['multiple'] ) ? true : false ) );
			
			break;
			
			case 'text':
				
				echo YT_Shortcode_Generator_Field_Types::text( $args['id'], $args['std'] );
			
			break;
			
			case 'number':
				
				echo YT_Shortcode_Generator_Field_Types::number( $args['id'], $args['std'] );
			
			break;
			
			case 'textarea':
				
				echo YT_Shortcode_Generator_Field_Types::textarea( $args['id'], $args['std'] );
			
			break;
			
			case 'repeatable':
				
				echo YT_Shortcode_Generator_Field_Types::repeatable( $args );
			
			break;
			
			case 'tag_search':
				
				echo YT_Shortcode_Generator_Field_Types::tag_search( $args );
			
			break;
			
			case 'html':
				
				echo YT_Shortcode_Generator_Field_Types::html( $args['desc'] );
			
			break;


			
			default:
				do_action( 'yt_shortcode_field_type', $args ); 
		}
	
	}
	
	
	/**
	 * Shortcode menu items
	 *
	 * @return	string
	 */
	public function shortcode_menu(){
		
		if( empty( $this->_shortcodes ) || !is_array( $this->_shortcodes ) ) 
			return array( '<li style="width:100%;"><span>' .  __( 'No shortcode found', YT_SHORTCODE_DOMAIN ) . '</span></li>' );
		
		$items = array();
		
		foreach( ( array ) $this->_shortcodes as $s_k => $s_v ){
			
			/* Separator*/
			if( 'shortcode_item_separator' == $s_k ){
			
				$item = '<li class="separator"><span></span></li>';
			
			}else{
			
				$item_icon = !empty( $s_v['settings']['iconfont'] ) 
					? '<i class="' . esc_attr( $s_v['settings']['iconfont'] ) . '"></i> ' 
					: ( !empty( $s_v['settings']['iconimage'] ) 
						? '<img alt="' . esc_attr( $s_v['name']  ) . '" src="' . esc_url( $s_v['settings']['iconimage'] ) . '"> ' 
						: '' );
						
				$item = '<li><a href="#' . $s_k . '-shortcode" data-shortcode="' . esc_attr( $s_k) . '" data-syntax="' . esc_attr( $s_v['settings']['syntax'] ) . '" title="' . esc_attr( $s_v['desc'] ) . '">' . $item_icon . $s_v['name'] . '</a></li>';
				
			}
			
			$items[] = $item;
		
		}
		
		return (array) $items;
		
		
	}
	
	
	/**
	 * Shortcode list array
	 *
	 *
	 * @return	array()
	 */
	public function get_shortcode_list(){
		
		if( empty( $this->_shortcodes ) ) 
			return array();
		
		$shortcode_list = array();
		
		foreach( ( array ) $this->_shortcodes as $s_k => $s_v ){
			
			if( 'shortcode_item_separator' !==  $s_k ){
				
				$shortcode_list[] = $s_k;
			
			}
			
		}
		
		return ( array ) $shortcode_list;
	
	}
	
	public function get_shortcode_syntax(){
		
		if( empty( $this->_shortcodes ) || !is_array( $this->_shortcodes ) ) 
			return;
			
		check_ajax_referer( 'yt_shortcode_nonce', 'nonce' );
		
		$shortcode_type = $_GET['type'];
		
		//print_r($this->_shortcode_list);
				
		if( $shortcode_type && in_array( $shortcode_type, ( array ) $this->_shortcode_list ) ){
			
			if( empty( $this->_shortcodes[$shortcode_type]['settings']['syntax'] ) )
				return;
				
			echo $this->_shortcodes[$shortcode_type]['settings']['syntax'];
		
		
		}
		
		die();
			
	
	}
	
	/**
	 * Shortcode array
	 *
	 *
	 * @return	array()
	 */
	public function shortcode_settings(){
		
		$yt_shortcodes = array();
		
		if( apply_filters( 'yt_shortcode_default_settings_fields', true ) )

			require_once('yt-shortcodes-config.php');
		
		return apply_filters('yt_shortcode_setting_fields', $yt_shortcodes );
			
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Admin Scripts and Styles
	 *
	 * @return	void
	 */
	public function admin_scripts(){
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'yt-shortcode-styles', 		YT_SHORTCODE_URI . 'editor/css/style.css' );
		//wp_enqueue_style( 'yt-shortcode-jquery-ui' , 	YT_SHORTCODE_URI . 'editor/css/jquery-ui-yt-shortcode.css' );
		
		wp_enqueue_script('wp-color-picker' );

		//wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_enqueue_script( 'yt-shortcode-scripts', 		YT_SHORTCODE_URI . 'editor/js/custom.js', array('jquery'), YT_SHORTCODE_VER, false );
		
		wp_localize_script( 'yt-shortcode-scripts', 'yt_shortcodeVars', apply_filters( 'yt_shortcode_vars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce('yt_shortcode_nonce'),
			'ytNotImage' => __('Note: This file is not a valid image!', YT_SHORTCODE_DOMAIN),
			'ytUseImage' => __('Use this one', YT_SHORTCODE_DOMAIN),
			'ytViewFile' => __('View File', YT_SHORTCODE_DOMAIN),
			'ytNotfound' => __('Not found', YT_SHORTCODE_DOMAIN),
		)) );
	}
	/**
	 * Enqueue Scripts and Styles
	 *
	 * @return	void
	 */
	public function front_scripts(){

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		if( apply_filters( 'yt_shortcode_front_css_bootstrap', true ) )
			wp_enqueue_style( 'bootstrap',  YT_SHORTCODE_URI . "css/bootstrap$suffix.css"  );
		
		if( apply_filters( 'yt_shortcode_front_css_shortcodes', true ) )
			wp_enqueue_style( 'yt-shortcodes',  YT_SHORTCODE_URI . 'css/shortcodes.css'  );
		
		wp_register_script( 'gmap-api',	'//maps.google.com/maps/api/js?sensor=true&v=3.6', array( 'jquery' ) );

		if( apply_filters( 'yt_shortcode_front_js_bootstrap', true ) )
			wp_enqueue_script( 'bootstrap',  YT_SHORTCODE_URI . "js/bootstrap$suffix.js", array('jquery'), YT_SHORTCODE_VER, true  );
		
		if( apply_filters( 'yt_shortcode_front_js_shortcodes', true ) )
			wp_enqueue_script( 'yt-shortcodes',  YT_SHORTCODE_URI . "js/yt-shortcodes$suffix.js", array('jquery'), YT_SHORTCODE_VER, true  );

	}
	/**
	 * Editor style
	 *
	 * @return	void
	 */
	public function editor_styles() {

		if( apply_filters( 'yt_shortcode_editor_style_css_bootstrap', true ) )
	    	add_editor_style( YT_SHORTCODE_URI . 'editor/css/editor-styles.css' );
	}
    
}

}//end class_exists

//if( is_admin() )
$GLOBALS['yt_shortcode_generator'] = new YT_Shortcode_Generator();