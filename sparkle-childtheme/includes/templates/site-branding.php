<?php
/**
 * Retrieve Options data
 */
$heading = is_home() ? 'h1' : 'h3';

$site_title = is_home() ? sprintf( '<h1 class="hidden">%s</h1>', get_bloginfo( 'name', 'display' ) ) : '';


//Branding
$branding = '<div class="col-md-2 site-branding pull-left">
	<'.$heading.' class="site-logo plain-text-logo"><a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home">' . get_bloginfo( 'name', 'display' ) . '</a></'.$heading.'>
</div>';

if( !yt_get_options('plain_logo') && yt_get_options('logo') ){
	$branding = '<div class="col-md-2 site-branding pull-left">
		<'.$heading.' class="site-logo image-logo"><a href="' . esc_url( home_url( '/' ) ) . '" title="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" rel="home"><img alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '" src="'. esc_url( yt_get_options('logo') ) .'">' . '</a></'.$heading.'>
	</div>';
}

$branding = apply_filters( 'yt_site_branding', $branding );

echo wp_kses( $site_title . $branding, array(
	'h1' => array(
		'class'	=> array()
	),
	'h3' => array(
		'class'	=> array()
	),
	'div' => array(
		'class'	=> array()
	),
	'a' => array(
		'class'	=> array(),
		'href'	=> array(),
		'title'	=> array(),
		'rel'	=> array()
	),
	'img' => array(
		'class'	=> array(),
		'src'	=> array(),
		'alt'	=> array()
	),
) );