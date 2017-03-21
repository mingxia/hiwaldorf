<?php
$id = get_the_ID();
$meta_info = yt_get_options('blog_post_meta_info');
$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
	$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';
$time_string = sprintf( $time_string,
	esc_attr( get_the_date( 'c' ) ),
	esc_html( get_the_date() ),
	esc_attr( get_the_modified_date( 'c' ) ),
	esc_html( get_the_modified_date() )
);
//echo $time_string;

$sharrre_template = '<strong class="shares-counter display-block primary-color" data-role="count">%s</strong><small class="display-block">' . _x('shares', 'Social sharing', 'wpthms') .'</small>';
$sharrre_template = '';
if( is_single() && in_array( 'share_buttons',$meta_info ) ):


?>
<div class="sparkle-sharrre-counter sharrre-counter shares-counter display-inline-block text-center">
	<strong class="shares-count display-block primary-color" data-service="facebook" data-url="<?php echo esc_attr( apply_filters( 'yt_post_share_counter_url' , get_permalink( $id ) ) );?>">0</strong> <small class="share-text display-block" data-singular="<?php echo esc_attr_x( 'Share', 'singular', 'wpthms');?>"><?php esc_html_e('Shares', 'wpthms')?></small>
</div>

<div class="sparkle-sharrre-with-entry-meta display-inline-block">
	<?php
		$styles = apply_filters( 'yt_site_social_sharing_services_styles', array(
			'style' => 'color',
			'size'	=> 'large'
		), 'style2' );

		yt_site_social_sharing_buttons( $styles, array(), $class = 'sparkle-sharrre-buttons');
	?>

	<?php
endif;

	echo '<div class="gray-icon clearfix">';

		if( in_array( 'author',$meta_info ) ){
			$byline = '<span class="author vcard"><a class="url fn n" href="' 
				. esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' 
				. get_avatar( get_the_author_meta( 'ID' ), 32 ) . ' ' 
				. esc_html( get_the_author() )
				. '</a></span>';

			// Author
			echo sprintf( '<span class="post-meta-info byline">%s</span>', $byline );
		}
		
		echo in_array( 'date',$meta_info ) ? sprintf( '<span class="post-meta-info posted-on">' . apply_filters('yt_icon_date_time', '<i class="fa fa-clock-o"></i>') . ' %1$s</span>',
				$time_string
			) : '';
		if( in_array( 'comments',$meta_info ) ){
			echo '<span class="post-meta-info with-cmt">' . apply_filters('yt_icon_comment', '<i class="fa fa-comments"></i>') . ' ';
			
				comments_number( '0', '1', '%' );
			echo '</span>';
		}
		
		if( in_array( 'likes',$meta_info ) && function_exists('yt_impressive_like_display') ){
			echo yt_impressive_like_display(get_the_ID(), false, 'post-meta-info hidden-xs hidden-sm');
		}
		
		if( in_array( 'views',$meta_info ) && function_exists('yt_simple_post_views_tracker_display') ){
		echo '<span class="post-meta-info post-views last-child" title="' . sprintf( __( '%d', 'wpthms') , number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) ) ) . '">' . apply_filters('yt_icon_postviews', '<i class="fa fa-eye"></i>') . ' ';
			echo number_format( yt_simple_post_views_tracker_display( get_the_ID(), false ) );
		echo '</span>';	
		}
	echo '</div>';

if( is_single() && in_array( 'share_buttons',$meta_info ) ):
	?>
</div>
<?php
endif;