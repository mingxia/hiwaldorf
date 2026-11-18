<header class="page-header archive-title hidden-print">
	<h1 class="page-title">
		<?php
			if ( is_category() ) :
				single_cat_title();

			elseif ( is_tag() ) :
				esc_html_e('Tag: ', 'yeahthemes-sparkle');
				single_tag_title();

			elseif ( is_author() ) :
				/* Queue the first post, that way we know
				 * what author we're dealing with (if that is the case).
				*/
				the_post();
				printf( esc_html__( 'Author: %s', 'yeahthemes-sparkle' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
				/* Since we called the_post() above, we need to
				 * rewind the loop back to the beginning that way
				 * we can run the loop properly, in full.
				 */
				rewind_posts();

			elseif ( is_day() ) :
				printf( esc_html__( 'Day: %s', 'yeahthemes-sparkle' ), '<span>' . get_the_date() . '</span>' );

			elseif ( is_month() ) :
				printf( esc_html__( 'Month: %s', 'yeahthemes-sparkle' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

			elseif ( is_year() ) :
				printf( esc_html__( 'Year: %s', 'yeahthemes-sparkle' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

			elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
				esc_html_e( 'Asides', 'yeahthemes-sparkle' );

			elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
				esc_html_e( 'Images', 'yeahthemes-sparkle');

			elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
				esc_html_e( 'Videos', 'yeahthemes-sparkle' );

			elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
				esc_html_e( 'Quotes', 'yeahthemes-sparkle' );

			elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
				esc_html_e( 'Links', 'yeahthemes-sparkle' );

			else :
				esc_html_e( 'Archives', 'yeahthemes-sparkle' );

			endif;
		?>
	</h1>
<?php

	// Show an optional term description.
	$term_description = term_description();
	if ( ! empty( $term_description ) ) :
		printf( '<div class="taxonomy-description">%s</div>', $term_description );
	endif;
	
	
	if( is_category()){
		$category = get_category( get_query_var( 'cat' ) );
			$cat_id = $category->cat_ID;
		$args = array(
			'hierarchical' => 0,
			'child_of' => $cat_id,
			'title_li' => '',
			'show_option_none' => ''
		);
		$children = get_categories(array('child_of' => $cat_id,'hide_empty' => 0));
		if (count($children) > 1){
			echo '<div class="slashes-navigation"><ul class="list-inline descendants-cats gray-2-primary">';
			wp_list_categories( $args );
			echo '</ul></div>';
		//has childern
		}

	}elseif( is_author( )){
		if(get_the_author_meta('description')){
			echo '<a href="'; the_author_meta('user_url'); echo '" class="alignleft gravatar">',get_avatar( get_the_author_meta('user_email'), '75', '' ),'</a>';
			echo wpautop( get_the_author_meta('description') );
			
		}
	}
?>
</header><!-- .page-header -->