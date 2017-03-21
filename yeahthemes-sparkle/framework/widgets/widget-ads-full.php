<?php
/**
 *	Plugin Name: Ads Full
 *	Description: Full width Advertising
 */
class YT_AdsFull_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-adsfull-widget yt-widget',
			'description' => esc_html__('Full width advertisements', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-adsfull-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-adsfull-widget', 
			esc_html__('(Theme) Ads Full', 'wpthms'), 
			$widget_ops, 
			$control_ops);
	
		//Then make sure our options will be added in the footer
	     add_action('admin_head', array( $this, 'widget_styles'), 10 );
	}
	function widget_styles(){
		echo str_replace(array("\r", "\n", "\t"), "", '<style>
			.yt-widget-ads-full-image-preview{
				max-width:100%;
				margin-top:15px;
			}
		</style>');
		
	}
	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
	
		// outputs the content of the widget
		
		extract( $args );
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		$adscode 	= !empty( $instance['adscode'] ) ? $instance['adscode'] : '';
		$image 		= !empty( $instance['image'] ) ? $instance['image'] : '';
		$url 		= !empty( $instance['url'] ) ? $instance['url'] : '';
		$alt 		= !empty( $instance['alt'] ) ? $instance['alt'] : '';

		if( !empty($instance['single_only']) && !is_single() )
			return;

		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		$output = '';

			if( $adscode || $image ){
				if( !empty( $adscode ) )
					$output .= $adscode;
				else
					$output .= !empty( $url ) 
					? sprintf( '<a href="%s" title="%s" target="_blank"><img src="%s" alt="%s"></a>', esc_url( $url ), esc_attr( $alt ), esc_url( $image ), esc_attr( $alt ) ) 
					: sprintf( '<img src="%s" alt="%s">', esc_url( $image ), esc_attr( $alt ) ) ;
			}
		echo !empty( $output ) ? sprintf( '<div class="yt-ads-spacefull-content">%s</div>', $output ) : '';
		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__('Advertisements','wpthms'),
			'single_only' => 0,
			'image' => '',
			'url' => '',
			'alt' => '',
			'adscode' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	    
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'single_only' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'single_only' )); ?>" type="checkbox" <?php checked( $instance['single_only'], 'on' ); ?>/>
	    	<label for="<?php echo esc_attr( $this->get_field_id('single_only') ); ?>"><?php esc_html_e( 'Show on single article only', 'wpthms' ); ?></label>
	    </p>

	    <p><em><?php esc_html_e('if the Ads code is empty, display image','wpthms')?></em></p>
	    
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'adscode' ) ); ?>"><?php esc_html_e('Ads code:','wpthms')?></label>
			<textarea rows="5" name="<?php echo esc_attr( $this->get_field_name('adscode') ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id('adscode') ); ?>"><?php echo !empty( $instance['adscode'] ) ? esc_textarea( $instance['adscode'] ) : '';?></textarea>
		</p>	
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e('Image:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo !empty( $instance['image'] ) ? esc_url( $instance['image'] ) : '';?>" />
			<?php echo !empty( $instance['image'] ) ? sprintf('<img src="%s" class="yt-widget-ads-full-image-preview">', esc_url( $instance['image'] ) ) : '';?>
		</p>
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php esc_html_e('URL:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" type="text" value="<?php echo !empty( $instance['url'] ) ? esc_url( $instance['url'] ) : '';?>" />
		</p>
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>"><?php esc_html_e('Alt text:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'alt' ) ); ?>" type="text" value="<?php echo !empty( $instance['alt'] ) ? esc_attr( $instance['alt'] ) : '';?>" />
		</p>
	
		<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		
		// processes widget options to be saved
		$instance = $old_instance;

		//Strip tags for title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['single_only'] = $new_instance['single_only'] ? 'on' : 0;
		
		if ( current_user_can('unfiltered_html') )
			$instance['adscode'] =  $new_instance['adscode'];
		else
			$instance['adscode'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['adscode']) ) ); // wp_filter_post_kses() expects slashed

		//$instance['adscode'] = $new_instance['adscode'];
		$instance['image'] = esc_url( $new_instance['image'] );
		$instance['url'] = esc_url( $new_instance['url'] );
		$instance['alt'] = esc_attr( $new_instance['alt'] );
		
		return $instance;
	}
}