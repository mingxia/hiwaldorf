<?php
/**
 * Yeah Shortcode function
 *
 * @author		wpthms
 * @copyright	GPL & GNU 2.0 +
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	Yeah shortcodes
 */

/*
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Returns current plugin version.
 * 
 * @return string Plugin version
 */

if ( ! function_exists( 'yt_shortcode_unautop' ) ) {
	function yt_shortcode_unautop( $content ) {
		$content = do_shortcode( shortcode_unautop( $content ) );
		$content = preg_replace( '#^<\/p>|^<br \/>|<p>$#', '', $content );
		return $content;
	} 
}

/**
 * Helper for Parsing shortcode atts
 *
 * @since 1.0
 */
if( !function_exists( 'yt_shortcode_parse_shortcode_atts' )){
	function yt_shortcode_parse_shortcode_atts( $content ) {
	    $content = preg_match_all( '/([^ ]*)=(\'([^\']*)\'|\"([^\"]*)\"|([^ ]*))/', trim( $content ), $c );
	    list( $dummy, $keys, $values ) = array_values( $c );
	    $c = array();
	    foreach ( $keys as $key => $value ) {
	        $value = trim( $values[ $key ], "\"'" );
	        $type = is_numeric( $value ) ? 'int' : 'string';
	        $type = in_array( strtolower( $value ), array( 'true', 'false' ) ) ? 'bool' : $type;
	        switch ( $type ) {
	            case 'int': $value = (int) $value; break;
	            case 'bool': $value = strtolower( $value ) == 'true'; break;
	        }
	        $c[ $keys[ $key ] ] = $value;
	    }
	    return $c;
	}
}
/**
 * Helper for Parsing shortcode to array
 *
 * @since 1.0
 */
if( !function_exists( 'yt_shortcode_parse_shortcode_to_array' )){
	function yt_shortcode_parse_shortcode_to_array( $content = '', $multilevel = false ){

		$array = array();

		$pattern = get_shortcode_regex();
		preg_match_all( '/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER );

		//print_r( $matches ); die();
		if( empty( $matches ) )
			return $array; 

		foreach ($matches as $key => $value) {
			# code...
			$array[$key]['tag'] = $value[2];
			$array[$key]['atts'] = yt_shortcode_parse_shortcode_atts( $value[3] );
			$array[$key]['content'] = $multilevel && false !== strpos( $value[5], '[' ) ? yt_shortcode_parse_shortcode_to_array( $value[5] ) : $value[5];
		}

		return $array;	
	}

}

add_action( 'init', 'yt_adding_shortcodes' );

if ( ! function_exists( 'yt_adding_shortcodes' ) ) {

	function yt_adding_shortcodes(){
		$shortcodes = array(
			'button' => 'yt_shortcode_button',
			'alert' => 'yt_shortcode_alert',
			'label' => 'yt_shortcode_label',
			'progress_bars' => 'yt_shortcode_progress_bars',
			'progress_bar' => 'yt_shortcode_progress_bar',
			'list_group' => 'yt_shortcode_list_group',
			'list_item' => 'yt_shortcode_list_item',
			'panel' => 'yt_shortcode_panel',
			'row' => 'yt_shortcode_row',
			'column' => 'yt_shortcode_column',
			'gmap' => 'yt_shortcode_gmap',

			'tabs' => 'yt_shortcode_tabs_wrapper',
			'tab' => 'yt_shortcode_tab_item',

			'accordion' => 'yt_shortcode_accordion_wrapper',
			'accordion_item' => 'yt_shortcode_accordion_item'	

		);
		$prefix = apply_filters( 'yt_shortcode_generator_no_conflict', false ) ? 'yt_' : '' ;
		foreach ($shortcodes as $key => $value) {
			if( function_exists( $value ) && is_callable( $value ) )
				add_shortcode( $prefix . $key, $value );
		}

		if( function_exists( 'yt_theme_shortcode_post_list' ) )
			add_shortcode( 'post_list', 'yt_theme_shortcode_post_list' );
	}

}


if ( ! function_exists( 'yt_shortcode_button' ) ) {
/**
 * Button
 */
	function yt_shortcode_button( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'title'		=> $content,
			'url' 		=> '#',
			'postid' 	=> '',

			'style' 	=> 'btn-default',
			'size' 		=> '',
			'fullwidth' => 'false',
			'class'		=> '',

			'target' 	=> '_self',
			'rel'		=> '',
			'css'		=> ''
			
		), $atts));

		//print_r( $atts);
		
		// Class
		$classes = array();

		$classes[] = 'btn';
		$classes[] = $style;

		if( 'true' == $fullwidth )
			$classes[] = 'btn-block';

		if( $size )
			$classes[] = $size;

		if( $class )
			$classes = array_merge( $classes, explode( ' ', $class ) );

		// Attributes
		$attributes = '';
		$attributes .= $title ? ' title="' . esc_attr( $title ) . '"' : '';
		$attributes .= $target ? ' target="' . esc_attr( $target ) . '"' : '';
		$attributes .= $rel ? ' rel="' . esc_attr( $rel ) . '"' : '';
		$attributes .= $css ? ' style="' . esc_attr( $css ) . '"' : '';

		// URL
		if( is_numeric( $postid ) && get_permalink( $postid ) )
			$url = get_permalink( $postid );
		
		$output = sprintf('<a href="%s" class="%s"%s>%s</a>', 
			esc_url( $url ),
			join( ' ', array_unique( $classes ) ),
			$attributes,
			$content );

	   	return apply_filters( 'yt_shortcode_button_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_alert' ) ) {
/**
 * Alerts
 */
	function yt_shortcode_alert( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'style' 	=> 'alert-default',
			'close' => 'false',
			
		), $atts));

		$output = '<div class="yt-shortcode-alert alert ' . esc_attr( $style ) . '">';
		$output .= 'true' == $close ? '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' : '';
			$output .= yt_shortcode_unautop( $content );
		$output .= '</div>';

		return apply_filters( 'yt_shortcode_alert_html', $output, $atts );
	}	

}


