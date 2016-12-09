<nav id="mobile-menu-nav-wrapper" class="mobile-navigation hidden-md hidden-lg" role="navigation">
	<?php
	if( 'hide' !== yt_get_options( 'header_social_links_position' ) )
		echo '<div class="site-social-networks">' . yt_site_social_networks() . '</div>';
	
	yt_site_mobile_nav();
	?>		
	<a href="javascript:void(0)" class="main-menu-toggle hidden-md hidden-lg"><span class="bar1"></span><span class="bar2"></span><span class="bar3"></span></a>
</nav>