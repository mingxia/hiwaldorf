<?php
$html = '';
		
global $post;

$post_backup = $post;

global $thumb_size;

$bk_thumb_size = $thumb_size;

echo '<ul class="post-list post-list-with-thumbnail post-list-with-format-icon active row">';
	if( !empty( $category_list ) ):
	?>
	<li class="sub-category-menu post-with-large-thumbnail col-md-3 col-lg-2">
		<ul class="sub-menu">
			<li data-cat="all" class="current loaded"><a href="<?php echo $atts['href'];?>"><?php echo sprintf( _x( 'All %s', 'Sub menu item of News Mega menu ', 'wpthms' ), $atts['title'] );?></a></li>
			<?php
			
			foreach ( $category_list as $cat_id ) {
				//print_r($this_cat);
				if( get_cat_name( $cat_id ) ):

					$category = get_category($cat_id);
					$cat_slug = !empty( $category->slug ) ? $category->slug : '';
				?>
					<li data-cat="<?php echo esc_attr( $cat_id );?>" class="cat-<?php echo $cat_slug;?> loaded"><a href="<?php echo esc_attr( get_category_link( $cat_id ) );?>" title="<?php echo esc_attr( get_cat_name( $cat_id ) );?>"><?php echo get_cat_name( $cat_id );?></a></li>
				<?php
				endif;
			}
		?></ul>
	</li>
	<?php endif;

	$myposts = get_posts( apply_filters( 'yt_site_ajax_mega_menu_by_category_query', array( 
		'posts_per_page' => 5, 
		'cat' => $data_cat,
		) )
	);
	
	$count = 0;
	foreach ( $myposts as $post ) :
		setup_postdata( $post ); 

		$count++;
		$format = get_post_format();
		if ( false === $format ) {
			$format = 'standard';
		}

		$post_cats = get_the_category(get_the_ID());
		$cat_tag_args = $cat_tag_args_temp = array();

		foreach ( $post_cats as $cat ) {

			$cat_tag_args_temp['cat_ID'] = $cat->cat_ID;
			$cat_tag_args_temp['name'] = $cat->name;
			$cat_tag_args_temp['slug'] = $cat->slug;
			if ( in_array( $cat->cat_ID, $category_list )) {
				$cat_tag_args['cat_ID'] = $cat->cat_ID;
				$cat_tag_args['name'] = $cat->name;
				$cat_tag_args['slug'] = $cat->slug;
				//echo 'Break';
				break;
			}
		}

		if(empty( $cat_tag_args) )
			$cat_tag_args = $cat_tag_args_temp;

		
		$thumb_size = 'medium';
		?>
		<li data-filter="all" style="<?php /*echo 'animation-delay:' . ($count * .1) .'s;-webkit-animation-delay:' . ($count* .1) .'s;';*/?>" class="post-with-large-thumbnail <?php echo 5 == $count || 4 == $count ? 'hidden-md ' : ''; ?>col-md-3 col-lg-2 cat-all">
			
				<?php if ( has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ) : ?>
				<div class="post-thumb large">
					<span class="cat-tag <?php echo esc_attr($cat_tag_args['slug'] );?>"><?php echo $cat_tag_args['name'];?></span>
					<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
						
						<?php 
							get_template_part( 'includes/templates/post-thumbnail' );
						?>
					</a>
					<?php 
						if (function_exists('wp_review_show_total') ) {
							$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
								if( 'star' !== $review_type )
									wp_review_show_total(true, 'review-total-only review-mark'); 
						}
					?>
				</div>
				<?php endif;?>
				
				<time class="entry-date" datetime="<?php echo esc_attr( get_the_time('c') ); ?>"><?php echo get_the_date();?></time>
				
				<h3 class="secondary-2-primary no-margin-bottom"><a href="<?php the_permalink();?>" rel="bookmark" class="post-title" title="<?php echo esc_attr( get_the_title() );?>"><?php the_title();?></a></h3>
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

	//Get the post from individual category 

	foreach ( $category_list as $cat_id ) :


		$myposts = get_posts( apply_filters( 'yt_site_ajax_sub_mega_menu_by_category_query', array( 
			'posts_per_page' => 5, 
			'cat' => $cat_id,
			) )
		);
		
		$count = 0;
		foreach ( $myposts as $post ) :
			setup_postdata( $post ); 

			$count++;

			$post_cat = get_category( $cat_id );
			$cat_tag_args = array();

			$cat_tag_args['cat_ID'] = $post_cat->cat_ID;
			$cat_tag_args['name'] = $post_cat->name;
			$cat_tag_args['slug'] = $post_cat->slug;
			
			?>
			<li data-filter="<?php echo esc_attr( $cat_id );?>" style="display:none;" class="post-with-large-thumbnail <?php echo 5 == $count || 4 == $count ? 'hidden-md ' : ''; ?>col-md-3 col-lg-2 cat-<?php echo $cat_tag_args['slug']?>">
				
					<?php if ( has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ) : ?>
					<div class="post-thumb large">
						<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
						
							<?php 
								get_template_part( 'includes/templates/post-thumbnail' );
							?>
						</a>
						<?php 
							if (function_exists('wp_review_show_total') ) {
								$review_type = get_post_meta( $post->ID, 'wp_review_type', true );
									if( 'star' !== $review_type )
										wp_review_show_total(true, 'review-total-only review-mark'); 
							}
						?>
					</div>
					<?php endif;?>
					
					<time class="entry-date" datetime="<?php echo esc_attr( get_the_time('c') ); ?>"><?php echo get_the_date();?></time>
					
					<h3 class="secondary-2-primary no-margin-bottom"><a href="<?php the_permalink();?>" rel="bookmark" class="post-title" title="<?php echo esc_attr( get_the_title() );?>"><?php the_title();?></a></h3>
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
	endforeach;
	//print_r(get_the_category(1));

echo '</ul>';

$thumb_size = $bk_thumb_size;

$post = $post_backup;