if ( ! function_exists( 'yt_shortcode_label' ) ) {
/**
 * Labels
 */
	function yt_shortcode_label( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'style' 	=> 'label-default',
			'close' => 'false',
			
		), $atts));

		$output = '<span class="yt-shortcode-label label ' . esc_attr( $style ) . '">';
			$output .= strip_tags( $content );
		$output .= '</span>';

		return apply_filters( 'yt_shortcode_label_html', $output, $atts );
	}	

}


if ( ! function_exists( 'yt_shortcode_progress_bars' ) ) {	
/**
 * Progress bars
 * 
 * Container
 * Bars
 */
	function yt_shortcode_progress_bars( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'striped' => 'false',
			'active' => 'false',
			
		), $atts));

		$output = sprintf( '<div class="yt-shortcode-progress progress %s%s">', ( 'true' == $striped ? ' progress-striped' : ''), ( 'true' == $active ? ' active' : '' ) );
			$output .= yt_shortcode_unautop( $content );
		$output .= '</div>';

		return apply_filters( 'yt_shortcode_progress_bars_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_progress_bar' ) ) {	

	function yt_shortcode_progress_bar( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'title' => '',
			'width' => '50',
			'style' => 'progress-bar-primary',
			
		), $atts));

		$style = 'progress-bar-primary' == $style ? '' : $style;
		$width = '' == $width ? 10 : $width;
		$output = '<div class="progress-bar ' . esc_attr( $style ) . '" style="width:' . esc_attr( intval( $width ) ) .'%;">';
			$output .= $title ? $title : '';
		$output .= '</div>';

		return apply_filters( 'yt_shortcode_progress_bar_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_list_group' ) ) {
		
/**
 * List group
 * 
 * Container
 * Items
 */
	function yt_shortcode_list_group( $atts, $content = null ) {
		
		if( !$content )
			return '';

		$output = '<div class="yt-shortcode-list-group list-group">';
			$output .= yt_shortcode_unautop( $content );
		$output .= '</div>';

		return apply_filters( 'yt_shortcode_list_group_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_list_item' ) ) {

	function yt_shortcode_list_item( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'title' => '',
			'type' => 'simple',
			'url' => '50',
			
		), $atts));

		$item_tag = '' !== $url ? 'a' : 'span';
		$link = '' !== $url ? sprintf( ' href="%s"', esc_url( $url ) ) : '';

		$output = '<div class="list-group">';
			$output .= yt_shortcode_unautop( $content );
		$output .= '</div>';

		$output = "<$item_tag class=\"list-group-item\"$link>";
		$output .= $title ? "<h4 class=\"list-group-item-heading\">$title</h4>" : '';
		$output .= $title ? wpautop( $content ) : $content;
		$output .= "</$item_tag>";

		return apply_filters( 'yt_shortcode_list_item_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_panel' ) ) {
/**
 * Panel
 */
	function yt_shortcode_panel( $atts, $content = null ) {

		extract( shortcode_atts( array(

			'title' => '',
			'style' => 'panel-default',
			'footer' => '',
			
		), $atts));

		$output = '<div class="yt-shortcode-panel panel ' . esc_attr( $style ) . '">';
			$output .= $title ? '<div class="panel-heading"><h3 class="panel-title">' . $title . '</h3></div>' : '';
			// Body
			$output .= '<div class="panel-body">';
				$output .= yt_shortcode_unautop( ( false !== strpos( $content, PHP_EOL ) ? wpautop( $content ) : $content ) );
			$output .= '</div>';

			$output .= $footer ? '<div class="panel-footer">' . $footer . '</div>' : '';

		$output .= '</div>';

		return apply_filters( 'yt_shortcode_panel_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_row' ) ) {
/**
 * Row
 */
	function yt_shortcode_row( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'id'	=> '',
			'class' => '',
		), $atts));

		$output = sprintf('<div id="%s" class="yt-shortcode-row row%s">', esc_attr( $id ), esc_attr( $class ) );
			$output .= yt_shortcode_unautop( $content );

		$output .= '</div>';

		return apply_filters( 'yt_shortcode_row_html', $output, $atts );
	}
}


