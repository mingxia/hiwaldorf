<?php
/* This file is not called from WordPress. We don't like that. */
! defined( 'ABSPATH' ) and exit;

/**
 * wpthms Theme Options Admin functions
 *
 * @package     WordPress
 * @subpackage  framework/admin/includes
 * @since       1.0.0
 * @author      wpthms
 */
if( is_admin() ){
	add_filter( 'ytto_before_save_option_data', 'yt_valid_options_before_save' );
	add_filter( 'ytto_options_panel_header_info', 'yt_theme_options_header_menu_link' );
	add_filter( 'ytto_option_panel_social_network', 'yt_theme_options_social_network' );
	add_filter( 'admin_footer_text', 'yt_footer_admin_credits');
	add_filter( 'update_footer', 'yt_framework_version', 15, 1 );
	add_filter( 'yt_admin_motion', '__return_false' );
	
	add_filter( 'yt_options_framework_vars', 'yt_options_metabox_option_js_vars');
	add_filter( 'yt_metaboxes_vars', 'yt_options_metabox_option_js_vars');
}
add_filter( 'wp_die_handler', 'yt_die_handler');



if ( ! function_exists( 'yt_get_theme_option' ) ) {
	
	function yt_get_theme_option( $id = null, $default = '' ) {

		$data = apply_filters( 'yt_before_return_theme_options_data' , get_option( wpthms_THEME_OPTIONS ) );

		return isset( $data[$id] ) ? $data[$id] : $default;
	}
}
/**
 * Get theme options from the database and process them with the load filter hook.
 *
 * _yt_get_options
 * yt_get_options
 *
 * @since 1.0
 * @return array
 */
if ( ! function_exists( '_yt_get_options' ) ) {
	
	function _yt_get_options( $id = null, $default = '' ) {
		
		$output;
		
		$data = apply_filters( 'yt_before_return_theme_options_data' , get_option( wpthms_THEME_OPTIONS ) );
		
		if( $id != null ){
			
			$output = isset( $data[$id] ) ? $data[$id] : $default;
				
		}else{
			
			$output = $data ? $data : array();
		
		}
		
		return $output;
	}
}
/**
 * For use in themes
 *
 * @since forever
 */

//print_r( $GLOBALS['yt_data'] );

if ( ! function_exists( 'yt_get_options' ) ) {
	
	function yt_get_options( $id = null, $default = '' ) {
		
		//global $wp_customize;
		
		$data = array();

		$output;
		
		/* Allow wp_customize to get the data real time*/
		//if ( isset( $wp_customize ) ) {
			
		$data = _yt_get_options();
			
		//}else{
		
			//$data = $yt_data;
		//}
		//print_r($yt_data);
		
		if( $id != null ){
			
			$output = isset( $data[$id] ) ? $data[$id] : $default;
				
		}else{
			
			$output = $data;
			
		}
		
		return $output;
	}
}
/**
 * Options data via Global variable: only return after_setup_theme
 */
add_action( 'after_setup_theme', create_function( '', '$GLOBALS["yt_data"] = yt_get_options();' ), 3 );
/**
 * Validate data field
 *
 * @param     mixed		$data before save
 * @param     string     $tye
 * @param     string     $field_id
 * @return    mixed
 *
 * @access    public
 * @since     1.0
 */
if ( ! function_exists( 'yt_validate_field' ) ) {

	function yt_validate_field( $data, $func = '', $args = '' ) {
		
		if ( !$data || !$func )
			return $data;
		
		$validated_data = 	$data;

		if( !is_callable( $func ) )
			return $data;
		
		/**
		 * if args exist for sanitize type, use call_user_func_array
		 */
		if( isset( $args ) && $args ){
		
			$validated_data = call_user_func_array( $func, array( $data, $args ) );
		
		/**
		 * else, us call_user_func
		 */	
		}else{
			
			$validated_data = call_user_func( $func, $data );
			
		}
		
		return $validated_data;
		
	}

}

/**
 * Helper function for validating data
 *
 * @param     mixed		$data
 * @param     array		$field 
 * @param     string		$filter_key key name for validating data by adding filter (post meta or theme options)
 * @return    mixed
 *
 * @access    public 
 * @since     1.0
 */

