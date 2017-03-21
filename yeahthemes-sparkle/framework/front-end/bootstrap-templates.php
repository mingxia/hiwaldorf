<?php
 // This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Overwrite the default templates
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	wpthms
 */


/**
 * Comment form
 *
 * @since 1.0
 */
if( !is_admin() ){
	add_filter( 'comment_form_default_fields', 'yt_bs_comment_form_default_fields');
	add_filter( 'comment_form_defaults', 'yt_bs_comment_form_defaults', 10, 2);
	add_filter( 'the_password_form', 'yt_bs_the_password_form');
	add_action( 'comment_form_top', 'yt_bs_comment_form_default_fields_start', 1);
	add_filter( 'comment_form_field_comment', 'yt_bs_comment_form_default_fields_end', 30);
}

if( !function_exists( 'yt_bs_comment_form_default_fields' ) ) {
	
	function yt_bs_comment_form_default_fields($fields){

		if ( ! isset( $args['format'] ) )
			$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';

		$commenter = wp_get_current_commenter();
		$req       = get_option( 'require_name_email' );
		$commenter = wp_get_current_commenter();
		$aria_req = ( $req ? " aria-required='true'" : '' );
		$html5    = 'html5' === $args['format'];
		
		$fields   =  array(
			'author' => '<p class="comment-form-author col-md-4 col-sm-4">' . '<label for="author">' . esc_html__( 'Name', 'wpthms' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input class="form-control" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . '/></p>',
			'email'  => '<p class="comment-form-email col-md-4 col-sm-4"><label for="email">' . esc_html__( 'Email', 'wpthms' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
						'<input class="form-control" id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . '/></p>',
			'url'    => '<p class="comment-form-url col-md-4 col-sm-4"><label for="url">' . esc_html__( 'Website', 'wpthms' ) . '</label> ' .
						'<input class="form-control" id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30"/></p>',
		);
		
		return $fields;
	}
}


if( !function_exists( 'yt_bs_comment_form_defaults' ) ) {
	
	function yt_bs_comment_form_defaults($defaults){
		
		$post_id = get_the_ID();
		
		$user = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';
		
		$req      = get_option( 'require_name_email' );
		$required_text = sprintf( ' ' . esc_html__('Required fields are marked %s', 'wpthms'), '<span class="required">*</span>' );
		
		$defaults['comment_notes_before'] = '<p class="comment-notes col-xs-12">' . esc_html__( 'Your email address will not be published.', 'wpthms' ) . ( $req ? $required_text : '' ) . '</p>';
		$defaults['comment_field'] = '<div class="clearfix"></div><p class="comment-form-comment col-xs-12"><label for="comment">' . _x( 'Comment <span class="required">*</span>', 'noun', 'wpthms' ) . '</label> <textarea class="form-control" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		$defaults['comment_notes_after'] = '<p class="form-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'wpthms' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>';
		$defaults['logged_in_as'] = '<p class="logged-in-as col-xs-12">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'wpthms' ), get_edit_user_link(), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>';

		return $defaults;
		
	}
}





if( !function_exists( 'yt_bs_comment_form_default_fields_start' ) ) {
	
	function yt_bs_comment_form_default_fields_start(){
		//if( !yt_is_woocommerce() ) 
			echo '<div class="row">';
	}
}


if( !function_exists( 'yt_bs_comment_form_default_fields_end' ) ) {
	
	function yt_bs_comment_form_default_fields_end( $field){
		//if( !yt_is_woocommerce() ) 
			$field = $field. '</div>';
			return $field;
	}
}
/**
 * Password form
 *
 * @since 1.0
 */

if( !function_exists( 'yt_bs_the_password_form' ) ) {
	function yt_bs_the_password_form() {
		global $post;
		$post = get_post( $post );
		$label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
		
		$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
		<p>' . esc_html__( 'This content is password protected. To view it please enter your password below:', 'wpthms' ) . '</p>
		<p><label for="' . esc_attr( $label ) . '">' . esc_html__( 'Password:', 'wpthms' ) . '</label> <input name="post_password" id="' . esc_attr( $label ) . '" type="password" size="20" /> <button type="submit" class="btn btn-primary	">' . esc_html__( 'Submit', 'wpthms' ) . '</button></p></form>';
		return $output;
	}
}

// add_filter('wp_list_categories', 'yt_bs_site_cat_count_span');

// function yt_bs_site_cat_count_span($links) {
// 	$links = str_replace( 
// 		array( '</a> (', ')' . "\n" .'</li>' ), 
// 		array( '</a> <span>(', ')</span></li>' ),
// 		$links
// 	);
// 	return $links;
// }

//add_filter( 'get_calendar', 'yt_bs_bs_get_calendar'  );

function yt_bs_bs_get_calendar($calendar) {
	$calendar = str_replace( '<table', '<table class="table"', $calendar);
	return $calendar;
}