if ( ! function_exists( 'yt_shortcode_column' ) ) {

/**
 * Column
 */
	function yt_shortcode_column( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'id'	=> '',
			'class' => 'col-md-4',
		), $atts));

		$class = '' == $class ? 'col-md-4' : $class;

		$output = sprintf( '<div id="%s" class="yt-shortcode-column %s">',  esc_attr( $id ), esc_attr( $class ) );

			$output .= yt_shortcode_unautop( $content );

		$output .= '</div>';

		return apply_filters( 'yt_shortcode_column_html', $output, $atts );
	}

}


if ( ! function_exists( 'yt_shortcode_gmap' ) ) {

	/**
	 * Google Map
	 */
	function yt_shortcode_gmap( $atts, $content = null ) {

		extract(shortcode_atts(array(
			'width' => '100%',
			'height' => '300px',
			'maptype' => 'ROADMAP' ,//HYBRID, TERRAIN, SATELLITE, ROADMAP
			'address' => '',
			'controls' => 'false',

			'zoom' => 15,
			'latitude' => 0,
			'longitude' => 0,
			'style' => '',
		  	'imageurl' => '',
			'imageid' => '',
			'popup' => 'false',
			
			'marker' => 'false',
			'marker_title' => '',
			'markerw' => 0,
			'markerh' => 0,
			'anchorh' => 0,
			'anchorv' => 0,

			'hue' => '',
			'saturation' => 0,
			'roadgeometry_lightness' => 0,
			'roadgeometry_visibility' => 'on',
			'roadlabels_visibility' => 'on',
	   	), $atts));

	   	$width = '' == $width ? '100%' : $width;
	   	$height = '' == $height ? '300px' : $height;
	   	$maptype = '' == $maptype ? 'ROADMAP' : $maptype;

		$controls_selection = '';
		$marker_selection = '';
		$image_selection = '';
		
		$hex_color = '(([a-fA-F0-9]){3}){1,2}$';
		if($hue){
			if ( preg_match( '/^' . $hex_color . '/i', $hue ) ) // Value is just 123abc, so prepend #.
				$hue = '#' . $hue;
		}
	   
		$image_url = $imageurl;

		if($imageid && is_numeric($imageid)){
			$imageid_url = wp_get_attachment_image_src($imageid,'full', true);
			$image_url = $imageid_url[0];
		}

		$output = '';

		static $id = 0;
		$id++;

		$map_data = array(
			'id'				=> "yt-google-map-canvas-$id",
			'width'				=> $width,
			'height'			=> $height,
			'address'			=> $address,
			'latitude'			=> ( float ) $latitude,
			'longitude'			=> ( float ) $longitude,
			'mapTypeId' 		=> $maptype,
			'zoom'				=> ( int ) $zoom,
			'icon' 				=> $image_url,
			'disableDefaultUI' 	=> $controls == 'true' ? false : true,

			'marker'			=> 'true' == $marker ? true : false,
			'markerTitle' 		=> $marker_title,
			'markerw' 			=> ( float ) $markerw,
			'markerh' 			=> ( float ) $markerh,
			'anchorh'			=> ( float ) $anchorh,
			'anchorv'			=> ( float ) $anchorv,
			'popup'				=> 'true' == $popup ? true : false,
			'infoWindow' 		=> $content,
			/**
			 * Customize yourself:
			 * https://developers.google.com/maps/documentation/javascript/examples/maptype-styled-complex
			 */
			'styles' => array(
				array(
					'stylers' => array(
						array( 
							'hue'=> $hue,
						),
						array( 

							'saturation' => $saturation
						)
					)
				),
				array(
					'featureType' 	=> 'road',
					'elementType' 	=> 'geometry',
					'stylers' 		=> array(
						array( 
							'visibility'=> $roadgeometry_visibility,
						),
						array( 
							
							'lightness' => $roadgeometry_lightness
						)
					)
				),
				array(
					'featureType' => 'road',
					'elementType' => 'labels',
					'stylers' => array(
						array( 
							'visibility'=> $roadlabels_visibility,
						)
					)
				)
			)

		);

		wp_enqueue_script( 'gmap-api' );

		$output = sprintf( '<div class="yt-google-map" id="%s" data-settings="%s"%s></div>',
			esc_attr( "yt-google-map-canvas-$id" ),
			esc_attr( json_encode( $map_data ) ),
			' style="' . esc_attr( $style ) . "width:$width;height:$height;" . '"'
		);

		return apply_filters( 'yt_shortcode_gmap_html', $output, $atts );
	}


}


