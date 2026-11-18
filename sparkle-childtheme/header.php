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
<!--<meta name='apple-itunes-app' content='app-id=1030393337'>-->
<title><?php wp_title( '|', true, 'right' ); ?></title>
<meta name="keywords" content="华德福，人智学，施泰纳，优律司美，活力农耕，康复村，教师培训，华德福工作坊，自由的哲学" />
<?php if (is_home()) { ?>
	<meta name="description" content="秉持“以全球化视野，做本土化教育”的原则，HiWaldorf紧紧围绕“行业观察、经验分享、信息发布、资源整合”这一使命，为探寻适于孩子之教育的家长，投身华德福教育的老师，践行华德福教育的学校以及所有不放弃自我成长的个体提供细致入微的服务和准确、丰富、多视角的内容，推动华德福教育在中国的发展。" />
<?php } ?>
<?php if ( is_single() ) { ?>
	<meta name="description" content=<?php wp_title( '|', true, 'right' ); ?> />
<?php } ?>
<meta name="applicable-device" content="pc">
<meta name="apple-mobile-web-app-title" content="HiWaldorf">
<meta name="apple-mobile-web-app-capable" content="yes" />
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
<!--如果是密码文章，则禁止复制，2021年9月8日-->
<?php if ( isset( $post ) && $post instanceof WP_Post && ! empty( $post->post_password ) ) { ?>
<script>
// 禁止右键
document.oncontextmenu = function() {
	return false
};
// 禁止图片拖放
document.ondragstart = function() {
	return false
};
// 禁止选择文本
document.onselectstart = function() {
	if (event.srcElement.type != "text" && event.srcElement.type != "textarea" && event.srcElement.type != "password") return false;
	else return true;
};
if (window.sidebar) {
	document.onmousedown = function(e) {
		var obj = e.target;
		if (obj.tagName.toUpperCase() == "INPUT" || obj.tagName.toUpperCase() == "TEXTAREA" || obj.tagName.toUpperCase() == "PASSWORD") return true;
		else return false;
	}
};
// 禁止frame标签引用
if (parent.frames.length > 0) top.location.replace(document.location);
</script>
<?php } ?>
<!--如果是密码文章，则禁止复制，2021年9月8日-->
<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri();?>/includes/js/html5shiv.js"></script>
	<script src="<?php echo get_template_directory_uri();?>/includes/js/respond.min.js"></script>
<![endif]-->
<!--google adsense，2023年11月22日<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6575449653438893" crossorigin="anonymous"></script>-->
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
