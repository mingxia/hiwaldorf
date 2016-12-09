<?php
/**
 *	Plugin Name: Ads 125
 *	Description: Advertising blocks
 */
class YT_Ads125_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-ads125-widget yt-widget',
			'description' => esc_html__('A set of 125px (width) advertisements', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-ads125-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-ads125-widget', 
			esc_html__('(Theme) Ads space 125px', 'wpthms'), 
			$widget_ops, 
			$control_ops);
	
		//Then make sure our options will be added in the footer
		add_action('admin_head', array( $this, 'widget_styles'), 10 );
	}

	function widget_styles(){
		echo str_replace(array("\r", "\n", "\t"), "", '<style>
			.yt-widget-reapeatable-field-image-preview{
				margin-top:10px;
				max-width:125px;
				height:auto;
			}
		</style>');
		
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
		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		$output = '';
		$ads_count = count( $image );

		//die();

		//$ads_count = $ads_count ? $ads_count : 1;
		
		$output .= '';

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
		echo !empty( $output ) ? sprintf( '<div class="yt-ads-space125-content">%s</div>', $output ) : '';
		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */
	function form( $instance ) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__('Advertisements','wpthms'),
			'single_only' => 0,
			'image' => array(),
			'url' => array(),
			'alt' => array(),
			'adscode' => array(),
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
	    
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'single_only' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'single_only' ) ); ?>" type="checkbox" <?php checked( $instance['single_only'], 'on' ); ?>/>
	    	<label for="<?php echo esc_attr( $this->get_field_id('single_only') ); ?>"><?php esc_html_e( 'Show on single article only', 'wpthms' ); ?></label>
	    </p>

	    <p><em><?php esc_html_e('if the Ads code is empty, display image','wpthms')?></em></p>
	    
	
	<div class="yt-widget-reapeatable-field-wrapper">
	<?php 

	$ads_count = count($instance['image']) > 1 ? count($instance['image']) : 1 ;
	//var_dump( $ads_count );
	for ($i=0; $i < $ads_count; $i++) { 
		# code...

	?>
		<div class="widget yt-widget-reapeatable-field">
			<div class="widget-top">
				<div class="widget-title-action">
					<a class="widget-action hide-if-no-js" href="#available-widgets"></a>
					<a class="widget-control-edit hide-if-js" href="javascript:return void(0);">
						<span class="edit"><?php echo esc_html_x( 'Edit', 'widget', 'wpthms' ); ?></span>
						<span class="add"><?php echo esc_html_x( 'Add', 'widget', 'wpthms' ); ?></span>
						<span class="screen-reader-text"><?php echo esc_html( $widget->name ); ?></span>
					</a>
				</div>
				<div class="widget-title"><h4><?php echo esc_html_x( 'Ad Block', 'widget', 'wpthms' ); ?><span class="in-widget-title"></span></h4></div>
			</div>

			<div class="widget-inside">
				<div class="widget-content">
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'adscode' ) ); ?>"><?php esc_html_e('Ads code:','wpthms')?></label>
						<textarea rows="5" name="<?php echo esc_attr( $this->get_field_name('adscode') ); ?>[]" class="widefat" id="<?php echo esc_attr( $this->get_field_id('adscode') ); ?>"><?php echo !empty( $instance['adscode'][$i] ) ? esc_textarea( $instance['adscode'][$i] ) : '';?></textarea>
					</p>	
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_html_e('Image:','wpthms')?></label>
						<input id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>[]" type="text" value="<?php echo !empty( $instance['image'][$i] ) ? esc_url( $instance['image'][$i] ) : '';?>" />
						<?php echo !empty( $instance['image'][$i] ) ? sprintf('<img src="%s" class="yt-widget-reapeatable-field-image-preview">', esc_url( $instance['image'][$i] ) ) : '';?>
					</p>
				    <p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"><?php esc_html_e('URL:','wpthms')?></label>
						<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>[]" type="text" value="<?php echo !empty( $instance['url'][$i] ) ? esc_url( $instance['url'][$i] ) : '';?>" />
					</p>
				    <p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>"><?php esc_html_e('Alt text:','wpthms')?></label>
						<input id="<?php echo esc_attr( $this->get_field_id( 'alt' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'alt' ) ); ?>[]" type="text" value="<?php echo !empty( $instance['alt'][$i] ) ? esc_attr( $instance['alt'][$i] ) : '';?>" />
					</p>
					<a class="yt-widget-reapeatable-field-remove" href="#remove"><?php esc_html_e('Delete','wpthms')?></a>
				</div>	
			</div>
		</div>	
			   
			
	<?php
	}//end for
	?>
	</div>
	<span class="button yt-widget-reapeatable-field-add"><?php esc_html_e('More ads','wpthms')?></span>
	<br />

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