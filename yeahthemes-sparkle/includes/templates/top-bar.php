<div class="site-top-menu hidden-xs hidden-sm" id="site-top-menu">
	<div class="container">
		<div class="row">
			<div class="col-md-6 site-top-menu-left">
				<?php do_action( 'yt_site_left_top_bar' );?>
				

				<?php if( 'top_menu' === yt_get_options( 'header_social_links_position' ) ):?>
				<div class="site-social-networks">
					<?php yt_site_social_networks( false );?>
				</div>
				<?php endif?>
			</div>

			<div class="col-md-6 site-top-menu-right text-right">
				<?php do_action( 'yt_site_right_top_bar' );?>
				<?php
				if ( has_nav_menu( 'top' ) ) {
					$top_menu = wp_nav_menu(
						apply_filters( 'yt_site_top_menu_navigation_args', array( 
							'theme_location' => 'top' , 
							'echo' => false, 
							'container_class' => 'site-top-navigation-menu-container',
							'menu_class'      => 'menu list-inline',
							'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'depth' => 1,
						))
					);

					echo $top_menu;
				}


				?>

			</div>
		</div>
	</div>
</div>