<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;


/**
 * Create custom menu nav item fields
 *
 * @package   wpthms
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @since     1.0
 */
class YT_Nav_Menu_Custom_Fields{
	var $_options = array();
	
	var $_menu_fields = array();
	
	var $_nav_class = '';
	
	function __construct( $options, $nav_class ){
		
		if( !is_array( $options ) && empty( $options ) && !class_exists( $nav_class ) )
			return;
			
		$this->_options = array_map( array( $this, 'normalize' ) , $options );
		$this->_menu_fields = apply_filters( 'yt_admin_nav_menu_field_types', array( 'text', 'textarea', 'select', 'checkbox' ) );
		$this->_nav_class = $nav_class;
		//print_r($this->_options);
		/*Start the awesomeness*/
		$this->init();
	}
	
	public function init(){
		
		add_action( 'wp_update_nav_menu_item', array( $this, 'admin_custom_nav_update' ),10, 3);	
		
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'admin_setup_custom_nav_menu_item' ) );
		
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'admin_nav_menu_walker_edit' ),10,2 );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		
		add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
		
		add_action( 'yt_admin_nav_menu_fields', array( $this, 'admin_nav_custom_field_generator' ) ); 
	}
	/**
	 * Helper: Fields normalizer
	 */
	static function normalize( $field ){
		
		$default = array(
			'id' => '',
			'name' 		=> '',
			'desc' 		=> '',
			'std' 		=> '',
			'type' 		=> '',
			'options' 	=> array(),
			'size'	=> 'wide'
		);

		$field = wp_parse_args( $field, $default );

		return $field;
		
	}
	/**
	 * Helper: Fields normalizer
	 */
	public function admin_scripts(){
		
	}
	
	public function admin_print_scripts(){
		
	}
	
	/**
	 * Saves new field to postmeta for navigation
	 */
	public function admin_custom_nav_update( $menu_id, $menu_item_db_id, $args ) {
		
		foreach( ( array ) $this->_options as $option ){
			if ( !$option['id'] && !in_array( $option['type'], $this->_menu_fields ) ) 
				return;
			
			$menu_item_id = 'menu-item-' . $option['id']; 
			$meta_key = '_' . str_replace( '-', '_' , $menu_item_id );
			
			//print_r($_REQUEST[$menu_item_id]);
			//exit();
			if ( isset( $_REQUEST[$menu_item_id] ) && is_array( $_REQUEST[$menu_item_id] ) ) {
				
				$meta_value = !empty( $_REQUEST[$menu_item_id][$menu_item_db_id] ) ? $_REQUEST[$menu_item_id][$menu_item_db_id] : '';
				
				update_post_meta( $menu_item_db_id, $meta_key, $meta_value );
				
			}
		
		}
	}
	/**
	 * Adds value of new field to $item object that will be passed to YT_Walker_Nav_Menu_Edit
	 */
	public function admin_setup_custom_nav_menu_item( $menu_item ) {
		foreach( ( array ) $this->_options as $option ){
			if ( $option['id'] && in_array( $option['type'], $this->_menu_fields ) ){
				
				$menu_item_id = 'menu-item-' . $option['id'];
				$meta_key = '_' . str_replace( '-', '_' , $menu_item_id );
				
				$item_key = str_replace( '-', '_' , $option['id'] );
				
				$menu_item->$item_key = get_post_meta( $menu_item->ID, $meta_key, true );
			}
		}
		return $menu_item;
	}
	/**
	 * Filter the Walker class used when adding nav menu items.
	 *
	 * @param string $class   The walker class to use. Default 'Walker_Nav_Menu_Edit'.
	 * @param int    $menu_id The menu id, derived from $_POST['menu'].
	 */
	public function admin_nav_menu_walker_edit( $walker, $menu_id) {
		
		if( $this->_nav_class && class_exists( $this->_nav_class ) )
			return $this->_nav_class;
		
		return $walker;
	}
	
	public function admin_nav_custom_field_generator( $item ){
		
		
		if( !is_array( $this->_options ) && empty( $this->_options ) && !class_exists( $this->_nav_class ) )
			return;
		
		foreach( ( array ) $this->_options as $option ){
			
			if( in_array( $option['type'], $this->_menu_fields ) ){
				
				$id = $option['id'];
				$item_key = str_replace( '-', '_' , $id );
				$std = $option['std'];
				$options = $option['options'];
				$size = !empty( $option['size'] ) && 'thin' == $option['size'] ? ' description-' . $option['size'] : ' description-wide';
				
				$output .= '<p class="field-' . esc_attr( $option['id'] ) . ' description' . esc_attr( $size ) . '">';
					$output .= '<label for="edit-menu-item-' . esc_attr( $id ) . '-' . esc_attr( $item->ID ) . '">';
					$output .= !empty( $option['name'] ) ? $option['name'] . '<br />' : '';
					
					$id_attr = 'edit-menu-item-' . $id . '-' . esc_attr( $item->ID );
					$name_attr = 'menu-item-' . $id . '[' . $item->ID . ']';
					$val_attr = isset( $item->$item_key ) ? $item->$item_key : $std;
					
					switch( $option['type'] ){
						/**
						 * Text
						 */
						case 'text':
							$output .= '<input type="text" id="' . esc_attr( $id_attr ) . '" class="widefat code edit-menu-item-' . esc_attr( $id ) . '" name="' . esc_attr( $name_attr ) .'" value="' . esc_attr( $val_attr ) . '" />';
						break;
						/**
						 * Textarea
						 */
						case 'textarea':
							$output .= '<textarea id="' . esc_attr( $id_attr ) . '" class="widefat edit-menu-item-' . esc_attr( $id ) . '" rows="3" cols="20" name="' . esc_attr( $name_attr ) .'">' . esc_html( $val_attr ) . '</textarea>';
						break;
						/**
						 * Select
						 */
						case 'select':

							$output .= '<select id="' . esc_attr( $id_attr ) . '" class="widefat edit-menu-item-' . esc_attr( $id ) . '" name="' . esc_attr( $name_attr ) .'">';
							
							if( !in_array( $val_attr, array_keys( $options ) ) ){
								$val_attr = $std;
							}
							foreach( $options as $k => $v) {
								$output .= '<option value="' . esc_attr( $k ) . '"' . selected( $k, $val_attr, false )  . '>' . esc_html( $v ) . '</option>';
							}
							
							$output .= '</select>';
						break;
						/**
						 * Checkbox
						 */
						case 'checkbox':
							$output .= '<input type="checkbox" id="' . esc_attr( $id_attr ) . '" value="1" name="' . esc_attr( $name_attr ) .'" ' . checked( $val_attr, 1, false ) . ' />';
							$output .= !empty( $option['desc'] ) ? $option['desc'] : '';
						break;
						
						default:
							$output .= '';
					}
					
					/*Description*/
					$output .= !empty( $option['desc'] ) && $option['type'] !== 'checkbox' ? '<span class="description">' . esc_html( $option['desc'] ) . '</span>' : '';
					$output .= '</label>';
				$output .= '</p>';
				
			}
		}
		printf( '<div class="yt-custom-menu-fields-wrapper">%s</div>', $output );
				
	}
}