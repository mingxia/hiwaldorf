<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package wpthms
 */
$page_title = get_post_meta( get_the_ID(), 'yt_page_title', true );
$page_content = get_post_meta( get_the_ID(), 'yt_page_content', true );

if( 'hide' !== $page_content ):
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if( 'hide' !== $page_title ): ?>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header><!-- .entry-header -->
	<?php endif; ?>
	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links pagination-nav">' . __( 'Pages:', 'wpthms' ),
				'after'  => '</div>',
				'link_before' => '<span class="page-numbers">',
				'link_after' => '</span>',
			) );
		?>
	</div><!-- .entry-content -->
	<?php edit_post_link( __( '—Edit', 'wpthms' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
</article><!-- #post-## -->
<?php
endif;