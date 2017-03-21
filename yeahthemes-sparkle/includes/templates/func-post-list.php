<?php

$instance['show_rating'] = isset( $instance['show_rating'] ) ? (bool) $instance['show_rating'] : false;
$instance['wrapper'] = isset( $instance['wrapper'] ) ? (bool) $instance['wrapper'] : true;
$instance['item_wrapper'] = !empty( $instance['item_wrapper'] ) ? $instance['item_wrapper'] : 'article';
$instance['offset'] = !empty( $instance['offset'] ) ? $instance['offset'] : 0;
$instance['excerpt'] = !empty( $instance['excerpt'] ) ? 1 : 0;

$args = array( 
	'posts_per_page' 	=> isset( $instance['number'] ) ? intval( $instance['number'] ) : 10,
	'post_type' 		=> array( 'post' ),
	'order'				=> $instance['order'],
	'orderby' 			=> $instance['orderby'],
	'offset' 			=> intval( $instance['offset'] ),
);

$ul_class = array( 
	'post-list',
	'post-list-with-thumbnail',
	'post-list-with-format-icon',
	'secondary-2-primary',
);
// Number style
if( 'number' == $instance['style'] ){
	$ul_class[] = 'number-style';
}
// Direction
$direction = !empty( $instance['direction'] ) && 'horizontal' == $instance['direction'] ? 'horizontal' : 'vertical';
$ul_class[] = $direction;

if( 'horizontal' == $direction ){
	$ul_class[] = 'row';
	$ul_class[] = 'col-' . $instance['column'];

}
// push offset number for infinite scroll
// $instance['offset'] = !empty( $instance['scroll_infinitely'] ) ? intval( $instance['offset'] ) : ( isset( $instance['number'] ) ? absint( $instance['number'] ) : 10 );

if( !empty( $instance['wrapper'] ) ):
?>
<ul class="<?php echo !empty( $ul_class ) ? esc_attr( join(' ',  $ul_class ) ) : '';?>" data-settings="<?php echo esc_attr( json_encode( $instance ) );?>">
<?php
endif;

	if( !empty( $instance['category'] ) ){
		$args['category__in'] = !is_array( $instance['category'] ) ? explode(',', $instance['category'] ) : $instance['category'];
	}

	if( !empty( $instance['tags'] ) ){
		$args['tag__in'] = is_array( $instance['tags'] ) ? $instance['tags'] : explode(',', $instance['tags'] );
	}

	if( 'meta_value_num' == $instance['orderby'] ){
		$args['meta_key'] = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
		$args['meta_value_num'] = '0';
		$args['meta_compare'] = '>';
	}

	

	if(class_exists( 'YT_Post_Helpers') && !empty( YT_Post_Helpers::$listed_post ) && apply_filters( 'yt_avoid_duplicated_posts', false ) ){
		$args['post__not_in'] = (array) YT_Post_Helpers::$listed_post;
	}

	if( is_singular('post' ) ){
		$args['post__not_in'][] = get_the_ID();
	}
	/*Date Parameters*/
	if( 'default' !== $instance['time_period'] ){
		
		$this_year = date('Y');
		$this_month = date('m');
		$this_week = date('W');

		if( 'this_week' == $instance['time_period'] ){
			$args['date_query'] = array(
				array(
					'year' => $this_year,
					'week' => $this_week,
				),
			);
		}elseif( 'last_week' == $instance['time_period'] ){

			if ( $this_week != 1 )
				$lastweek = $this_week - 1;
			else
				$lastweek = 52;

			if ($lastweek == 52)
				$this_year = $this_year - 1;

			$args['date_query'] = array(
				array(
					'year' => $this_year,
					'week' => $lastweek,
				),
			);
		}elseif( 'this_month' == $instance['time_period'] ){

			$args['date_query'] = array(
				array(
					'year' => $this_year,
					'month' => $this_month,
				),
			);
		}elseif( 'last_month' == $instance['time_period'] ){
			if ( $this_month != 1 )
				$this_month = $this_month - 1;
			else
				$this_month = 12;

			if ($this_month == 12)
				$this_year = $this_year - 1;

			$args['date_query'] = array(
				array(
					'year' => $this_year,
					'month' => $this_month,
				),
			);

			//yt_pretty_print( $args['date_query'] ); die();
		}elseif( 'last_30days' == $instance['time_period'] ){
			$args['date_query'] = array(
				array(
					'after'     => date('F j, Y', strtotime('today - 30 days')),
					'before'    => date('F j, Y'),
					'inclusive' => true,
				),
			);
		}elseif( 'last_60days' == $instance['time_period'] ){
			$args['date_query'] = array(
				array(
					'after'     => date('F j, Y', strtotime('today - 60 days')),
					'before'    => date('F j, Y'),
					'inclusive' => true,
				),
			);
		}elseif( 'last_90days' == $instance['time_period'] ){
			$args['date_query'] = array(
				array(
					'after'     => date('F j, Y', strtotime('today - 90 days')),
					'before'    => date('F j, Y'),
					'inclusive' => true,
				),
			);
		}
	}

	if( !empty( $instance['exclude_format'] ) && $instance['exclude_format'] ){
		$exclude_format_temp = array();
		foreach( $instance['exclude_format'] as $format ){
			$exclude_format_temp[] = "post-format-$format";
		}

		$args['tax_query'] = array(
		    array(
		      'taxonomy' 	=> 'post_format',
		      'field' 		=> 'slug',
		      'terms' 		=> $exclude_format_temp,
		      'operator' 	=> 'NOT IN'
		    )
		);
	}

	// Backup global post
	$temp_post = $GLOBALS['post'];

	// print_r( $args ); die();
	
	$myposts = get_posts( apply_filters( 'yt_posts_with_thumnail_widget_query', $args ) );
