<?php
/**
 * The template for displaying image attachments.
 *
 * @package wpthms
 */

get_header();?>
	
	<div class="container main-columns">

		<div class="row">
		
			<?php yt_before_primary(); ?>
			
			<div id="primary" <?php yt_section_classes( 'content-area image-attachment', 'primary' );?>>
				
				<?php yt_primary_start(); ?>
				
				<main id="content" <?php yt_section_classes( 'site-content', 'content' );?>>

				<?php while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

							<div class="entry-meta margin-bottom-30 hidden-print">
								<?php
									$metadata = wp_get_attachment_metadata();
									printf( __( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>', 'wpthms' ),
										esc_attr( get_the_date( 'c' ) ),
										esc_html( get_the_date() ),
										esc_url( wp_get_attachment_url() ),
										$metadata['width'],
										$metadata['height'],
										esc_url( get_permalink( $post->post_parent ) ),
										esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
										get_the_title( $post->post_parent )
									);
								?>
							</div><!-- .entry-meta -->

							<nav role="navigation" id="image-navigation" class="image-navigation">
								<div class="nav-previous"><?php previous_image_link( false, __( '<span class="meta-nav">&larr;</span> Previous', 'wpthms' ) ); ?></div>
								<div class="nav-next"><?php next_image_link( false, __( 'Next <span class="meta-nav">&rarr;</span>', 'wpthms' ) ); ?></div>
							</nav><!-- #image-navigation -->
						</header><!-- .entry-header -->

						<div class="entry-content">
							<div class="entry-thumbnail margin-bottom-30">
									<?php yt_the_attached_image(); ?>

								<?php if ( has_excerpt() ) : ?>
								<div class="entry-caption">
									<?php the_excerpt(); ?>
								</div><!-- .entry-caption -->
								<?php endif; ?>
							</div><!-- .entry-attachment -->

							<?php
								the_content();
								wp_link_pages( array(
									'before' => '<div class="page-links pagination-nav">' . __( 'Pages:', 'wpthms' ),
									'after'  => '</div>',
									'link_before' => '<span class="page-numbers">',
									'link_after' => '</span>',
								) );
							?>
						</div><!-- .entry-content -->

						<footer class="entry-meta margin-bottom-30">
							<?php
								if ( comments_open() && pings_open() ) : // Comments and trackbacks open
									printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'wpthms' ), esc_url( get_trackback_url() ) );
								elseif ( ! comments_open() && pings_open() ) : // Only trackbacks open
									printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'wpthms' ), esc_url( get_trackback_url() ) );
								elseif ( comments_open() && ! pings_open() ) : // Only comments open
									 _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'wpthms' );
								elseif ( ! comments_open() && ! pings_open() ) : // Comments and trackbacks closed
									_e( 'Both comments and trackbacks are currently closed.', 'wpthms' );
								endif;

								edit_post_link( __( '—Edit', 'wpthms' ), ' <span class="edit-link">', '</span>' );
							?>
						</footer><!-- .entry-meta -->
					</article><!-- #post-## -->

					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || '0' != get_comments_number() )
							comments_template();
					?>

				<?php endwhile; // end of the loop. ?>

				</main><!-- #content -->
				
				<?php yt_primary_end(); ?>
			
			</div><!-- #primary -->
			
			<?php yt_after_primary(); ?>

			<?php

				$current_layout = yt_get_current_layout( yt_get_options('layout') ); 
				// Columns will be controlled using css.
				if( in_array( $current_layout, array('default', 'double-sidebars') ) ){
					get_sidebar();
					get_sidebar('secondary');
				}elseif('fullwidth' == $current_layout ){
					// No sidebar
				}else{
					// Default Main sidebar
					get_sidebar();
				}
			?>
			</div>
	</div>
	
<?php get_footer(); ?>