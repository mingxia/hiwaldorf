<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
//add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
if( defined( 'WPB_VC_VERSION' ) ):

require_once( 'vc-addons/class.vc-addons.php');
require_once( 'vc-addons/post-list.php');

// Finally initialize code
$GLOBALS['ytvc_theme_post_list'] = new YTVC_ThemePostList();


endif;// class_exists('bbPress')