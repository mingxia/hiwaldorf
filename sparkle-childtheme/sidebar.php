<?php
/**
 * The Sidebar containing the main widget areas.
 * 在使用动态首页的前提下，设置首页与文章页采用不同的侧栏
 * @package yeahthemes
 */
?>
	<?php yt_before_secondary(); ?>
	
	<div id="secondary" <?php yt_section_classes( 'widget-area', 'secondary' );?> role="complementary">
	
		<?php yt_secondary_start(); ?>
		
		<?php

			if( function_exists( 'yt_theme_dynamic_sidebars' ) )
				if(is_front_page()){
					yt_theme_dynamic_sidebars( 'yt_page_default_sidebar', 'homeright' );
				}else{
					yt_theme_dynamic_sidebars( 'yt_page_default_sidebar', 'main-sidebar' );

			}

		?>
		
		<?php yt_secondary_end(); ?>
		
	</div><!-- #secondary -->
	
	<?php yt_after_secondary(); ?>
