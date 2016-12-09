<?php
/**
 *	Plugin Name: Category's descendants
 *	Description:  A widget that display a single category's descendants.
 */
	
class YT_Category_Descendants_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-categories-descendants-widget yt-widget',
			'description' => esc_html__('A list or dropdown of a single category\'s descendants.', 'wpthms')
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-categories-descendants-widget',
		);
		
		parent::__construct( 
			'yt-categories-descendants-widget', 
			esc_html__('(Theme) Category\'s descendants', 'wpthms'), 
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
		$category_id = $instance['category_id'];
		$show_as_dropdown = isset( $instance['show_as_dropdown'] ) ? $instance['show_as_dropdown'] : false;
		$show_post_counts = isset( $instance['show_post_counts'] ) ? $instance['show_post_counts'] : false;
		$show_hierarchy = isset( $instance['show_hierarchy'] ) ? $instance['show_hierarchy'] : false;

		echo !empty( $before_widget ) ? $before_widget : '';

		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';

		//If a category was selected, display it.
		if ( $category_id ) :
			if ( $show_as_dropdown ) : ?>
			
				<?php wp_dropdown_categories( "orderby=name&hierarchical={$show_hierarchy}&show_count={$show_post_counts}&use_desc_for_title=0&child_of=". esc_attr( $category_id ) ); ?>
			
			<?php else : ?>
			<ul>
				<?php wp_list_categories( "title_li=&orderby=name&hierarchical={$show_hierarchy}&show_count={$show_post_counts}&use_desc_for_title=0&child_of=". esc_attr( $category_id ) ); ?>
			</ul>
			<?php endif;
		endif;

		//After widget
		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => esc_html__('Categories', 'wpthms'), 'category_id' => '', 'show_as_dropdown' => false, 'show_post_counts' => false, 'show_hierarchy' => false );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'wpthms'); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'category_id' ) ); ?>"><?php esc_html_e('Category to be displayed:', 'wpthms'); ?></label>
			<?php wp_dropdown_categories('show_option_all=' . esc_html__('Select Category', 'wpthms') . '&hierarchical=1&orderby=name&selected='.$instance['category_id'].'&name='. esc_attr( $this->get_field_name( 'category_id' ) ) .'&class=widefat'); ?>

		</p>


		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_as_dropdown' ) ); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $instance['show_as_dropdown'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_as_dropdown' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_as_dropdown' ) ); ?>" value="1" <?php checked($instance['show_as_dropdown'], 'on'); ?> />
				<?php esc_html_e('Show as dropdown', 'wpthms'); ?>
			</label><br />

			<label for="<?php echo esc_attr( $this->get_field_id( 'show_post_counts' ) ); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $instance['show_post_counts'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_post_counts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_post_counts' ) ); ?>" value="1" <?php checked( $instance['show_post_counts'], 'on'); ?> />
				<?php esc_html_e('Show post counts', 'wpthms'); ?>
			</label><br />

			<label for="<?php echo esc_attr( $this->get_field_id( 'show_hierarchy' ) ); ?>">
				<input class="checkbox" type="checkbox" <?php checked( $instance['show_hierarchy'], true ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_hierarchy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_hierarchy' ) ); ?>" value="1" <?php checked( $instance['show_hierarchy'], 'on'); ?> />
				<?php esc_html_e('Show hierarchy', 'wpthms'); ?>
			</label>
		</p>
			
	<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags for title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		//No need to strip tags for categories.
		$instance['category_id'] = 	$new_instance['category_id'];
		$instance['show_as_dropdown'] = $new_instance['show_as_dropdown'] ? 'on' : '';
		$instance['show_post_counts'] = $new_instance['show_post_counts'] ? 'on' : '';
		$instance['show_hierarchy'] = $new_instance['show_hierarchy'] ? 'on' : '';

		return $instance;
	}
}