<?php
/**
 *	Plugin Name: Twitter Timelines
 *	Description: Display your latest tweets.
 */
class YT_Twitter_Timelines_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-twitter-timelines-widget yt-widget',
			'description' => esc_html__('Display your latest tweets.', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-twitter-timelines-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-twitter-timelines-widget', 
			esc_html__('(Theme) Twitter Feeds', 'wpthms'), 
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
		$username = $instance['username'];
		$no_of_tweets = $instance['no_of_tweets'];
		$output = '';
		
		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		
		if( function_exists( 'yt_twitter_user_timelines' ) ){
			$tweets = yt_twitter_user_timelines( $no_of_tweets, $username );

			if( !is_wp_error( $tweets ) && !empty( $tweets ) ):
			$output .= '<ul>';
	        	foreach($tweets as $tweet){
	        		if( !empty( $tweet['tweet'] ) )
						$output .= '<li><div class="tweet-content">'. $tweet['tweet'] .'</div> <span class="tweet-time">'. $tweet['time'] .'</span></li>';
				}
			$output .= '</ul>';
			endif;
			
		}

		echo apply_filters( 'yt_twitter_timelines_content', $output );
			
		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__('Latest Tweets','wpthms'),
			'username' => 'wpthms',
			'no_of_tweets' => '3'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( esc_attr( $instance['title'] ) ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e('Username:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['username'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'no_of_tweets' ) ); ?>"><?php esc_html_e('Number of tweets to show:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'no_of_tweets' ) ); ?>" class='widefat' name="<?php echo esc_attr( $this->get_field_name( 'no_of_tweets' ) ); ?>" type="number" value="<?php echo esc_attr( ( int) $instance['no_of_tweets'] ); ?>" />
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
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['no_of_tweets'] = (int) $new_instance['no_of_tweets'];
		
		return $instance;
	}
}