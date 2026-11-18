<?php
/**
 * Compact author information for single articles.
 * Preserves WordPress author URL, biography and avatar.
 */
$hiw_author_id   = (int) get_the_author_meta( 'ID' );
$hiw_author_name = get_the_author_meta( 'display_name' );
$hiw_author_url  = get_author_posts_url( $hiw_author_id );
$hiw_author_bio  = get_the_author_meta( 'description' );
?>
<section class="post-author-area hiwaldorf-author-card hidden-print" aria-label="发布者信息">
    <a class="hiwaldorf-author-avatar" href="<?php echo esc_url( $hiw_author_url ); ?>" aria-label="<?php echo esc_attr( '查看' . $hiw_author_name . '的文章' ); ?>">
        <?php echo get_avatar( $hiw_author_id, 56, '', $hiw_author_name, array( 'class' => 'hiwaldorf-author-image' ) ); ?>
    </a>
    <div class="hiwaldorf-author-content">
        <div class="hiwaldorf-author-heading">
            <span class="hiwaldorf-author-label">发布者</span>
            <a class="hiwaldorf-author-name" href="<?php echo esc_url( $hiw_author_url ); ?>" rel="author"><?php echo esc_html( $hiw_author_name ); ?></a>
        </div>
        <?php if ( $hiw_author_bio ) : ?>
            <div class="hiwaldorf-author-bio"><?php echo wpautop( esc_html( $hiw_author_bio ) ); ?></div>
        <?php endif; ?>
        <a class="hiwaldorf-author-all-posts" href="<?php echo esc_url( $hiw_author_url ); ?>">查看全部文章 <span aria-hidden="true">→</span></a>
    </div>
</section>
