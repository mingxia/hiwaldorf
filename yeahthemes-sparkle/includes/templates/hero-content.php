<?php

$data = yt_get_options();

$hero_layout 	= $data['herobanner_layout'];
$hero_style 	= $data['herobanner_style'];
$hero_effect	= $data['herobanner_effect'];
$hero_style_random = $data['herobanner_style_random'];
$hero_gap 		= $data['herobanner_gap'];
$hero_class = array();

// Class style
$hero_class[] = $hero_layout;
if( 'color' == $hero_style ){
	$hero_class[] = 'style-color-gradient';
}elseif( 'mixed' == $hero_style ){
	$hero_class[] = ' style-mixed-color-gradient';
}
$hero_class[] = "brick-align-{$data['herobanner_brickalign']}";

$hero_class[] = "gap-$hero_gap";
// overlay color
$overlay_color = array( 
	'shamrock-skyblue', 
	'cerise-yellow',
	'yellow-skyblue',
	'shamrock-yellow',
	'waikawagray-cerise',
	'pictonblue-purple'
);
$color_count = 0;

// If is randon , do shuffling an array
if( 'mixed' == $hero_style && $hero_style_random ){			
	shuffle( $overlay_color );
}

$slider_settings = apply_filters( 'yt_site_hero_banner_carousel_settings', array(
	'selector' => '.slides > li',
	//'directionNav' => false,
	'randomize' => 'true',
	'pausePlay' => false,
	'animationLoop' => false
) , 'carousel' );

$hero_posts = class_exists('YT_Site_Hero_Banner') ? YT_Site_Hero_Banner::get_hero_posts() : array();
// print_r( $hero_posts );
if ( $hero_posts ) {

	if( $data['herobanner_shuffleposts'] )
		shuffle($hero_posts);
	/*Begin site hero*/ 
	echo '<div id="site-hero" class="site-hero ' . esc_attr( join(' ', $hero_class ) ) . ' border-bottom clearfix hidden-print' . ( 'none' == $hero_effect ? '' : ' yt-loading fadeIn animated' ) . '" data-layout="'.esc_attr( $hero_layout ).'" data-style="'.esc_attr( $hero_style ).'" data-effect="'.esc_attr( $hero_effect ).'">
		<div class="container">
			<div class="site-hero-inner">';
		/*Slider wrapper for carousel*/
		if( 'carousel' == $hero_layout ){
			echo '<div class="yeahslider" data-init="false" data-settings="' . esc_attr( json_encode( $slider_settings ) ) . '">
				<ul class="slides">';
		}
	$temp_post = $GLOBALS['post'];

	$count = 0;
	/*Start the Loop*/ 
	foreach ( (array) $hero_posts as $order => $post ) :
		setup_postdata( $post );

		$count++;
		/*Push the id to array*/
		$hero_post_ids[] = $post->ID;

		if( class_exists( 'YT_Post_Helpers') )
			YT_Post_Helpers::$listed_post[] = get_the_ID();

		$hrb_class 			= array( 'hero-brick' );
		$col = '';
		$categories 		= get_the_category();
		$cat_tag 			= '';
		
		if( 'default' == $hero_layout ){
			if( 1 == $count  )
				$col = 'hero-brick-large col-md-6';
			else
				$col = 'hero-brick-small col-md-3';

		}elseif( 'symmetry_brick' == $hero_layout ){
			 if( in_array( $count, array( 1, 6 ) ) )
				$col = 'hero-brick-large col-md-6';
			else
				$col = 'hero-brick-small col-md-3';			 	
			
		}elseif( 'two_one_two' == $hero_layout ){
			
			if( 2 == $count ){
				$col = 'hero-brick-large col-md-6';
			}else{
				$col = 'hero-brick-small col-md-3';
			}

		}elseif( 'two_three' == $hero_layout ){
			
			if( in_array( $count, array(1, 2) ) ){
				$col = 'hero-brick-large col-md-6';
			}else{
				$col = 'hero-brick-small col-md-4';
			}

		}elseif('triple' == $hero_layout){
			$col = 'hero-brick-small col-md-4';
		}elseif('carousel' == $hero_layout){
			$col = 'hero-brick-small col-sm-6 col-md-3';
		}
		$hrb_class[] = $col;
		if( 'none' != $hero_effect )
			$hrb_class[]		= 'visibility-hidden';

		// Category tag
		if( !empty( $categories[0] ) && apply_filters( 'yt_site_hero_element_cat', true ) ){
			$category 	= $categories[0];
			$cat_tag  	.= '<span class="entry-category">';
			$cat_tag 	.= '<a class="cat-tag ' . esc_attr( $category->slug ) . '" href="'. esc_attr( get_category_link( $category->term_id ) ) .'" title="' . esc_attr( sprintf( __( 'View all posts in %s', 'wpthms' ), $category->name ) ) . '">'.$category->cat_name.'</a>';
			$cat_tag 	.= '</span>';
			//$class 		.= ' category-' . $category->slug;
		}

		// Carousel brick
		if( 'carousel' == $hero_layout ){
			if( 1 == $count )
				echo '<li>';
		}

		include( locate_template( 'content-hero-post.php' ) );

		// Carousel brick closing tags
		if( 'carousel' == $hero_layout ){
			
			if($count > 1 && 0 == $count % 4 && $count < count($hero_posts) ){
                //$count = 0;
                echo '</li><li>';
            }

            if( $count == count($hero_posts) ){
                echo '</li>';
            }

		}

		$color_count++;

		// Reset counter for overlay
		if( $color_count == 5 ){
			$color_count = 0;
		}

	endforeach;
	$GLOBALS['in_hero_loop'] = false;
	$GLOBALS['post'] = $temp_post;

		/* End slider wrapper for Carousel */
		if( 'carousel' == $hero_layout ){
			echo '</ul>
				</div>';
		}

	/*End site hero*/ 
		echo '</div>
		</div>
	</div>';
} else {
	// no posts found
	echo '<div id="site-hero" class="site-hero border-bottom clearfix hidden-print yt-loading fadeIn animated text-center"><h3>' . __('You need to add more than 5 posts so the hero banner can show blocks properly', 'wpthms') . '</h3></div>';
}