<?php
/**
 *	Plugin Name: Social Media
 *	Description: Display Posts by category using Ajax
 */
// This is required to be sure Walker_Category_Checklist class is available

class YT_Social_Media_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-social-media-widget yt-widget',
			'description' => __('Display Posts by category using Ajax', 'wpthms')
		);
		
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-social-media-widget',
		);
		
		parent::__construct( 
			'yt-social-media-widget', 
			__('(Theme) Social Media', 'wpthms'), 
			$widget_ops, 
			$control_ops);
		
		//Then make sure our options will be added in the footer
		add_action('admin_head', array( $this, 'widget_styles'), 10 );
		add_action('admin_print_footer_scripts', array( $this, 'widget_scripts'));
		add_action('wp_footer', array( $this, 'frontent_scripts') );

	}
	function widget_styles(){
		$output = '<style>
			.yt-widget-reapeatable-field{
				padding:10px;
				border:1px solid #EEE;
				background:#fafafa;
				margin-bottom:15px;
			}
			.yt-widget-reapeatable-field-image-preview{
				margin-top:10px;
				max-width:125px;
				height:auto;
			}
		</style>';


		/*Inline css :P */
		$output = str_replace(array("\r", "\n", "\t"), "", $output);

		echo $output . "\n";
		
	}

	function widget_scripts(){

		$output ='
		<script type=\'text/javascript\'>
			/* <![CDATA[ */
			;(function($) {

				"use strict";

				$(document)
				.on("click", ".yt-widget-reapeatable-field-add", function(e){
					var $el = $(this),
						field = $el.prev(".yt-widget-reapeatable-field"),
						cloneField = field.clone(false),
						parent = $el.closest(".widget-content");
					$(cloneField).find(":input").val("");
					$(cloneField).find(".yt-widget-reapeatable-field-image-preview").remove();
					$(cloneField).insertAfter( field );
					e.preventDefault();
				}).on("click", ".yt-widget-reapeatable-field-remove", function(e){
					var $el = $(this),
						parent = $el.closest(".yt-widget-reapeatable-field");

					if( parent.siblings( ".yt-widget-reapeatable-field" ).length ){
						parent.remove();
					}else{
						alert( "' . __('You need a least one block', 'wpthms') . '");
					}
					e.preventDefault();
				})
				.on( "ready", function(){

				});
			})(jQuery);
			/* ]]> */
		</script>';

		$output = str_replace(array("\r", "\n", "\t"), "", $output);

		echo $output . "\n";
	}
	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
	
		// outputs the content of the widget
		
		extract( $args );
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		$adscode 	= !empty( $instance['adscode'] ) ? $instance['adscode'] : array();
		$image 		= !empty( $instance['image'] ) ? $instance['image'] : array();
		$url 		= !empty( $instance['url'] ) ? $instance['url'] : array();
		$alt 		= !empty( $instance['alt'] ) ? $instance['alt'] : array();
		
		if( $instance['single_only'] && !is_single() )
			return;

		echo $before_widget;		
		
		//Echo widget title
		if ( $title ){ echo $before_title . $title . $after_title; }
		$output = '';
		$ads_count = count( $image );

		//die();

		//$ads_count = $ads_count ? $ads_count : 1;
		
		$output .= '<div class="yt-ads-space125-content">';

		for ($i=0; $i < $ads_count; $i++) { 


			if( !empty( $adscode[$i] ) || !empty( $image[$i] ) ){
				$output .= '<div class="yt-ads-space125-block">';

				if( !empty( $adscode[$i] ))
					$output .= $adscode[$i];
				else
					$output .= !empty( $url[$i] ) 
					? sprintf( '<a href="%s" title="%s" target="_blank"><img src="%s" alt="%s"></a>', esc_url( $url[$i] ), esc_attr( $alt[$i] ), esc_url( $image[$i] ), esc_attr( $alt[$i] ) ) 
					: sprintf( '<img src="%s" alt="%s">', esc_url( $image[$i] ), !empty( $alt[$i] ) ? esc_attr( $alt[$i] ) : '' ) ;

				$output .= '</div>';
			}
		}
		$output .= '</div>';
		echo $output;
		echo $after_widget;
	}

	/**
	 * Widget Settings
	 */
	function form( $instance ) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Advertisements','wpthms'),
			'single_only' => 0,
			'image' => array(),
			'url' => array(),
			'alt' => array(),
			'adscode' => array(),
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	    
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','wpthms')?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
	    	<input id="<?php echo $this->get_field_id( 'single_only' ); ?>" name="<?php echo $this->get_field_name( 'single_only' ); ?>" type="checkbox" <?php checked( $instance['single_only'], 'on' ); ?>/>
	    	<label for="<?php echo $this->get_field_id('single_only'); ?>"><?php _e( 'Show on single article only', 'wpthms' ); ?></label>
	    </p>

	    <p><em><?php _e('if the Ads code is empty, display image','wpthms')?></em></p>
	    

	<?php 

	$ads_count = count($instance['image']) > 1 ? count($instance['image']) : 1 ;
	//var_dump( $ads_count );
	for ($i=0; $i < $ads_count; $i++) { 
		# code...

	?>
	    <div class="yt-widget-reapeatable-field">
		    <p>
				<label for="<?php echo $this->get_field_id( 'adscode' ); ?>"><?php _e('Ads code:','wpthms')?></label>
				<textarea rows="5" name="<?php echo $this->get_field_name('adscode1'); ?>[]" class="widefat" id="<?php echo $this->get_field_id('adscode'); ?>"><?php echo !empty( $instance['adscode'][$i] ) ? esc_textarea( $instance['adscode'][$i] ) : '';?></textarea>
			</p>	
			<p>
				<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e('Image:','wpthms')?></label>
				<input id="<?php echo $this->get_field_id( 'image' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'image' ); ?>[]" type="text" value="<?php echo !empty( $instance['image'][$i] ) ? esc_url( $instance['image'][$i] ) : '';?>" />
				<?php echo !empty( $instance['image'][$i] ) ? sprintf('<img src="%s" class="yt-widget-reapeatable-field-image-preview">', $instance['image'][$i] ) : '';?>
			</p>
		    <p>
				<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('URL:','wpthms')?></label>
				<input id="<?php echo $this->get_field_id( 'url' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'url' ); ?>[]" type="text" value="<?php echo !empty( $instance['url'][$i] ) ? esc_url( $instance['url'][$i] ) : '';?>" />
			</p>
		    <p>
				<label for="<?php echo $this->get_field_id( 'alt' ); ?>"><?php _e('Alt text:','wpthms')?></label>
				<input id="<?php echo $this->get_field_id( 'alt' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'alt' ); ?>[]" type="text" value="<?php echo !empty( $instance['alt'][$i] ) ? esc_attr( $instance['alt'][$i] ) : '';?>" />
			</p>
			<a class="yt-widget-reapeatable-field-remove" href="#remove"><?php _e('Delete','wpthms')?></a>
		</div>
	<?php
	}//end for
	?>
		<span class="button yt-widget-reapeatable-field-add"><?php _e('More ads','wpthms')?></span>
		<br>
		<br>
		<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		
		// processes widget options to be saved
		$instance = $old_instance;

		//Strip tags for title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['single_only'] = $new_instance['single_only'] ? 'on' : 0;
		$instance['adscode'] = $new_instance['adscode'];
		$instance['image'] = $new_instance['image'];
		$instance['url'] = $new_instance['url'];
		$instance['alt'] = $new_instance['alt'];
		
		return $instance;
	}
}