<?php

// allow using php include
$thumb_size = !empty( $thumb_size ) ? $thumb_size : 'post-thumbnail';
$thumb_size = !empty( $args['thumb_size'] ) ? $args['thumb_size'] : $thumb_size;

// allow overwriting by global variable
if( isset( $GLOBALS['thumb_size'] ) ){
	$thumb_size = $GLOBALS['thumb_size'];
}


if( apply_filters( 'yt_theme_use_background_image', false ) ){

	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), $thumb_size );

	// Support Lazyload plugin
	if ( class_exists( 'LazyLoad_Images' ) ) {
						
		$template = '<div class="thumb-w" data-lazy-src="%s"></div>';

	}else{
		$template = '<div class="thumb-w" style="background-image:url(%s);"></div>';
	}

	// bg image
	echo !empty( $thumbnail[0] ) ? sprintf( $template, esc_attr( $thumbnail[0] ) ) : '<div class="thumb-w"></div>' ;


}else{
	
	echo sprintf( '<div class="thumb-w">%s</div>', get_the_post_thumbnail( null, $thumb_size ) );
}