if ( ! function_exists( 'yt_shortcode_tabs_wrapper' ) ) {
/**
 * Tabs
 */

	function yt_shortcode_tabs_wrapper( $atts, $content ){
		extract(shortcode_atts(array(
			'style' => 'default', //pills
	   	), $atts));

		$output = '';
		
		$titles_html = array();
		$panes_html = array();
		
		$tabs = yt_shortcode_parse_shortcode_to_array( $content );
		
		if( empty( $tabs ))
			return $output;

		$prefix = uniqid();

		$i = 0;
		foreach ($tabs as $k => $tab_shortcode) {
			if( !empty( $tab_shortcode['tag'] ) && 'tab' == $tab_shortcode['tag'] ){
				$i++;

				if( !empty( $tab_shortcode['atts']['title'] ) && !empty( $tab_shortcode['content'] ) ){
					$e_id = esc_attr( sanitize_html_class( $prefix . '-' . $tab_shortcode['atts']['title'] ) );
					$titles_html[] = '<li role="presentation"' . ( ( $i == 1 ) ? ' class="active"' : '') . '><a href="#' . $e_id . '" aria-controls="' . $e_id . '" role="tab" data-toggle="tab">' . esc_html( $tab_shortcode['atts']['title'] ) . '</a></li>';
					$panes_html[] = '<div role="tabpanel" class="tab-pane' . ( ( $i == 1 ) ? ' active' : '') . '" id="' . $e_id . '">' . wpautop( $tab_shortcode['content'] ) . '</div>';
		
				}
			}
		}

		$tabs_style = 'pills' == $style ? 'nav-pills' : 'nav-tabs' ;


		$output = '<div role="tabpanel" class="yt-shortcode-tabs">
		  <!-- Nav tabs -->
		  <ul class="nav ' . esc_attr( $tabs_style ) . '" role="tablist">' . implode( "\n", $titles_html ) . '</ul>

		  <!-- Tab panes -->
		  <div class="tab-content">' . implode( "\n", $panes_html ) . '</div>
		</div>';

		return apply_filters( 'yt_shortcode_tabs_html', $output, $atts );
		
	}

}


