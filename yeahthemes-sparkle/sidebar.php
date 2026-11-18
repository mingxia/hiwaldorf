<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package wpthms
 */
?>
	<?php yt_before_secondary(); ?>
	
	<div id="secondary" <?php yt_section_classes( 'widget-area', 'secondary' );?> role="complementary">
	
		<?php yt_secondary_start(); ?>
		
		<?php

			if( function_exists( 'yt_theme_dynamic_sidebars' ) )
				yt_theme_dynamic_sidebars( 'yt_page_default_sidebar', 'main-sidebar' );

		?>
		
		<?php yt_secondary_end(); ?>
		
	</div><!-- #secondary -->
	
	<?php yt_after_secondary(); ?>
