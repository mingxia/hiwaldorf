<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
* displays pdf button
*/
function hi2pdf_display_pdf_button( $content ) {

  // if is generated pdf don't show pdf button
  $pdf = get_query_var( 'pdf' );

  if( apply_filters( 'hi2pdf_hide_button_isset', isset( $_POST['hi2pdfg_action_create'] ) ) ) {

    if ( $pdf || apply_filters( 'hi2pdf_hide_button_equal', $_POST['hi2pdfg_action_create'] == 'hi2pdfg_action_create' )  ) {

        remove_shortcode('hi2pdf-button');
        $content = str_replace( "[hi2pdf-button]", "", $content );

        return $content;

    }

  } else {

    if ( $pdf ) {

        remove_shortcode('hi2pdf-button');
        $content = str_replace( "[hi2pdf-button]", "", $content );

        return $content;

    }

  }

  global $post;
  $post_type = get_post_type( $post->ID );

  $option_post_types = sanitize_option( 'hi2pdf_pdfbutton_post_types', get_option( 'hi2pdf_pdfbutton_post_types', array() ) );

  // TODO button checkboxes?
  if ( is_archive() || is_front_page() || is_home() ) { return $content; }

  // return content if not checked
  if( $option_post_types ) {

      if ( ! in_array( get_post_type( $post ), $option_post_types ) ) {

        return $content;

      }

  }

  if( $option_post_types ) {

      if ( in_array( get_post_type( $post ), $option_post_types ) ) {

        $c = $content;

        $pdfbutton_position = sanitize_option( 'hi2pdf_pdfbutton_position', get_option( 'hi2pdf_pdfbutton_position', 'before' ) );

        $template = new Hi2PDF_Template_Loader;

        if( $pdfbutton_position ) {

            if ( $pdfbutton_position == 'shortcode' ) {
              return $c;
            }

            if( $pdfbutton_position == 'before' ) {

              ob_start();

              $content = $template->get_template_part( 'hi2pdf-button' );

              return ob_get_clean() . $c;


            } else if ( $pdfbutton_position == 'after' ) {

              ob_start();

              $content = $template->get_template_part( 'hi2pdf-button' );

              return $c . ob_get_clean();

            }

        }

      }

  } else {

    return $content;

  }

}

add_filter( 'the_content', 'hi2pdf_display_pdf_button' );

/**
* output the pdf
*/
function hi2pdf_output_pdf( $query ) {

  // TODO sanitize validate...
  $pdf = get_query_var( 'pdf' );

  if( $pdf ) {

      include('mpdf60/mpdf.php');

      // page orientation
      $hi2pdf_page_orientation = get_option( 'hi2pdf_page_orientation', '' );

      if ( $hi2pdf_page_orientation == 'horizontal') {

        $format = apply_filters( 'hi2pdf_pdf_format', 'A4' ).'-L';

      } else {

        $format = apply_filters( 'hi2pdf_pdf_format', 'A4' );

      }

      // font size
      $hi2pdf_font_size = get_option( 'hi2pdf_font_size', '12' );
      $hi2pdf_font_family = '';

      // margins
      $hi2pdf_margin_left = get_option( 'hi2pdf_margin_left', '15' );
      $hi2pdf_margin_right = get_option( 'hi2pdf_margin_right', '15' );
      $hi2pdf_margin_top = get_option( 'hi2pdf_margin_top', '50' );
      $hi2pdf_margin_bottom = get_option( 'hi2pdf_margin_bottom', '30' );
      $hi2pdf_margin_header = get_option( 'hi2pdf_margin_header', '15' );

      // creating and setting the pdf
      $mpdf = new mPDF('utf-8', $format, $hi2pdf_font_size, $hi2pdf_font_family,
        $hi2pdf_margin_left, $hi2pdf_margin_right, $hi2pdf_margin_top, $hi2pdf_margin_bottom, $hi2pdf_margin_header
      );


      // make chinese characters work in the pdf
      $mpdf->useAdobeCJK = true;
      $mpdf->autoScriptToLang = true;
      $mpdf->autoLangToFont = true;

      // header
      $pdf_header_html = hi2pdf_get_template( 'hi2pdf-header' );
      $mpdf->SetHTMLHeader( $pdf_header_html );

      // footer
      $pdf_footer_html = hi2pdf_get_template( 'hi2pdf-footer' );
      $mpdf->SetHTMLFooter( $pdf_footer_html );

      $mpdf->WriteHTML( apply_filters( 'hi2pdf_before_content', '' ) );
      $mpdf->WriteHTML( hi2pdf_get_template( 'hi2pdf-index' ) );
      $mpdf->WriteHTML( apply_filters( 'hi2pdf_after_content', '' ) );

      // action to do (open or download)
      $pdfbutton_action = sanitize_option( 'hi2pdf_pdfbutton_action', get_option( 'hi2pdf_pdfbutton_action', 'open' ) );

      if( $pdfbutton_action == 'open') {

        $mpdf->Output();

      } else {

        global $post;
        $mpdf->Output( get_the_title( $post->ID ).'.pdf', 'D' );

      }

      exit;

  }

}

add_action( 'wp', 'hi2pdf_output_pdf' );

/**
* returs a template
* @param string template name
*/
function hi2pdf_get_template( $template_name ) {

    $template = new Hi2PDF_Template_Loader;

    ob_start();
    $template->get_template_part( $template_name );
    return ob_get_clean();

}

/**
* returns an array of active post, page, attachment and custom post types
* @return array
*/
function hi2pdf_get_post_types() {

    $args = array(
       'public'   => true,
       '_builtin' => false
    );

    $post_types = get_post_types( $args );
    $post_arr = array( 'post' => 'post', 'page' => 'page', 'attachment' => 'attachment' );

    foreach ( $post_types  as $post_type ) {

      $arr = array( $post_type => $post_type );
      $post_arr += $arr;

    }

    $post_arr = apply_filters( 'hi2pdf' . '_posts_arr', $post_arr );

    return $post_arr;

}

