<?php
class YT_Shortcode_Generator_Field_Types {
	/*
	 * Colorpicker
	 *
	 * @param $id
	 * @param $val
	 * @param $std
	 */
	static function colorpicker( $id, $std, $element = 'parent' ){
		
		$output = '<input data-std="' . esc_attr( $std ) . '" data-id="' . esc_attr( $id ) . '" data-element="' . $element . '" class="yt-shortcode-colorpicker yt-shortcode-input" name="' . esc_attr( $id ) . '" type="text" value="' . esc_attr( $std ) . '"/>';
		
		return $output;
			
	}/**
	 * Native media library uploader
	 *
	 * @uses get_option()
	 *
	 * @access public
	 * @since 1.0.0
	 *
	 * @param $id
	 * @param $val
	 *
	 * @return string
	 */
	static function media( $id, $val, $title = '', $media_by = 'url', $element = 'parent'  ){
		
		$output = '';
		$media_url = '';
		$media_id = '';
		
		$media_url = $val ? $val : '';
			
		$hide_remove_btn = $media_url ? '' : 'yt-hidden';
		
		$output .= '
			<input data-element="' . $element . '" type="text" class="upload yt-shortcode-media-input yt-shortcode-input yt-shortcode-input-text" name="' . esc_attr( $id ) . '" data-id="'. esc_attr( $id ) .'" value="' . ( is_numeric( $media_url ) ? esc_attr( $media_url ) : esc_url( $media_url ) ) . '" />';	
		$output .= '<div class="yt-shortcode-media-action">
			<span class="button yt-shortcode-media-upload-button yt-button hide-if-no-js" data-by="' . ( $media_by && in_array( $media_by, array( 'url', 'id' ) ) ? $media_by : 'url'  ) . '" data-title="' . ( $title ? esc_attr( $title ) : __('Add Media', YT_SHORTCODE_DOMAIN)) . '">&uarr; ' . __('Upload',YT_SHORTCODE_DOMAIN) . '</span>
			<span class="button yt-shortcode-media-remove-button yt-button hide-if-no-js '. esc_attr( $hide_remove_btn ) . '" title="' . esc_attr( $id ) . '">' . __('Remove',YT_SHORTCODE_DOMAIN) . '</span>
		</div>' . "\n";
		$output .= '<div class="yt-shortcode-media-screenshot">';
		
		
		/*parse_str(parse_url($media_url, PHP_URL_QUERY), $args);
		print_r($args);*/
		
		$media_type = pathinfo( self::_clean_url( $media_url ), PATHINFO_EXTENSION );
		
		if( !empty( $media_url ) && in_array( $media_type, array( 'jpg', 'jpeg', 'png', 'gif', 'svg', 'ico' ) ) ){	
		
	    	$output .= '<a class="yt-shortcode-media-uploaded-image" href="' . esc_attr( $media_url ) . '">
				<img src="' . esc_url( $media_url ) . '" alt="" />
			</a>';
			
		}
			
		$output .= '</div>'; 
	
		return $output;
		
	}
	/*
	 * Select for associative array
	 *
	 * @param $id
	 * @param $val
	 * @param $options
	 * @param $class
	 * @param $attr
	 */
	static function select( $id, $val, $options = array(), $element = 'parent', $multiple = true, $class = '', $attr = ''){
		
		$output = '';
		$output .= '<div class="yt-shortcode-select-wrapper' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"' . ( $attr ? ' ' . $attr : '' ) . '>
		
			<select data-id="' . esc_attr( $id ) . '" data-element="' . esc_attr( $element ) . '" class="yt-shortcode-select yt-shortcode-input"' . ($multiple ? ' multiple="multiple"' : '') . '>';
				
			
				
			/*If is Associative Array*/
				
			foreach ($options as $k => $v) {
					
				$output .= '<option value="' . esc_attr( $k ) . '" ' . selected( $val , $k, false ) . ' >' . esc_attr( $v ) . '</option>';	
			}
				
			
		$output .= '</select>
		
		</div>';
		
		return $output;
	}