if ( ! function_exists( 'yt_validate_field_data' ) ) {
	
	function yt_validate_field_data( $data, $field, $filter_key = 'option') {
		
		if( isset( $field['settings']['sanitize'] ) && false == $field['settings']['sanitize'] )
			return $data;
		
		if( isset( $field['settings']['sanitize'] ) && $field['settings']['sanitize'] && is_callable( $field['settings']['sanitize'] ) ){
				
			$sanitize_args = !empty( $field['settings']['sanitize_args'] ) ? $field['settings']['sanitize_args'] : '';
				
			$data = yt_validate_field( $data , $field['settings']['sanitize'], $sanitize_args );
		
		}else{
			
			switch( $field['type'] ){
				
				case 'background_options':
				case 'border':
				case 'typography':
				
					
					if( is_array( $field['std'] ) && isset( $field['std']['color'] ) ){
						
						$data['color'] = !empty( $data['color'] ) ? yt_validate_field( yt_valid_hex_color( $data['color'] ), 'sanitize_text_field' ) : '';
						
					}
					
				break;

				case 'multicheck':
					if( is_array( $data ) )
						$data = array_filter( $data );
				break;
				case 'colorpicker':
						
					$data = !empty( $data ) ? yt_validate_field( yt_valid_hex_color( $data ), 'sanitize_text_field' ) : '';
					
				break;
					
				case 'media':
				
					$data = !empty( $data ) ? yt_validate_field( $data, 'sanitize_text_field' ) : '';
					
				break;
					
				case 'repeatable_field':
					
					foreach( (array) $data as $c_k => $c_v ){
						
						foreach( (array) $field['options'] as $cfield => $vfield){
							
							if( in_array( $vfield['type'], array( 'text', 'textarea', 'media' ) ) ){
							
								$data[$c_k][$vfield['id']] = !empty( $data[$c_k][$vfield['id']] ) ? yt_validate_field_data( $data[$c_k][$vfield['id']], $vfield ) : ''; 
							}
							
						}
						
					}
				
				break;
			
				case 'text':
				
					$allowed_tags = array(
						'strong' => array(),
						'b' => array(),
						'em' => array(),
						'i' => array()
					);	
					
					$data = !empty( $data ) ? yt_validate_field( $data , 'wp_kses', $allowed_tags ) : '';
				
				break;
					
				case 'textarea':
					$temp = $data;
					if ( current_user_can( 'unfiltered_html' ) ) {
						$data = $temp;
					}else {
						$data = !empty( $data ) ? yt_validate_field( $data, 'wp_kses_post' ) : '';
					}
					
				break;

				case 'wysiwyg':
					$temp = $data;
					if ( current_user_can( 'unfiltered_html' ) ) {
						$data = $temp;
					}else {
						global $allowedtags;
						$data = wpautop(wp_kses( $temp, $allowedtags));
					}
				break;
				/**
				 * Since 1.0.3
				 */
				case 'group_options':
					$temp = $data;
					foreach( (array) $temp as $c_k => $c_v ){
						
						if( !empty( $field['options'] )){
							foreach( (array) $field['options'] as $cfield => $vfield){
								
								if( $c_k == $vfield['id'] ){
									$data[$c_k] = !empty( $data[$c_k] ) ? yt_validate_field_data( $data[$c_k], $vfield ) : ''; 
									
								}
								
							}
						}
					}


				break;
				
				default:
					/* Allow custom field type to validate value via filter */
					$data = apply_filters( sanitize_key( 'yt_validate_' . $filter_key . '_' . $field['type'] . '_type' ), $data, $field );
			
			}
			
		}
		
		return $data;
	}
}

/**
 * Validate untrusted data before saving to database
 *
 * @param     mixed		$data before save
 * @return    mixed
 *
 * @access    public 
 * @since     1.0
 */
 
if ( ! function_exists( 'yt_valid_options_before_save' ) ) {

	function yt_valid_options_before_save( $data ) {
		
		global $yt_options;
		
		/* Important! If you delete these lines, your sky will fall on your head :p */
		if( !is_array( $data ) ){
			return $data;	
		}
		
		if( !function_exists( 'yt_validate_field_data' ) ){
			return $data;	
		}
		
		foreach ( $yt_options as $option ) {
			
		
			if( isset( $option['id'] ) && !empty( $data[$option['id']] ) ){
				
				$data[$option['id']] = yt_validate_field_data( $data[$option['id']], $option, $filter_key = 'option');
				
			}
			
		}
			
		return $data;

	}
}

/**
 * Filter to add std values for theme options default array ( Use for resetting options )
 *
 * @param     array		$defaults
 * @return    string		$field
 *
 * @access    public 
 * @since     1.0
 */ 

//add_filter( 'ytto_option_default_data', 'yt_options_defaults_filter', 10, 2 );

