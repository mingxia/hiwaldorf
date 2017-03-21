<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package wpthms
 */

get_header(); ?>
	
	<div class="container">

		<div class="row">
			
			<?php yt_before_primary(); ?>
			
			<div id="primary" <?php yt_section_classes( 'content-area', 'primary' );?>>
				
				<?php yt_primary_start(); ?>
				
				<main id="content" <?php yt_section_classes( 'site-content', 'content' );?> role="main">

					<section class="error-404 not-found">
						<header class="page-header">
							<h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'wpthms' ); ?></h1>
						</header><!-- .page-header -->

						<div class="page-content">
							<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'wpthms' ); ?></p>

							<?php get_search_form(); ?>
							<br>
							<?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

							<?php if ( yt_categorized_blog() ) : // Only show the widget if site has multiple categories. ?>
							<div class="widget widget_categories">
								<h2 class="widgettitle"><?php _e( 'Most Used Categories', 'wpthms' ); ?></h2>
								<ul>
								<?php
									wp_list_categories( array(
										'orderby'    => 'count',
										'order'      => 'DESC',
										'show_count' => 1,
										'title_li'   => '',
										'number'     => 10,
									) );
								?>
								</ul>
							</div><!-- .widget -->
							<?php endif; ?>

							<?php
							/* translators: %1$s: smiley */
							$archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'wpthms' ), convert_smilies( ':)' ) ) . '</p>';
							the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
							?>


						</div><!-- .page-content -->
					</section><!-- .error-404 -->

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