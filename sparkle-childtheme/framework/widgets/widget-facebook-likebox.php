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
			'description' => 'Display your Facebook fan page'
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-facebook-likebox-widget',
		);
		
		parent::__construct( 
			'yt-facebook-likebox-widget', 
			'(Theme) Facebook Fan page', 
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
		
		
		$facepile = ''; 
		if ( array_key_exists( 'facepile', $instance ) ) {
			$facepile = !empty( $instance['facepile'] ) && $instance['facepile'] == 'on' ? 'true' : 'false';
			
		}
		$height = $instance['height'] ? $instance['height'] : 590 ;
		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		
		if($url){
			
		?>
		<div class="yt-fb-likebox">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = '//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2';
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
	    	<div class="fb-page" data-href="<?php echo esc_url( $url );?>" data-tabs="timeline" data-height="<?php echo esc_attr( $height );?>" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="<?php echo esc_attr($facepile);?>"><blockquote cite="http://www.facebook.com/facebook" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/facebook">Facebook</a></blockquote></div>
	    </div>
	    <?php

		}else{
			echo 'Oops!, You need to input your Facebook page URL';
		}
		
		echo !empty( $after_widget ) ? $after_widget : '';
	}
	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => 'Find us on Facebook',
			'url' => 'http://www.facebook.com/facebook',
			'facepile' => 0,
			'height' => 70,
			'color' => 'light'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	    
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>">Facebook Page Url:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['url'] ); ?>" />
		</p>  	
	    <p>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'facepile' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'facepile' ) ); ?>" type="checkbox" <?php checked( $instance['facepile'], 'on' ); ?>/>
	    	<label for="<?php echo esc_attr( $this->get_field_id('facepile') ); ?>">Show Friend's Faces?</label>
	    </p>
	    <p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' )); ?>">Height:</label>
			<input  step="10" min="70" max="1000" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" value="<?php echo esc_attr( $instance['height'] ); ?>" style="width:4em;"/>
		</p>
	    <p><em>Default is 130, if you wish to show timelines, increase height to 300 or higher!</em></p>	
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
		$instance['facepile'] = $new_instance['facepile'] ? 'on' : 0;
		$instance['height'] = intval( $new_instance['height'] ) <= 0 ? 70 : intval( $new_instance['height'] );
		
		return $instance;
	}
}


?>