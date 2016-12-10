<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Return array with all fields in metabox
*/
function hi2pdf_get_custom_fields_settings() {

	$fields = array();

	$fields['_hide_pdfbutton'] = array(
		'name' => __( 'Disable PDF Button:' , 'hi2pdf' ),
		'description' => '',
		'type' => 'checkbox',
		'default' => '',
		'section' => ''
	);

	return $fields;

}

/**
* Add metabox to post types
*/
function hi2pdf_meta_box_setup () {

	// get post types selected in settings

	$pdfbutton_post_types = sanitize_option( 'hi2pdf_pdfbutton_post_types', get_option( 'hi2pdf_pdfbutton_post_types' ) );

	if( $pdfbutton_post_types ) {

		// add metabox to selected post types
		foreach ( $pdfbutton_post_types as $post_type ) {
			add_meta_box( 'post-data', __( 'DK PDF', 'hi2pdf' ), 'hi2pdf_meta_box_content', $post_type, 'normal', 'high' );
		}

	}

}

add_action( 'add_meta_boxes', 'hi2pdf_meta_box_setup' );

/**
* Add content to metabox
*/
function hi2pdf_meta_box_content () {

	global $post_id;
	$fields = get_post_custom( $post_id );
	$field_data = hi2pdf_get_custom_fields_settings();

	$html = '';

	$html .= '<input type="hidden" name="' . 'hi2pdf' . '_nonce" id="' . 'hi2pdf' . '_nonce" value="' . wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';

	if ( 0 < count( $field_data ) ) {
		$html .= '<table class="form-table">' . "\n";
		$html .= '<tbody>' . "\n";

		foreach ( $field_data as $k => $v ) {
			$data = $v['default'];

			if ( isset( $fields[$k] ) && isset( $fields[$k][0] ) ) {
				$data = $fields[$k][0];
			}

			if( $v['type'] == 'checkbox' ) {
				$html .= '<tr valign="top"><th scope="row">' . $v['name'] . '</th><td><input name="' . esc_attr( $k ) . '" type="checkbox" id="' . esc_attr( $k ) . '" ' . checked( 'on' , $data , false ) . ' /> <label for="' . esc_attr( $k ) . '"><span class="description">' . $v['description'] . '</span></label>' . "\n";
				$html .= '</td></tr>' . "\n";
			} else {
				$html .= '<tr valign="top"><th scope="row"><label for="' . esc_attr( $k ) . '">' . $v['name'] . '</label></th><td><input name="' . esc_attr( $k ) . '" type="text" id="' . esc_attr( $k ) . '" class="regular-text" value="' . esc_attr( $data ) . '" />' . "\n";
				$html .= '<p class="description">' . $v['description'] . '</p>' . "\n";
				$html .= '</td></tr>' . "\n";
			}

		}

		$html .= '</tbody>' . "\n";
		$html .= '</table>' . "\n";

	}

	echo $html;

}

/**
* Save metabox data
*/
function hi2pdf_meta_box_save ( $post_id ) {

	global $post, $messages;

	if ( isset( $_POST[ 'hi2pdf' . '_nonce'] ) ) {

		// Verify nonce
		if ( ! wp_verify_nonce( $_POST[ 'hi2pdf' . '_nonce'], plugin_basename( __FILE__ ) ) ) {
			return $post_id;
		}

	} else {

		return $post_id;

	}

	// Verify user permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Handle custom fields
	$field_data = hi2pdf_get_custom_fields_settings();
	$fields = array_keys( $field_data );

	foreach ( $fields as $f ) {

		${$f} = '';

		if( isset( $_POST[$f] ) ) {
			${$f} = strip_tags( trim( $_POST[$f] ) );
		}

		// Escape the URLs.
		if ( 'url' == $field_data[$f]['type'] ) {
			${$f} = esc_url( ${$f} );
		}

		if ( ${$f} == '' ) {
			delete_post_meta( $post_id , $f , get_post_meta( $post_id , $f , true ) );
		} else {
			update_post_meta( $post_id , $f , ${$f} );
		}

	}

}

add_action( 'save_post', 'hi2pdf_meta_box_save' );
