<?php
/**
 *	Plugin Name: Facebook Likebox
 *	Description: Display your Facebook fan page
 */
	
class YT_Facebook_Likebox_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-facebook-likebox-widget yt-widget',
			'description' => esc_html__('Display your Facebook fan page', 'wpthms')
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-facebook-likebox-widget',
		);
		
		parent::__construct( 
			'yt-facebook-likebox-widget', 
			esc_html__('(Theme) Facebook Fan page', 'wpthms'), 
			$widget_ops, 
			$control_ops);
	
	}


	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
		
		// outputs the content of the widget
		
		extract( $args );
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$url = $instance['url'];
		
		
		$stream = ''; 
		if ( array_key_exists( 'stream', $instance ) ) {
			$stream = !empty( $instance['stream'] ) && $instance['stream'] == 'on' ? 'true' : 'false';
			
		}
		$height = $instance['height'] ? $instance['height'] : 590 ;
		$color = $instance['color'];
		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		
		if($url){
			
		?>
		<div class="yt-fb-likebox">
	    <iframe src="//www.facebook.com/plugins/likebox.php?href=<?php echo esc_url( $url );?>&amp;width&amp;height=<?php echo esc_attr( $height );?>&amp;colorscheme=<?php echo esc_attr( $color );?>&amp;show_faces=true&amp;header=false&amp;stream=<?php echo esc_attr( $stream );?>&amp;show_border=false" style="border:none; overflow:hidden; height:<?php echo esc_attr( $height );?>px;"></iframe>
	    </div>
	    <?php

		}else{
			esc_html_e('Oops!, You need to input your Facebook page URL','wpthms');
		}
		
		echo !empty( $after_widget ) ? $after_widget : '';
	}
	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__('Find us on Facebook','wpthms'),
			'url' => 'http://www.facebook.com/wpthms',
			'stream' => 0,
			'height' => 290,
			'color' => 'light'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	    
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms');?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php esc_html_e('Facebook Page Url:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['url'] ); ?>" />
		</p>  	
	    <p>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'stream' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'stream' ) ); ?>" type="checkbox" <?php checked( $instance['stream'], 'on' ); ?>/>
	    	<label for="<?php echo esc_attr( $this->get_field_id('stream') ); ?>"><?php esc_html_e( 'Show stream', 'wpthms' ); ?></label>
	    </p>
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' )); ?>"><?php esc_html_e('Height:','wpthms')?></label>
			<input  step="10" min="290" max="1000" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['height'] ); ?>" style="width:4em;"/>
		</p>
	    <p><em><?php esc_html_e('Heght should be 590 if "Show stream" option is enabled (default: 290)','wpthms')?></em></p>	
	    	
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'color' ) ); ?>"><?php esc_html_e('Color:','wpthms')?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('color') ); ?>" name="<?php echo esc_attr( $this->get_field_name('color') ); ?>">
	            <option <?php if ( 'light' == $instance['color'] ) echo 'selected="selected"'; ?>><?php _e('light','wpthms')?></option>
				<option <?php if ( 'dark' == $instance['color'] ) echo 'selected="selected"'; ?>><?php _e('dark','wpthms')?></option>
	        </select>
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
		$instance['url'] 	= esc_url( $new_instance['url'] );
		$instance['stream'] = $new_instance['stream'] ? 'on' : 0;
		$instance['height'] = intval( $new_instance['height'] ) <= 0 ? 290 : intval( $new_instance['height'] );
		$instance['color'] 	= $new_instance['color'];
		
		return $instance;
	}
}


?>