if ( ! function_exists( 'yt_shortcode_tab_item' ) ) {

	function yt_shortcode_tab_item( $atts, $content ){
		
		$html = $content ;
		
		return $html;
	}

}


if ( ! function_exists( 'yt_shortcode_accordion_wrapper' ) ) {


/**
 * Accordion
 */

	function yt_shortcode_accordion_wrapper( $atts, $content ){
		extract(shortcode_atts(array(
			'style' => 'default', //pills
	   	), $atts));



		$output = '';
		
		$titles_html = array();
		$panes_html = array();
		
		$tabs = yt_shortcode_parse_shortcode_to_array( $content );
		
		if( empty( $tabs ))
			return $output;
		
		
		$prefix = uniqid();
		$acc_style = in_array( $style, array( 'default', 'primary', 'success', 'info', 'warning', 'danger') ) ? "panel-{$style}" : 'panel-default' ;

		
		$i = 0;
		foreach ($tabs as $k => $tab_shortcode) {
			if( !empty( $tab_shortcode['tag'] ) && 'accordion_item' == $tab_shortcode['tag'] ){
				$i++;

				if( !empty( $tab_shortcode['atts']['title'] ) && !empty( $tab_shortcode['content'] ) ){
					$e_id = esc_attr( sanitize_html_class( $prefix . '-' . $tab_shortcode['atts']['title'] ) );
					
					$panes_html[] = '<div class="panel ' . esc_attr( $acc_style ) . '">
				<div class="panel-heading" role="tab" id="heading-' . $e_id . '">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion-' . esc_attr( sanitize_html_class( $prefix ) ) . '" href="#' . $e_id . '" aria-expanded="' . ( ( $i + 1 == 1 ) ? 'true' : 'false') . '" aria-controls="' . $e_id . '">' . esc_html( $tab_shortcode['atts']['title'] ) . '</a>
					</h4>
				</div>
				<div id="' . $e_id . '" class="panel-collapse collapse' . ( ( $i == 1 ) ? ' in' : '') . '" role="tabpanel" aria-labelledby="heading-' . $e_id . '">
					<div class="panel-body">' . wpautop( $tab_shortcode['content'] ) . '</div>
				</div>
			</div>';
		
				}
			}
		}

		$output = '<div id="accordion-' . esc_attr( sanitize_html_class( $prefix ) ) . '" class="panel-group yt-shortcode-accordion" role="tablist" aria-multiselectable="true">
			' . implode( "\n", $panes_html ) . '
		</div>';

		return apply_filters( 'yt_shortcode_accordion_html', $output, $atts );
		
	}

}


if ( ! function_exists( 'yt_shortcode_accordion_item' ) ) {

	function yt_shortcode_accordion_item( $atts, $content ){
		
		$html = $content;
		
		return $html;
	}

}