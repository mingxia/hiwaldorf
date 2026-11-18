<?php
/*
Plugin Name: YT Simple Post Views Tracker (HiWaldorf)
Plugin URI: https://github.com/mingxia/hiwaldorf/tree/master/hiwaldorf-yt-plugins/yt-simple-post-views-tracker
Description: Original wpthms Sparkle companion plugin; maintained by HiWaldorf for existing sites.
Version: 2026.11.0
Author: wpthms (original), HiWaldorf (maintenance)
Author URI: https://hiwaldorf.com/
 Update URI: https://github.com/mingxia/hiwaldorf
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

define( 'YT_POST_VIEWS_TRACKER_VER', '2026.11.0' );
define( 'YT_POST_VIEWS_TRACKER_URI', plugin_dir_url( __FILE__ ) );
define( 'YT_POST_VIEWS_TRACKER_DIR', plugin_dir_path( __FILE__ ) );
define( 'YT_POST_VIEWS_TRACKER_PLUGIN', __FILE__ );
define( 'YT_POST_VIEWS_TRACKER_DIRNAME', dirname( plugin_basename( __FILE__ ) ) );


add_action( 'after_setup_theme', 'yt_simple_post_views_setup' );
/**
 * Setup
 *
 * @since 1.0.2
 */
function yt_simple_post_views_setup(){

	$support_ajax = apply_filters( 'yt_simple_post_views_use_ajax', false );
	if( $support_ajax ){

		add_action('wp_ajax_nopriv_yt_simple_post_views_ajax', 'yt_simple_post_views_ajax_counter');  
		add_action('wp_ajax_yt_simple_post_views_ajax', 'yt_simple_post_views_ajax_counter');
		add_action('wp_enqueue_scripts', 'yt_simple_post_views_scripts', 100); 
	}else{

		add_action( 'wp_head', 'yt_simple_post_views_counter' );
	}

	add_shortcode( 'yt_post_views', 'yt_simple_post_views_tracker_shortcode' );


}

/**
 * Script
 *
 * @since 1.0.2
 */
function yt_simple_post_views_scripts(){
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	wp_enqueue_script('yt-simple-post-view-js', 		YT_POST_VIEWS_TRACKER_URI . "js/yt-simple-post-view$suffix.js", array('jquery'), '1.0', true );  
	wp_localize_script('yt-simple-post-view-js', 'ytSimplePostViewVars', array(  
		'ajax_url' => admin_url('admin-ajax.php'),  
		'nonce' => wp_create_nonce('yt-simple-post-view-nonce'),
		'post_id' => get_the_ID(),
	));
	
}

/**
 * Do a count
 */
