<?php
global $post;

$backup_post = $post;

if( !empty( $post )){
    $post_id = $post->ID;
}else{
    $post_id = 0;
}

$tags = wp_get_post_tags( $post_id );
if ($tags):

    $tag_ids = array();  
    foreach( $tags as $tag )
        $tag_ids[] = $tag->term_id; 

    $args = array(  
        'tag__in' => $tag_ids,  
        'post__not_in' => array( $post_id ),  
        'posts_per_page'=> 5 //number of posts to display
    );       

    if(class_exists( 'YT_Post_Helpers') && !empty( YT_Post_Helpers::$listed_post ) && apply_filters( 'yt_avoid_duplicated_posts', false ) ){
        $args['post__not_in'] = array_merge( $args['post__not_in'],  (array) YT_Post_Helpers::$listed_post );
    }
    $related_articles = get_posts( apply_filters( 'yt_site_single_post_related_articles_query', $args, $post_id ) );
    if( $related_articles ):?>
    <div class="entry-stuff margin-bottom-30 related-articles hidden-print secondary-2-primary">
        <h3 class="related-articles-title"><?php _e( 'Related Articles', 'wpthms'); ?></h3>
        <ul class="list-unstyled list-with-separator post-list-with-format-icon">
        <?php
        foreach ( $related_articles as $post ) : 
            setup_postdata( $post ); 

            $format = get_post_format();
            if ( false === $format ) {
                $format = 'standard';
            }
            if(class_exists( 'YT_Post_Helpers') )
                YT_Post_Helpers::$listed_post[] = get_the_ID();
            ?>
            <li class="<?php echo esc_attr( "format-$format" );?>"><a href="<?php echo esc_attr( get_permalink() );?>" class="post-title" title="<?php echo esc_attr( get_the_title() );?>"><i class="fa fa-file-text-o "></i> <?php the_title();?></a> - <time class="entry-date published" datetime="<?php echo esc_attr( get_the_time('c') ); ?>"><?php echo get_the_date();?></time></li>
        <?php endforeach;?>

        </ul>
    </div>
    <?php
    endif;
    wp_reset_postdata();
endif;

$post = $backup_post;