	/*
	 * Select for indexed array
	 *
	 * @param $id
	 * @param $val
	 * @param $options
	 * @param $class
	 * @param $attr
	 */
	static function select_alt( $id, $val, $options = array(), $element = 'parent', $multiple = true, $class = '', $attr = ''){
		
		$output = '';
		$output .= '<div class="yt-shortcode-select-wrapper' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '"' . ( $attr ? ' ' . $attr : '' ) . '>
		
			<select data-id="' . esc_attr( $id ) . '" data-element="' . esc_attr( $element ) . '" class="yt-shortcode-select yt-shortcode-input"' . ($multiple ? ' multiple="multiple"' : '') . '>';
				
			
				
			/*If is Associative Array*/
			
			foreach ($options as $option) {
					
				$output .= '<option value="' . esc_attr( $option ) . '" ' . selected( $val , $option, false ) . ' >' . esc_attr( $option ) . '</option>';	
			}
				
			
		$output .= '</select>
		
		</div>';
		
		return $output;
	}
	/*
	 * Text field
	 *
	 * @param $id
	 * @param $val
	 */
	static function text( $id, $val, $element = 'parent', $class = '' ){
		
		return '<input data-id="'. esc_attr( $id ) .'" data-element="' . $element . '" class="yt-shortcode-input-text yt-shortcode-input yt-shortcode-input-text' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '" type="text" value="'. esc_html( $val ) .'" />';
		
	}
	/*
	 * Number field
	 *
	 * @param $id
	 * @param $val
	 */
	static function number( $id, $val, $element = 'parent', $class = '' ){
		
		return '<input data-id="'. esc_attr( $id ) .'" data-element="' . $element . '" class="yt-shortcode-input-text yt-shortcode-input yt-shortcode-input-text' . ( $class ? ' ' . esc_attr( $class ) : '' ) . '" type="number" value="'. esc_html( $val ) .'" style="width:80px;"/>';
		
	}
	/*
	 * Textarea
	 *
	 * @param $id
	 * @param $val
	 * @param $rows
	 */
	static function textarea( $id, $val, $element = 'parent', $rows = '8'){
		
		return '<textarea data-id="' . esc_attr( $id ) . '" data-element="' . $element . '" class="yt-shortcode-textarea yt-shortcode-input" rows="' . esc_attr( $rows ) . '">' . esc_textarea( $val ) . '</textarea>';
	
	}
	
