<?php
/**
 * yt_post_formats_controls_metabox
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */
if( is_admin() )
	add_filter( 'yt_meta_boxes', 'yt_post_formats_controls_metabox' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
if( !function_exists( 'yt_post_formats_controls_metabox' ) ) {
	function yt_post_formats_controls_metabox( $meta_boxes ) {
	
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
		
		
		/*Pull the post format to array*/
		$supported_post_formats = array();
		
		if ( current_theme_supports( 'post-formats' ) ) {
			
			$supported_post_formats[] = 'standard';
			
			$post_formats = get_theme_support( 'post-formats' );
			
			if ( is_array( $post_formats[0] ) ) {
				$supported_post_formats = array_merge( $supported_post_formats, $post_formats[0] );
				
			}
		
			$post_format_controls = array(); 
			
			/*standard*/
			$format_standard = apply_filters( 'yt_post_format_settings_metabox_standard', array(
				array( 
					'type' => 'tab',
					'name' =>  'Standard',
					'settings' => array(
						'cookie' => 0
					)
				),
				array( 
					'type' => 'html',
					'class' => 'yt-widefat-section',
					'std' => 'Standard format allows you to add post thumbnail image, you can set via <a href="#postimagediv" onClick="wpthms.Helper.triggering(\'#postimagediv #set-post-thumbnail\', \'click\')">Featured image</a>',
				)
			));
			/*image*/
			$format_image = apply_filters( 'yt_post_format_settings_metabox_image', array(
				array( 
					'type' => 'tab',
					'name' => 'Image'
				),
				array( 
					'name' =>  'Image',
					'desc' => 'Upload an image from media uploader or input external image URL.',
					'id' => '_format_image',
					'std' => '',
					'type' => 'media'
				),
				array( 
					'name' =>  'Image URL',
					'desc' => 'URL of above image.',
					'id' => '_format_url',
					'std' => '',
					'type' => 'text',
					'settings' => array(
						'sanitize' => 'esc_url',
					)
				)
			));
			/*gallery*/
			$format_gallery = apply_filters( 'yt_post_format_settings_metabox_gallery', array(
			
				array( 
					'type' => 'tab',
					'name' => 'Gallery'
					
				),
				array( 
					'name' =>  'Gallery',
					'desc' => 'Click "Add image", Hold Ctrl/Command + click to select multiple files.<br>To reorder items, drag and drop to reorder.',
					'id' => '_format_gallery',
					'std' => '',
					'type' => 'gallery'
				)
			));
			/*audio*/
			$format_audio = apply_filters( 'yt_post_format_settings_metabox_audio', array(
				array( 
					'type' => 'tab',
					'name' => 'Audio'
					
				),
				
				array( 
					'type' => 'html',
					'class' => 'yt-widefat-section',
					'std' => 'Audio format allows you to add a track cover (for direct URL only), you can set via <a href="#postimagediv" href="#postimagediv" onClick="wpthms.Helper.triggering(\'#postimagediv #set-post-thumbnail\', \'click\')">Featured image</a>',
				),
				array( 
					'name' => 'Audio URL (oEmbed)',
					'desc' => 'Input your internal audio file or paste an external audio file here (Allowed formats: MP3, WAV, WMA, OGG)',
					'id' => '_format_audio_embed',
					'std' => '',
					'type' => 'oembed'
				)
			));
			/*video*/
			$format_video = apply_filters( 'yt_post_format_settings_metabox_video', array(
				array( 
					'type' => 'tab',
					'name' => 'Video'
					
				),
				array( 
					'name' => 'Video URL (oEmbed)',
					'desc' => 'Input your internal video file or paste an external video file here (Allowed formats: MP4, WebM).<br>What Sites Can I Embed From? <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">checkout</a>.',
					'id' => '_format_video_embed',
					'std' => '',
					'type' => 'oembed'
				)
			));
			/*quote*/
			$format_quote = apply_filters( 'yt_post_format_settings_metabox_quote', array(
				array( 
					'type' => 'tab',
					'name' => 'Quote'
					
				),
				array( 
					'name' => 'Source Name',
					'desc' => 'Author of quote',
					'id' => '_format_quote_source_name',
					'std' => '',
					'type' => 'text',
				),
				array( 
					'name' => 'Source URL',
					'desc' => 'URL of Where the quote was taken',
					'id' => '_format_quote_source_url',
					'std' => '',
					'type' => 'text',
					'settings' => array(
						'sanitize' => 'esc_url',
					)
				)
			));
			/*link*/
			$format_link = apply_filters( 'yt_post_format_settings_metabox_link', array(
				array( 
					'type' => 'tab',
					'name' => 'Link'
				),
				array( 
					'name' => 'URL',
					'desc' => 'Your URL your want to share',
					'id' => '_format_link_url',
					'std' => '',
					'type' => 'text',
					'settings' => array(
						'sanitize' => 'esc_url',
					)
				)
			));
			/*chat*/
			$format_chat = apply_filters( 'yt_post_format_settings_metabox_chat', array(
				array( 
					'type' => 'tab',
					'name' => 'Chat'
				)
			));
			/*status*/
			$format_status = apply_filters( 'yt_post_format_settings_metabox_status', array(
				array( 
					'type' => 'tab',
					'name' => 'Status',
					'settings' => array(
						'attr' => 'data-format="status"'
					)
				)
			));
			/*aside*/
			$format_aside = apply_filters( 'yt_post_format_settings_metabox_aside', array(
				array( 
					'type' => 'tab',
					'name' => 'Aside'
				),
				array( 
					'type' => 'html',
					'class' => 'yt-widefat-section',
					'std' => 'Use your editor to compose aside post',
				)
			));
			foreach( $supported_post_formats as $post_format){
				$post_format_controls = array_merge(	 $post_format_controls,  ${'format_' . $post_format}  );
			}
			
			$meta_boxes[] = array(
				'id'         => 'yt_post_format_settings_metabox',
				'title'      => 'Post Format Settings',
				'pages'      => array( 'post', ), // Post type
				'context'    => 'normal',
				'priority'   => 'high',
				'show_names' => true, // Show field names on the left
				'fields'     => $post_format_controls
			);
		
		}
		return $meta_boxes;
	}	
}