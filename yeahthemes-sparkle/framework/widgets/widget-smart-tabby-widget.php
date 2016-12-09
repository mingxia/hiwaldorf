<?php

/**
* Tabs Widget
*/
class YT_Smart_Tabbed_Widget extends YT_Smart_Widget {
	/**
	* Widget Setup
	*/
	function __construct() {

		$widget_ops = array(
			'classname' => 'yt-smart-tabby-widget yt-widget',
			'description' => esc_html__('Display widgets inside a tab', 'wpthms')
		);

		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-smart-tabby-widget',
			//'width' => 400
		);

		parent::__construct( 
			'yt-smart-tabby-widget', 
			esc_html__('(Theme) Smart Tabby widget', 'wpthms'), 
			$widget_ops, 
			$control_ops
		);

	}
	/**
	 * Widget Settings
	 */
	function form( $instance ) {
		global $wp_registered_widgets;

		$default = array( 
			'title' => esc_html__('Smart widget','wpthms'),
			'widgets' =>  '',
			'size'	=> 'small',
		);
		$instance = wp_parse_args( ( array ) $instance, $default );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<em><?php esc_html_e('Title only show for "small" size.', 'wpthms') ?></em>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e('Title Size', 'wpthms') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr($this->get_field_name( 'size' ) ); ?>">
				<option value="small" <?php if ( 'small' == $instance['size'] ) echo 'selected="selected"'; ?>><?php esc_html_e('Small','wpthms')?></option>
				<option value="big" <?php if ( 'big' == $instance['size'] ) echo 'selected="selected"'; ?>><?php esc_html_e('Big','wpthms')?></option>
			</select>
		</p>
		<em><?php esc_html_e('Size option is for tab only.', 'wpthms') ?></em>
		
		<p></p>
		<input type="hidden" class="widefat" name="<?php echo esc_attr( $this->get_field_name('widgets') ); ?>" id="<?php echo esc_attr($this->get_field_id('widgets') ); ?>" value="<?php echo esc_attr( htmlentities( $instance['widgets'] ) ); ?>" >
		<div class="yt-widget-extends" data-setting="#<?php echo esc_attr( $this->get_field_id('widgets') ); ?>" style="min-height: 100px;border: 1px dashed #ddd;padding: 0 10px;margin-bottom:10px;">
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
		//print_r( $new_instance ); die();
		//Strip tags for title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['widgets'] = $new_instance['widgets'];
		$instance['size'] = in_array( $new_instance['size'], array( 'big', 'small' ) ) ? $new_instance['size'] : 'small';

		return $instance;
	}

	/**
	 * Display nested Widgets
	 */
	function yt_display_widgets( $args, $instance ){
		extract( $args, EXTR_SKIP );

		global $wp_registered_widget_updates;

		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'widgets' => '',
			'size' => 'small',

		));

		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$widgets = explode(':yt-sm-data:', $instance['widgets'] );

		if( !empty($widgets) && is_array($widgets) ){ ?>
		<?php 
			echo !empty( $title ) && 'small' == $instance['size'] ? ( $before_title . $title . $after_title ) : '';
		?>
		<div class="yt-tabby-tabs">

			<div class="yt-tabby-tabs-header slashes-navigation<?php echo 'big' == $instance['size'] ? esc_attr( ' widget-title size-big' ) : ' size-small';?>">
				<ul class="secondary-2-primary">

					<?php
					$i = 0;
					foreach ($widgets as $widget ) {
						$active = '';
						if( $i == 0 ){ 
							$active='active';
						} 
						$i++;

						if( !empty( $widget ) ) {
							$url = rawurldecode($widget);
							parse_str($widget, $nested_widget );
							$nested_instance = !empty($nested_widget ['widget-'.$nested_widget ['id_base']]) ? array_shift( $nested_widget ['widget-'.$nested_widget ['id_base']] ) : array();

							$widget = $this->yt_get_widgets( $nested_widget ['id_base'], $i );
							// $widget = isset( $wp_registered_widget_updates[$nested_widget ['id_base']] ) ? $wp_registered_widget_updates[$nested_widget ['id_base']]['callback'][0] : false;
							if( $widget ) {
								$widget_title = isset($nested_instance['title']) ? $nested_instance['title'] : $widget->name;
								if( 'big' == $instance['size'] )
									echo sprintf( '<li class="%s"><a href="#%s">%s</a></li>', esc_attr( $active ), esc_attr( $nested_widget ['widget-id'] ), $widget_title );
								else
									echo sprintf( '<li class="%s"><a href="#%s">%s</a></li>', esc_attr( $active ), esc_attr( $nested_widget ['widget-id'] ), $widget_title );
								
							}
						}
					} ?>
				</ul>
			</div>

			<div class="yt-tabby-tabs-content yt-smart-tabby-widget-content">
				<?php
				$i=0;
				foreach ($widgets as $widget) {
					$active = '';
					if( $i == 0 ) {
						$active='active';
					}
					$i++;
					if( !empty( $widget ) ) {
						$url = rawurldecode( $widget );
						parse_str($widget, $nested_widget );
						$nested_instance = isset( $nested_widget ['widget-'.$nested_widget ['id_base']]) ? array_shift( $nested_widget ['widget-'.$nested_widget ['id_base']] ) : array();

						$widget = $this->yt_get_widgets( $nested_widget ['id_base'], $i );
						// $widget = isset( $wp_registered_widget_updates[$nested_widget ['id_base']] ) ? $wp_registered_widget_updates[$nested_widget ['id_base']]['callback'][0] : false;

						if( isset( $nested_widget ['id_base'] ) && $widget ) {
							$widget_options = $widget->widget_options; ?>

							<div class="<?php echo esc_attr( 'widget_'.$nested_widget ['widget-id'].' ' . $widget_options['classname'] ); ?> <?php echo !empty( $active ) ? $active : ''; ?>">
							<?php  
								$default_args = array( 
									'before_title' 		=> apply_filters( 'yt_sidebar_before_title', '<h3 class="widget-title hidden">' ),
									'after_title'		=> apply_filters( 'yt_sidebar_after_title', '</h3>' ),
									'before_widget' => '', 
									'after_widget' => '', 
								);

								//var_dump( $nested_instance);
								/*Init nested widget content*/
								$widget->widget( $default_args, $nested_instance );
							?>
							</div>
							<?php
						}
					}
				}?>
			</div>
		</div>	
		<?php
		
		} 
	}
}

?>