	static function tag_search( $_args ){
		$value = array();
		$temp = array();
		$args_val = array();
		$tax = ( !empty( $_args['settings']['tax'] ) ? esc_attr( $_args['settings']['tax'] ) : 'post_tag' );

		if( !empty(  $_args['value']) ){
			if( !is_array( $_args['value'] ) ){
				$args_val = explode( ',', $_args['value'] );
			}else{
				$args_val = $_args['value'];
			}
				
			foreach ( ( array ) $args_val as $key) {
				$obj = get_term_by('id', $key , $tax );
				if( $obj )
					$temp[$obj->term_id] = $obj->name;
			}
			$value = $temp;
			
		}

		$value_html = '';
		if( !empty( $value )){
			foreach ($value as $k => $v) {
				$value_html .= '<span data-id="' . esc_attr( $k ) .'"><a class="ntdelbutton">x</a>&nbsp;' . esc_html( $v ) . '</span>';
			}
		}

		$output = '<input class="yt-input yt-ajax-tag-search widefat' . ( !empty( $_args['class'] ) ? ' ' . esc_attr( $_args['class'] ) : '' ) . '" data-id="'. esc_attr( $_args['id'] ) .'" data-tax="' . esc_attr( $tax ) . '" type="text" ' . ( !empty( $_args['settings']['attr'] ) ? $_args['settings']['attr'] : '' ) . ' />
		<!--<input type="button" class="button" value="'. esc_attr_x( 'Add', 'Add tag button', YT_SHORTCODE_DOMAIN ) .'">-->
		<input type="hidden" class="yt-hidden yt-shortcode-input-text yt-shortcode-input yt-shortcode-input-text" data-id="' . esc_attr( $_args['id'] ) . '" value="'.esc_attr( join(',', $args_val ) ).'">
		<p class="yt-tag-list tagchecklist" style="margin-bottom:0;">' . $value_html . '</p>';

		return $output;
	}
	/*
	 * Textarea
	 *
	 * @param $id
	 * @param $val
	 * @param $rows
	 */
	static function repeatable( $args = array() ){
		
		if( !is_array( $args ) || empty( $args ) )
			return;
			
		if( empty( $args['options'] ) )
			return;
		
		
	$output = '<div class="yt-shortcode-child-field-type-repeatable-sortable">';
		
		$output .= '<div class="yt-shortcode-child-field-type-repeatable-row yt-clear">';
		
		foreach( ( array ) $args['options'] as $k => $v ){

			$v = wp_parse_args( $v, array(
				'name' 		=> '',
				'desc' 		=> '',
				'std' 		=> '',
				'type' 		=> '',
				'settings'	=> array(),
				'options'	=> array()
			) );
			
			$output .= '<div class="yt-clear yt-shortcode-child-field-type-row yt-shortcode-child-field-type-' . $v['type'] . '">';
			
			$output .= !empty( $v['name'] ) ? '<h3>' . $v['name'] . '</h3>' : '';
			
			switch( $v['type'] ){
				
				case 'text':
					
					$output .= self::text( $k, $v['std'], 'children' );
				
				break;
				
				case 'number':
					
					$output .= self::number( $k, $v['std'], 'children' );
				
				break;
				
				case 'textarea':
					
					$output .= self::textarea( $k, $v['std'], 'children' );
				
				break;
				
				case 'select':
					
					$output .= self::select( $k, $v['std'], $v['options'], 'children', ( !empty( $v['settings']['multiple'] ) ? true : false ) );
				
				break;
				
				case 'select_alt':
					
					$output .= self::select_alt( $k, $v['std'], $v['options'], 'children', ( !empty( $v['settings']['multiple'] ) ? true : false ) );
				
				break;
				
				case 'media':
					
					$output .= self::media( $k, $v['std'], $v['name'], ( !empty( $v['settings']['media_by'] )? $v['settings']['media_by'] : 'url' ), 'children' );
				
				break;
				
				default:
				
					do_action( 'yt_shortcode_repeatable_field_type', $k, $v ); 
				
			}
			
			$output .= '<div class="yt-shortcode-field-type-desc">';
			
				$output .= !empty( $v['desc'] ) ? $v['desc'] : '';
			
			$output .= '</div>';
			
			$output .= '</div>';
			
		}
		
		
		$output .= '<span data-action="remove" class="button button-small">X</span></div>';
		
	$output .= '</div>';
	
		$output .= !empty( $args['settings']['syntax'] ) ? '<div class="hidden yt-hidden yt-shortcode-child-field-type-repeatable-syntax">' . $args['settings']['syntax'] . '</div>' : '';
		$output .= '<textarea data-id="' . esc_attr( $args['id'] ) . '" data-element="parent" class="yt-shortcode-input-textarea yt-shortcode-input hidden yt-hidden">' . $args['std'] . '</textarea>';
		$output .= '<span href="#" class="button button-large yt-shortcode-button-add-more-repeatable-field">' . __( 'Add more', YT_SHORTCODE_DOMAIN ) . ( !empty( $args['settings']['add_more'] ) ? ' ' . $args['settings']['add_more'] : '') . '</span>';
		
		
		return $output;
	
	}
	/**
	 * Free HTML
	 *
	 */
	static function html( $desc ){

		return $desc;
	}

	/**
	 * Helpers
	 *
	 * Clean URL
	 */
	static function _clean_url( $url ) {
		// If there is no url, return.
		if ( '' == $url )
			return;
			
		$url = preg_replace('/\?.*/', '', $url);
		
		return $url;
	  
	}
	/**
	 * Check if is associative array
	 */
	static function _is_assoc( $array ) {
		
		return (bool)count(array_filter(array_keys($array), 'is_string'));
	  
	}
	
	
}