/**
* set query_vars
*/
function hi2pdf_set_query_vars( $query_vars ) {

  $query_vars[] = 'pdf';

  return $query_vars;

}

add_filter( 'query_vars', 'hi2pdf_set_query_vars' );

/**
* sanitizes hi2pdf options
*/
function hi2pdf_sanitize_options() {

    add_filter( 'pre_update_option_hi2pdf_pdfbutton_text', 'hi2pdf_update_field_hi2pdf_pdfbutton_text', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdfbutton_post_types', 'hi2pdf_update_field_hi2pdf_pdfbutton_post_types', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdfbutton_action', 'hi2pdf_update_field_hi2pdf_pdfbutton_action', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdfbutton_position', 'hi2pdf_update_field_hi2pdf_pdfbutton_position', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdfbutton_align', 'hi2pdf_update_field_hi2pdf_pdfbutton_align', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_page_orientation', 'hi2pdf_update_field_hi2pdf_page_orientation', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_font_size', 'hi2pdf_update_field_hi2pdf_font_size', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_margin_left', 'hi2pdf_update_field_hi2pdf_margin_left', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_margin_right', 'hi2pdf_update_field_hi2pdf_margin_right', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_margin_top', 'hi2pdf_update_field_hi2pdf_margin_top', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_margin_bottom', 'hi2pdf_update_field_hi2pdf_margin_bottom', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_margin_header', 'hi2pdf_update_field_hi2pdf_margin_header', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_header_image', 'hi2pdf_update_field_hi2pdf_pdf_header_image', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_header_show_title', 'hi2pdf_update_field_hi2pdf_pdf_header_show_title', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_header_show_pagination', 'hi2pdf_update_field_hi2pdf_pdf_header_show_pagination', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_footer_text', 'hi2pdf_update_field_hi2pdf_pdf_footer_text', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_footer_show_title', 'hi2pdf_update_field_hi2pdf_pdf_footer_show_title', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_footer_show_pagination', 'hi2pdf_update_field_hi2pdf_pdf_footer_show_pagination', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_pdf_custom_css', 'hi2pdf_update_field_hi2pdf_pdf_custom_css', 10, 2 );
    add_filter( 'pre_update_option_hi2pdf_print_wp_head', 'hi2pdf_update_field_hi2pdf_print_wp_head', 10, 2 );


}

add_action( 'init', 'hi2pdf_sanitize_options' );

/**
* sanitizes hi2pdf_pdfbutton_text option
*/
function hi2pdf_update_field_hi2pdf_pdfbutton_text( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdfbutton_post_types option
*/
function hi2pdf_update_field_hi2pdf_pdfbutton_post_types( $new_value, $old_value ) {
    // TODO sanitize_text_field doesn't work
    //$new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdfbutton_action option
*/
function hi2pdf_update_field_hi2pdf_pdfbutton_action( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdfbutton_position option
*/
function hi2pdf_update_field_hi2pdf_pdfbutton_position( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdfbutton_align option
*/
function hi2pdf_update_field_hi2pdf_pdfbutton_align( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_page_orientation option
*/
function hi2pdf_update_field_hi2pdf_page_orientation( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_font_size option
*/
function hi2pdf_update_field_hi2pdf_font_size( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_margin_left option
*/
function hi2pdf_update_field_hi2pdf_margin_left( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_margin_right option
*/
function hi2pdf_update_field_hi2pdf_margin_right( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_margin_top option
*/
function hi2pdf_update_field_hi2pdf_margin_top( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_margin_bottom option
*/
function hi2pdf_update_field_hi2pdf_margin_bottom( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_margin_header option
*/
function hi2pdf_update_field_hi2pdf_margin_header( $new_value, $old_value ) {
    $new_value = intval( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_header_image option
*/
function hi2pdf_update_field_hi2pdf_pdf_header_image( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_header_show_title option
*/
function hi2pdf_update_field_hi2pdf_pdf_header_show_title( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_header_show_pagination option
*/
function hi2pdf_update_field_hi2pdf_pdf_header_show_pagination( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_footer_text option
*/
function hi2pdf_update_field_hi2pdf_pdf_footer_text( $new_value, $old_value ) {

    $arr = array(
        'a' => array(
            'href' => array(),
            'title' => array(),
            'class' => array(),
            'style' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
        'hr' => array(),
        'p' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        ),
        'h1' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        ),
        'h2' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        ),
        'h3' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        ),
        'h4' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        ),
        'div' => array(
           'title' => array(),
           'class' => array(),
           'style' => array()
        )
    );

    $new_value = wp_kses( $new_value, $arr );
    return $new_value;

}

/**
* sanitizes hi2pdf_pdf_header_show_pagination option
*/
function hi2pdf_update_field_hi2pdf_pdf_footer_show_title( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_header_show_pagination option
*/
function hi2pdf_update_field_hi2pdf_pdf_footer_show_pagination( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}

/**
* sanitizes hi2pdf_pdf_custom_css option
*/
function hi2pdf_update_field_hi2pdf_pdf_custom_css( $new_value, $old_value ) {
    $new_value = wp_filter_nohtml_kses( $new_value );
    $new_value = str_replace('\"', '"', $new_value);
    $new_value = str_replace("\'", "'", $new_value);
    return $new_value;
}

/**
* sanitizes hi2pdf_print_wp_head option
*/
function hi2pdf_update_field_hi2pdf_print_wp_head( $new_value, $old_value ) {
    $new_value = sanitize_text_field( $new_value );
    return $new_value;
}
