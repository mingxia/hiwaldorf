<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Browser Detector
 *
 * @package   wpthms
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @since     1.0
 */
class YT_Query_Helper { 
	
	/**
     * Get query post
     * @param $args
     * @return WP_Query
     */
    static function get_query_post( $args = array() ) {
    	
        $default = array(
            'post_type' => array( 'post' ),
            'post_status' => 'publish'
        );
        $args = wp_parse_args( $args, $defaults );

        $query = new WP_Query($args);
        
        return $query;
    }
    /**
     * used by the ajax search feature
     * @param $args
     * @return WP_Query
     */
    static function get_query_search( $args = array() ) {
    	
        $default = array(
            's' => '',
            'post_type' => array( 'post' ),
            'posts_per_page' => 5,
            'post_status' => 'publish'
        );
        $args = wp_parse_args( $args, $defaults );

        $query = new WP_Query($args);
        return $query;
    }
}