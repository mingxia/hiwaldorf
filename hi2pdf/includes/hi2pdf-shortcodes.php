<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
* [hi2pdf-button]
* This shortcode is used to display Hi2PDF Button
* doesn't has attributes, uses settings from Hi2PDF Settings / PDF Button
*/
function hi2pdf_button_shortcode( $atts, $content = null ) {

	$template = new Hi2PDF_Template_Loader;

	ob_start();

	$template->get_template_part( 'hi2pdf-button' );

	return ob_get_clean();

}

add_shortcode( 'hi2pdf-button', 'hi2pdf_button_shortcode' );

/**
* [hi2pdf-remove]content to remove[/hi2pdf-remove]
* This shortcode is used remove pieces of content in the generated PDF
* @return string
*/
function hi2pdf_remove_shortcode( $atts, $content = null ) {

	$pdf = get_query_var( 'pdf' );

  	if( apply_filters( 'hi2pdf_hide_button_isset', isset( $_POST['hi2pdfg_action_create'] ) ) ) {
    	if ( $pdf || apply_filters( 'hi2pdf_hide_button_equal', $_POST['hi2pdfg_action_create'] == 'hi2pdfg_action_create' )  ) {

			$removed_content = '';

		// if not returns the content inside the shortcode
		} else {

			$removed_content = $content;

		}

	} else {

		// if is pdf returns an empty string
		if( $pdf ) {

			$removed_content = '';

		// if not returns the content inside the shortcode
		} else {

			$removed_content = $content;

		}

	}

	return $removed_content;

}

add_shortcode( 'hi2pdf-remove', 'hi2pdf_remove_shortcode' );

/**
* [hi2pdf-pagebreak]
* Allows adding page breaks for sending content after this shortcode to the next page.
* Uses <pagebreak /> http://mpdf1.com/manual/index.php?tid=108
* @return string
*/
function hi2pdf_pagebreak_shortcode( $atts, $content = null ) {

	$pdf = get_query_var( 'pdf' );

  	if( apply_filters( 'hi2pdf_hide_button_isset', isset( $_POST['hi2pdfg_action_create'] ) ) ) {
    	if ( $pdf || apply_filters( 'hi2pdf_hide_button_equal', $_POST['hi2pdfg_action_create'] == 'hi2pdfg_action_create' )  ) {

			$output = '<pagebreak />';

		} else {

			$output = '';

		}

	} else {

		if( $pdf ) {

			$output = '<pagebreak />';

		} else {

			$output = '';

		}

	}

	return $output;

}

add_shortcode( 'hi2pdf-pagebreak', 'hi2pdf_pagebreak_shortcode' );
