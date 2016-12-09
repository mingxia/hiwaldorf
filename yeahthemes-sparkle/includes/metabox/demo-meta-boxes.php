<?php

/**
 * Sample Functions
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */

add_filter( 'yt_meta_boxes', 'yt_rita_metabox' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function yt_rita_metabox( $meta_boxes ) {

	// Start with an underscore to hide fields from custom fields list
	$shortname = 'yt_';
	$url =  wpthms_FRAMEWORK_URI . 'admin/assets/images/';	
	
	//Background Images Reader
	$bg_images_path = get_template_directory() . '/images/bg/'; // change this to where you store your bg images
	$bg_images_url = get_template_directory_uri() . '/images/bg/'; // change this to where you store your bg images
	
	
	$bg_images = array();
	
	if ( is_dir($bg_images_path) ) {
		if ($bg_images_dir = opendir($bg_images_path) ) { 
			while ( ($bg_images_file = readdir($bg_images_dir)) !== false ) {
				if(stristr($bg_images_file, '.png') !== false || stristr($bg_images_file, '.jpg') !== false) {
					$bg_images[] = $bg_images_url . $bg_images_file;
				}
			}    
		}
	}
	
	$meta_boxes[] = array(
		'id'         => 'test_metabox',
		'title'      => 'Test Metabox',
		'pages'      => array( 'post', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			
			//Text
			array( 
				'name' => 'Text',
				'desc' => 'Your description about this setting',
				'id' => $shortname . 'text',
				'std' => 'Your default text',
				'type' => 'text',
				'settings' => array(
					
					'sanitize' => 'esc_url',
				)
			),
			
			//Textarea
			array( 
				'name' => 'Textarea',
				'desc' => 'Your description about this setting',
				'id' => $shortname . 'textarea',
				'std' => 'Your default textarea content',
				'type' => 'textarea'
			),
			
			//Checkbox
			array( 
				'name' => 'Checkbox',
				'desc' => 'Your description about this setting',
				'label' => 'Label for this checkbox',
				'id' => $shortname . 'checkbox',
				'std' => 1,
				'type' => 'checkbox',
				'settings' => array(
					
					'folds' => '0',
				)
			),
			
			array( 
				'type' => 'tab',
				'name' => 'Colorpicker',
				
			),
		)
	);
	
	return $meta_boxes;
}
	