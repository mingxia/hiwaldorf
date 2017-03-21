<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package wpthms
 */

get_header(); ?>
	
	<div class="container main-columns">

		<div class="row">

			<?php yt_before_primary(); ?>
	
			<div id="primary" <?php yt_section_classes( 'content-area', 'primary' );?>>
				
				<?php yt_primary_start(); ?>
				
				<main id="content" <?php yt_section_classes( 'site-content', 'content' );?> role="main">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'wpthms' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					</header><!-- .page-header -->

					<?php /* Start the Loop */ ?>
					
					<?php yt_before_loop(); ?>
					
					<?php while ( have_posts() ) : the_post(); ?>
						
						<?php yt_loop_start(); ?>

						<?php get_template_part( 'content', 'search' ); ?>
						
						<?php yt_loop_end(); ?>

					<?php endwhile; ?>

					<?php yt_after_loop(); ?>
					
					<?php yt_direction_nav( 'nav-below' ); ?>

				<?php else : ?>

					<?php get_template_part( 'no-results', 'search' ); ?>

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
