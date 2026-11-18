<?php
/**
 *	Plugin Name: Posts with thumbnail
 *	Description: Display Posts with thumbnail
 */
// This is required to be sure Walker_Category_Checklist class is available
//require_once ABSPATH . 'wp-admin/includes/template.php';

class YT_Posts_With_Thubnail_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-posts-with-thumbnail-widget yt-widget',
			'description' => 'Display Posts with thumbnail'
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-posts-with-thumnail-widget',
		);
		
		parent::__construct( 
			'yt-posts-with-thumnail-widget', 
			'(Theme) Posts with thumbnail', 
			$widget_ops, 
			$control_ops);
		
		//Then make sure our options will be added in the footer
		add_action('admin_head', array( $this, 'widget_styles'), 10 );
		add_action('admin_print_footer_scripts', array( $this, 'widget_scripts'));

	}
	function widget_styles(){
		$output = '';

		/*Inline css :P */
		echo str_replace(array("\r", "\n", "\t"), "", $output);
		
	}
	function widget_scripts(){
	}
	
	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
		
		// outputs the content of the widget

		extract( $args );
		
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		echo '' . $before_widget;
		
		//Echo widget title
		if ( $title )
			echo '' . $before_title . $title . $after_title;
		
		$output = '';
		$instance['item_wrapper'] = 'none';
		if( function_exists( 'yt_site_post_list') ){
			echo '<div>';
				yt_site_post_list( $instance );
			echo '</div>';
		}
		?>

		<div></div>
		<?php
		
		echo '' . $after_widget;
	}

	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => 'Popular Posts',
			'category' => array(),
			'show_icon' => 0,
			'show_cat'	=> 0,
			'show_date' => 0,
			'show_rating' => 0,
			'scroll_infinitely' => 0,
			'order' => 'DESC',
			'orderby' => 'comment_count',
			'time_period' => 'default',
			'style' => 'small',
			'tags' => array(),
			'number' => 5,
			'adscode' => '',
			'adscode_between' => 5
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		$number   = isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;
		?>
		
		<p><label>Title: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></label></p>
		
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id('category') ); ?>"><strong>Category:</strong></label>
		<?php

		if( class_exists( 'YT_Widget_Framework' ) ){ 
			$field_args = array(
				'id' => $this->get_field_id('category'),
				'name' => $this->get_field_name('category'),
				'data_name' => 'widget-' . $this->id_base . '[' . $this->number . '][category]',
				'value' => !empty( $instance['category'] ) ? $instance['category'] : '',
				'settings' => array(
					'tax' => 'category'
				)
			);

			echo YT_Widget_Framework::field('tag_search', $field_args ); 
		}
		?>
		</p>
		<p><em>Select categories you wish to show, if no categories selected, show all!</em></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id('tags') ); ?>"><strong>Tagged In (optional):</strong></label>
			<?php
			if( class_exists( 'YT_Widget_Framework' ) ){ 
				$field_args = array(
					'id' => $this->get_field_id('tags'),
					'name' => $this->get_field_name('tags'),
					'data_name' => 'widget-' . $this->id_base . '[' . $this->number . '][tags]',
					'value' => !empty( $instance['tags'] ) ? $instance['tags'] : '',
					'settings' => array()
				);

				echo YT_Widget_Framework::field('tag_search', $field_args ); 
			}
			?>
		</p>
		<p><em>Specify tags to retrieve posts from. Keep typing and you will be suggested</em></p>
		<p>
			<label><strong>Thumbnail style:</strong></label>
			<select name="<?php echo esc_attr( $this->get_field_name('style') ); ?>" id="<?php echo esc_attr( $this->get_field_id('style') ); ?>" class="widefat">
				<option value="small"<?php selected( $instance['style'], 'small' ); ?>>Default (Small)</option>
				<option value="large"<?php selected( $instance['style'], 'large' ); ?>>Large</option>
				<option value="mixed"<?php selected( $instance['style'], 'mixed' ); ?>>First Large</option>
				<option value="number"<?php selected( $instance['style'], 'number' ); ?>>Number (no thumb)</option>
				<option value="nothumb"<?php selected( $instance['style'], 'nothumb' ); ?>>Title Only</option>
				<option value="thumb_first"<?php selected( $instance['style'], 'thumb_first' ); ?>>First item have thumbnail</option>
			</select></label>
		</p>
		
		<p>
			<label><strong>Order:</strong>
			<select name="<?php echo esc_attr( $this->get_field_name('order') ); ?>" id="<?php echo esc_attr( $this->get_field_id('order') ); ?>" class="widefat">
				<option value="DESC"<?php selected( $instance['order'], 'DESC' ); ?>>Descending</option>
				<option value="ASC"<?php selected( $instance['order'], 'ASC' ); ?>>Ascending</option>
			</select></label>
		</p>
		
		<p>
			<label><strong>Order by:</strong>
			<select name="<?php echo esc_attr( $this->get_field_name('orderby') ); ?>" id="<?php echo esc_attr( $this->get_field_id('orderby') ); ?>" class="widefat">
				
				<option value="date"<?php selected( $instance['orderby'], 'date' ); ?>>Date</option>
				<option value="title"<?php selected( $instance['orderby'], 'title' ); ?>>Title</option>
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>>Post slug</option>
				<option value="author"<?php selected( $instance['orderby'], 'author' ); ?>>Author</option>
				<option value="comment_count"<?php selected( $instance['orderby'], 'comment_count' ); ?>>Number of comments</option>
				<option value="modified"<?php selected( $instance['orderby'], 'modified' ); ?>>Last modified date</option>
				<option value="rand"<?php selected( $instance['orderby'], 'rand' ); ?>>Random order</option>
			
				<?php if( function_exists('yt_simple_post_views_tracker_display') ){ ?>
				<option value="meta_value_num"<?php selected( $instance['orderby'], 'meta_value_num' ); ?>>Post views</option>
				<?php } ?>
			</select></label>
		</p>
		<p>
			<label><strong>Time period:</strong>
			<select name="<?php echo esc_attr( $this->get_field_name('time_period') ); ?>" id="<?php echo esc_attr( $this->get_field_id('time_period') ); ?>" class="widefat">
				<option value="default"<?php selected( $instance['time_period'], 'default' ); ?>>Default</option>
				<option value="this_week"<?php selected( $instance['time_period'], 'this_week' ); ?>>This week</option>
				<option value="last_week"<?php selected( $instance['time_period'], 'last_week' ); ?>>Last week</option>
				<option value="this_month"<?php selected( $instance['time_period'], 'this_month' ); ?>>This Month</option>
				<option value="last_month"<?php selected( $instance['time_period'], 'last_month' ); ?>>Last Month</option>
				<option value="last_30days"<?php selected( $instance['time_period'], 'last_30days' ); ?>>Last 30 days</option>
				<option value="last_60days"<?php selected( $instance['time_period'], 'last_60days' ); ?>>Last 60 days</option>
				<option value="last_90days"<?php selected( $instance['time_period'], 'last_90days' ); ?>>Last 90 days</option>
			</select></label>
		</p>
		<p><label><input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_icon') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_icon') ); ?>" <?php checked($instance['show_icon'], 'on') ?>/> <strong>Show views/comment counter</strong></label></p>
		
		<p><label><input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_cat') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_cat') ); ?>" <?php checked($instance['show_cat'], 'on') ?>/> <strong>Show category tag</strong></label></p>
		
		<p><label><input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_date') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_date') ); ?>" <?php checked($instance['show_date'], 'on') ?>/> <strong>Show post date</strong></label></p>

		<p><label><input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('show_rating') ); ?>" name="<?php echo esc_attr( $this->get_field_name('show_rating') ); ?>" <?php checked($instance['show_rating'], 'on') ?>/> <strong>Show review result</strong></label></p>
		
		<p><label>Number of posts to show: 
		<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>" size="3" style="width:60px;" /></label></p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'adscode' ) ); ?>">Ads code:</label>
			<textarea rows="5" name="<?php echo esc_attr( $this->get_field_name('adscode') ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id('adscode') ); ?>"><?php echo !empty( $instance['adscode'] ) ? esc_textarea( $instance['adscode'] ) : '';?></textarea>
		</p>
		<p><label><?php printf( 'Display ads every %s posts', '<input id="' . esc_attr( $this->get_field_id( 'adscode_between' ) ) . '" name="' . esc_attr( $this->get_field_name( 'adscode_between' ) ) . '" type="number" min="2" value="' . esc_attr( $instance['adscode_between'] ) . '" size="3" style="width:60px;" />' ); ?>
		</label></p>

		<p><label><input class="checkbox" type="checkbox" id="<?php echo esc_attr( $this->get_field_id('scroll_infinitely') ); ?>" name="<?php echo esc_attr( $this->get_field_name('scroll_infinitely') ); ?>" <?php checked($instance['scroll_infinitely'], 'on') ?>/> <strong>Scroll infinitely!</strong></label></p>
		<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		
		// processes widget options to be saved
		$instance = $old_instance;
	
		$new_instance = wp_parse_args((array) $new_instance, array( 
			'title' => '',
			'order' => 'DESC',
			'orderby' => 'date',
			'time_period' => 'default',
			'number' => 0,
			'category' => array(),
			'tags' => array(),
			'style' => 'small',
			'show_icon' => 0,
			'show_cat' => 0,
			'show_date' => 0,
			'show_rating' => 0,
			'scroll_infinitely' => 0
		));
		$instance['title'] = $new_instance['title'];
		//Strip tags for title and name to remove HTML 
		
		
		if ( in_array( $new_instance['order'], array( 'DESC', 'ASC' ) ) ) {
			$instance['order'] = $new_instance['order'];
		} else {
			$instance['order'] = 'DESC';
		}
		
		if ( in_array( $new_instance['orderby'], array( 'meta_value_num', 'date', 'title', 'name', 'author', 'comment_count', 'modified', 'rand' ) ) ) {
			$instance['orderby'] = $new_instance['orderby'];
		} else {
			$instance['orderby'] = 'date';
		}
		if ( in_array( $new_instance['time_period'], array( 'default', 'this_week', 'last_week', 'this_month', 'last_month', 'last_30days', 'last_60days', 'last_90days') ) ) {
			$instance['time_period'] = $new_instance['time_period'];
		} else {
			$instance['time_period'] = 'default';
		}
		
		$instance['category'] = $new_instance['category'];
		$instance['tags'] = $new_instance['tags'];
		$instance['style'] = $new_instance['style'];
		$instance['number'] = (int) $new_instance['number'] == 0 ? 10 : $new_instance['number'];

		$instance['show_icon'] = $new_instance['show_icon'] ? 'on' : 0;
		$instance['show_cat'] = $new_instance['show_cat'] ? 'on' : 0;
		$instance['show_date'] = $new_instance['show_date'] ? 'on' : 0;
		$instance['show_rating'] = $new_instance['show_rating'] ? 'on' : 0;
		$instance['scroll_infinitely'] = $new_instance['scroll_infinitely'] ? 'on' : 0;
		$instance['adscode'] = $new_instance['adscode'];
		$instance['adscode_between'] = $new_instance['adscode_between'];
		
		return $instance;
	}
}