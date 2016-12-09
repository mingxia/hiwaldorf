<?php
$primary_menu = '';
//Navnav
if ( has_nav_menu( 'primary' ) ) {
	$primary_menu = wp_nav_menu(
		apply_filters( 'yt_site_primary_navigation_args', array( 
			'theme_location' => 'primary', 
			'echo' => false, 
			'container_class' => 'site-navigation-menu-container',
			'menu_class'      => 'menu',
			'depth' => 5,
			'walker' => new YT_Walker_Nav_Mega_Menu_By_Category()
		))
	);
}else{
	$primary_menu = '<ul class="menu"><li>' . sprintf( __('Create a Menu in <a href="%s">Menus</a> and assign it as Primary Menu in <a href="%s">Theme Location</a>', 'wpthms'), admin_url('nav-menus.php'), admin_url('nav-menus.php?action=locations') ) . '</li></ul>';
}

$site_navigaton = '<nav id="site-navigation" class="col-sm-12 col-md-10 main-navigation" role="navigation">
	<div class="sr-only skip-link"><a href="#content" title="' . esc_attr( 'Skip to content', 'wpthms' ) . '">' . __( 'Skip to content', 'wpthms' ) . '</a></div>

	' . $primary_menu . '
	<a href="javascript:void(0)" class="main-menu-toggle hidden-md hidden-lg">
		<span class="bar1"></span>
		<span class="bar2"></span>
		<span class="bar3"></span>
	</a>
</nav><!-- #site-navigation -->';

//Allow editing site
$site_navigaton = yt_apply_atomic( 'site_primary_menu', $site_navigaton );

echo $site_navigaton;