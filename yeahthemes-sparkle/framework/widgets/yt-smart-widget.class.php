<?php  
/**
 * Smartly Dynamic Widget
 * @since 1.0
 */
class YT_Smart_Widget extends WP_Widget {

	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		echo !empty( $before_widget ) ? $before_widget : '';
			$this->yt_display_widgets( $args, $instance );
		echo !empty( $after_widget ) ? $after_widget : '';
	}
	/**
	 * Widget Settings
	 */
	function form( $instance ) {
		global $wp_registered_widgets;
		$instance = wp_parse_args( $instance, array( 
			'widgets'    => ''
		) );
		?>
		<input type="hidden" class="widefat" name="<?php echo esc_attr( $this->get_field_name('widgets') ) ?>" id="<?php echo esc_attr( $this->get_field_id('widgets') ); ?>" value="<?php echo esc_attr( htmlentities( $instance['widgets'] ) ) ?>" >
		<div class="yt-widget-extends" data-setting="#<?php echo esc_attr( $this->get_field_id('widgets') ); ?>" >
		<p><?php esc_html_e('Drag & Drop Widgets Here','wpthms') ?></p>
		<?php
		$widgets = explode(':yt-sm-data:', $instance['widgets'] );
		if( !empty( $widgets ) && is_array( $widgets ) ){
			$number = 1;
			foreach ( $widgets as $widget ) {
				if( !empty( $widget ) ) {
					$url = rawurldecode( $widget );
					parse_str( $widget,$nested_widget );
					$this->yt_smart_widgets_form( $nested_widget, $number );
				}
				$number++;
			}
		}
		?>
		</div>
		<?php
	}
	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;

		$instance['widgets'] = $new_instance['widgets'];

		return $instance;
	}

	/******************************************************
	 * Get nested widgets data
	 */
	function yt_get_widgets( $id_base, $number ){
		global $wp_registered_widgets;

		$widget = false;
		foreach ( $wp_registered_widgets as $key => $value ) {
			if( strpos( $key, $id_base ) === 0 ) {
				if( isset( $wp_registered_widgets[ $key ]['callback'][0]) && is_object( $wp_registered_widgets[ $key ]['callback'][0] ) ) {
					$classname = get_class( $wp_registered_widgets[ $key ]['callback'][0] );
					$widget = new $classname;
					$widget->id_base = $id_base;
					$widget->number = $number;
					break;
				}
			}
		}

		return $widget;
	}
	/**
	 * Overwrite this function to display custom structure
	 * Display nested Widgets (front-end)
	 */
	function yt_display_widgets( $args, $instance ){
	}

	/**
	 * Display nested wigets ( form)
	 */
	function yt_smart_widgets_form($nested_widget, $number){

		$instance = !empty($nested_widget['widget-'.$nested_widget['id_base']]) ? array_shift( $nested_widget['widget-'.$nested_widget['id_base']] ) : array();
		$widget = $this->yt_get_widgets( $nested_widget['id_base'], $number );
		if( $widget ) { ?>
			<div id="<?php echo esc_attr($nested_widget['widget-id']); ?>" class="widget">
				<div class="widget-top">
					<div class="widget-title-action">
						<a class="widget-action hide-if-no-js" href="#available-widgets"></a>
						<a class="widget-control-edit hide-if-js" href="<?php echo esc_url( add_query_arg( $query_arg ) ); ?>">
							<span class="edit"><?php echo esc_html_x( 'Edit', 'widget', 'wpthms' ); ?></span>
							<span class="add"><?php echo esc_html_x( 'Add', 'widget', 'wpthms' ); ?></span>
							<span class="screen-reader-text"><?php echo esc_html( $widget->name ); ?></span>
						</a>
					</div>
					<div class="widget-title"><h4><?php echo esc_html( $widget->name ); ?><span class="in-widget-title"></span></h4></div>
				</div>

				<div class="widget-inside">
					<div class="widget-content">
					<?php if( isset($nested_widget['id_base'] ) ) { 

						//echo yt_pretty_print($instance); 
						$widget->form($instance); 
					} else { 
						echo "\t\t<p>" . esc_html__('There are no options for this widget.','wpthms') . "</p>\n"; 
					} ?>
				</div>
				<input data-yt="true" type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr($nested_widget['widget-id']); ?>" />
				<input data-yt="true" type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($nested_widget['id_base']); ?>" />
				<input data-yt="true" type="hidden" name="widget-width" class="widget-width" value="<?php echo esc_attr($nested_widget['widget-width']); ?>">
				<div class="widget-control-actions">
					<div class="alignleft">
					<a class="widget-control-remove" href="#remove"><?php esc_html_e('Delete','wpthms'); ?></a> |
					<a class="widget-control-close" href="#close"><?php esc_html_e('Close','wpthms'); ?></a>
					</div>
					<div class="alignright widget-control-noform">
					<?php submit_button( esc_html__( 'Save', 'wpthms' ), 'button-primary widget-control-save right', 'savewidget', false, array( 'id' => 'widget-' . esc_attr( $nested_widget['widget-id'] ) . '-savewidget' ) ); ?>
					<span class="spinner"></span>
					</div>
					<br class="clear" />
				</div>
			</div>

			<div class="widget-description"><?php echo ( $widget_description = wp_widget_description($widget_id) ) ? "$widget_description\n" : "$widget_title\n"; ?></div>
		</div>
		<?php
		} 
	}
}

