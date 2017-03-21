<?php
$posts_query = $el_class = $args = $my_query = $speed = $swiper_options = '';
$content = $link = $thumb_size = $link_target = $slides_per_view = $wrap = '';
$autoplay = $hide_pagination_control = $hide_prev_next_buttons = $title = '';
$posts = array();
extract(shortcode_atts(array(
    'title' => '',
    'posts_query' => '',
    'speed' => '5000',
    'slides_per_view' => '4',
    'autoplay' => 'no',
    'hide_pagination_control' => '',
    'hide_prev_next_buttons' => '',
    'wrap' => '',
    'el_class' => '',
    
), $atts));
list($args, $my_query) = vc_build_loop_query($posts_query); //
$el_class = $this->getExtraClass($el_class);

wp_enqueue_style('flexslider');
wp_enqueue_script('flexslider');

$slider_settings = apply_filters( 'yt_vc_carousel_settings', array(
    'selector' => '.slides > li',
    'slideshow' => 'yes' == $autoplay ? true : false,
    'directionNav' => 'yes' == $hide_prev_next_buttons ? false : true,
    'controlNav' =>  'yes' == $hide_pagination_control ? false : true,
    'pausePlay' => false,
    'animationLoop' => false,
    'slideshowSpeed' => intval( $speed ),
    'animationLoop' => 'yes' == $wrap ? true : false,
) , 'carousel' );
?>  
<div class="yt-vc-element yt-vc-carousel row<?php echo esc_attr( $el_class )?>">
    <?php echo wpb_widget_title( array('title' => $title, 'extraclass' => 'yt-vc-element-heading col-sm-12') );?>
    <div class="yeahslider secondary-2-primary clearfix" data-settings="<?php echo esc_attr( json_encode( $slider_settings ) );?>">
        <ul class="slides post-list post-list-with-thumbnail horizontal">
            <?php
            $counter = $counter_reset = 0;
            $col;
            if( $slides_per_view == '2')
                $col = '6';
            elseif( $slides_per_view == '3' )
                $col = '4';
            elseif( $slides_per_view == '6' )
                $col = '2';
            else
                $col = '3';
            
            while ( $my_query->have_posts() ):
                $my_query->the_post(); // Get post from query
            $counter++;
            $counter_reset++;

            
            $extra_classes = "col-xs-6 col-sm-6 col-md-$col margin-bottom-15";
            $extra_classes .= 3 == $counter_reset ? 'clear-left-xs clear-left-sm' : $extra_classes;

            $categories = get_the_category();
            $cat_tag            = '';

            if( !empty( $categories[0] ) ){
                $category   = $categories[0];
                $cat_tag    .= '<span class="cat-tag ' . esc_attr( $category->slug ) . '">'.$category->cat_name.'</span>';
            }

            if( $counter == 1)
                echo '<li class="post-with-large-thumbnail">';
            ?>
            
                <article <?php post_class( $extra_classes );?>>
                    <?php if( has_post_thumbnail() && get_the_post_thumbnail() && ! post_password_required() ): ?>
                    <div class="post-thumb large">
                        <?php echo $cat_tag;?>
                        <a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php esc_attr( get_the_title() ); ?>"><?php the_post_thumbnail('medium'); ?></a>
                        <?php 
                            if (function_exists('wp_review_show_total') ) {
                                $review_type = get_post_meta( $my_query->post->ID, 'wp_review_type', true );
                                    if( 'star' !== $review_type )
                                        wp_review_show_total(true, 'review-total-only review-mark'); 
                            }
                        ?>
                    </div>
                    <?php endif;?>
                    
                    <time class="entry-date published" datetime="<?php echo esc_attr( get_the_time('c') ); ?>"><?php echo get_the_date();?></time>
                    <strong><a href="<?php echo esc_attr( get_permalink() );?>" rel="bookmark"><?php the_title();?></a></strong>
                    <?php 
                    if (function_exists('wp_review_show_total') ) {
                        $review_type = get_post_meta( $my_query->post->ID, 'wp_review_type', true );
                            if( 'star' == $review_type )
                                wp_review_show_total(true, 'review-total-only review-mark'); 
                    }
                    ?>
                </article>

            <?php
            if( $slides_per_view == $counter_reset ){
                $counter_reset = 0;
            }
            if( $counter > 1 && 0 == $counter % $slides_per_view && $counter < $my_query->post_count ){

                echo '</li><li class="post-with-large-thumbnail">';
            }

            if( $counter == $my_query->post_count ){
                echo '</li>';
            }
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div>
</div>
<?php return; ?>