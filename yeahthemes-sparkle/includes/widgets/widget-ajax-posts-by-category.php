<?php
/**
 *	Plugin Name: Ajax Posts by Category
 *	Description: Display Posts by category using Ajax
 */
// This is required to be sure Walker_Category_Checklist class is available

class YT_Ajax_Posts_By_Category_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-ajax-posts-by-cat-widget yt-widget',
			'description' => __('Display Posts by category using Ajax', 'wpthms')
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-ajax-posts-by-cat-widget',
		);
		
		parent::__construct( 
			'yt-ajax-posts-by-cat-widget', 
			__('(Theme) Ajax Posts by Category', 'wpthms'), 
			$widget_ops, 
			$control_ops);
		
		//Then make sure our options will be added in the footer
		add_action('admin_head', array( $this, 'widget_styles'), 10 );
		add_action('admin_print_footer_scripts', array( $this, 'widget_scripts'));
		add_action('wp_footer', array( $this, 'frontent_scripts') );
		add_action('yt_ajax_yt-ajax-posts-by-category', array( $this, 'get_ajax_posts_by_category'));
		add_action('yt_ajax_nopriv_yt-ajax-posts-by-category', array( $this, 'get_ajax_posts_by_category'));

	}
	function widget_styles(){
		$output = '';


		/*Inline css :P */
		$output = str_replace(array("\r", "\n", "\t"), "", $output);

		echo $output . "\n";
		
	}

	function widget_scripts(){
	}

	function frontent_scripts(){
	}

	function get_ajax_posts_by_category(){

		$nonce = isset( $_GET['nonce'] ) ? $_GET['nonce'] : '';
		$data = isset( $_GET['data'] ) ? $_GET['data'] : '';
		$cats = isset( $_GET['cats'] ) ? $_GET['cats'] : '';
		$number = isset( $_GET['number'] ) ? intval( $_GET['number'] ) : 10 ;
		$order = isset( $_GET['order'] ) && in_array( $_GET['order'], array('DESC', 'ASC') ) ? $_GET['order'] : '';
		$orderby = isset( $_GET['orderby'] ) && in_array( $_GET['orderby'], array( 'meta_value_num', 'date', 'title', 'name', 'author', 'comment_count', 'modified' ) ) ? $_GET['orderby'] : 'date';
		$index = !empty( $_GET['index']) ? intval( $_GET['index'] ) : 1;
		
		check_ajax_referer( THEMESLUG . '_ajax_posts_by_cat', 'nonce');	

		$output = '';
		//die($cats);
		$category_list = explode(',', $cats );

		global $post;

		$post_backup = $post;
		$args = array( 
			'posts_per_page' => $number, 
			'cat' => $cats,
			'order' => $order,
			'orderby' => $orderby 
		);

		if( 'meta_value_num' == $orderby ){
			$args['meta_key'] = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
			$args['meta_value_num'] = '0';
			$args['meta_compare'] = '>';
		}


		$myposts = get_posts( apply_filters( 'yt_ajax_posts_by_cat_widget_query_ajax', $args ) );
		echo '<ul class="post-list post-list-with-thumbnail post-list-with-format-icon vertical active" data-index="' . esc_attr( $index  ) . '">';
		$counter = 0;
		if( !empty( $myposts )):
			
			foreach ( $myposts as $post ) : 
				setup_postdata( $post );
				$counter++;
				$format = get_post_format();
				if ( false === $format ) {
					$format = 'standard';
				}
	            if(class_exists( 'YT_Post_Helpers') )
	                YT_Post_Helpers::$listed_post[] = get_the_ID();
			?>
				<li data-id="<?php the_ID(); ?>" class="<?php echo esc_attr( "format-{$format}" );?>">
				
						<span class="entry-meta clearfix">
							
							<time class="entry-date published pull-left" datetime="<?php the_time('c'); ?>">
								<?php echo is_singular( 'post' ) && !empty( $temp_post->ID ) && $post->ID == $temp_post->ID ? __('Reading Now','wpthms') : get_the_date();?>
							</time>
							<?php
							if( 'meta_value_num' == $orderby  && function_exists('yt_simple_post_views_tracker_display') ){
							echo '<span class="small gray-icon post-views pull-right" title="' . sprintf( __( '%d Views', 'wpthms') , number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ) . '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
								echo number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ;
							echo '</span>';	
							}else{
							echo '<span class="small gray-icon with-cmt pull-right">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
								comments_number( __( '0', 'wpthms' ), __( '1', 'wpthms' ), __( '%', 'wpthms' ));
							echo '</span>';
							}
							?>
						</span>
						<?php

						if( has_post_thumbnail($post->ID) && get_the_post_thumbnail($post->ID) ):?>
						<div class="post-thumb">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?></a>
							<?php 
								if (function_exists('wp_review_show_total') ) {
									$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
										if( 'star' !== $review_type )
											wp_review_show_total(true, 'review-total-only review-mark'); 
								}
							?>
						</div>
						<?php endif;?>
						<a <?php echo is_singular( 'post' ) && !empty( $temp_post->ID ) && $post->ID == $temp_post->ID ? ' class="post-title active" ' : ' class="post-title" ';?>href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>">
							<?php the_title(); ?>
						</a>
						<?php 
							if (function_exists('wp_review_show_total') ) {
								$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
									if( 'star' == $review_type )
										wp_review_show_total(true, 'review-total-only review-mark'); 
							}
						?>
					
				</li>
			<?php
				
			endforeach;
			wp_reset_postdata();
		else:
			echo sprintf( '<li>%s</li>', __('No Posts found', 'wpthms') );
		endif;
		echo '</ul>';
		$post = $post_backup;

		die();
	}
	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
		
		// outputs the content of the widget

		extract( $args );
		
		$all_link = isset( $instance['all_link']) ? $instance['all_link'] : 1;
		$adscode 	= !empty( $instance['adscode'] ) ? $instance['adscode'] : '';
		$adscode_between 	= !empty( $instance['adscode_between'] ) ? intval( $instance['adscode_between'] ) : 5;
		
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		echo $before_widget;
		
		//Echo widget title
		if ( $title && !$all_link)
			echo $before_title . $title . $after_title;
		
		$output = '';
		
		global $post;

		$cat_ids = array();

		if( !empty( $instance['category'] ) && is_array( $instance['category'] ) ){
			$cat_ids = $instance['category'];
		}else{
			$cat_ids = array_keys( yt_get_category_list() );
		}

		//Assigns menu cat ids
		$menu_cat_ids = $cat_ids;

		$active_cat = 0;

		if( !empty( $instance['sibling_cats'] ) ):
			if( is_singular( 'post' ) ){

				$current_post_cats = get_the_category( $post->ID );
				$cat_ids = array();
				foreach($current_post_cats as $cat) {
					$cat_ids[] = $cat->term_id;
				}

				$cat_obj = get_the_category( $post->ID );
				$active_cat = isset( $cat_obj[0]->cat_ID ) ? $cat_obj[0]->cat_ID : $active_cat ;

				$cat_ids = array_keys( yt_get_sibling_categories($post->ID) );
			

			/* If is Cate*/
			}else if( is_category( ) && 'post' == get_post_type() ){
				
				if( get_query_var( 'cat' ) ){
					$cat_ids = array();
					$cat_ids[] = get_query_var( 'cat' );
					$active_cat = get_query_var( 'cat' );
				}

				$cat_ids = array_keys( yt_get_sibling_categories(null, $cat_ids) );


			}
		endif;
		?>
	<div class="yt-ajax-posts-by-cat yt-sliding-tabs <?php echo $instance['style']; ?>-header"<?php echo empty( $instance['disable_ajax'] ) ? sprintf( ' data-settings="%s"', esc_attr( json_encode( $instance ) ) ) : ''?>>
		<?php if( $instance['header'] ):?>
		<div class="yt-sliding-tabs-header yt-tabby-tabs-header slashes-navigation widget-title smooth-scroller swiper-container yt-tabby-tabs">

			<ul class="swiper-wrapper secondary-2-primary">
				<?php echo $all_link && $title 
					? sprintf( '<li data-id="%s" class="swiper-slide %s"><a href="#all" title="%s">%s</a></li>', 
						esc_attr( join( ',', $menu_cat_ids ) ),
						esc_attr( is_singular( 'post' ) || is_category() ? '' : 'active'),
						sprintf( __('View posts in %s', 'wpthms'), $title),
						$title ) 
					: '' ;

					$index = 0;
					$index_temp = 0;
					foreach( ( array ) $menu_cat_ids as $cat_id ){
						$index_temp++;

						if( $active_cat == $cat_id ){
							$index = $index_temp;
						}
						
						$cat_name = get_the_category_by_ID( $cat_id );
						
						echo sprintf( '<li data-id="%s" class="swiper-slide %s"><a title="%s" href="%s">%s</a></li>', 
							esc_attr( $cat_id ),
							esc_attr( $active_cat && $active_cat == $cat_id ? 'active' : '' ),
							esc_attr( $cat_name ),
							esc_url( get_category_link( $cat_id ) ),
							$cat_name
						);
					}
				
				?>				
			</ul>
		</div>
		<span class="yt-sliding-tabs-header-trigger" data-action="<?php echo $instance['style'] == 'collapsed' ? 'expand' : 'collapse'; ?>"><span></span></span>
		<?php endif;?>
		
		<div class="yt-sliding-tabs-content yt-tabby-tabs-content">
			<ul class="post-list post-list-with-thumbnail post-list-with-format-icon secondary-2-primary vertical active" data-index="<?php echo esc_attr( $index );?>">
			<?php

				$args = array( 
					'posts_per_page' 	=> isset( $instance['number'] ) ? absint( $instance['number'] ) : 10,
					'cat'				=> join( ',', $cat_ids ),
					'post_type' 		=> array( 'post' ),
					'order'				=> $instance['order'],
	 				'orderby' 			=> $instance['orderby'],
				);

				if( 'meta_value_num' == $instance['orderby'] ){
					$args['meta_key'] = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
					$args['meta_value_num'] = '0';
					$args['meta_compare'] = '>';
				}

				if(!empty( YT_Post_Helpers::$listed_post ) && apply_filters( 'yt_avoid_duplicated_posts', false ) ){
					$args['post__not_in'] = YT_Post_Helpers::$listed_post;
				}
				//print_r($args);

				$temp_post = $post;
				
				$myposts = get_posts( apply_filters( 'yt_ajax_posts_by_cat_widget_query', $args ) );

				$counter = 0;
				//print_r($args);
				foreach ( $myposts as $post ) : 
					setup_postdata( $post );

					YT_Post_Helpers::$listed_post[] = get_the_ID();
					$counter++;

					$format = get_post_format();
					if ( false === $format ) {
						$format = 'standard';
					}
				?>
					<li data-id="<?php the_ID(); ?>" class="<?php echo esc_attr( "format-{$format}" );?>">
						<span class="entry-meta clearfix">
							
							<time class="entry-date published pull-left" datetime="<?php the_time('c'); ?>">
								<?php echo is_singular( 'post' ) && !empty( $temp_post->ID ) && $post->ID == $temp_post->ID ? __('Reading Now','wpthms') : get_the_date();?>
							</time>
							<?php
							if( 'meta_value_num' == $instance['orderby'] && function_exists('yt_simple_post_views_tracker_display') ){
							echo '<span class="small gray-icon post-views pull-right" title="' . sprintf( __( '%d Views', 'wpthms') , number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ) . '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
								echo number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ;
							echo '</span>';	
							}else{
							echo '<span class="small gray-icon with-cmt pull-right">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
								comments_number( __( '0', 'wpthms' ), __( '1', 'wpthms' ), __( '%', 'wpthms' ));
							echo '</span>';
							}
							?>
						</span>
						<?php

						if( has_post_thumbnail($post->ID) && get_the_post_thumbnail($post->ID) ):?>
						<div class="post-thumb">
							<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); ?></a>
							<?php 
								if (function_exists('wp_review_show_total') ) {
									$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
										if( 'star' !== $review_type )
											wp_review_show_total(true, 'review-total-only review-mark'); 
								}
							?>
						</div>
						<?php endif;?>
						<a <?php echo is_singular( 'post' ) && !empty( $temp_post->ID ) && $post->ID == $temp_post->ID ? ' class="post-title active" ' : ' class="post-title" ';?>href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>">
							<?php the_title(); ?>
						</a>
						<?php 
							if (function_exists('wp_review_show_total') ) {
								$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
									if( 'star' == $review_type )
										wp_review_show_total(true, 'review-total-only review-mark'); 
							}
						?>
					</li>
				<?php
				if( !empty( $adscode ) && $counter % intval( $adscode_between ) == 0  )
					echo sprintf( '<li class="text-center">%s</li>', do_shortcode( $adscode ) );

				endforeach;
				wp_reset_postdata();
				
				$post = $temp_post;
				//var_dump( YT_Post_Helpers::$listed_post);
			?>
				<!--<li>
					<article>
						<time class="entry-date published" datetime="2013-11-24T19:03:05+00:00">November 24, 2013</time>
						<div class="post-thumb">
							<a href="#"><img src="http://placehold.it/60x60/EEE/555"></a>
						</div>
						<a href="#" rel="bookmark">
							<strong>Converting Our Stories Into Multi-Screen Experiences</strong>
						</a>
					</article>
				</li>-->
				
			
			</ul>
		</div>
	</div>
		<?php
		
		echo $output;
		echo $after_widget;
	}

	/**
	 * Widget Settings
	 */
	function form($instance) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => __( 'Trending Now', 'wpthms' ),
			'header' => 'on',
			'style' => 'collapsed',
			'all_link' => 'on',
			'category' => array(),
			'sibling_cats' => 0,
			'disable_ajax' => 0,
			'order' => 'DESC',
			'orderby' => 'date',
			'number' => 10,
			'adscode' => '',
			'adscode_between' => 5
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		
		$number   = isset( $instance['number'] ) ? absint( $instance['number'] ) : 6;
		?>
		
		<p><label><?php _e('Title:', 'wpthms'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></label></p>
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('header'); ?>" name="<?php echo $this->get_field_name('header'); ?>" <?php checked($instance['header'], 'on') ?>/> <label for="<?php echo $this->get_field_id('header'); ?>"><strong><?php _e('Show Header (Category menu)', 'wpthms'); ?></strong></label></p>
		<p><strong><?php _e('Category:','wpthms')?></strong></p>
		<p>
		
		<?php
		
		if( class_exists( 'YT_Walker_Category_Checklist' ) ){
			$walker = new YT_Walker_Category_Checklist(
				$this->get_field_name('category'), $this->get_field_id('category')
			);
			echo '<ul class="yt-scrollable-checklist-wrapper">';
				wp_category_checklist( 0, 0, $instance['category'], FALSE, $walker, FALSE);
			echo '</ul>';
		}else{
			$category_list = yt_get_category_list();
		
			foreach( ( array ) $category_list as $cat_id => $cat_name ){
				
				echo sprintf( '<input type="checkbox" class="checkbox" name="%s[]" id="%s" value="%s" %s/> <label for="%s">%s</label><br>',
					esc_attr( $this->get_field_name('category') ),
					esc_attr( $this->get_field_name('category') . '_' . $cat_id ),
					esc_attr( $cat_id ),
					( is_array( $instance['category'] ) && in_array( $cat_id, $instance['category'] ) ? 'checked="checked"' : '' ),
					esc_attr( $this->get_field_name('category') . '_' . $cat_id ),
					$cat_name
					
				);
				
			}
		}

		?>
		</p>
		<p><em><?php _e('Select categories you wish to show, if no categories selected, show all!','wpthms')?></em></p>
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('sibling_cats'); ?>" name="<?php echo $this->get_field_name('sibling_cats'); ?>" <?php checked($instance['sibling_cats'], 'on') ?>/> <label for="<?php echo $this->get_field_id('sibling_cats'); ?>"><strong><?php _e('Retrieve posts from sibling categories on single post and category page automatically', 'wpthms'); ?></strong></label></p>
		<p>
			<label for="<?php echo $this->get_field_id('style'); ?>"><strong><?php _e( 'Init Style:', 'wpthms' ); ?></strong></label>
			<select name="<?php echo $this->get_field_name('style'); ?>" id="<?php echo $this->get_field_id('style'); ?>" class="widefat">
				<option value="collapsed"<?php selected( $instance['style'], 'collapsed' ); ?>><?php _e('Horizontal', 'wpthms'); ?></option>
				<option value="expanded"<?php selected( $instance['style'], 'expanded' ); ?>><?php _e('Vertical', 'wpthms'); ?></option>
			</select>
		</p>
		<p><label><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('all_link'); ?>" name="<?php echo $this->get_field_name('all_link'); ?>" <?php checked($instance['all_link'], 'on') ?>/> <strong><?php _e('Show widget title on header menu', 'wpthms'); ?></strong></label></p>

		<p><label><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('disable_ajax'); ?>" name="<?php echo $this->get_field_name('disable_ajax'); ?>" <?php checked($instance['disable_ajax'], 'on') ?>/> <strong><?php _e('Disable Ajax', 'wpthms'); ?></strong></label></p>
		
		<p>
			<label><strong><?php _e( 'Order:', 'wpthms' ); ?></strong>
			<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
				<option value="DESC"<?php selected( $instance['order'], 'DESC' ); ?>><?php _e('Descending', 'wpthms'); ?></option>
				<option value="ASC"<?php selected( $instance['order'], 'ASC' ); ?>><?php _e('Ascending', 'wpthms'); ?></option>
			</select></label>
		</p>
		
		<p>
			<label><strong><?php _e( 'Order by:', 'wpthms' ); ?></strong>
			<select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				
				<option value="date"<?php selected( $instance['orderby'], 'date' ); ?>><?php _e('Date', 'wpthms'); ?></option>
				<option value="title"<?php selected( $instance['orderby'], 'title' ); ?>><?php _e('Title', 'wpthms'); ?></option>
				<option value="name"<?php selected( $instance['orderby'], 'name' ); ?>><?php _e( 'Post slug' , 'wpthms'); ?></option>
				<option value="author"<?php selected( $instance['orderby'], 'author' ); ?>><?php _e( 'Author' , 'wpthms'); ?></option>
				<option value="comment_count"<?php selected( $instance['orderby'], 'comment_count' ); ?>><?php _e( 'Number of comments' , 'wpthms'); ?></option>
				<option value="modified"<?php selected( $instance['orderby'], 'modified' ); ?>><?php _e( 'Last modified date' , 'wpthms'); ?></option>
				<option value="rand"<?php selected( $instance['orderby'], 'rand' ); ?>><?php _e( 'Random order' , 'wpthms'); ?></option>
				<?php if( function_exists('yt_simple_post_views_tracker_display') ){ ?>
				<option value="meta_value_num"<?php selected( $instance['orderby'], 'meta_value_num' ); ?>><?php _e('Post views', 'wpthms'); ?></option>
				<?php } ?>
			</select></label>
		</p>
		<p><label><?php _e( 'Number of posts to show:', 'wpthms' ); ?>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" min="2" value="<?php echo $number; ?>" size="3" style="width:60px;" /></label></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adscode' ); ?>"><?php _e('Ads code:','wpthms')?></label>
			<textarea rows="5" name="<?php echo $this->get_field_name('adscode'); ?>" class="widefat" id="<?php echo $this->get_field_id('adscode'); ?>"><?php echo !empty( $instance['adscode'] ) ? esc_textarea( $instance['adscode'] ) : '';?></textarea>
		</p>
		<p><label><?php printf( __( 'Display ads every %s posts', 'wpthms' ), '<input id="' . $this->get_field_id( 'adscode_between' ) . '" name="' . $this->get_field_name( 'adscode_between' ) . '" type="number" min="2" value="' . esc_attr( $instance['adscode_between'] ) . '" size="3" style="width:60px;" />' ); ?>
		</label></p>
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
			'header' => 0, 
			'all_link' => 0,
			'style' => 'collapsed',
			'disable_ajax' => 0,
			'order' => 'DESC',
			'orderby' => 'date',
			'number' => 0,
			'category' => array(),
			'adscode' => '',
			'adscode_between' => 0			
		));
		$instance['title'] = $new_instance['title'];
		//Strip tags for title and name to remove HTML 
		$instance['header'] = $new_instance['header'] ? 'on' : 0;
		$instance['all_link'] = $new_instance['all_link'] ? 'on' : 0;
		$instance['style'] = $new_instance['style'];
		$instance['disable_ajax'] = $new_instance['disable_ajax'] ? 'on' : 0;
		$instance['sibling_cats'] = $new_instance['sibling_cats'] ? 'on' : 0;
		$instance['adscode'] = $new_instance['adscode'];
		$instance['adscode_between'] = $new_instance['adscode_between'];
		
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
		
		$instance['category'] = $new_instance['category'];
		$instance['number'] = (int) $new_instance['number'] == 0 ? 10 : $new_instance['number'];
		
		return $instance;
	}
}