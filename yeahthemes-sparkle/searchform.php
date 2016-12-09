<?php
/**
 * The template for displaying search forms in wpthms
 *
 * @package wpthms
 */
?>

<?php yt_before_search_form();?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	
	<?php yt_search_form_start();?>
	
		<label class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'wpthms' ); ?></label>
		<input type="search" class="search-field form-control" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'wpthms' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'wpthms' ); ?>">
	
	<?php 
	echo apply_filters( 'yt_site_search_submit_button', 
		sprintf ( '<input type="submit" class="search-submit btn btn-primary" value="%s">', esc_attr_x( 'Search', 'submit button', 'wpthms' ) ) 
		);
	?>
	
	<?php yt_search_form_end();?>
</form>

<?php yt_after_search_form();?>