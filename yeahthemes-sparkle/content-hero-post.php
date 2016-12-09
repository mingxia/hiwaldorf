<?php
/**
 * @package wpthms
 */

if(class_exists( 'YT_Post_Helpers') )
	YT_Post_Helpers::$listed_post[] = get_the_ID();

$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
$thumbnail = !empty( $image_attributes ) ? $image_attributes[0] : '';

?>
<div class="<?php echo esc_attr( join(' ', $hrb_class) );?>">
	<div class="hero-brick-inner">
		<article <?php post_class('hero-brick-content');?>>
			<header class="entry-header">
				<?php echo !empty( $cat_tag) ? $cat_tag : ''; ?>
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" title="%s">', esc_url( get_permalink() ), esc_attr( get_the_title() ) ), '</a></h2>', true );?>
				<div class="entry-meta">

					<span class="pull-left posted-on"><i class="fa fa-clock-o"></i> <time class="entry-date published" datetime="<?php echo esc_attr( get_the_time('c') );?>"><?php echo get_the_date();?></time>
						<?php 

						if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ){
							echo $time_string = sprintf( '<time class="updated hidden" datetime="%1$s">%2$s</time>', 
								esc_attr( get_the_modified_date( 'c' ) ),
								esc_html( get_the_modified_date() )
							);
						}else{
							echo '<time class="updated hidden" datetime="' . esc_attr( get_the_time('c') ) . '">' . get_the_date() . '</time>';
						}
						?>
					</span>
					<span class="pull-right with-cmt"><i class="fa fa-comments"></i> <?php echo comments_popup_link( __( '0', 'wpthms' ), __( '1', 'wpthms' ), __( '%', 'wpthms' ));?></span>
					<?php 
					echo sprintf( '<span class="hidden"> by %s</span>', 
						sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
							esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
							esc_attr( sprintf( __( 'View all posts by %s', 'wpthms' ), get_the_author() ) ),
							esc_html( get_the_author() )
						) 
					);
					?>
				</div>
			</header>
			<div class="entry-content">
				<?php the_excerpt( );?>
			</div>
			
			<?php 
				$template = '<div class="thumb-w" style="background-image:url(%s);"></div>';
			?>
			<div class="hero-brick-bg<?php echo ( 'mixed' == $hero_style ? esc_attr( ' goverlay-' . $overlay_color[ $color_count ] ) : '' );?>">
				
                <?php echo !empty( $thumbnail ) ? sprintf( $template, esc_attr( $thumbnail ) ) : '<div class="thumb-w"></div>' ;?>
                
            </div>
            <a href="<?php echo esc_url( get_permalink() );?>" class="hero-brick-urlo"></a>

		</article>
	</div>
</div>