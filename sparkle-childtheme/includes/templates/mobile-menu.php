<nav id="mobile-menu-nav-wrapper" class="mobile-navigation hidden-md hidden-lg" role="navigation">
	<?php
	if ( 'hide' !== yt_get_options( 'header_social_links_position' ) && function_exists( 'wpcc_output_navi' ) ) {
		wpcc_output_navi( '' );
	}

		yt_site_mobile_nav();

		echo '<div class="site-social-networks">' . yt_site_social_networks() . '<a href="https://hiwaldorf.com/wp-content/uploads/2020/01/20200114130306417.png" target="_blank" title="直达小程序"><i class="fa fa-mobile" aria-hidden="true"></i>华德福+</a></div>';
	
	?>
	<a href="javascript:void(0)" class="main-menu-toggle hidden-md hidden-lg">
		<span class="bar1"></span>
		<span class="bar2"></span>
		<span class="bar3"></span>
	</a>
</nav>
