<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'hi2pdf_enqueue_styles', 15 );
add_action( 'wp_enqueue_scripts', 'hi2pdf_enqueue_scripts', 10 );
add_action( 'admin_enqueue_scripts', 'hi2pdf_admin_enqueue_scripts', 10, 1 );
add_action( 'admin_enqueue_scripts', 'hi2pdf_admin_enqueue_styles', 10, 1 );

function hi2pdf_enqueue_styles () {

	wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0' );
	wp_enqueue_style( 'font-awesome' );

	wp_register_style( 'hi2pdf-frontend', plugins_url( 'hi2pdf/assets/css/frontend.css' ), array(), Hi2PDF_VERSION );
	wp_enqueue_style( 'hi2pdf-frontend' );

}

function hi2pdf_enqueue_scripts () {

	wp_register_script( 'hi2pdf-frontend', plugins_url( 'hi2pdf/assets/js/frontend.js' ), array( 'jquery' ), Hi2PDF_VERSION, true );
	wp_enqueue_script( 'hi2pdf-frontend' );

}

function hi2pdf_admin_enqueue_styles ( $hook = '' ) {

	wp_register_style( 'hi2pdf-admin', plugins_url( 'hi2pdf/assets/css/admin.css' ), array(), Hi2PDF_VERSION );
	wp_enqueue_style( 'hi2pdf-admin' );

}

function hi2pdf_admin_enqueue_scripts ( $hook = '' ) {

	wp_register_script( 'hi2pdf-settings-admin', plugins_url( 'hi2pdf/assets/js/settings-admin.js' ), array( 'jquery' ), Hi2PDF_VERSION );
	wp_enqueue_script( 'hi2pdf-settings-admin' );

	wp_register_script( 'hi2pdf-ace', plugins_url( 'hi2pdf/assets/js/src-min/ace.js' ), array(), Hi2PDF_VERSION );
	wp_enqueue_script( 'hi2pdf-ace' );

	wp_register_script( 'hi2pdf-admin', plugins_url( 'hi2pdf/assets/js/admin.js' ), array( 'jquery' ), Hi2PDF_VERSION );
	wp_enqueue_script( 'hi2pdf-admin' );

}
