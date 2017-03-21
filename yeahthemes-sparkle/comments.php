<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to yt_comment() which is
 * located in the inc/template-tags.php file.
 *
 * @package wpthms
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

	<div id="comments" class="comments-area hidden-print">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title pull-left">
			<?php
				printf( _nx( 'One comment', '%1$s Comments', get_comments_number(), 'comments title', 'wpthms' ),
					number_format_i18n( get_comments_number() ));
			?>
		</h3>
		
		<p class="write-comment-link pull-right"><a href="#respond"><?php echo apply_filters('yt_icon_write_comment', '<i class="fa fa-pencil"></i>') . ' ' . __( 'Leave a reply &rarr;', 'wpthms' ) ;?></a></p>
		
		<div class="clearfix"></div>
		
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation padding-bottom-15 border-bottom clearfix" role="navigation">
			<h3 class="screen-reader-text sr-only"><?php _e( 'Comment navigation', 'wpthms' ); ?></h3>
			<div class="nav-previous pull-left"><?php previous_comments_link( __( '&larr; Older Comments', 'wpthms' ) ); ?></div>
			<div class="nav-next pull-right"><?php next_comments_link( __( 'Newer Comments &rarr;', 'wpthms' ) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation ?>

		<ol class="comment-list">
			<?php
				/* Loop through and list the comments. Tell wp_list_comments()
				 * to use yt_comment() to format the comments.
				 * If you want to overload this in a child theme then you can
				 * define yt_comment() and that will be used instead.
				 * See yt_comment() in inc/template-tags.php for more.
				 */
				wp_list_comments( array( 'callback' => 'yt_comment', 'walker' => new YT_Walker_Comment(), ) );
			?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="comment-navigation margin-top-15 margin-bottom-15 clearfix" role="navigation">
			<h3 class="screen-reader-text sr-only"><?php _e( 'Comment navigation', 'wpthms' ); ?></h3>
			<div class="nav-previous pull-left"><?php previous_comments_link( __( '&larr; Older Comments', 'wpthms' ) ); ?></div>
			<div class="nav-next pull-right"><?php next_comments_link( __( 'Newer Comments &rarr;', 'wpthms' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // have_comments() ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php _e( 'Comments are closed.', 'wpthms' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
