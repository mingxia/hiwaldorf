<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
/***************************************************************************************/
/* WPML Configuration
/***************************************************************************************/

if( class_exists( 'SitePressLanguageSwitcher')){
	global $icl_language_switcher;

	remove_filter('wp_nav_menu_items', array($icl_language_switcher, 'wp_nav_menu_items_filter'), 10, 2);
}

if( class_exists( 'SitePress' )){


add_filter( 'yt_theme_options_interfacesettings_overall', 'yt_wpml_theme_option_interfacesettings_header'  );

function yt_wpml_theme_option_interfacesettings_header( $options ) {


    $options[] = array(
        'name' => __('WPML language switcher','wpthms'),
        'desc' => '',
        'id' => 'header_top_bar_menu_lang_switcher',
        'std' => 'hide',
        'type' => 'toggles',
        'options' => array(
            'show' => __('Show', 'wpthms'), 
            'hide' => __('Hide', 'wpthms')
        )
    );

    return $options;
}

add_action( 'yt_site_left_top_bar', 'yt_wpml_top_bar_lang_switcher', 5 );

function yt_wpml_top_bar_lang_switcher(){

	if( !function_exists( 'icl_get_languages' ) )
		return;

    if( 'show' !== yt_get_options( 'header_top_bar_menu_lang_switcher' ) )
        return;

    $languages = icl_get_languages();

    $langs = array();

    foreach ($languages as $lang) {
    	$langs[] = sprintf( '<a href="%s" title="%s">%s</a>', 
    		esc_url( $lang['url'] ), 
    		esc_attr( $lang['native_name'] ), 
    		strtoupper( $lang['language_code'] )
    	);
    }
    echo '<div id="top-lang-switcher" class="lang-switch">' . join( '&nbsp;/&nbsp;', $langs ) . '</div>';
 
}

}