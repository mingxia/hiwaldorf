<?php
/**
 * Image format
 * @package wpthms
 */

if(class_exists( 'YT_Post_Helpers') )
	YT_Post_Helpers::$listed_post[] = get_the_ID();

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'yt_before_archive_post_entry_header' );?>
	
	<header class="entry-header article-header hidden margin-bottom-30">

		<?php do_action( 'yt_archive_post_entry_header_start' );?>
		<?php if(function_exists( 'yt_site_post_entry_category') ) yt_site_post_entry_category();?>

		<h2 class="entry-title secondary-2-primary margin-bottom-15"><a href="<?php echo esc_attr( get_permalink() ); ?>" rel="bookmark" title="<?php echo esc_attr( strip_tags( get_the_title() ) ); ?>"><?php the_title(); ?></a></h2>
		<?php if( !is_search() && 'hide' !== yt_get_options( 'blog_post_meta_info_mode' )): ?>
		<div class="entry-meta margin-bottom-30 hidden-print" style="display:none">
			<span class="posted-on">
				
				<?php
					$time_string = '<time class="entry-date published ' . ( get_the_time( 'U' ) == get_the_modified_time( 'U' ) ? 'updated' : '' ) . 'pull-left" datetime="%1$s">%2$s</time>';
					if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
						$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';
				
					echo sprintf( $time_string,
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() ),
						esc_attr( get_the_modified_date( 'c' ) ),
						esc_html( get_the_modified_date() )
					);
				?>
			</span>
			<span class="byline">

				<span class="author vcard">
					<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php echo esc_html( get_the_author() ); ?>
					</a>
				</span>
			</span>			
		</div><!-- .entry-meta -->
		<?php endif; ?>
				
		<?php if ( has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ) : ?>
		<div class="entry-thumbnail">
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
				
				<?php 
					get_template_part( 'includes/templates/post-thumbnail' );
				?>
			</a>
		</div>
		<?php endif; ?>

		<?php do_action( 'yt_archive_post_entry_header_end' );?>

	</header><!-- .entry-header -->
	
	<?php do_action( 'yt_before_archive_post_entry_content' );?>
	
	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary margin-bottom-30">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content article-content">

		<?php do_action( 'yt_archive_post_entry_content_start' );?>
		
		<?php 
		if( 'automatic' == yt_get_options( 'excerpt_output' ) ){
			the_excerpt();
		}else{
			the_content();

			wp_link_pages( array(
				'before' => '<p class="page-links pagination-nav">' . __( 'Pages:', 'wpthms' ),
				'after'  => '</p>',
				'link_before' => '<span class="page-numbers">',
				'link_after' => '</span>',
			) );
		}
		
		/*Rating*/
		if( is_home() && 'show' === yt_get_options( 'blog_post_review_info' ) && function_exists( 'wp_review_show_total') ){
			$rating = wp_review_show_total(false, 'review-total-only review-mark inline-block');
			if( $rating )
				echo sprintf( '<div class="margin-bottom-15">%s</div>', sprintf(__('Rating: %s', 'wpthms'), $rating ) );
		}
		/*Read more*/
		echo 'show' == yt_get_options( 'blog_readmore_button' ) ? '<p><a class="more-tag btn btn-default btn-lg margin-top-15" href="'. get_permalink( get_the_ID() ) . ( 'manual' == yt_get_options( 'excerpt_output' ) ? "#more-" . get_the_ID() : '' ) . '"> ' . __('Read More...','wpthms') . '</a></p>' : '';
		?>
		
		<?php do_action( 'yt_archive_post_entry_content_end' );?>

	</div><!-- .entry-content -->
	<?php endif; ?>

	<?php if ( current_user_can('edit_post', get_the_ID()) ) {?>
	<footer class="entry-meta">
		<?php edit_post_link( __( '—Edit', 'wpthms' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
	<?php }?>
	<!-- noptimize --><script type="text/html" data-role="header .entry-meta">
	<?php 
		$metadesc_style = yt_get_options('blog_post_meta_info_mode');
		if( 'large' == $metadesc_style && function_exists( 'yt_site_impressive_post_meta_description' ) )
			yt_site_impressive_post_meta_description(); 
		elseif( 'small' == $metadesc_style && function_exists( 'yt_site_post_meta_description' ) )
			yt_site_post_meta_description(); 
	?>
	</script>
	<!-- /noptimize -->
</article><!-- #post-<?php the_ID(); ?>## -->