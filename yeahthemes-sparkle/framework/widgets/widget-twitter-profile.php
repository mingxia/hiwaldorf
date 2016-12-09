<?php
/**
 *	Plugin Name: Twitter Profile
 *	Description: Display your Twitter Profile.
 */
class YT_Twitter_Profiles_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-twitter-profile-widget yt-widget',
			'description' => esc_html__('Display your Twitter Profile.', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-twitter-profile-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-twitter-profile-widget', 
			esc_html__('(Theme) Twitter Profile', 'wpthms'), 
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

		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
			
		$response_data = yt_twitter_user_profile( $username );		
		//print_r( $response_data);
		if( !empty( $response_data ) ):
			 
		?>
	    <div class="yt-twitter-profile-wrapper">
	    	<div class="yt-twitter-profile-header">
	            <a href="<?php echo !empty( $response_data['screen_name'] ) ? esc_url( "https://twitter.com/" . $response_data['screen_name'] ) : '';?>" title="<?php echo esc_attr( sprintf( esc_html__('%s on Twitter', 'wpthms' ), $response_data['screen_name'] ) );?>"><img width="48" height="48" src="<?php echo esc_url( str_replace('_normal','', $response_data['profile_image_url']) );?>" alt="<?php echo esc_attr( $response_data['name'] );?>"></a>
	            <h4><?php echo !empty( $response_data['name'] ) ? esc_html($response_data['name']) : '';?></h4>
	            <a href="https://twitter.com/<?php echo esc_attr( $response_data['screen_name'] );?>" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false"><?php esc_html_e('Follow @','wpthms')?><?php echo !empty( $response_data['screen_name'] ) ? esc_html( $response_data['screen_name'] ) : '';?></a>
	        </div>
	        <div class="yt-twitter-profile-body"><?php echo !empty( $response_data['description'] ) ? esc_html( $response_data['description'] ) : '';?></div> 
	        <div class="yt-twitter-profile-footer">
	            <span class="status-count"><strong><?php echo !empty( $response_data['statuses_count'] ) ? esc_html( $response_data['statuses_count'] ) : '';?></strong> <?php esc_html_e('Tweets','wpthms');?></span>
	            <span class="friends-count"><strong><?php echo !empty( $response_data['friends_count'] ) ? esc_html( $response_data['friends_count'] ) : '';?></strong> <?php esc_html_e('Following','wpthms');?></span>
	            <span class="followers-count"><strong><?php echo !empty( $response_data['followers_count'] ) ? esc_html( $response_data['followers_count'] ) : '';?></strong> <?php esc_html_e('Followers','wpthms');?></span>
	        </div>
	    </div>
		<?php
		endif;

		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */	 
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => 'My Profile',
			'username' => 'wpthms'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e('Username:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['username'] ); ?>" />
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
		
		return $instance;
	}
}