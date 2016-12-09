<?php
do_action( 'yt_start_the_awesomeness' );
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package wpthms
 */
?><!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
<!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>

<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri();?>/includes/js/html5shiv.js"></script>
	<script src="<?php echo get_template_directory_uri();?>/includes/js/respond.min.js"></script>
<![endif]-->

</head>

<body <?php body_class(); ?>>

<!--[if lt IE 9]>
<div id="yt-ancient-browser-notification">
	<div class="container">
		<?php echo '<p>Oops! Your browser is <strong><em>ancient!</em></strong> :( - <a href="http://' . 'browsehappy.com/" target="_blank">Upgrade to a different browser</a> or <a href="http://' . 'www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p>';?>
	</div>
</div>
<![endif]-->

<?php yt_before_wrapper();?>

<div id="page" class="hfeed site full-width-wrapper">

<?php yt_wrapper_start(); ?> 

<div class="inner-wrapper">
	
	<?php yt_before_header(); ?>
	
	<header id="masthead" class="site-header full-width-wrapper hidden-print" role="banner">
		<?php yt_header_start();?>
		<?php

			// Top bar
			if( 'hide' != yt_get_options( 'header_top_bar_menu' ) ){
				get_template_part( 'includes/templates/top-bar' );
			}
		?>
		<div class="site-banner" id="site-banner">
			<div class="container">
				<div class="row">
				<?php
					// Site Branding
					get_template_part( 'includes/templates/site-branding' );

					// Main Navigation
					get_template_part( 'includes/templates/main-navigation' );
					

				?>
				</div>
			</div>
		</div>

		<?php 
		// newsticker
		if(yt_get_options('site_news_tickers_mode')){
			get_template_part( 'includes/templates/site-newsticker' );
		}
		?>
		<?php yt_header_end();?>
	</header><!-- #masthead -->
	
	<?php yt_after_header(); //Call Action Hooks?>
	
	<div id="main" <?php yt_section_classes( 'site-main', 'main' );?>>
		<?php yt_main_start(); ?>
