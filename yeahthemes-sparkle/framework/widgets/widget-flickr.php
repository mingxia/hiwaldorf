<?php
/**
 *	Plugin Name: Flickr widget
 *	Description: A widget that displays your Flickr photos
 */
class YT_Flickr_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-flickr-widget yt-widget',
			'description' => esc_html__('Display Flickr photos.', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-flickr-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-flickr-widget', 
			esc_html__('(Theme) Flickr Photos', 'wpthms'), 
			$widget_ops, 
			$control_ops);
	}
	/**
	 * Display Widget
	 */	

	function widget( $args, $instance ) {
		extract( $args );

		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$flickrID = $instance['flickrID'];
		$postcount = $instance['postcount'];
		$type = $instance['type'];
		$display = $instance['display'];

		// Before widget (defined by theme functions file)
		echo !empty( $before_widget ) ? $before_widget : '';

		// Display the widget title if one was input
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';

		// Display Flickr Photos
		 ?>
			
		<div class="yt-flickr-content-wrapper">
		
			<!-- noptimize --><script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo esc_attr( $postcount ); ?>&amp;display=<?php echo esc_attr( $display ); ?>&amp;size=s&amp;layout=x&amp;source=<?php echo esc_attr( $type ); ?>&amp;<?php echo esc_attr( $type ); ?>=<?php echo esc_attr( $flickrID ); ?>"></script><!-- /noptimize -->

		</div>
		
		<?php

		// After widget (defined by theme functions file)
		echo !empty( $after_widget ) ? $after_widget : '';
		
	}
	/**
	 * Widget Settings
	 */	 
	function form( $instance ) {

		// Set up some default widget settings
		$defaults = array(
			'title' => 'Flickr Photos',
			'flickrID' => '52617155@N08',
			'postcount' => '6',
			'type' => 'user',
			'display' => 'latest',
			'size' => 's',
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'wpthms') ?></label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'flickrID' ) ); ?>"><?php esc_html_e('Flickr ID:', 'wpthms') ?> (<a href="http://idgettr.com/">idGettr</a>)</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'flickrID' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'flickrID'  ) ); ?>" value="<?php echo esc_attr( $instance['flickrID'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'postcount' ) ); ?>"><?php esc_html_e('Number of Photos:', 'wpthms') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'postcount' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'postcount' ) ); ?>" class="widefat">
			<?php for ( $i = 3; $i <= 12; $i += 3) { ?>
				<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $instance['postcount'], $i ); ?>><?php echo esc_html( $i ); ?></option>
			<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php esc_html_e('Type (user or group):', 'wpthms') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>" class="widefat">
				<option <?php if ( 'user' == $instance['type'] ) echo 'selected="selected"'; ?>><?php esc_html_e('user','wpthms')?></option>
				<option <?php if ( 'group' == $instance['type'] ) echo 'selected="selected"'; ?>><?php esc_html_e('group','wpthms')?></option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>"><?php esc_html_e('Display (random or latest):', 'wpthms') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'display' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display' ) ); ?>" class="widefat">
				<option <?php if ( 'random' == $instance['display'] ) echo 'selected="selected"'; ?>><?php esc_html_e('random','wpthms')?></option>
				<option <?php if ( 'latest' == $instance['display'] ) echo 'selected="selected"'; ?>><?php esc_html_e('latest','wpthms')?></option>
			</select>
		</p>
			
		<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['flickrID'] = strip_tags( $new_instance['flickrID'] );

		// No need to strip tags
		$instance['postcount'] = (int) $new_instance['postcount'];
		$instance['type'] = $new_instance['type'];
		$instance['display'] = $new_instance['display'];
		
		return $instance;
	}
}