<div class="post-author-area hidden-print margin-bottom-30">
    <a href="<?php echo esc_url( get_author_posts_url(get_the_author_meta( 'ID' ) ) ); ?>" class="alignleft gravatar"><?php echo get_avatar( get_the_author_meta('user_email'), '75', '' );?></a>
    <div class="author-meta-content">
        <h3 class="secondary-2-primary"><?php echo __('By','wpthms');?> <a href="<?php echo esc_url( get_author_posts_url(get_the_author_meta( 'ID' ) ) );?>"><strong><?php the_author_meta('display_name');?></strong></a></h3>
    
        <?php 
        if(get_the_author_meta('description')){
            
            echo wpautop( get_the_author_meta('description') );
            
        }
        ?>
        <p><?php _e('View all articles by ','wpthms');?><strong class="primary-2-secondary"><?php the_author_posts_link();?></strong></p>

    </div>
    
</div>

<!--/Author Info-->