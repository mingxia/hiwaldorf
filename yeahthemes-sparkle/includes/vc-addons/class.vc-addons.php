<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;
//add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );
class YTVC_Addons {
  
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'init' ) );

    }
 
    public function init() {
        // Check if Visual Composer is installed
        if ( ! defined( 'WPB_VC_VERSION' ) ) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array( $this, 'show_notice' ));

            return;
        }
        $this->map();

        add_action( 'admin_head', array( $this, 'admin_head'), 10 );

        // Register CSS and JS
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_print_footer_scripts', array( $this, 'admin_footer_scripts'));
    }

    public function map(){
       
    }
 
    /*
    Shortcode logic how it should be rendered
    */
    public function shortcode( $atts, $content = null ) {
     
        return '';
    }
    /*
    Shortcode logic how it should be rendered
    */
    public function animation_class( $css_animation = '' ) {
        
        $output = '';
        
        if ( $css_animation != '' ) {
            wp_enqueue_script( 'waypoints' );
            $output = ' wpb_animate_when_almost_visible wpb_' . $css_animation;
            //$output = ' yt_vc_anim visibility-hidden __s__' . $css_animation . '__f__';
        }

        return $output;
    }

    public function get_animations( ) {
        $animation = array(
            __( 'No', 'wpthms' ) => '',
            __( 'Top to bottom', 'wpthms' ) => 'top-to-bottom',
            __( 'Bottom to top', 'wpthms' ) => 'bottom-to-top',
            __( 'Left to right', 'wpthms' ) => 'left-to-right',
            __( 'Right to left', 'wpthms' ) => 'right-to-left',
            __( 'Appear from center', 'wpthms' ) => "appear"
        );
        // if( function_exists( 'yt_get_option_vars' ) )
        //     $animation = yt_get_option_vars('entrance_animations');
        return $animation;
    }
    /**
     * Admin head stuff
     */
    public function admin_head() {
    }
 
    /**
     * Frontend Scripts
     */
    public function enqueue_scripts() {
    }

    /**
     * Load plugin css and javascript files which you may need on front end of your site
     */
    public function admin_footer_scripts() {
    }
    
    /**
     * Show notice if your plugin is activated but Visual Composer is not
     */
    public function show_notice() {
        echo '
        <div class="updated">
          <p>'.__('<strong>Addons</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'wpthms').'</p>
        </div>';
    }
}