<?php 
/**
* hi2pdf-button.php
* This template is used to display DK PDF Button
*
* Do not edit this template directly,
* copy this template and paste in your theme inside a directory named hi2pdf
*/
?>

<?php
/**
* BUG (with Docu enabled) duplications on single doc next/prev
* Temp. workaround Docu actually can't use PDF Button
* check if single Docu
*/

global $post;
$post_type = get_post_type( $post->ID );

// check if we're using polylang plugin
if( function_exists( 'pll_register_string' )  ) {

	// get button text setting value from polylang
	$pdfbutton_text = pll__( 'PDF Button' );

} else {

	$pdfbutton_text = sanitize_option( 'hi2pdf_pdfbutton_text', get_option( 'hi2pdf_pdfbutton_text', 'PDF Button' ) );

}

$pdfbutton_align = sanitize_option( 'hi2pdf_pdfbutton_align', get_option( 'hi2pdf_pdfbutton_align', 'right' ) );

?>

<?php

$hide_pdfbutton = sanitize_meta( '_hide_pdfbutton', get_post_meta( $post->ID,  '_hide_pdfbutton' ), 'checkbox' );

// only show button if _hide_pdfbutton post meta is not checked
if ( ! $hide_pdfbutton ) { ?>

	<div class="hi2pdf-button-container" style="<?php echo apply_filters( 'hi2pdf_button_container_css', '' );?> text-align:<?php echo $pdfbutton_align;?> ">

		<a class="hi2pdf-button" href="<?php echo esc_url( add_query_arg( 'pdf', $post->ID ) );?>" target="_blank"><span class="hi2pdf-button-icon"><i class="fa fa-file-pdf-o"></i></span> <?php echo $pdfbutton_text;?></a>

	</div>

<?php }

?>