// print_r( $args ); die();
	$image_size = $instance['style'];

	$count = 0;
	$post_ids = array();


	global $thumb_size;

	$bk_thumb_size = $thumb_size;
	//print_r($args);
	foreach ( $myposts as $post ) : 
		setup_postdata( $post );
		// Assign global post to post item for functions
		$GLOBALS['post'] = $post;
		
		$count++;
		$format = get_post_format();
		if ( false === $format ) {
			$format = 'standard';
		}

		$format_icon = '';
		// if( 'video' == $format )
		// 	$format_icon = 'play';
		// elseif( 'audio' == $format )
		// 	$format_icon = 'music';
		// elseif( 'gallery' == $format )
		// 	$format_icon = 'picture-o';
		// elseif( 'quote' == $format )
		// 	$format_icon = 'quote-left';
		// elseif( 'link' == $format )
		// 	$format_icon = 'link';

		// $format_icon = $format_icon ? ' <i class="fa fa-' . $format_icon . ' format-icon gray-icon"></i>' : '';

		if( class_exists( 'YT_Post_Helpers') )
			YT_Post_Helpers::$listed_post[] = get_the_ID();

		$post_ids[] = get_the_ID();
		$categories = get_the_category();
		$cat_tag 			= '';
	
		if( !empty( $instance['show_cat'] ) && !empty( $categories[0] ) && apply_filters( 'yt_posts_with_thumnail_widget_cat', true ) ){
			$category 	= $categories[0];
			$cat_tag 	.= '<span class="cat-tag ' . esc_attr( $category->slug ) . '">' . esc_html($category->cat_name ) . '</span>';
			
		}

		$liClass = array();
		if( 'large' == $instance['style'] 
			|| ( in_array( $instance['style'], array( 'thumb_first', 'mixed' ) ) 
			&& 1 == $count ) )

			$liClass[] = 'post-with-large-thumbnail';
		
		if( ( in_array( $instance['style'], array( 'thumb_first', 'mixed' ) ) && 1 == $count ) )
			$liClass[] = 'title-alt';

		if( 'none' == $instance['item_wrapper'] )
			$liClass[] =  "format-{$format}";

		if( 'horizontal' == $direction ){
			$liClass[] = 'col-xs-6';
			$liClass[] = 'col-sm-6';
			$liColumn = intval( $instance['column'] ) ? $instance['column'] : 4;

			if( 6 == $liColumn ){
				$liClass[] = 'col-md-2';
			}elseif( 4 == $liColumn ){
				$liClass[] = 'col-md-3';
			}elseif( 3 == $liColumn ){
				$liClass[] = 'col-md-4';
			}elseif( 2 == $liColumn ){
				$liClass[] = 'col-md-6';
			}
		}
		$thumb_size = ('small' == $instance['style'] ? 'thumbnail' : 'medium');
	?>
		<li data-id="<?php echo esc_attr( get_the_ID() ); ?>"<?php echo !empty( $liClass ) ? ' class="' . esc_attr( join(' ', $liClass ) ) . '"' : '';?>>
			<?php if( 'none' !== $instance['item_wrapper'] ){?>
			<<?php echo esc_attr($instance['item_wrapper'] );?> class="hentry">
			<?php }//end if none wrapper?>
				<?php if( in_array( $instance['style'], array( 'small','nothumb', 'number') ) || ( ( in_array( $instance['style'], array( 'mixed', 'thumb_first') ) ) && 1 !== $count ) ){?>
				<span class="entry-meta clearfix">
					<?php if( !empty( $instance['show_date'] ) ){?>
						<?php
						$time_string = '<time class="entry-date published pull-left" datetime="%1$s">%2$s</time>';
						if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
							$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';
					
						$time_string = sprintf( $time_string,
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() ),
							esc_attr( get_the_modified_date( 'c' ) ),
							esc_html( get_the_modified_date() )
						);

						echo $time_string;
						?>
					
					<?php }?>

					<?php echo 'none' !== $instance['item_wrapper'] ? sprintf( '<span class="hidden"> by %s</span>', 
							sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
								esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
								esc_attr( sprintf( __( 'View all posts by %s', 'wpthms' ), get_the_author() ) ),
								esc_html( get_the_author() )
							) 
						) : '';
					?>
					<?php

					if( !empty( $instance['show_icon'] ) ){
						if( 'meta_value_num' == $instance['orderby'] && function_exists('yt_simple_post_views_tracker_display') ){
						echo '<span class="small gray-icon post-views pull-right" title="' . sprintf( __( '%d Views', 'wpthms') , number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ). '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
							number_format( yt_simple_post_views_tracker_display( get_the_ID() ) );
						echo '</span>';	
						}else{
						echo '<span class="small gray-icon with-cmt pull-right">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
							comments_number( __( '0', 'wpthms' ), __( '1', 'wpthms' ), __( '%', 'wpthms' ));
						echo '</span>';
						}
					}
					
					?>
				</span>
				<?php
				}

				if( !in_array( $instance['style'], array( 'number', 'nothumb') ) && has_post_thumbnail() && get_the_post_thumbnail() ) :?>
					<?php if( ( 'thumb_first' == $instance['style'] && 1 == $count ) || in_array( $instance['style'], array( 'small', 'large', 'mixed' ) )):?>
					
						
						<div class="post-thumb<?php echo esc_attr('thumb_first' == $instance['style'] && 1 == $count || 'large' == $instance['style'] || ( 'mixed' == $instance['style'] && 1 == $count ) ? ' large' : '' );?>">
							
							<?php if ( has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ) : ?>
							<div class="entry-thumbnail">
								<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				
									<?php 

										get_template_part( 'includes/templates/post-thumbnail' );
									?>
								</a>
							</div>
							<?php endif; ?>
							<?php echo $cat_tag;?>
							<?php 
								if (function_exists('wp_review_show_total') && !empty( $instance['show_rating'] ) ) {
									$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
										if( 'star' !== $review_type )
											wp_review_show_total(true, 'review-total-only review-mark'); 
								}
							?>
					</div>
				<?php 
					endif;
				endif;?>
				
				<?php 

				if( 'large' == $instance['style'] || ( in_array($instance['style'] , array( 'thumb_first', 'mixed') ) && 1 == $count ) ){?>
					<span class="entry-meta clearfix">
						<?php if( !empty( $instance['show_date'] ) ){
						
							$time_string = '<time class="entry-date published pull-left" datetime="%1$s">%2$s</time>';
							if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
								$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';
						
							$time_string = sprintf( $time_string,
								esc_attr( get_the_date( 'c' ) ),
								esc_html( get_the_date() ),
								esc_attr( get_the_modified_date( 'c' ) ),
								esc_html( get_the_modified_date() )
							);

							echo $time_string;
							
							?>
						<?php }?>
						<?php echo 'none' !== $instance['item_wrapper'] ? sprintf( '<span class="hidden"> by %s</span>', 
								sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
									esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
									esc_attr( sprintf( __( 'View all posts by %s', 'wpthms' ), get_the_author() ) ),
									esc_html( get_the_author() )
								) 
							) : '';
						?>
						<?php

						if( !empty( $instance['show_icon'] ) ){
							if( 'meta_value_num' == $instance['orderby'] && function_exists('yt_simple_post_views_tracker_display') ){
							echo '<span class="small gray-icon post-views pull-right" title="' . esc_attr( sprintf( __( '%d Views', 'wpthms') , number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ) ) . '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
								echo number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ;
							echo '</span>';	
							}else{
							echo '<span class="small gray-icon with-cmt pull-right">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
								comments_number( __( '0', 'wpthms' ), __( '1', 'wpthms' ), __( '%', 'wpthms' ));
							echo '</span>';
							}

							
						}
						
						?>
					</span>
				<?php
				}

				if( ( in_array($instance['style'] , array( 'thumb_first', 'mixed') ) ) && 1 == $count){?>
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" class="post-title">
						<?php the_title(); ?>
					</a>
				<?php
					if( 'large' !== $instance['style'] && $instance['excerpt'] ){
						$excerpt = get_the_excerpt();
						$excerpt_length = !empty( $instance['excerpt_length'] ) ? absint( $excerpt_length ) : 20;
						$excerpt_length = $excerpt_length ? $excerpt_length : 20;
						$trimmed_excerpt = wp_trim_words( $excerpt, $excerpt_length, '...');
						echo sprintf('<span class="clear">%s</span>', $trimmed_excerpt );

					}
					
				}else{
				?>
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>" class="post-title">
						<?php echo  'number' == $instance['style'] ? '<span class="gray-2-secondary number">' . ( $count < 10 ? '0'. $count : $count ) . '</span>' : '' ;?>
						<?php the_title(); ?>
					</a>
				<?php
				}

				if( 'large' == $instance['style'] && $instance['excerpt'] ){
					$excerpt = get_the_excerpt();
					$excerpt_length = !empty( $instance['excerpt_length'] ) ? absint( $excerpt_length ) : 20;
					$excerpt_length = $excerpt_length ? $excerpt_length : 20;
					$trimmed_excerpt = wp_trim_words( $excerpt, $excerpt_length, '...');
					echo sprintf('<span class="clear">%s</span>', $trimmed_excerpt );

				}
				?>
				
				<?php 
					if (function_exists('wp_review_show_total') && !empty( $instance['show_rating'] ) ) {
						$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
							if( 'star' == $review_type )
								wp_review_show_total(true, 'review-total-only review-mark'); 
					}
				?>
			<?php if( 'none' !== $instance['item_wrapper'] ){?>
			</<?php echo esc_attr($instance['item_wrapper'] );?>>
			<?php }?>
		</li>
	<?php

		if( !empty( $instance['adscode'] ) && $count % intval( $instance['adscode_between'] ) == 0  )
			echo sprintf( '<li class="text-center ad-space">%s</li>', do_shortcode( $instance['adscode'] ) );
	endforeach;
	$thumb_size = $bk_thumb_size;
	$count = 0;
	wp_reset_postdata();
	
	// Restore Global post
	$GLOBALS['post'] = $temp_post;
	//var_dump( YT_Post_Helpers::$listed_post );
if( !empty( $instance['wrapper'] ) ):
?>
</ul>
<?php
endif;

if( !empty( $instance['scroll_infinitely'] ) )
	echo '<div data-action="load-more-post" data-role="milestone" data-listed="' . esc_attr( join(',', $post_ids) ) .'"></div>';