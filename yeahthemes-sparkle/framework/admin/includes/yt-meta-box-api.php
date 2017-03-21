<?php
/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;

/**
 * wpthms  Metabox Generator
 *
 * @package     WordPress
 * @subpackage  framework/admin/includes
 * @since       1.0.0
 * @author      wpthms
 */

define( 'wpthms_META_BOX_VERSION', '1.0.0' );

/**
 * Defines the url to which is used to load local resources.
 * This may need to be filtered for local Window installations.
 * If resources do not load, please check the wiki for details.
 */
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
       //winblows
    define( 'wpthms_META_BOX_URI', trailingslashit( str_replace( DIRECTORY_SEPARATOR, '/', str_replace( str_replace( '/', DIRECTORY_SEPARATOR, WP_CONTENT_DIR ), WP_CONTENT_URL, dirname(__FILE__) ) ) ) );
    
} else {
	
    define( 'wpthms_META_BOX_URI', apply_filters( 'yt_meta_box_url', trailingslashit( str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) ) ) ) );
	
}

/**
 * Create meta boxes
 */
if ( ! class_exists( 'YT_Meta_Box' ) ) {
	
class YT_Meta_Box {
	
	public $_meta_box;

	function __construct( $meta_box ) {
		
		if ( !is_admin() ) return;

		$this->_meta_box = self::normalize( $meta_box );
		//print_r($this->_meta_box);
		global $pagenow;

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );

		add_filter( 'yt_metabox_show_on', array( $this, 'add_metabox_by_id' ), 10, 2 );
		add_filter( 'yt_metabox_show_on', array( $this, 'add_metabox_by_page_template' ), 10, 2 );
		
		/* Scripts and style */
		add_action( 'admin_enqueue_scripts', array( $this, 'metabox_scripts' ), 10 );
		add_action( 'admin_print_scripts', array( $this, 'print_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'print_styles' ) );
		
	}
	
	/**
	 * Add metaboxes
	 */
	public function add_metabox() {
		
		$this->_meta_box['context'] = empty($this->_meta_box['context']) ? 'normal' : $this->_meta_box['context'];
		$this->_meta_box['priority'] = empty($this->_meta_box['priority']) ? 'high' : $this->_meta_box['priority'];
		$this->_meta_box['show_on'] = empty( $this->_meta_box['show_on'] ) ? array('key' => false, 'value' => false) : $this->_meta_box['show_on'];

		foreach ( $this->_meta_box['pages'] as $page ) {
			if( apply_filters( 'yt_metabox_show_on', true, $this->_meta_box ) )
				add_meta_box( $this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show_metabox'), $page, $this->_meta_box['context'], $this->_meta_box['priority']) ;
		
		}
	}

	/**
	 * Show On Filters
	 * Use the 'yt_metabox_show_on' filter to further refine the conditions under which a metabox is displayed.
	 * Below you can limit it by ID and page template
	 */

	/**
	 * Add metabox for specified Post/Page id
	 */
	function add_metabox_by_id( $display, $meta_box ) {
		
		if ( 'id' !== $meta_box['show_on']['key'] )
			return $display;

		// If we're showing it based on ID, get the current ID
		if( isset( $_GET['post'] ) ) {
			
			$post_id = $_GET['post'];
			
		}
		elseif( isset( $_POST['post_ID'] ) ) { 
		
			$post_id = $_POST['post_ID'];
			
		}
		
		if( !isset( $post_id ) )
			return false;

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// If current page id is in the included array, display the metabox

		if ( in_array( $post_id, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
			
	}

	/**
	 * Add metabox for specified Page Template
	 */
	function add_metabox_by_page_template( $display, $meta_box ) {
		
		if( 'page-template' !== $meta_box['show_on']['key'] )
			return $display;

		// Get the current ID
		if( isset( $_GET['post'] ) ) $post_id = $_GET['post'];
		elseif( isset( $_POST['post_ID'] ) ) $post_id = $_POST['post_ID'];
		if( !( isset( $post_id ) || is_page() ) ) return false;

		// Get current template
		$current_template = get_post_meta( $post_id, '_wp_page_template', true );

		// If value isn't an array, turn it into one
		$meta_box['show_on']['value'] = !is_array( $meta_box['show_on']['value'] ) ? array( $meta_box['show_on']['value'] ) : $meta_box['show_on']['value'];

		// See if there's a match
		if( in_array( $current_template, $meta_box['show_on']['value'] ) )
			return true;
		else
			return false;
			
	}

	/**
	 * Show metabox fields
	 */
	function show_metabox() {

		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="wp_meta_box_nonce" value="' . esc_attr( wp_create_nonce( basename(__FILE__) ) ) . '" />';
		
		do_action( 'yt_metabox_before_metabox_wrapper', $post, $this->_meta_box );
		
		echo '<div class="form-table yt-metabox yt-core-ui">';
		
		
		$_tab_array = array();
		$_tab_break_counter = 0;
		
		foreach ( $this->_meta_box['fields'] as $field ) {
			
			if( 'tab' == $field['type'] ){
				
				$_tab_array[$_tab_break_counter][] = $field['name'];
				
			}elseif( 'tab_break' == $field['type'] ){
				
				$_tab_break_counter++;
				
			}
			
		
		}
		
		//print_r($_tab_array);
		
		/* Folding array for toggle checkbox */
		$_folding_arr = array();
		$_tab_count = 0;
		$_tab_break_loop_counter = 0;
		
		foreach ( $this->_meta_box['fields'] as $field ) {
			
			if( 'tab' === $field['type'] ){
				$_tab_count++;
			}
			
			// Set up blank or default values for empty ones
			if ( !isset( $field['name'] ) ) $field['name'] = '';
			if ( !isset( $field['desc'] ) ) $field['desc'] = '';
			$field['std'] = apply_filters( 'yt_meta_std_filter', ( isset( $field['std'] ) ? $field['std'] : '' ), $field );
			if ( 'file' == $field['type'] && !isset( $field['allow'] ) ) $field['allow'] = array( 'url', 'attachment' );
			if ( 'file' == $field['type'] && !isset( $field['save_id'] ) )  $field['save_id']  = false;


			// if( in_array( $field['type'], array( 'multicheck' ) ) )
			// 	$field['multiple'] = true;
			
			$post_custom_keys = get_post_custom_keys( $post->ID  );
			
			$meta = isset( $field['id'] ) && $field['id'] /*&& in_array( $field['id'], $post_custom_keys)*/ ? get_post_meta( $post->ID, $field['id'], !$field['multiple'] ) : null;
			//var_dump( get_post_custom_keys( $post->ID  ));

			// print_r( $meta );
			
			$meta = null !== $post_custom_keys ? $meta : ( !empty( $field['std'] ) ? $field['std'] : null );



			//die();
			
			$section_class = 'yt-' . $field['type'] . '-field-row yt-section yt-section-' . str_replace( '_', '-', $field['type'] );
			
			if ( in_array( $field['type'], array( 'title', 'wysiwyg') ) || !$this->_meta_box['show_names'] ) {
				$section_class .= ' yt-widefat-section';
			}
			
			if( isset( $field['class'] ) && $field['class'] ){
				$section_class .= ' ' . $field['class'];
			}
			
			if( isset( $field['settings']['hide_label'] ) && $field['settings']['hide_label'] ){
				$section_class .= ' yt-section-hide-label';
			}
			
			/* if checkbox is 1, push it to array */
			if( 'checkbox' == $field['type'] && $meta ){
				$_folding_arr[] = $field['id'];
			}
			
			//hide items in checkbox group
			
			if ( array_key_exists( 'fold', isset( $field['settings'] ) ? $field['settings'] : array() ) && !isset( $field['settings']['fold_value'] ) ) {
				
				if ( !empty( $field['settings']['fold'] ) && in_array( $field['settings']['fold'], $_folding_arr ) ) {
					
					$section_class .= ' f_' . $field['settings']['fold'] . ' ';
					
				} else {
					
					$section_class .= ' f_' . $field['settings']['fold'] . ' yt-temp-hide ';
					
				}
				
			}elseif ( array_key_exists( 'fold', 
				isset( $field['settings'] ) ? $field['settings'] : array() ) 
				&& isset( $field['settings']['fold_value'] ) ) {
					
					$section_class .= ' f_' . $field['settings']['fold'] . ' yt-temp-hide ';
					
			}
			
			
			$attr_datafold = !empty( $field['settings']['fold_value'] ) ? $field['settings']['fold_value'] : '';
			
			$attr_datafold = $attr_datafold ? ' data-fold="' . esc_attr( $attr_datafold ) . '"' : '';
			
			if( !in_array( $field['type'], array( 'tab', 'tab_break' ) ) ){
				
				echo '<div class="' . esc_attr( $section_class ) .  '"' . $attr_datafold . '>';
					
				if( $this->_meta_box['show_names'] == true ) {
					
					echo '<div class="yt-field-label"><label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['name'] ) . '</label></div>';
					echo '<div class="yt-controls ">';
				
				}else{
					echo '<div class="yt-field-label yt-hidden"><label for="' . esc_attr( $field['id'] ) . '">' . esc_html( $field['name'] ) . '</label></div>';
					echo '<div class="yt-controls ">';
				
				}
				
				echo empty( $field['settings']['before'] ) ? '' : $field['settings']['before'];
			
			}
			/* build the arguments array */
			$_args = array(
				'name'        			=> $field['name'],
				'type'        			=> $field['type'],
				'id'          			=> isset( $field['id'] ) ? $field['id'] : '',
				'value'       			=> $meta,
				'desc'        			=> isset( $field['desc'] ) ? $field['desc'] : '',
				'std'         			=> isset( $field['std'] ) ? $field['std'] : '',
				'class'       			=> isset( $field['class'] ) ? $field['class'] : '',
				'options'    			=> isset( $field['options'] ) && !empty( $field['options'] ) ? $field['options'] : array(),
				'settings'				=> isset( $field['settings'] ) ? $field['settings'] : array(),
				
			);			switch ( $field['type'] ) {
				
				case 'tab':
					
					if( $_tab_count == 1 ){
						$tab_id = 'yt-group-tab-' . yt_clean_string( $this->_meta_box['id'], '_', '-' ) . yt_clean_string( join( '-', $_tab_array[$_tab_break_loop_counter] ) , '-', '' );
						echo '<!--yt-group-tab-wrapper--><div class="yt-group-tab-wrapper" id="' . esc_attr( $tab_id ) . '"'. ( isset( $field['settings']['cookie']) && 0 == $field['settings']['cookie'] ? ' data-cookie="0"' : '') .'>';
							
							//print_r( $nested_tab_array );
							//$tab_key_tab = $sub_parent_class ? $sub_parent_class . $parent_class : self::clean_string( $parent_class, '-', ' ' );
							//$tab_key_tab = $tab_key_tab . '_' . $_tab_break_loop_counter;
							
							//print_r($_tab_array[$_tab_break_loop_counter]);
							
							if( !empty( $_tab_array[$_tab_break_loop_counter] ) ){
							
								echo '<ul class="yt-group-tab-header" data-ul-tab="' . esc_attr( $_tab_break_loop_counter ) . '">';
									
								foreach( $_tab_array[$_tab_break_loop_counter] as $tab_k => $tab_v ){
									
									echo '<li data-index="' . esc_attr( $tab_k + 1 ) . '"><span>' . esc_html( $tab_v ) . '</span></li>';
								
								}
								
								echo '</ul>';
							}
						
					}
					
					if( $_tab_count >= 2 ){
						
						echo '</div><!--/tab-->' . "\n";	
						
					}
				
					echo '<div class="yt-group-tab" data-tab="' . esc_attr( $_tab_count ) . '">';
					
				break;
				
				case 'tab_break':
					
					if( $_tab_count > 0 ){
					
						echo '</div><!--/tab_by_tab_break--></div><!--/yt-group-tab-wrapper-->' . "\n";
						
						$_tab_count = 0;
					}
					
					$_tab_break_loop_counter++;
					
				break;
				
				case 'wysiwyg':
					wp_editor( $meta ? $meta : $field['std'], $field['id'], isset( $field['options'] ) ? $field['options'] : array() );
					
				break;
				
				default:
				
					if( function_exists( 'yt_display_by_type' ) ){
						if( !is_array( yt_display_by_type( $_args ) )){
							echo yt_display_by_type( $_args );
						}
						else{
							echo 'Undefined field type';	
						}
					}
					
				//default:
					//do_action('cmb_render_' . $field['type'] , $field, $meta);
					
			}
			
			if( !in_array( $field['type'], array( 'tab', 'tab_break' ) ) ){
			
				echo !empty( $field['desc'] ) ? '<p class="yt-metabox-description yt-clear">' . $field['desc'] . '</p>' : '';
				echo empty( $field['settings']['after'] ) ? '' : $field['settings']['after'];
				
				echo '</div>','</div>';
			
			}
			
		}
		
		/*End the last tab automatically if no tab_break found */
		if( $_tab_count > 0 ){
						
			echo '</div><!--/tab_automatically--></div><!--/yt-group-tab-wrapper-->' . "\n";
			
			/*Reset $_tab_count*/
			$_tab_count = 0;
		}
		
		echo '</div>';
		
		do_action( 'yt_metabox_after_metabox_wrapper', $post, $this->_meta_box );
	}
	
	/**
	 * Save metabox data
	 */
	function save_metabox( $post_id )  {

		/* verify nonce */
		if ( ! isset( $_POST['wp_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['wp_meta_box_nonce'], basename(__FILE__) ) ) {
			
			return $post_id;
			
		}

		/* check autosave */
		if ( defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			
			return $post_id;
			
		}

		/* check permissions */
		if ( 'page' == $_POST['post_type'] ) {
			
			if ( !current_user_can( 'edit_page', $post_id ) ) {
				
				return $post_id;
				
			}
			
		} elseif ( !current_user_can( 'edit_post', $post_id ) ) {
			
			return $post_id;
			
		}

		foreach ( $this->_meta_box['fields'] as $field ) {
			
			
			$name = !empty($field['id']) ? $field['id'] : '';
			
			if ( ! isset( $field['multiple'] ) )
				$field['multiple'] = ( 'multicheck' == $field['type'] ) ? true : false;
				
			$old = get_post_meta( $post_id, $name, !$field['multiple'] );
			$new = isset( $_POST[$name] ) ? $_POST[$name] : ( $field['multiple'] ? array() : '' );

			/* Validate data */

			// print_r( $old ); die();
			
			if( function_exists( 'yt_validate_field_data' ) ){
				$new = yt_validate_field_data( $new, $field, 'meta', $post_id);
			}
			
			
			if ( $field['multiple'] ) {
				
				delete_post_meta( $post_id, $name );
				
				if ( !empty( $new ) ) {
					
					foreach ( $new as $add_new ) {
						
						add_post_meta( $post_id, $name, $add_new, false );
						
					}
					
				}
			} elseif ( '' !== $new && $new != $old  ) {
				
				update_post_meta( $post_id, $name, $new );
				
			} elseif ( '' == $new || array() == $new ) {
				
				delete_post_meta( $post_id, $name );
				
			}
			
			
		}
	}
	
	/**
	 * Metabox scripts
	 */
	function metabox_scripts( $hook ) {

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		
		if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'page-new.php' || $hook == 'page.php' ) {
			
			do_action( 'yt_before_metabox_styles' );
			
			wp_enqueue_style('yt-admin-style', wpthms_FRAMEWORK_URI . 'admin/assets/css/admin-style.css');
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_style('yt-jqueryui', 		wpthms_FRAMEWORK_URI . "admin/assets/css/jqueryui.custom.css");
			
			do_action( 'yt_after_metabox_styles' );
			
			do_action( 'yt_before_metabox_scripts' );
			
			wp_enqueue_script('yt-plugins', 	wpthms_FRAMEWORK_URI . "admin/assets/js/wpthms.plugins{$suffix}.js", array( 'jquery' ), '1.0', true);
			wp_enqueue_script('yt-scripts', 	wpthms_FRAMEWORK_URI . "admin/assets/js/wpthms{$suffix}.js", array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-autocomplete', 'jquery-ui-slider', 'jquery-ui-datepicker', 'wp-color-picker', 'yt-plugins' ), '1.0', true);

			/*Prevent duplicating translation string*/
			static $params = null;

			if( null == $params){
				$params = apply_filters( 'yt_metaboxes_vars', array( ) );
				wp_localize_script('yt-scripts', 'yt_optionsVars', $params );
			}
			
			do_action( 'yt_after_metabox_scripts' );
			
		}
	}

	function print_scripts(){
		
	}
	
	function print_styles(){
		
	}
	
	
	static function normalize( $meta_box ){
		// Set default values for meta box
		$meta_box = wp_parse_args( $meta_box, array(
			'id'			=> 'yt_default_metabox_id',
			'title'			=> 'wpthms Metabox',
			'pages'			=> array( 'page', ), // Post type
			'context'		=> 'normal',
			'priority'		=> 'high',
			'show_names'	=> true,
		) );
		
		// Set default values for fields
		$meta_box['fields'] = self::normalize_fields( $meta_box['fields'] );

		return $meta_box;
	}
	/**
	 * Normalize parameters for meta box
	 *
	 */
	static function normalize_fields( $fields ){
		
		foreach ( $fields as &$field ){
			
			$field = wp_parse_args( $field, array(
				'id'			=> '',
				'multiple'		=> false,
				'std'			=> '',
				'desc'			=> '',
				'settings'		=> array(),
				'before'		=> '',
				'after'			=> '',
			) );
		}
		
		return $fields;
		
	}
	
}
 
}
/**
 * Register meta boxes via filter
 *
 * @return void
 */

add_action( 'admin_init', 'yt_meta_boxes_init' );

function yt_meta_boxes_init() {
	
	if ( ! class_exists( 'YT_Meta_Box' ) ) return;
	
	$yt_meta_boxes = apply_filters ( 'yt_meta_boxes' , array() );
	
	foreach ( $yt_meta_boxes as $meta_box ) {
		
		$meta_box['id'] = new YT_Meta_Box( $meta_box );
		
	}
	
}
