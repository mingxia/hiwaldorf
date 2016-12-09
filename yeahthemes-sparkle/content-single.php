<?php
/**
 * @package wpthms
 */
$format = get_post_format();

if( false === $format )
	$format = 'standard';

$formats_meta = yt_get_post_formats_meta( get_the_ID());

/**
 *Quote format
 */
$quote_author = !empty( $formats_meta['_format_quote_source_name'] )  ? '<cite class="entry-format-meta margin-bottom-30">' . $formats_meta['_format_quote_source_name'] . '</cite>' : '';
$quote_author = !empty( $formats_meta['_format_quote_source_url'] ) ? sprintf( '<cite class="entry-format-meta margin-bottom-30"><a href="%s">%s</a></cite>', $formats_meta['_format_quote_source_url'], $formats_meta['_format_quote_source_name'] ) : $quote_author;

/**
 *Link format
 */
$share_url = !empty( $formats_meta['_format_link_url'] ) ? $formats_meta['_format_link_url'] : get_permalink( get_the_ID() );

//print_r($formats_meta);
$share_url_text = !empty( $formats_meta['_format_link_url'] )  
	? sprintf( '<p class="entry-format-meta margin-bottom-30">%s <a href="%s">#</a></p>',
		$formats_meta['_format_link_url'],
		get_permalink( get_the_ID() ) )
	: '';

/**
 *Extra class for entry title
 */
$entry_title_class = ' margin-bottom-30';
if( 'quote' === $format  && $quote_author 
	|| 'link' === $format  && $share_url_text 
){
	$entry_title_class = '';
}


$entry_title = get_the_title( get_the_ID() );
if( 'link' === $format  && $share_url_text  ){
	$entry_title = sprintf('<a href="%s" title="%s" target="_blank" rel="external" class="secondary-2-primary">%s</a>', $share_url, get_the_title( get_the_ID() ), get_the_title( get_the_ID() ) );
}