if ( ! function_exists( 'yt_options_defaults_filter' ) ) {

	function yt_options_defaults_filter( $defaults, $field ){
		//$org_data = yt_get_options();
		//die( print_r($org_data));
		
		if( isset( $field['id'] ) ){
			
			if( $field['type'] == 'repeatable_field' ){
				
				if( !isset( $field['std'] ) || ( isset( $field['std'] ) && !$field['std'] ) ){
					
					$std_temp = array();
					
					foreach( ( array ) $field['options'] as $coption ){
							
						if( isset( $coption['id'] ) ){
						
							$std_temp[0][$coption['id']] = isset( $coption['std'] ) && $coption['std'] ? $coption['std'] : '';  
							
						}
							
					}
					
					$defaults[$field['id']] = $std_temp;
						
				}
				
			}elseif( $field['type'] == 'media' ){
			
				$defaults[$field['id']] = $field['std'] && isset( $field['std'] ) ? $field['std'] : '';
				
			}elseif( in_array( $field['type'], array( 'typography', 'background_options', 'border' ) ) ){
				
				foreach( ( array ) $field['std'] as $std_k => $std_v ){
					
					$defaults[$field['id']][$std_k] = $std_v ? $std_v : '';
					
				}
				
				//$defaults[$field['id']]['color'] = is_array( $field['std'] ) && isset( $field['std']['color'] ) ? $field['std']['color'] : '';
			}
				
		}
		
		return $defaults;
	}
}

/**
 * Theme info
 *
 * @param     array		$list
 * @return    array		$filisteld
 *
 * @access    public 
 * @since     1.0
 */
 

if ( ! function_exists( 'yt_theme_options_header_menu_link' ) ) {
	
	function yt_theme_options_header_menu_link( $list) {
		
		$themedata = wp_get_theme();
		$themename = yt_clean_string( $themedata->Name, '_', '-' );
		
		$list[] = esc_html__('Author: ','wpthms') . THEMEAUTHOR;
		$list[] = esc_html__('Version: ','wpthms') . THEMEVERSION;
		
		return $list;
	}

}

/**
 * Social network info
 *
 * @param     array		$list
 * @return    array		$list
 *
 * @access    public 
 * @since     1.0
 */
 


if ( ! function_exists( 'yt_theme_options_social_network' ) ) {
	
	function yt_theme_options_social_network( $list) {
		
		$list[] = '<a href="https://' . 'www.facebook.com/wpthms"><i class="fa fa-facebook-square"></i>Facebook</a>';
		//$list[] = '<iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fwpthms&amp;send=false&amp;layout=button_count&amp;width=150&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=like&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:80px; height:20px;" allowTransparency="true"></iframe>';
		$list[] = '<a href="https://' . 'twitter.com/wpthms"><i class="fa fa-twitter"></i>@wpthms</a>';
		
		return $list;
	}
	
}

/**
 * wp-admin footer text creadit
 *
 * @return    string		
 *
 * @access    public 
 * @since     1.0
 */
 


if ( ! function_exists( 'yt_footer_admin_credits' ) ) {
	
	function yt_footer_admin_credits () {
		$themedata = wp_get_theme();
		
		$themename = $themedata->Name;
		$themeDescription = $themedata->Description;
		$themeURI = $themedata->get( 'ThemeURI' );
		$version = $themedata->Version;
		$author = $themedata->Author;
		

		echo sprintf( esc_html__('Thank you for creating with %s | %s', 'wpthms' ),
			'<a href="http://wordpress.org" target="_blank">WordPress</a>',
			sprintf( '<a href="%s" target="_blank">%s</a> - %s', esc_url( $themeURI ), esc_html( $themename ), esc_html( $themeDescription ) . ' ' . esc_html__('by', 'wpthms') ) . ' ' . $author
		);
	}

}

/**
 * Admin footer credits
 *
 * @access public
 * @since 1.0
 */


if( !function_exists( 'yt_framework_version' ) ) {
	function yt_framework_version( $type ){
		return $type . ' - YeahFramework v' . wpthms_FRAMEWORK_VERSION ;
	}
}
/**
 *
 * yt_die_handler
 *
 **/



if ( ! function_exists( 'yt_die_handler' ) ) {
	function yt_die_handler() {
		
		return apply_filters( 'yt_die_handler', 'yt_custom_die_handler' );
		
	}
}
/**
 *
 * Function used as real die handler
 *
 * @param message - message for the error page
 * @param title - title of the error page
 * @param args - additional params
 *
 * @return null
 *
 **/