function yt_simple_post_views_counter( $post_id = 0 ){

	if( apply_filters( 'yt_simple_post_views_tracker_exclude_logged_in_users', false ) && is_user_logged_in() )
		return;

	if( apply_filters( 'yt_simple_post_views_tracker_exclude_admin_users', false ) && current_user_can('manage_options') )
		return;
	
	if( empty( $post_id ) && isset( $GLOBALS['post'] ) ){
		$post = $GLOBALS['post'];
		$post_id = $post->ID;
	}

	// Credit https://github.com/monperrus/crawler-user-agents/blob/master/crawler-user-agents.json
	$bots = array(
		'Googlebot', 'Googlebot-Mobile', 'Googlebot-Image', 'Googlebot-News', 'Googlebot-Video', 'AdsBot-Google', 'Mediapartners-Google', 'bingbot', 'slurp', 'java', 'wget', 'curl', 'Commons-HttpClient', 'Python-urllib', 'libwww', 'httpunit', 'nutch', 'Go-http-client', 'phpcrawl', 'msnbot', 'jyxobot', 'FAST-WebCrawler', 'FAST Enterprise Crawler', 'biglotron', 'teoma', 'convera', 'seekbot', 'gigabot', 'gigablast', 'exabot', 'ngbot', 'ia_archiver', 'GingerCrawler', 'webmon ', 'httrack', 'webcrawler', 'grub.org', 'UsineNouvelleCrawler', 'antibot', 'netresearchserver', 'speedy', 'fluffy', 'bibnum.bnf', 'findlink', 'msrbot', 'panscient', 'yacybot', 'AISearchBot', 'IOI', 'ips-agent', 'tagoobot', 'MJ12bot', 'dotbot', 'woriobot', 'yanga', 'buzzbot', 'mlbot', 'yandexbot', 'purebot', 'Linguee Bot', 'Voyager', 'CyberPatrol', 'voilabot', 'baiduspider', 'citeseerxbot', 'spbot', 'twengabot', 'postrank', 'turnitinbot', 'scribdbot', 'page2rss', 'sitebot', 'linkdex', 'Adidxbot', 'blekkobot', 'ezooms', 'dotbot', 'Mail.RU_Bot', 'discobot', 'heritrix', 'findthatfile', 'europarchive.org', 'NerdByNature.Bot', 'sistrix crawler', 'ahrefsbot', 'Aboundex', 'domaincrawler', 'wbsearchbot', 'summify', 'ccbot', 'edisterbot', 'seznambot', 'ec2linkfinder', 'gslfbot', 'aihitbot', 'intelium_bot', 'facebookexternalhit', 'yeti', 'RetrevoPageAnalyzer', 'lb-spider', 'sogou', 'lssbot', 'careerbot', 'wotbox', 'wocbot', 'ichiro', 'DuckDuckBot', 'lssrocketcrawler', 'drupact', 'webcompanycrawler', 'acoonbot', 'openindexspider', 'gnam gnam spider', 'web-archive-net.com.bot', 'backlinkcrawler', 'coccoc', 'integromedb', 'content crawler spider', 'toplistbot', 'seokicks-robot', 'it2media-domain-crawler', 'ip-web-crawler.com', 'siteexplorer.info', 'elisabot', 'proximic', 'changedetection', 'blexbot', 'arabot', 'WeSEE:Search', 'niki-bot', 'CrystalSemanticsBot', 'rogerbot', '360Spider', 'psbot', 'InterfaxScanBot', 'Lipperhey SEO Service', 'CC Metadata Scaper', 'g00g1e.net', 'GrapeshotCrawler', 'urlappendbot', 'brainobot', 'fr-crawler', 'binlar', 'SimpleCrawler', 'Livelapbot', 'Twitterbot', 'cXensebot', 'smtbot', 'bnf.fr_bot', 'A6-Indexer', 'ADmantX', 'Facebot', 'Twitterbot', 'OrangeBot', 'memorybot', 'AdvBot', 'MegaIndex', 'SemanticScholarBot', 'ltx71', 'nerdybot', 'xovibot', 'BUbiNG', 'Qwantify', 'archive.org_bot', 'Applebot', 'TweetmemeBot', 'crawler4j', 'findxbot', 'SemrushBot', 'yoozBot', 'lipperhey', 'y!j-asr', 'Domain Re-Animator Bot', 'AddThis', 'Screaming Frog SEO Spider', 'MetaURI', 'Scrapy', 'LivelapBot', 'OpenHoseBot', 'CapsuleChecker', 'collection@infegy.com', 'IstellaBot', 'DeuSu', 'betaBot', 'Cliqzbot', 'MojeekBot', 'netEstate NE Crawler', 'SafeSearch microdata crawler', 'Gluten Free Crawler', 'Sonic', 'Sysomos', 'Trove', 'deadlinkchecker', 'Slack-ImgProxy', 'Embedly', 'RankActiveLinkBot'
	);

	$supported = !empty( $_SERVER['HTTP_USER_AGENT'] ) 
		&& is_singular() 
		&& !preg_match( '/' . implode( '|', $bots ) . '/i', $_SERVER['HTTP_USER_AGENT'] );

	/* Allow filtering condition*/
	$supported = apply_filters( 'yt_simple_post_views_tracker_supported_condition', $supported );

	if( $supported ){
		
		/* Allow mirgrating post views from other plugins, old theme by filtering post views meta key*/
		$post_views_meta_key = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
		$current_views 		 = (int) get_post_meta( $post_id, $post_views_meta_key, true );
		
		/* Add post meta if meta key is empty */
		if( '' == $current_views )
			add_post_meta( $post_id, $post_views_meta_key, 0 );

		$current_views++;

		update_post_meta( $post_id, $post_views_meta_key, $current_views );

	}


}
//add_action( 'wp_head', 'yt_simple_post_views_tracker_display' );
/**
 * Return/Echo Post Views
 */
function yt_simple_post_views_tracker_display( $post_id = '', $echo = true ){

	if( empty( $post_id ) && isset( $GLOBALS['post'] ) ){
		$post = $GLOBALS['post'];
		$post_id = $post->ID;
	}

	$post_views_meta_key = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views');
	$current_views 		 = (int) get_post_meta( $post_id, $post_views_meta_key, true );
	
	$current_views = apply_filters( 'yt_simple_post_views_tracker_view_count', $current_views );
	if( $echo )
		echo $current_views;
	else 
		return $current_views;

}

/**
 * Ajax
 *
 * @since 1.0.2
 */
function yt_simple_post_views_ajax_counter(){

	if( apply_filters( 'yt_simple_post_views_tracker_exclude_logged_in_users', false ) && is_user_logged_in() )
		return;

	if( apply_filters( 'yt_simple_post_views_tracker_exclude_admin_users', false ) && current_user_can('manage_options') )
		return;

	// Check for nonce security
	$nonce = $_POST['nonce'];
	$post_id = $_POST['post_id'];
 
	if ( ! wp_verify_nonce( $nonce, 'yt-simple-post-view-nonce' ) && $post_id )
		die();

	$post_views_meta_key = apply_filters( 'yt_simple_post_views_tracker_meta_key', '_post_views' );
	$current_views 		 = (int) get_post_meta( $post_id, $post_views_meta_key, true );
	
	/* Add post meta if meta key is empty */
	if( '' == $current_views )
		add_post_meta( $post_id, $post_views_meta_key, 0 );

	$current_views++;

	update_post_meta( $post_id, $post_views_meta_key, $current_views );

	die();
}
/**
 * Shortcode
 *
 * @since 1.0.3
 */
function yt_simple_post_views_tracker_shortcode(){
	return yt_simple_post_views_tracker_display( get_the_ID(), false );
}