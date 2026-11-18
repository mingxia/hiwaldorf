<?php
/*
Plugin Name: YT Sidebar Generator (HiWaldorf)
Plugin URI: https://github.com/mingxia/hiwaldorf/tree/master/hiwaldorf-yt-plugins/yt-sidebar-generator
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

define( 'YT_SIDEBAR_GENERATOR_VER', '2026.11.0' );
define( 'YT_SIDEBAR_GENERATOR_URI', plugin_dir_url( __FILE__ ) );
define( 'YT_SIDEBAR_GENERATOR_DIR', plugin_dir_path( __FILE__ ) );
define( 'YT_SIDEBAR_GENERATOR_PLUGIN', __FILE__ );
define( 'YT_SIDEBAR_GENERATOR_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );

	
class YT_Sidebar_Generator{

	private $option_key = '';
	private $sidebar_key = '';
	public $supported_posttypes = array();

	function __construct(){

		add_action( 'plugins_loaded', array( $this, 'text_domain') );
		add_action( 'after_setup_theme', array( $this, 'init') );
	}

	/**
	 * Init
	 *
	 * @params option_key
	 * @since 1.0.0
	 */
	public function init(){

		$this->option_key = apply_filters( 'yt_sidebar_generator_option_key', 'sbg_sidebars' );
		$this->sidebar_key = apply_filters( 'yt_sidebar_generator_sidebar_key', '_sidebar' );
		$this->supported_posttypes = apply_filters( 'yt_sidebar_generator_supported_posttypes', array('page') );
		$this->hooks();

	}

	/**
	 * Add theme menu
	 *
	 * @params option_key
	 * @since 1.0.0
	 */
	public function hooks(){


		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts'), 1 );

		add_action( 'admin_menu',array( $this, 'admin_menu' ) );

		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );

		add_action( 'wp_ajax_yt-sidebar-generator-action', array( $this, 'process_sidebar' ), 10 );

		global $pagenow;

		if ( is_admin() && in_array( $pagenow , array( 'post.php', 'post-new.php' ) ) ) {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 10 );
			add_action( 'save_post', array( $this, 'save_meta_box_data' ), 10 );
		}
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 1.0.0
	 */
	public function text_domain() {
		load_plugin_textdomain( 'wpthms', false, YT_SIDEBAR_GENERATOR_DIRNAME . '/langs/' ); 
	}

	/**
	 * Register defined sidebars
	 *
	 * @params option_key
	 * @since 1.0.0
	 */
	public function register_sidebars(){
		$sidebars = get_option( $this->option_key);

		if( empty( $sidebars ) )
			return;

		foreach ( ( array ) $sidebars as $sidebar_id => $sidebar_name ) {
			register_sidebar(
				array(
					'name' 				=> $sidebar_name,
					'id' 				=> $sidebar_id,
					'before_title' 		=> apply_filters( 'yt_sidebar_generator_sidebar_before_title', '<h3 class="widget-title">' ),
					'after_title'		=> apply_filters( 'yt_sidebar_generator_sidebar_after_title', '</h3>' ),
					'before_widget' 	=> apply_filters( 'yt_sidebar_generator_sidebar_before_widget', '<aside id="%1$s" class="widget %2$s">' ),
					'after_widget' 		=> apply_filters( 'yt_sidebar_generator_sidebar_after_widget', '</aside>' )
				)
			);	
		}
	}

	/**
	 * Add theme menu
	 *
	 * @since 1.0.0
	 */
	public function admin_menu(){
		$hook = add_theme_page( 
			__( 'Sidebar Generator', 'wpthms' ),
			__( 'Sidebars', 'wpthms' ),
			'manage_options',
			'sidebar-generator',
			array( $this, 'admin_page')
		);

		add_action( "load-$hook", array( $this, 'sidebar_help_tab' ) );

	}

	/**
	 * Add theme page content for sidebar generator
	 *
	 * @since 1.0.0
	 */
	public function admin_page(){

		$sidebars = get_option( $this->option_key);
		?>
		<div class="wrap">
			<h2><?php _e( 'Sidebar Generator', 'wpthms' );?></h2>

			<div class="yt-sidebar-generator-wrapper" style="max-width:600px;">
				<p><?php _e('The sidebar name/id are for your use only. It will not be visible to any of your visitors.','wpthms');?></p>
				<hr>
				<p><?php _e('-To add new sidebar, Enter sidebar name to the below field then click add sidebar.<br>-To remove sidebar, click &times; button then confirm.','wpthms');?></p>
				
				<form>
   					<?php wp_nonce_field('yt-sidebar-generator-ajax-action','yt_sidebar_generator_ajaxify_nonce'); ?>
					<p>
						<input name="sidebar_name" type="text" size="18" id="sidebar_name" value="">
						<button class="button yt-sidebar-generator-add-sidebar" data-type="add"><?php _e('+ Add sidebar', 'wpthms');?></button>
						<span class="spinner"></span>
					</p>
					<table class="widefat" id="yt-sidebar-generator-table">
						<tr>
							<th><?php _e('Name','wpthms');?></th>
							<th><?php _e('ID','wpthms');?></th>
							<th width="10%"><?php _e('Remove','wpthms');?></th>
						</tr>

						<?php 

						if( !$sidebars ):?>
						<tr class="no-sidebar-tr">
							<td colspan="3"><?php _e('No Sidebars defined','wpthms');?></td>	
						</tr>
						<?php else:

							foreach ( ( array ) $sidebars as $sidebar_id => $sidebar_name ) {

								?>
								<tr>
									<td><?php echo $sidebar_name;?></td>
									<td><?php echo $sidebar_id;?></td>
									<td><button class="button button-small yt-sidebar-generator-remove-sidebar" data-type="remove" data-id="<?php echo esc_attr( $sidebar_id );?>">&times;</button></td>
								</tr>
								<?php
							}
						endif;

						?>
					</table>
					<p class="yt-sidebar-generator-notice"></p>
					
					<table class="form-table" style="width: 100%;">
						<tr>
							<th style="width: 20%;">
								<label><?php echo esc_html__( 'Sidebars data' );?></label>
							</th>
							<td>
								<?php
									$sidebar_data = !is_serialized( $sidebars ) ? maybe_serialize( $sidebars ) : $sidebars; 
								?>
								<textarea name="sidebar_transfer" type="text" size="18" id="sidebar_transfer" rows="5" cols="30" style="width:100%;"><?php echo esc_textarea( base64_encode( $sidebar_data ) );?></textarea>
								<button class="button yt-sidebar-generator-import-data" data-type="import"><?php echo esc_html__( 'Import Data', 'wpthms' );?></button>
								<p class="description"><?php esc_html__( 'You can tranfer the saved sidebars data between different sites.', 'wpthms' ); ?></p>
								
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>

		<?php

	}
	/**
	 * Script action
	 *
	 * @since 1.0.0
	 */
	public function admin_scripts($hook){
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		/**
		 * Only enqueue script for specified setting page
		 */
		if ( strpos( $hook, 'sidebar-generator') !== false ) {
		    wp_enqueue_script( 'yt-sidebar-generator-scripts',  YT_SIDEBAR_GENERATOR_URI . "js/yt-sidebar-generator{$suffix}.js", array('jquery'), YT_SIDEBAR_GENERATOR_VER, true  );
			wp_localize_script( 'yt-sidebar-generator-scripts', 'yt_sidebarGeneratorVars',  array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'msgAddSidebarName' => __('Please enter Sidebar name!', 'wpthms'),
				'msgConfirmRemove' => __( "Are you sure you want to remove this sidebar ?\nThis action will remove any widgets you have assigned to this sidebar.\nProceed?", 'wpthms'),
				'msgConfirmImport' =>  __( "The existed sidebars won't be overwritten, only new sidebars (if available) will be added to the list.\nProceed?", 'wpthms')
			));
		}

	}

	/**
	 * Add/Remove sidebar in action
	 *
	 * @since 1.0.0
	 */
	public function process_sidebar(){

		if( empty( $_POST['nonce'] )) die('-1'); 

		$nonce = $_POST['nonce'];

		$action = 'yt-sidebar-generator-ajax-action';

		/**
		 * Check sercurity
		 */

		$adminurl = strtolower( admin_url() );
		$referer = strtolower( wp_get_referer() );
		$result = !empty( $nonce ) ? wp_verify_nonce( $nonce , $action) : false;
		if ( !$result && !(-1 == $action && strpos( $referer, $adminurl) === 0) ) {
			die( '-1' );
		}

		$value = !empty( $_POST['value'] ) ? $_POST['value'] : '';
		$type = !empty( $_POST['type'] ) ? $_POST['type'] : '';


		if( empty( $value ) && empty( $type ) )
			die('-1');

		if( !in_array( $type, array( 'add', 'remove', 'import') ) )
			die('-1');

		/**
		 * Get saved option
		 */
		$sidebars = get_option( $this->option_key);

		$return = array( 
			'success' => true,
		);

		if( 'import' == $type ){

			$data = !empty( $_POST['value'] ) ? maybe_unserialize( base64_decode( $_POST['value'] ) ) : '';

			if( !empty( $data ) ){
				$temp_sidebars = $sidebars;

				foreach ( (array) $data as $key => $value) {
					if( !isset( $sidebars[$key] ) ){
						$temp_sidebars[$key] = $value;		
					}
				}

				if( $temp_sidebars !== $sidebars ){
					update_option( $this->option_key, $temp_sidebars );
				}
			}

			$return['data'] = array(
				'message'	=> __('Data has been successfully imported! Refreshing the page...', 'wpthms'),
				'type'		=> $type,
			);

		}
		elseif( 'add' == $type ){
			$sidebar_name = preg_replace( '/[^a-zA-Z0-9 ]/', '', $value );
			$sidebar_name = preg_replace ("/ +/", " ", $sidebar_name); // convert all multispaces to space
			$sidebar_name = preg_replace ("/^ /", "", $sidebar_name);  // remove space from start
			$sidebar_name = preg_replace ("/ $/", "", $sidebar_name);  // and end
			$sidebar_id = preg_replace( '/[^a-zA-Z0-9-\-]/', '', strtolower( trim( str_replace( ' ', '-', $sidebar_name ) ) ) );
			
			$return['data'] = array(
				'message'	=> __('Sidebar added!', 'wpthms'),
				'type'		=> $type,
				'id'		=> $sidebar_id,
				'name'		=> $sidebar_name,
			);

			if( isset( $sidebars[$sidebar_id] ) ){
				$return['success'] = false;
				$return['data']['message'] = __('Sidebar already exists, please use a different name.', 'wpthms');
			}else{
				$sidebars[$sidebar_id] = $sidebar_name;
				update_option( $this->option_key, $sidebars );
			}
			
		}else{
			if( isset( $sidebars[$value] ) ){
				unset( $sidebars[$value] );
				$return['data'] = array(
					'message'	=> __('Sidebar removed!', 'wpthms'),
					'type'		=> $type,
				);

				update_option( $this->option_key, $sidebars );

				//Wipe all widget inside this sidebar
				/*$widgets = get_option('sidebars_widgets');
				
				if( isset(  $widgets[$value] ) )
					unset( $widgets[$value] );
			    update_option('sidebars_widgets', $widgets);*/

			}else{
				$return['success'] = false;
				$return['data'] = array(
					'message'	=> __('Sidebar doesn\'t exist!', 'wpthms'),
					'type'		=> $type,
				);
			}


		}

		wp_send_json($return);

	}

	/**
	 * Help Screen
	 *
	 * @since 1.0.2
	 */
	
	function sidebar_help_tab () {
	    $screen = get_current_screen();

	    $msg = "To use dynamic sidebar, make sure you've replaced <code>dynamic_sidebar()</code> with <code>yt_dynamic_sidebar()</code> in your template sidebar files.

For Example:

<pre>if( defined('YT_SIDEBAR_GENERATOR_VER') && function_exists( 'yt_dynamic_sidebar') )			
	yt_dynamic_sidebar('default');
else
	dynamic_sidebar( 'default' );</pre>";


	    // Add my_help_tab if current screen is My Admin Page
	    $screen->add_help_tab( array(
	        'id'	=> 'yt_sbg_help',
	        'title'	=> __('Installation'),
	        'content'	=> wpautop(  __( $msg, 'wpthms' ) ) ,
	    ) );
	}


	/**
	 * Adds a box to the main column on the Post and Page edit screens.
	 *
	 * @since 1.0.2
	 */
	function add_meta_box() {

		$screens = $this->supported_posttypes;

		if( empty( $screens  ) )
			return;

		foreach ( $screens as $screen ) {

			add_meta_box(
				'yt_sidebar_settings',
				__( 'Sidebar Settings', 'wpthms' ),
				array( $this, 'metabox_callback' ),
				$screen,
				'side'
			);
		}
	}

	/**
	 * Prints the box content.
	 *
	 * @since 1.0.2
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	function metabox_callback( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'yt_sbg_metabox', 'yt_sbg_metabox_nonce' );

		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */

		do_action( 'yt_sbg_before_sidebar_meta_box' );

		$sidebar_data = get_post_meta( $post->ID, $this->sidebar_key, true );

		// print_r( $GLOBALS['wp_registered_sidebars'] );
		if( !empty( $GLOBALS['wp_registered_sidebars'] ) ){
			echo '<label class="screen-reader-text">' . __('Sidebar Settings', 'wpthms' ) . '</label>';
			echo '<p><select id="yt_' . esc_attr( $this->sidebar_key ) . '" name="' . esc_attr( $this->sidebar_key ) . '">';
			foreach ( $GLOBALS['wp_registered_sidebars'] as $key => $value) {
				echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $value['id'] ), selected( $sidebar_data, $value['id'], false ), esc_html( $value['name'] ) );
			}
			echo sprintf( '<option value="none" %s>%s</option>', selected( $sidebar_data, $value['id'], false ), esc_html_x( 'None', 'sidebar value', 'wpthms' ) );
			echo '</select></p>';
			echo '<p>' . __( 'Choose sidebar your sidebar', 'wpthms' ) . '</p>';
		}else{
			echo '<p>' . __( 'No sidebar found!', 'wpthms' ) . '</p>';
		}

		do_action( 'yt_sbg_after_sidebar_meta_box' );
	}

	/**
	 * When the post is saved, saves our custom data.
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 * @since 1.0.2
	 */
	function save_meta_box_data( $post_id ) {

		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['yt_sbg_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['yt_sbg_metabox_nonce'], 'yt_sbg_metabox' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */
		
		// Make sure that it is set.
		if ( ! isset( $_POST[$this->sidebar_key] ) ) {
			return;
		}

		// Sanitize user input.
		$sidebar_data = sanitize_title( $_POST[$this->sidebar_key] );

		// Update the meta field in the database.
		update_post_meta( $post_id, $this->sidebar_key, $sidebar_data );

		do_action( 'yt_sbg_after_saved_sidebar_data', $post_id );
	}


	/**
	 * Get sidebar
	 *
	 * @since 1.0.2
	 */
	public function get_sidebar( $default = '', $key ){
		global $wp_query, $wp_registered_sidebars ;
		$post = $wp_query->get_queried_object();
		$meta_key = !empty( $key ) ? sanitize_key( $key ) : $this->sidebar_key;
		$selected_sidebar = !empty( $post->ID ) ? sanitize_title( get_post_meta($post->ID, $meta_key, true) ) : '';

		if( $selected_sidebar && isset( $wp_registered_sidebars[$selected_sidebar] ) )
			dynamic_sidebar( $selected_sidebar );
		else
			dynamic_sidebar( $default );
	}
	
}

$GLOBALS['yt_sidebar_generator'] = new YT_Sidebar_Generator();
/**
 * Alternative function for dynamic_sidebar
 *
 * @since 1.0.2
 */
if( !function_exists( 'yt_dynamic_sidebar') ){
	function yt_dynamic_sidebar( $default = '', $key = '' ){

		$GLOBALS['yt_sidebar_generator']->get_sidebar( $default, $key );
		
	}
}