<?php



/**
 * VARIABLES
 *
 * optionsTrueFalse
 */
$optionsTrueFalse = array(
	'true'=> __('True', YT_SHORTCODE_DOMAIN),
	'false'=> __('False', YT_SHORTCODE_DOMAIN),
);
/**
 * optionsShowHide
 */
$optionsShowHide = array(
	'show' => __('Show',YT_SHORTCODE_DOMAIN),
	'hide' => __('Hide',YT_SHORTCODE_DOMAIN)
);
/**
 * optionsAlignment
 */
$optionsAlignment = array(
	'center' => __('Center',YT_SHORTCODE_DOMAIN),
	'left' => __('Left',YT_SHORTCODE_DOMAIN),
	'right' => __('Right',YT_SHORTCODE_DOMAIN)
);
/**
 * optionsPosition
 */
$optionsPosition = array(
	'top' => __('Top',YT_SHORTCODE_DOMAIN),
	'right' => __('Right',YT_SHORTCODE_DOMAIN),
	'bottom' => __('Bottom',YT_SHORTCODE_DOMAIN),
	'left' => __('Left',YT_SHORTCODE_DOMAIN),
);
/**
 * optionsOrderBy
 */
$optionsOrderBy = array(
	'DESC' => __('Descending',YT_SHORTCODE_DOMAIN),
	'ASC' => __('Ascending',YT_SHORTCODE_DOMAIN)
);

$optionsListIconFont = apply_filters( 'yt_shortcode_vars_iconfont', array());

if(empty($optionsListIconFont)) 
	$optionsListIconFont[''] = __('No icon found.', YT_SHORTCODE_DOMAIN);

/**
 * BEGIN DECLARING SHORTCODE ARRAY
 */
$prefix = apply_filters( 'yt_shortcode_generator_no_conflict', false ) ? 'yt_' : '' ;
/**
 * Button
 */
