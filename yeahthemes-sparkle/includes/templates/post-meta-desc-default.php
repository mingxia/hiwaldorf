<?php
$meta_info = yt_get_options('blog_post_meta_info');
//Author
$author_output = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s %4$s</a></span>',
	esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
	esc_attr( sprintf( __( 'View all posts by %s', 'wpthms' ), get_the_author() ) ),
	get_avatar( get_the_author_meta( 'ID' ), 32 ),
	esc_html( get_the_author() )
);

//Category
$categories = get_the_category();
$categories_output = array();

if($categories){
	foreach($categories as $category) {
		$categories_output[] = '<a class="'.$category->slug.'" href="'. get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'wpthms' ), $category->name ) ) . '">'.$category->cat_name.'</a>';
	}
}

//Time
$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
	$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';

$time_string = sprintf( $time_string,
	esc_attr( get_the_date( 'c' ) ),
	esc_html( get_the_date() ),
	esc_attr( get_the_modified_date( 'c' ) ),
	esc_html( get_the_modified_date() )
);

$date_output = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
	esc_url( get_permalink() ),
	esc_attr( get_the_time() ),
	$time_string
);

/**
 * Output
 */
if( in_array( 'author',$meta_info ) || in_array( 'category',$meta_info ) || in_array( 'share_buttons',$meta_info ) ):
echo '<div class="up clearfix">';
	
	echo in_array( 'author',$meta_info ) ? sprintf( __( '<span class="by-author"><span>by</span> %s</span>', 'wpthms' ), $author_output	) : '';
	

	// echo in_array( 'category',$meta_info ) ? sprintf( __( '&nbsp;<span class="in-cat"><span>in</span> %s</span>', 'wpthms' ),join( ', ', $categories_output  ) ) : '';

	if( in_array( 'share_buttons',$meta_info ) && function_exists('yt_site_social_sharing_buttons') && !is_search() ){
		

		$styles = apply_filters( 'yt_site_social_sharing_services_styles', array(
			'style' => 'color',
			'size'	=> 'small'
		), 'style1' );

		$services = array(
			'twitter' => array(
				'title' => __('Twitter', 'wpthms'),
			),
			'facebook' => array(
				'title' => __('Facebook', 'wpthms'),
			),
			'google-plus' => array(
				'title' => __('Share on Google+', 'wpthms'),
			),
			'linkedin' => array(
				'title' => __('Share on Linkedin', 'wpthms'),
			),
			'pinterest' => array(
				'title' => __('Pin this Post', 'wpthms'),
			),
			'tumblr' => array(
				'title' => __('Share on Tumblr', 'wpthms'),
			),
			'more' => array(
				'title' => __('More services', 'wpthms'),
			),
		);

		yt_site_social_sharing_buttons( $styles, $services, $class = 'pull-right');
	}
echo '</div>
<div class="divider meta-divider clearfix"></div>';
endif;

if( in_array( 'date',$meta_info ) || in_array( 'comments',$meta_info ) || in_array( 'likes',$meta_info ) || in_array( 'views',$meta_info ) || in_array( 'sizer',$meta_info ) ):
echo '<div class="down gray-icon clearfix">';

//echo '<div class="pull-left">';
	echo in_array( 'date',$meta_info ) ? sprintf( '<span class="post-meta-info posted-on">' . apply_filters('yt_icon_date_time', '<i class="fa fa-clock-o"></i>') . ' %1$s</span>',
		$time_string
	) : '';
	if( in_array( 'comments',$meta_info ) ){
		echo '<span class="post-meta-info with-cmt">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
			comments_popup_link( __( '0 Comments', 'wpthms' ), __( '1 Comment', 'wpthms' ), __( '% Comments', 'wpthms' ));
		echo '</span>';
	}
	
	if( in_array( 'likes',$meta_info ) && function_exists('yt_impressive_like_display') ){
		echo yt_impressive_like_display(get_the_ID(), false, 'post-meta-info');
	}
	
	if( in_array( 'views',$meta_info ) && function_exists('yt_simple_post_views_tracker_display') ){
	echo '<span class="post-meta-info post-views last-child" title="' . sprintf( __( '%d Views', 'wpthms') , yt_simple_post_views_tracker_display( get_the_ID(), false ) ) . '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
		echo number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) );
	echo '</span>';	
	}

//echo '</div>';

	if( in_array( 'sizer',$meta_info ) && function_exists( 'yt_theme_font_size_changer') && is_single() ) {
		yt_theme_font_size_changer('pull-right');
	}
echo '</div>';
endif;