$feature_image = yt_get_options('blog_single_post_featured_image');
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'yt_before_single_post_entry_header' );?>

	<header class="entry-header">

		<?php do_action( 'yt_single_post_entry_header_start' );?>
		<?php if(function_exists( 'yt_site_post_entry_category') ) yt_site_post_entry_category();?>

		<h1 class="entry-title <?php echo $entry_title_class; ?>"><?php echo $entry_title; ?></h1>
		
		 
		<?php echo 'quote' === $format ? $quote_author : '';?>
		<?php echo 'link' === $format ? $share_url_text : '';?>

		<?php if( 'hide' !== yt_get_options( 'blog_post_meta_info_mode' )): ?>
		<div class="entry-meta margin-bottom-30 hidden-print" style="display:none">
			<span class="posted-on">
				
				<?php
					$time_string = '<time class="entry-date published ' . ( get_the_time( 'U' ) == get_the_modified_time( 'U' ) ? 'updated' : '' ) . 'pull-left" datetime="%1$s">%2$s</time>';
					if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
						$time_string .= '<time class="updated hidden" datetime="%3$s">%4$s</time>';
				
					echo sprintf( $time_string,
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() ),
						esc_attr( get_the_modified_date( 'c' ) ),
						esc_html( get_the_modified_date() )
					);
				?>
			</span>
			<span class="byline">

				<span class="author vcard">
					<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
						<?php echo esc_html( get_the_author() ); ?>
					</a>
				</span>
			</span>			
		</div><!-- .entry-meta -->
		<?php endif?>
		<?php
		/*Standard*/
		if( in_array( $format, array( 'standard', 'quote', 'link' ) ) ){
			
			if ( 'show' == $feature_image && has_post_thumbnail() && ! post_password_required() ) : ?>
			<div class="entry-thumbnail margin-bottom-30">
				<div class="post-thumbnail">
				
					<?php 
						get_template_part( 'includes/templates/post-thumbnail' );
					?>
	            </div>
				<?php
				if( ($thumb_excerpt = yt_get_thumbnail_meta( get_post_thumbnail_id( get_the_ID() ), 'post_excerpt' )) ){
					echo '<span class="entry-caption thumbnail-caption">' . $thumb_excerpt . '</span>';
				}
				//echo yt_get_post_thumbnail_meta( get_the_ID(), 'post_excerpt' ) ? yt_get_post_thumbnail_meta(null) : '';

				//print_r( yt_get_post_thumbnail_meta());
				?>
			</div>
			<?php endif;
			
			
		
		}
		elseif( 'image' === $format ){
			if ( ! post_password_required() && yt_get_the_post_format_image() ) : ?>
			<div class="entry-media margin-bottom-30">
			<?php echo yt_get_the_post_format_image(); ?>
			</div>
			<?php endif;
		}
		/*Audio*/
		elseif( 'audio' === $format ){
			if ( has_post_thumbnail() && ! post_password_required() ) : ?>
			<div class="entry-thumbnail<?php echo !yt_get_the_post_format_audio() ? ' margin-bottom-30' : ''; ?>">
				<?php the_post_thumbnail( 'post-thumbnail'); ?>
			</div>
			<?php endif;
			if ( yt_get_the_post_format_audio() && !post_password_required() ) : ?>
			<div class="entry-format-media <?php echo has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ? 'with-cover ' : ''; ?>margin-bottom-30">
				<?php echo yt_get_the_post_format_audio(); ?>
			</div>
			<?php endif;
			
		/*Gallery*/	
		}elseif( 'gallery' === $format ){
			if ( yt_get_the_post_format_gallery() && !post_password_required() ) : ?>
			<div class="entry-format-media margin-bottom-30">
				<?php echo yt_get_the_post_format_gallery(); ?>
			</div>
			<?php endif;
			
		/*Video*/
		}elseif( 'video' === $format ){
			if ( yt_get_the_post_format_video() && !post_password_required() ) : ?>
			<div class="entry-format-media margin-bottom-30">
				<?php echo yt_get_the_post_format_video(); ?>
			</div>
			<?php endif;
		}
		?>

		<?php do_action( 'yt_single_post_entry_header_end' );?>
	</header><!-- .entry-header -->

	<?php do_action( 'yt_before_single_post_entry_content' );?>

	<div class="entry-content">

		<?php do_action( 'yt_single_post_entry_content_start' );?>
		
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links pagination-nav">' . __( 'Pages:', 'wpthms' ),
				'after'  => '</div>',
				'link_before' => '<span class="page-numbers">',
				'link_after' => '</span>',
			) );
		?>

		<?php do_action( 'yt_single_post_entry_content_end' );?>

	</div><!-- .entry-content -->
	
	<?php do_action( 'yt_before_single_post_entry_footer' );?>

	<?php
	
	$tag_list = get_the_tag_list( '', '' );
	if ( $tag_list ) :
	
	?>

	<footer class="entry-meta hidden-print">
		<?php do_action( 'yt_single_post_entry_footer_start' );?>

		<?php
			$meta_text = '';
			/* translators: used between list items, there is a space after the comma */
			

			if ( '' != $tag_list ) {
				$meta_text = '<div class="entry-tags">';
				$meta_text .= __( '<strong class="tag-heading">%1$s Tags:</strong> %2$s', 'wpthms' );
				$meta_text .= '</div>';
			} 

			printf(
				$meta_text,
				apply_filters('yt_icon_tag', '<i class="fa fa-tag"></i>'),
				$tag_list
			);
		?>
		<?php do_action( 'yt_single_post_entry_footer_end' );?>
	</footer><!-- .entry-meta -->
	<?php endif;?>


	<?php do_action( 'yt_after_single_post_entry_footer' );?>
	<!-- noptimize --><script type="text/html" data-role="header .entry-meta">
	<?php 
		$metadesc_style = yt_get_options('blog_post_meta_info_mode');
		if( 'large' == $metadesc_style && function_exists( 'yt_site_impressive_post_meta_description' ) )
			yt_site_impressive_post_meta_description(); 
		elseif( 'small' == $metadesc_style && function_exists( 'yt_site_post_meta_description' ) )
			yt_site_post_meta_description(); 
	?>
	</script>
	<!-- /noptimize -->
	
</article><!-- #post-<?php the_ID(); ?>## -->