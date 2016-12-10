<?php
/*
 * Plugin Name: Hi2PDF
 * Version: 1.0
 * Plugin URI: http://hiwaldorf.com
 * Description: Create PDF documents from your WordPress pages
 * Author: Mingxia
 * Author URI: http://hiwaldorf.com
 * Requires at least: 3.9
 * Tested up to: 4.7.0
 *
 * Text Domain: hi2pdf
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Hi2PDF' ) ) {

	final class Hi2PDF {

		private static $instance;

		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Hi2PDF ) ) {

				self::$instance = new Hi2PDF;

				self::$instance->setup_constants();

				add_action( 'plugins_loaded', array( self::$instance, 'hi2pdf_load_textdomain' ) );

				self::$instance->includes();

			}

			return self::$instance;

		}

		public function hi2pdf_load_textdomain() {

			load_plugin_textdomain( 'hi2pdf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		}

		private function setup_constants() {

			if ( ! defined( 'Hi2PDF_VERSION' ) ) { define( 'Hi2PDF_VERSION', '1.0' ); }
			if ( ! defined( 'Hi2PDF_PLUGIN_DIR' ) ) { define( 'Hi2PDF_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); }
			if ( ! defined( 'Hi2PDF_PLUGIN_URL' ) ) { define( 'Hi2PDFPLUGIN_URL', plugin_dir_url( __FILE__ ) ); }
			if ( ! defined( 'Hi2PDF_PLUGIN_FILE' ) ) { define( 'Hi2PDF_PLUGIN_FILE', __FILE__ ); }

		}

		private function includes() {

			// settings / metaboxes
			if ( is_admin() ) {

				require_once Hi2PDF_PLUGIN_DIR . 'includes/class-settings.php';
				$settings = new Hi2PDF_Settings( $this );

				require_once Hi2PDF_PLUGIN_DIR . 'includes/class-admin-api.php';
				$this->admin = new Hi2PDF_Admin_API();

				require_once Hi2PDF_PLUGIN_DIR . 'includes/hi2pdf-metaboxes.php';

			}

			// load css / js
			require_once Hi2PDF_PLUGIN_DIR . 'includes/hi2pdf-load-js-css.php';

			// functions
			require_once Hi2PDF_PLUGIN_DIR . 'includes/hi2pdf-functions.php';

			// shortcodes
			require_once Hi2PDF_PLUGIN_DIR . 'includes/class-template-loader.php';
			require_once Hi2PDF_PLUGIN_DIR . 'includes/hi2pdf-shortcodes.php';

		}

		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'hi2pdf' ), Hi2PDF_VERSION );
		}

		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'hi2pdf' ), Hi2PDF_VERSION );
		}

	}

}

function Hi2PDF() {

	return Hi2PDF::instance();

}

Hi2PDF();