/**
 * Smartly Dynamic Widget scripts
 * @since 1.0
 */
add_action('admin_enqueue_scripts', 'yt_smart_widget_enqueue_scripts', 6 );

if(!function_exists( 'yt_smart_widget_enqueue_scripts' )){

    function yt_smart_widget_enqueue_scripts( $hook ){
		if( 'widgets.php' != $hook )
	        return;
	    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	    wp_enqueue_script( 'yt-smart-widget-script', wpthms_FRAMEWORK_URI . "admin/assets/js/admin-smart-widget{$suffix}.js", array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-autocomplete', 'admin-widgets' ), '', true );
	
    }
}

/**
 * Widget Framework
 * @since 1.0.3
 */

class YT_Widget_Framework{

	static function init(){
		
		if( !is_admin() )
			return;
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts') );
	}

	static function admin_enqueue_scripts( $hook ){
		if( 'widgets.php' != $hook )
	        return;

	    wp_enqueue_style('wp-color-picker');
	    wp_enqueue_media();
	    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	    wp_enqueue_style( 'yt-widget-style', wpthms_FRAMEWORK_URI . "admin/assets/css/admin-widget-style.css" );
	    wp_enqueue_script( 'wp-color-picker');
	    wp_enqueue_script( 'yt-widget-script', wpthms_FRAMEWORK_URI . "admin/assets/js/admin-widget-script{$suffix}.js", array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-autocomplete', 'admin-widgets', 'wp-color-picker' ), '', true );
	    wp_localize_script( 'yt-widget-script', 'yt_widgetVars', array(
	    	'msgAlertLastField' => esc_js( __( 'You need at least one block', 'wpthms' ) )
	    ) );
	}

	static function field( $type = 'text', $args = array() ){
		$type = $type ? $type : 'text';

		$output = '';

		if( empty( $args ) )
			return $output;

		$_args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'data_name' => '',
			'value' => '',
			'std' => '',
			'options' => array(),
			'settings' => array(),

		) );

		switch ( $type) {
			/**
			 * Text
			 */
			case 'text':
				$output = sprintf('<input id="%s" class="widefat" name="%s" data-name="%s" type="text" value="%s" />', 
					esc_attr( sanitize_title( $_args['id'] ) ), 
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ), 
					esc_attr( $_args['value'] ) 
				);
			break;
			/**
			 * Textarea
			 */
			case 'textarea':
				$output = sprintf( '<textarea rows="5" id="%s" name="%s" data-name="%s" class="widefat">%s</textarea>',
					esc_attr( sanitize_title( $_args['id'] ) ), 
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ), 
					esc_textarea( $_args['value'] ) 
				);
			break;
			/**
			 * Number
			 */
			case 'number':
				$output .= sprintf('<input id="%s" name="%s" data-name="%s" type="number" value="%s" size="3" style="width:60px;" />',
					esc_attr( sanitize_title( $_args['id'] ) ), 
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ),
					esc_attr( intval( $_args['value'] ) )
				);
			break;
			/**
			 * Select box
			 */
			case 'select':
				$options = '';
				if( !empty( $_args['options'] )){
					foreach ( ( array ) $_args['options'] as $key => $value) {
						$options .= sprintf( '<option value="%s" %s>%s</option>', 
							$key, 
							selected( $_args['value'] , $key, false),
							esc_html( $value )
						);
					}
				}
				$output = sprintf( '<select id="%s" name="%s" data-name="%s">%s</select>',
					esc_attr( sanitize_title( $_args['id'] ) ), 
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ), 
					$options
				);
			break;
			/**
			 * Checkbox
			 */
			case 'checkbox':
				$output = sprintf('<input class="checkbox" type="checkbox" id="%s" name="%s" data-name="%s" %s/>',
					esc_attr( sanitize_title( $_args['id'] ) ), 
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ),
					checked( $_args['value'], 'on', false )
				);
			break;
			/**
			 * Multi checkboxes
			 */
			case 'multicheck':
				$output = '<ul class="yt-scrollable-checklist-wrapper">';
				if( !empty( $_args['options'] )){
					foreach ( ( array ) $_args['options'] as $key => $value) {
						$output .= sprintf('<li><label><input type="checkbox" name="%s" data-name="%s" value="%s"%s/>%s</label></li>',
							esc_attr( $_args['name'] ), 
							esc_attr( $_args['data_name'] ),
							esc_attr( $key ),
							( in_array( $key, $_args['value'] ) ? ' checked="checked"' : ''),
							esc_html( $value )
						);
					}
				}
				$output .= '</ul>';
			break;
			/**
			 * Media
			 */
			case 'media':
				$output = '';
			break;
			/**
			 * Colorpicker
			 */
			case 'colorpicker':
				$output = sprintf( '<input id="%s" class="yt-colorpicker" name="%s" data-name="%s" type="text" value="%s" data-std="%s"/>',
					esc_attr( sanitize_title( $_args['id'] ) ),
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ),
					esc_attr( $_args['value'] ),
					esc_attr( $_args['std'] ) 
				);
			break;			
			/**
			 * Tag search
			 */
			case 'tag_search':

				$value = array();
				$temp = array();
				$args_val = array();
				$ids = array();
				$tax = ( !empty( $_args['settings']['tax'] ) ? $_args['settings']['tax'] : 'post_tag' );

				if( !empty(  $_args['value']) ){
					if( !is_array( $_args['value'] ) ){
						$args_val = explode( ',', $_args['value'] );
					}else{
						$args_val = $_args['value'];
					}
					
					foreach ( ( array ) $args_val as $key) {
						$obj = get_term_by('id', $key , $tax );
						if( $obj ){
							$temp[$obj->term_id] = $obj->name;
							$ids[] = $key;
						}
					}
					$value = $temp;
					
				}
				$value_html = '';

				if( !empty( $value )){
					foreach ($value as $k => $v) {
						$value_html .= '<span data-id="' . esc_attr( intval( $k ) ) .'"><a class="ntdelbutton">x</a>&nbsp;' . esc_html( $v ) . '</span>';
					}
				}

				$output = sprintf( '<input class="yt-input yt-ajax-tag-search widefat" id="%1$s" data-id="%1$s" data-tax="%5$s" type="text"/>
				<input type="hidden" class="yt-hidden" name="%2$s" data-name="%3$s" value="%4$s">
				<span class="yt-tag-list tagchecklist" style="margin-bottom:0;">%6$s</span>',
					esc_attr( sanitize_title( $_args['id'] ) ),
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ),
					esc_attr( join(',', $ids ) ),
					esc_attr( $tax ),
					$value_html
				);

			break;


			/**
			 * Text
			 */
			case 'post_search':
				$value = array();
				$temp = array();
				$args_val = array();
				$ids = array();
				$post_type = ( !empty( $_args['settings']['post_type'] ) ? $_args['settings']['post_type'] : 'post' );
				
				if( is_array( $post_type ) )
					$post_type = join( ',', $post_type );

				if( !empty(  $_args['value']) ){
					if( !is_array( $_args['value'] ) ){
						$args_val = explode( ',', $_args['value'] );
					}else{
						$args_val = $_args['value'];
					}
						
					foreach ( ( array ) $args_val as $key) {
						$title = get_the_title( intval( $key ) );
						if( $title ){
							$temp[$key] = $title;
							$ids[] = $key;
						}
					}
					$value = $temp;
					
				}
				$value_html = '';

				if( !empty( $value )){
					foreach ($value as $k => $v) {
						$value_html .= '<span data-id="' . esc_attr( intval( $k ) ) .'"><a class="ntdelbutton">x</a>&nbsp;' . esc_html( $v ) . '</span>';
					}
				}

				$output = sprintf( '<input class="yt-input yt-ajax-post-search widefat" id="%1$s" data-id="%1$s" data-type="%5$s" type="text"/>
				<input type="hidden" class="yt-hidden" name="%2$s" data-name="%3$s" value="%4$s">
				<span class="yt-tag-list tagchecklist" style="margin-bottom:0;">%6$s</span>',
					esc_attr( sanitize_title( $_args['id'] ) ),
					esc_attr( $_args['name'] ), 
					esc_attr( $_args['data_name'] ),
					esc_attr( join(',', $ids ) ),
					esc_attr( $post_type ),
					$value_html
				);
			break;	
			
			default:
				$output = esc_html__('Undefined field type', 'wpthms' );
			break;

		}
		return $output;

	}
}

add_action( 'widgets_init', array( 'YT_Widget_Framework', 'init' ) );