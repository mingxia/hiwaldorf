<?php
/**
 * The second Sidebar containing the extra widget areas.
 *
 * @package wpthms
 */
?>
	
	<?php yt_before_tertiary(); ?>
	
	<div id="tertiary" <?php yt_section_classes( 'widget-area', 'tertiary' );?> role="complementary">
	
		<?php yt_tertiary_start(); ?>
		
		<?php

			if( function_exists( 'yt_theme_dynamic_sidebars' ) )
				yt_theme_dynamic_sidebars( 'yt_page_sub_sidebar', 'sub-sidebar' );

		?>
		
		<?php yt_tertiary_end(); ?>
		
	</div><!-- #secondary -->
	
	<?php yt_after_tertiary(); ?>
