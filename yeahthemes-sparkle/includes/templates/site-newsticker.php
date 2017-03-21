<?php

$slider_settings = apply_filters( 'yt_site_headline_scroller_settings', array(
	'selector' => '.slides > li',
	'controlNav' => false,
	'slideshowSpeed' => 5000,
	'pausePlay' => false,
	'direction' => 'vertical',
	'animationLoop' => false,
	'smoothHeight' => true,
	'pauseOnAction' => false,
	'prevText' => sprintf( '<span class="btn-link hidden-xs"><i class="fa fa-chevron-up"></i> <span class="sr-only">%s</span></span>', __('Previous', 'wpthms') ),
	'nextText' => sprintf( '<span class="btn-link hidden-xs"><i class="fa fa-chevron-down"></i> <span class="sr-only">%s</span></span>', __('Next', 'wpthms') )
), 'slider');
?>

<div class="site-breaking-news hidden-xs border-bottom clearfix">
	<div class="container">
		<div class="row">
			<div class="col-md-2">
				<h3><?php echo yt_get_options('site_news_tickers_title'); ?></h3>
			</div>
			<div class="col-md-7">
				
					
			<?php

				global $post;
				/*Backup global post*/
				$backup_post = $post;

				$post_id = !empty( $post->ID ) ? $post->ID : 0;

				$news_ticker = yt_get_options('site_news_tickers_query_posts');
				$news_ticker = (array) $news_ticker;
				//print_r( $news_ticker ); die();

				$news_ticker_query_args = array(
					'cat' => !empty( $news_ticker['cat'] ) ? $news_ticker['cat'] : '',
					'tag__in' => !empty($news_ticker['tag']) ? explode( ',', $news_ticker['tag'] ) : array(), 
					'posts_per_page' => !empty( $news_ticker['posts_per_page'] ) ? $news_ticker['posts_per_page'] : 10,
					'order' => !empty( $news_ticker['order'] ) ? $news_ticker['order'] : 'DESC', 
					'orderby' => !empty( $news_ticker['orderby'] ) ? $news_ticker['orderby'] : 'date', 
				);
				//print_r( $news_ticker_query_args ); die();
				$excludeformat = array_filter( !empty( $news_ticker['excludeformat'] ) ? $news_ticker['excludeformat'] : array() );
				if( !empty( $excludeformat ) && is_array( $excludeformat ) ){
					$exclude_format_temp = array();
					foreach( $excludeformat as $format ){
						if($format)
							$exclude_format_temp[] = "post-format-$format";
					}

					$news_ticker_query_args['tax_query'] = array(
					    array(
					      'taxonomy' 	=> 'post_format',
					      'field' 		=> 'slug',
					      'terms' 		=> $exclude_format_temp,
					      'operator' 	=> 'NOT IN'
					    )
					);
				}
				/*Allow ordering posts by post views*/ 
				if( !empty( $news_ticker['orderby'] ) && 'meta_value_num' == $news_ticker['orderby'] ){
					$news_ticker_query_args['meta_key'] = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
					$news_ticker_query_args['meta_value_num'] = '0';
					$news_ticker_query_args['meta_compare'] = '>';
				}

				$the_query = new WP_Query( apply_filters( 'yt_site_news_ticker_query', $news_ticker_query_args, $post_id ) );
				//echo yt_pretty_print( $news_ticker_query_args );
				if ( $the_query->have_posts() ) {
					echo '<div class="yeahslider news-scroller site-headlines" data-settings="'.esc_attr( json_encode( $slider_settings ) ).'">
					<ul class="slides secondary-2-primary post-list-with-format-icon">';
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						$format = get_post_format();
						if ( false === $format ) {
							$format = 'standard';
						}
						
						echo '<li class="' . esc_attr( "format-$format" ) . '">
							<a href="' . get_permalink() . '" title="' . get_the_title() . '" rel="bookmark" class="post-title">' . get_the_title() . '</a>	- <time class="entry-date published" datetime="'.esc_attr( get_the_time('c') ).'">'.get_the_date().'</time>
						</li>';
					}
					echo '</ul>
					</div>';
				} else {
					// no posts found
				}
				/* Restore original Post Data */
				wp_reset_postdata();
				$post = $backup_post;
			?>
						
			</div>
			<div class="col-md-3">
				<?php do_action( 'yt_site_new_ticker_last_column' );?>
			</div>
		</div>
	</div>
</div>