if ( ! function_exists( 'yt_custom_die_handler' ) ) {
	function yt_custom_die_handler( $message, $title = '', $args = array() ) {
		
		$defaults = array( 'response' => 404 );
		$r = wp_parse_args($args, $defaults);
	
		$have_gettext = function_exists('__');
	
		if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
			if ( empty( $title ) ) {
				$error_data = $message->get_error_data();
				if ( is_array( $error_data ) && isset( $error_data['title'] ) )
					$title = $error_data['title'];
			}
			$errors = $message->get_error_messages();
			switch ( count( $errors ) ) :
			case 0 :
				$message = '';
				break;
			case 1 :
				$message = "<p>{$errors[0]}</p>";
				break;
			default :
				$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
				break;
			endswitch;
		} elseif ( is_string( $message ) ) {
			$message = sprintf( '<p>%s</p>', $message );
		}
	
		if ( isset( $r['back_link'] ) && $r['back_link'] ) {
			$back_text = $have_gettext ? __('&laquo; Back', 'wpthms' ) : '&laquo; Back';
			$message .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
		}
	
		if ( !function_exists( 'did_action' ) || !did_action( 'admin_head' ) ) :
			if ( !headers_sent() ) {
				status_header( $r['response'] );
				nocache_headers();
				header( 'Content-Type: text/html; charset=utf-8' );
			}
	
			if ( empty($title) )
				$title = $have_gettext ? esc_html__('WordPress &rsaquo; Error', 'wpthms' ) : 'WordPress &rsaquo; Error';
	
			$text_direction = 'ltr';
			if ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] )
				$text_direction = 'rtl';
			elseif ( function_exists( 'is_rtl' ) && is_rtl() )
				$text_direction = 'rtl';
	?>
	<!DOCTYPE html>
	<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo sprintf( 'dir="%s"', esc_attr( $text_direction ) ); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
		<?php echo sprintf( '<%1$s>%2$s</%1$s>', 'title', esc_html( $title ) );  ?>
		<link href="https://fonts.googleapis.com/css?family=Fredericka+the+Great" rel="stylesheet" type="text/css">
		<style>
			body{ 
				text-align:center;
				padding:50px 0;
				font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
				font-weight:300;
				line-height:30px;
				color: #777;
				max-width: 1000px;
				margin: 0 auto
			}
			
			a{ 
				color: #39C;
				border-bottom:1px solid #EEE;
				text-decoration:none;
				transition:all .2s linear;
			}
			
			a:hover{
				color: #000;
				border-bottom: 1px solid #9cf;
			}
			h1, h2, h3, h4, h5, h6{
				color:#000;
			}
			h1{
				font-family: 'Fredericka the Great', cursive;
				font-size: 4.5em;
				font-weight: normal;
				line-height: 1.2em;
				margin:20px 0;
			}
			@media (max-width: 768px) {
				h1{
					font-size: 55px;
				}
			}
		</style>
	</head>
	<body id="error-page">
	<?php endif; ?>
		<a href="./index.php" class="cssLogo"><?php echo esc_html( $title ); ?></a>
		<h1><?php echo str_replace('WordPress &rsaquo; ', '', esc_attr( $title ) ); ?></h1>
		<?php echo( $message ); ?>
		<p class="errorinfo"><a href="./index.php"><?php echo esc_html__( 'Homepage', 'wpthms' ); ?></a></p>
	</body>
	</html>
	<?php
		die();
	}
}

/**
 * Registers the default Admin color schemes
 *
 * @since 1.0.0
 */

function yt_options_metabox_option_js_vars( $array ){
	$array = array_merge( $array, array(
		'frameworkURL'	=> wpthms_FRAMEWORK_URI,
		'nonce' 	=> wp_create_nonce('ajax-nonce'),
		'themeUrl'	=> get_template_directory_uri() . '/',
		'blogUrl'	=> site_url() . '/',
		'notImageMsg' => esc_js( __('Note: This file is not a valid image!', 'wpthms') ),
		'useImageMsg' => esc_js( __('Use this one', 'wpthms') ),
		'viewFileMsg' => esc_js( __('View File', 'wpthms') ),
		'importOptionsMsg' => esc_js( __('Click OK to import options', 'wpthms') ),
		'restoreOptionsMsg' => esc_js( __('Warning: All of your current options will be replaced with the data from your last backup! Proceed?', 'wpthms') ),
		'backupOptionsMsg' => esc_js( __('Click OK to backup your current saved options.', 'wpthms') ),
		'resetOptionsMsg' => esc_js( sprintf( __('Click OK to reset. All settings will be lost and replaced with default settings! %s Notice: You can use backup function to save theme option data before reset.', 'wpthms' ), "\n\n" ) ),
		'oembedPreviewErrorMsg' => esc_js( __('Cannot preview this file', 'wpthms') ),
		'deleteGalleryTitle' => esc_js( __('Delete', 'wpthms') )
	) );

	return $array;
}


//print_r($yt_data);

		
