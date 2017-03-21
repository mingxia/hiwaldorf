<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package wpthms
 */

get_header();
$post_layout = yt_get_options( 'archive_post_layout' ); 
$content_part = 'classic' == $post_layout ? '-classic' : '';
?>
	
	<div class="container main-columns">

		<div class="row">
	
			<?php yt_before_primary(); ?>
			
			<div id="primary" <?php yt_section_classes( 'content-area', 'primary' );?>>
				
				<?php yt_primary_start(); ?>

				<?php
					/**
					 * Archive header
					 *
					 * @since 1.0
					 */
					get_template_part( 'includes/templates/archive-title' );
				?>
				
				<main id="content" <?php yt_section_classes( 'site-content', 'content' );?> role="main">

				<?php if ( have_posts() ) : ?>

					<?php /* Start the Loop */ ?>
					<?php yt_before_loop(); ?>
					
					<?php while ( have_posts() ) : the_post(); ?>
					
						<?php yt_loop_start(); ?>

						<?php
							/* Include the Post-Format-specific template for the content.
							 * If you want to overload this in a child theme then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( "content{$content_part}", get_post_format() );
						?>
						
						<?php yt_loop_end(); ?>

					<?php endwhile; ?>
					
					<?php yt_after_loop(); ?>

					<?php 
					if( 'number' == yt_get_options( 'blog_pagination' ) )
						yt_pagination_nav();
					else
						yt_direction_nav( 'nav-below' ); 
					?>

				<?php else : ?>

					<?php get_template_part( 'no-results', 'archive' ); ?>

				<?php endif; ?>

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