$yt_shortcodes['button'] = apply_filters( 'yt_shortcode_args_button', array(
			
	'name' => __('Button', YT_SHORTCODE_DOMAIN),
	'desc' => __('Insert button with various of colors, sizes...', YT_SHORTCODE_DOMAIN),
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[button url="{{url}}" postid="{{postid}}" style="{{style}}" size="{{size}}" target="{{target}}" fullwidth="{{fullwidth}}"]{{text}}[/button]',
	),
	'options' => array(
		'text' => array(
			'name' => __('Button text',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'Button',
			'type' => 'text'
		),
		'url' => array(
			'name' => __('External URL',YT_SHORTCODE_DOMAIN),
			'desc' => __('Add the button\'s url eg http://example.com',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'text'
		),
		
		'postid' => array(
			'name' => __('Internal Pages', YT_SHORTCODE_DOMAIN),
			'desc' => __('You can also link to internal pages. input page id here',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'text',
		),
		'style' => array(
			'name' => __('Button Style',YT_SHORTCODE_DOMAIN),
			'desc' => __('Select the button\'s color style',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'select',
			'options' => array(
				'btn-default'=> __('Default', YT_SHORTCODE_DOMAIN),
				'btn-primary' => __('Primary (Theme color)', YT_SHORTCODE_DOMAIN),
				'btn-success' => __('Success', YT_SHORTCODE_DOMAIN),
				'btn-info' => __('Info', YT_SHORTCODE_DOMAIN),
				'btn-warning' => __('Warning', YT_SHORTCODE_DOMAIN),
				'btn-danger' => __('Danger', YT_SHORTCODE_DOMAIN),
				'btn-link' => __('Link', YT_SHORTCODE_DOMAIN),
			)
		),
		'size' => array(
			'name' => __('Button size', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'default',
			'type' => 'select',
			'options' => array(
				'default' => __('Default', YT_SHORTCODE_DOMAIN),
				'btn-lg'=> __('Large', YT_SHORTCODE_DOMAIN),
				'btn-sm' => __('Small', YT_SHORTCODE_DOMAIN),
				'btn-xs' => __('Extra small', YT_SHORTCODE_DOMAIN),
			)
		),
		'target' => array(
			'name' => __('Button target', YT_SHORTCODE_DOMAIN),
			'desc' => __('_self = open in same window. _blank = open in new window', YT_SHORTCODE_DOMAIN),
			'std' => '_self',
			'type' => 'select',
			'options' => array(
				'_blank'=> '_blank',
				'_parent'=> '_parent',
				'_self'=> '_self',
				'_top'=> '_top',
				'new'=> 'new',
			)
		),
		'fullwidth' => array(
			'name' => __('Fullwidth button', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'false',
			'type' => 'select',
			'options' => $optionsTrueFalse
		),
		'css' => array(
			'name' => __('Custom CSS',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => '',
			'type' => 'textarea'
		)
		
	)

));	
/**
 * Alerts
 */
$yt_shortcodes['alert'] = apply_filters( 'yt_shortcode_args_alert', array(
			
	'name' => __('Alert box', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[alert style="{{style}}" close="{{close}}"]{{content}}[/alert]',
	),
	'options' => array(	
		'style' => array(
			'name' => __('Style',YT_SHORTCODE_DOMAIN),
			'desc' => __('Select the alert\'s color style',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'select',
			'options' => array(
				'alert-default'=> __('Default', YT_SHORTCODE_DOMAIN),
				'alert-success' => __('Success', YT_SHORTCODE_DOMAIN),
				'alert-info' => __('Info', YT_SHORTCODE_DOMAIN),
				'alert-warning' => __('Warning', YT_SHORTCODE_DOMAIN),
				'alert-danger' => __('Danger', YT_SHORTCODE_DOMAIN),
			)
		),
		'close' => array(
			'name' => __('Close button', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'false',
			'type' => 'select',
			'options' => $optionsTrueFalse
		),
		'content' => array(
			'name' => __('Content',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'This is an alert box!',
			'type' => 'textarea'
		),
	)

));	
/**
 * Label
 */
$yt_shortcodes['label'] = apply_filters( 'yt_shortcode_args_label', array(
			
	'name' => __('Label', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[label style="{{style}}"]{{text}}[/label]',
	),
	'options' => array(	
		'style' => array(
			'name' => __('Style',YT_SHORTCODE_DOMAIN),
			'desc' => __('Select the label\'s color style',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'select',
			'options' => array(
				'label-default'=> __('Default', YT_SHORTCODE_DOMAIN),
				'label-primary' => __('Primary', YT_SHORTCODE_DOMAIN),
				'label-success' => __('Success', YT_SHORTCODE_DOMAIN),
				'label-info' => __('Info', YT_SHORTCODE_DOMAIN),
				'label-warning' => __('Warning', YT_SHORTCODE_DOMAIN),
				'label-danger' => __('Danger', YT_SHORTCODE_DOMAIN),
			)
		),
		'text' => array(
			'name' => __('Label text',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'Label',
			'type' => 'text'
		),
	)

));	

/**
 * Progress bars
 */
$yt_shortcodes['progress_bars'] = apply_filters( 'yt_shortcode_args_progress_bars', array(
			
	'name' => __('Progress bars', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[progress_bars striped="{{striped}}" active="{{active}}"]{{content}}[/progress_bars]'
	),
	'options' => array(
		
		
		'striped' => array(
			'name' => __('Is striped', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'false',
			'type' => 'select',
			'options' => $optionsTrueFalse
		),
		'active' => array(
			'name' => __('Is active', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'false',
			'type' => 'select',
			'options' => $optionsTrueFalse
		),

		'content' => array(
			'name' => __('Allowed multiple bars in a stack ', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => '',
			'type' => 'repeatable',
			'settings' => array(
				'iconfont' => '',
				'iconimage' => '',
				'syntax' => '[progress_bar title="{{title}}" width="{{width}}" style="{{style}}"]',
				'add_more' => __('progress bar', YT_SHORTCODE_DOMAIN),
			
			),
			'options' => array(
			
				'title' => array(
					'std' => 'Text here',
					'type' => 'text',
					'name' => __('Title', YT_SHORTCODE_DOMAIN),
					'desc' => __('Add the progress bar title', YT_SHORTCODE_DOMAIN)
				),
				'width' => array(
					'std' => '50',
					'type' => 'text',
					'name' => __('Width', YT_SHORTCODE_DOMAIN),
					'desc' => __('Width of progress bar in percent (input numeric number only)', YT_SHORTCODE_DOMAIN)
				),
				'style' => array(
					'std' => '',
					'type' => 'select',
					'name' => __('Style', YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'options' => array(
						'progress-bar-primary' => __('Primary', YT_SHORTCODE_DOMAIN), 
						'progress-bar-success' => __('Success', YT_SHORTCODE_DOMAIN),
						'progress-bar-info' => __('Info', YT_SHORTCODE_DOMAIN),
						'progress-bar-warning' => __('Warning', YT_SHORTCODE_DOMAIN),
						'progress-bar-danger' => __('Danger', YT_SHORTCODE_DOMAIN),
					)
				)
			)
		),
	)
));
/**
 * Panel
 */
$yt_shortcodes['panel'] = apply_filters( 'yt_shortcode_args_panel', array(
			
	'name' => __('Panel', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[panel style="{{style}}" title="{{title}}" footer="{{footer}}"]{{content}}[/panel]'
	),
	'options' => array(	
		'style' => array(
			'name' => __('Style',YT_SHORTCODE_DOMAIN),
			'desc' => __('Select the Panel\'s color style',YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'select',
			'options' => array(
				'panel-default'=> __('Default', YT_SHORTCODE_DOMAIN),
				'panel-primary' => __('Primary', YT_SHORTCODE_DOMAIN),
				'panel-success' => __('Success', YT_SHORTCODE_DOMAIN),
				'panel-info' => __('Info', YT_SHORTCODE_DOMAIN),
				'panel-warning' => __('Warning', YT_SHORTCODE_DOMAIN),
				'panel-danger' => __('Danger', YT_SHORTCODE_DOMAIN),
			)
		),
		'title' => array(
			'name' => __('Header title',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'Panel title',
			'type' => 'text'
		),
		'content' => array(
			'name' => __('Content',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => 'Panel content',
			'type' => 'textarea'
		),
		'footer' => array(
			'name' => __('Footer',YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => '',
			'type' => 'text'
		),
	)
));
/**
 * List
 */
$yt_shortcodes['list_group'] = apply_filters( 'yt_shortcode_args_list_group', array(
			
	'name' => __('List group', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[list_group]{{content}}[/list_group]'
	),
	'options' => array(
		'content' => array(
			'name' => __('Add list items', YT_SHORTCODE_DOMAIN),
			'desc' => __('if only content is entered, simple style will be used', YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'repeatable',
			'settings' => array(
				'iconfont' => '',
				'iconimage' => '',
				'syntax' => '[list_item title="{{title}}" url="{{url}}"]{{content}}[/list_item]',
				'add_more' => __('item', YT_SHORTCODE_DOMAIN),
			
			),
			'options' => array(
			
				'title' => array(
					'std' => 'This is a list group item',
					'type' => 'text',
					'name' => __('Title (optional)', YT_SHORTCODE_DOMAIN),
					'desc' => ''
				),
				'url' => array(
					'std' => '',
					'type' => 'text',
					'name' => __('URL', YT_SHORTCODE_DOMAIN),
					'desc' => __('URL for this item (optional)', YT_SHORTCODE_DOMAIN)
				),
				'content' => array(
					'name' => __('Content',YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'std' => 'This is a list group item content',
					'type' => 'textarea'
				)
			)
		),
	)
));
/**
 * Column
 */
$yt_shortcodes['column'] = apply_filters( 'yt_shortcode_args_column', array(
			
	'name' => __('Column', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[row]{{content}}[/row]'
	),
	'options' => array(
			'intro' => array(
				'type' => 'html',
				'desc' => 'The theme use 12 columns <a href="http://getbootstrap.com/css/#grid">grid system</a> from Boostrap to make your website adapt to any screen size. You can create a basic grid system that starts out stacked on mobile devices and tablet devices (the extra small to small range) before becoming horizontal on desktop (medium) devices. For more information, <a href="http://getbootstrap.com/css/#grid-example-basic" target="blank">click here</a>'
			),
		'content' => array(
			'name' => __('Add column', YT_SHORTCODE_DOMAIN),
			'desc' => __('if no column class was specified, use default column: col-md-4', YT_SHORTCODE_DOMAIN),
			'std' => '',
			'type' => 'repeatable',
			'settings' => array(
				'iconfont' => '',
				'iconimage' => '',
				'syntax' => '[column class="{{xs}}{{sm}}{{md}}{{lg}}"]{{content}}[/column]',
				'add_more' => __('column', YT_SHORTCODE_DOMAIN),
			
			),
			'options' => array(
			
				'xs' => array(
					'std' => '',
					'type' => 'select',
					'name' => __('Extra small (<768px)', YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'options' => array(
						'' => '',
						' col-xs-1' => '1/12',
						' col-xs-2' => '2/12',
						' col-xs-3' => '3/12',
						' col-xs-4' => '4/12',
						' col-xs-5' => '5/12',
						' col-xs-6' => '6/12',
						' col-xs-7' => '7/12',
						' col-xs-8' => '8/12',
						' col-xs-9' => '9/12',
						' col-xs-10' => '10/12',
						' col-xs-11' => '11/12',
						' col-xs-12' => '12/12',
					)
				),
				'sm' => array(
					'std' => '',
					'type' => 'select',
					'name' => __('Small (≥768px)', YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'options' => array(
						'' => '',
						' col-sm-1' => '1/12',
						' col-sm-2' => '2/12',
						' col-sm-3' => '3/12',
						' col-sm-4' => '4/12',
						' col-sm-5' => '5/12',
						' col-sm-6' => '6/12',
						' col-sm-7' => '7/12',
						' col-sm-8' => '8/12',
						' col-sm-9' => '9/12',
						' col-sm-10' => '10/12',
						' col-sm-11' => '11/12',
						' col-sm-12' => '12/12',
					)
				),
				'md' => array(
					'std' => '',
					'type' => 'select',
					'name' => __('Medium (≥992px)', YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'options' => array(
						'' => '',
						' col-md-1' => '1/12',
						' col-md-2' => '2/12',
						' col-md-3' => '3/12',
						' col-md-4' => '4/12',
						' col-md-5' => '5/12',
						' col-md-6' => '6/12',
						' col-md-7' => '7/12',
						' col-md-8' => '8/12',
						' col-md-9' => '9/12',
						' col-md-10' => '10/12',
						' col-md-11' => '11/12',
						' col-md-12' => '12/12',
					)
				),
				'lg' => array(
					'std' => '',
					'type' => 'select',
					'name' => __('Large (≥1200px)', YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'options' => array(
						'' => '',
						' col-lg-1' => '1/12',
						' col-lg-2' => '2/12',
						' col-lg-3' => '3/12',
						' col-lg-4' => '4/12',
						' col-lg-5' => '5/12',
						' col-lg-6' => '6/12',
						' col-lg-7' => '7/12',
						' col-lg-8' => '8/12',
						' col-lg-9' => '9/12',
						' col-lg-10' => '10/12',
						' col-lg-11' => '11/12',
						' col-lg-12' => '12/12',
					)
				),
				'content' => array(
					'name' => __('Content',YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'std' => 'Grumpy wizards make toxic brew for the evil Queen and Jack. 1234567890',
					'type' => 'textarea'
				)
			)
		),
	)
));

/**
 * Tabs
 */
$yt_shortcodes['tabs'] = apply_filters( 'yt_shortcode_args_tabs', array(
			
	'name' => __('Tabs', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[tabs style="{{style}}"]{{content}}[/tabs]'
	),
	'options' => array(
		'style' => array(
			'type' => 'select',
			'name' => __('Style', YT_SHORTCODE_DOMAIN),
			'options' => array(
				'default' => __('Default', YT_SHORTCODE_DOMAIN),
				'pills' => __('Pills', YT_SHORTCODE_DOMAIN),
			)
		),
		'content' => array(
			'name' => __('Add tab', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => '',
			'type' => 'repeatable',
			'settings' => array(
				'iconfont' => '',
				'iconimage' => '',
				'syntax' => '[tab title="{{title}}"]{{content}}[/tab]',
				'add_more' => __('tab', YT_SHORTCODE_DOMAIN),
			
			),
			'options' => array(
				'title' => array(
					'name' => __('Tab title',YT_SHORTCODE_DOMAIN),
					'desc' => __('Title must be different to different to others',YT_SHORTCODE_DOMAIN),
					'std' => '',
					'type' => 'text'
				),
				'content' => array(
					'name' => __('Tab Content',YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'std' => 'Grumpy wizards make toxic brew for the evil Queen and Jack. 1234567890',
					'type' => 'textarea'
				)
			)
		),
	)
));

/**
 * Accordion
 */
$yt_shortcodes['accordion'] = apply_filters( 'yt_shortcode_args_accordion', array(
			
	'name' => __('Accordion', YT_SHORTCODE_DOMAIN),
	'desc' => '',
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[accordion style="{{style}}"]{{content}}[/accordion]'
	),
	'options' => array(
		'style' => array(
			'type' => 'select',
			'name' => __('Style', YT_SHORTCODE_DOMAIN),
			'options' => array(
				'default' => __('Default', YT_SHORTCODE_DOMAIN),
				'primary' => __('Primary color', YT_SHORTCODE_DOMAIN),
				'success' => __('Success', YT_SHORTCODE_DOMAIN),
				'info' => __('Info', YT_SHORTCODE_DOMAIN),
				'warning' => __('Warning', YT_SHORTCODE_DOMAIN),
				'danger' => __('Danger', YT_SHORTCODE_DOMAIN),
			)
		),
		'content' => array(
			'name' => __('Add accordion', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'std' => '',
			'type' => 'repeatable',
			'settings' => array(
				'iconfont' => '',
				'iconimage' => '',
				'syntax' => '[accordion_item title="{{title}}"]{{content}}[/accordion_item]',
				'add_more' => __('accordion', YT_SHORTCODE_DOMAIN),
			
			),
			'options' => array(
				'title' => array(
					'name' => __('Title',YT_SHORTCODE_DOMAIN),
					'desc' => __('Title must be different to different to others',YT_SHORTCODE_DOMAIN),
					'std' => '',
					'type' => 'text'
				),
				'content' => array(
					'name' => __('Content',YT_SHORTCODE_DOMAIN),
					'desc' => '',
					'std' => 'Grumpy wizards make toxic brew for the evil Queen and Jack. 1234567890',
					'type' => 'textarea'
				)
			)
		),
	)
));

$yt_shortcodes['shortcode_item_separator'] = '';
/**
 * Google Maps 
 */
$yt_shortcodes['gmap'] = apply_filters( 'yt_shortcode_args_gmap', array(
	
	'name' => __('Google Maps', YT_SHORTCODE_DOMAIN),
	'desc' => __('Google Maps', YT_SHORTCODE_DOMAIN),
	'settings' => array(
		'iconfont' => '',
		'iconimage' => '',
		'syntax' => '[gmap address="{{address}}" maptype="{{maptype}}" height="{{height}}" latitude="{{latitude}}" longitude="{{longitude}}" zoom="{{zoom}}" hue="{{hue}}" saturation="{{saturation}}" roadgeometry_lightness="{{roadgeometry_lightness}}" roadgeometry_visibility="{{roadgeometry_visibility}}" roadlabels_visibility="{{roadlabels_visibility}}" controls="{{control}}" marker="{{marker}}" marker_title="{{marker_title}}" imageurl="{{imageurl}}" imageid="{{imageid}}" markerw="{{markerw}}" markerh="{{markerh}}" anchorh="{{anchorh}}" anchorv="{{anchorv}}" popup="{{popup}}"]{{html}}[/gmap]'
	),
	'options' => array(
		'address' => array(
			'name' => __('Address', YT_SHORTCODE_DOMAIN),
			'desc' => __('Enter your address (e.g: Melbourne Avenue, Birmingham, United Kingdom) ', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
		),
		'latitude' => array(
			'name' => __('Latitude', YT_SHORTCODE_DOMAIN),
			'desc' => __('Enter Latitude <a href="http://9webtools.com/find-my-latitude-and-longitude/" target="_blank">Find your latitude,longitude</a>', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '0',
		),
		'longitude' => array(
			'name' => __('Longitude', YT_SHORTCODE_DOMAIN),
			'desc' => __('Enter Longitude', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '0',
		),
		'maptype' => array(
			'name' => __('Select the Maptype', YT_SHORTCODE_DOMAIN),
			'desc' => __('HYBRID, TERRAIN, SATELLITE, ROADMAP', YT_SHORTCODE_DOMAIN),
			'type' => 'select',
			'options' => array(
				'ROADMAP' => __('Road Map',YT_SHORTCODE_DOMAIN),
				'TERRAIN' => __('Terrain',YT_SHORTCODE_DOMAIN),
				'HYBRID' => __('Hybrid',YT_SHORTCODE_DOMAIN),
				'SATELLITE' => __('Satellite',YT_SHORTCODE_DOMAIN)
			)
		),
		'height' => array(
			'name' => __('Height', YT_SHORTCODE_DOMAIN),
			'desc' => __('default is 300) ', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
		),
		'html' => array(
			'name' => __('HTML', YT_SHORTCODE_DOMAIN),
			'desc' => __('Short description in the Marker popup (e.g: My Hometown) ', YT_SHORTCODE_DOMAIN),
			'type' => 'textarea',
			'std' => __('My Hometown',YT_SHORTCODE_DOMAIN),
		),
		'zoom' => array(
			'type' => 'select_alt',
			'std' => '15',
			'options' => array('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22'),
			'name' => __('Zoom level', YT_SHORTCODE_DOMAIN),
			'desc' => __('Zoom	(default : 15)) ', YT_SHORTCODE_DOMAIN),
		),
		'hue' => array(
			'type' => 'colorpicker',
			'std' => '',
			'name' => __('Map Hue', YT_SHORTCODE_DOMAIN),
			'desc' => __('Style your map by changing the hue color to any color (hex color start with #) Eg: Your main color of your site : #33B3D3', YT_SHORTCODE_DOMAIN),
		),
		'saturation' => array(
			'name' => __('Map saturation', YT_SHORTCODE_DOMAIN),
			'desc' => __('Alter the saturation of your map. Range (grayscale) -100 - 0 - 100 (color)', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '0',
		),
		'roadgeometry_lightness' => array(
			'name' => __('Road geometry lightness', YT_SHORTCODE_DOMAIN),
			'desc' => __('Alter the saturation of your map. Range (dark) -100 - 0 - 100 (light)', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '0',
		),
		'roadgeometry_visibility' => array(
			'type' => 'select',
			'options' => array(
				'on',
				'simplified',
				'off'
			),
			'std' => 'on',
			'name' => __('Road geometry visibility', YT_SHORTCODE_DOMAIN),
			'desc' => ''
		),
		'roadlabels_visibility' => array(
			'name' => __('Road labels visibility', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'type' => 'select',
			'options' => array(
				'on',
				'simplified',
				'off'
			),
			'std' => 'on',
		),
		
		/*'style' => array(
			'type' => 'textarea',
			'std' => '0',
			'name' => __('Map Style', YT_SHORTCODE_DOMAIN),
			'desc' => __('Customize your map style (colors of road, water, transit, ...ect) at <a href="http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html" target="_blank"> and paste the generated JSON data here', YT_SHORTCODE_DOMAIN)
		),*/
		'control' => array(
			'type' => 'select',
			'name' => __('Show Control', YT_SHORTCODE_DOMAIN),
			'options' => $optionsTrueFalse
		),
		'marker' => array(
			'type' => 'select',
			'name' => __('Show Marker', YT_SHORTCODE_DOMAIN),
			'options' => $optionsTrueFalse
		),
		'marker_title' => array(
			'name' => __('Marker title', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'type' => 'text',
		),
		'imageid' => array(
			'name' => __('Internal Image', YT_SHORTCODE_DOMAIN),
			'desc' => __('Upload Map pointer ', YT_SHORTCODE_DOMAIN),
			'type' => 'media',
			'settings' => array(
				'media_by' => 'id'
			)
		),
		'imageurl' => array(
			'name' => __('External Image url', YT_SHORTCODE_DOMAIN),
			'desc' => __('Map pointer url that you want to display on map', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
		),
		'markerw' => array(
			'name' => __('Marker width', YT_SHORTCODE_DOMAIN),
			'desc' => __('Width of marker in px', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '',
		),
		'markerh' => array(
			'name' => __('Marker height', YT_SHORTCODE_DOMAIN),
			'desc' => __('Height of marker in px', YT_SHORTCODE_DOMAIN),
			'type' => 'text',
			'std' => '',
		),
		'anchorh' => array(
			'name' => __('Marker Anchor Horizontal position', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'type' => 'text',
			'std' => '',
		),
		'anchorv' => array(
			'name' => __('Marker Anchor Vertical position', YT_SHORTCODE_DOMAIN),
			'desc' => '',
			'type' => 'text',
			'std' => '',
		),
		'popup' => array(
			'name' => __('Show Popup', YT_SHORTCODE_DOMAIN),
			'type' => 'select',
			'std'	=> 'false',
			'options' => $optionsTrueFalse,
		)
	)
) );


$yt_shortcodes['shortcode_item_separator'] = '';