<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
* adds dashboard page
*/
function hi2pdf_welcome_screen_page(){
    add_dashboard_page('DK PDF Welcome', 'DK PDF Welcome', 'manage_options', 'hi2pdf-welcome', 'hi2pdf_welcome_page');
}

// output hi2pdf-welcome dashboard page
function hi2pdf_welcome_page(){ ?>

    <div class="wrap">

      <h1>Welcome to DK PDF <?php echo DKPDF_VERSION;?></h1>
      <h2 style="font-size:140%;">What's new in this version:</h2>
      <ul>
        <li>
          <h3 style="margin-top:20px;">DK PDF admin menu</h3>
          <?php
            $img1 = plugins_url( 'assets/images/hi2pdf-admin-menu.jpg', DKPDF_PLUGIN_FILE );
          ?>
          <img style="margin-bottom:20px;width:100%;height:auto;"src="<?php echo $img1;?>">
        </li>
        <li>
          <h3 style="margin-top:20px;">PDF Setup tab for adjusting page orientation, font size and margins of the PDF</h3>
          <?php
            $img2 = plugins_url( 'assets/images/hi2pdf-setup-tab.jpg', DKPDF_PLUGIN_FILE );
          ?>
          <img style="margin-bottom:20px;width:100%;height:auto;"src="<?php echo $img2;?>">
        </li>
        <li>
          <h3 style="margin-top:20px;">[hi2pdf-remove] shortcode for removing pieces of content in the generated PDF</h3></li>
          <p><a href="http://wp.dinamiko.com/demos/hi2pdf/doc/hi2pdf-remove-shortcode/" target="_blank">See more info here</a></p>
      </ul>

    </div>

<?php }

add_action('admin_menu', 'hi2pdf_welcome_screen_page');

/**
* Fires when plugin is activated or upgraded
*/
function hi2pdf_welcome_redirect( $plugin ) {

   if( $plugin == 'hi2pdf/hi2pdf.php' ) {

       wp_redirect( admin_url( 'index.php?page=hi2pdf-welcome' ) );
       die();

   }
}

add_action( 'activated_plugin', 'hi2pdf_welcome_redirect' );

/**
* removes hi2pdf-welcome link in Dashboard submenu
*/
function hi2pdf_remove_menu_entry(){
    remove_submenu_page( 'index.php', 'hi2pdf-welcome' );
}

add_action( 'admin_head', 'hi2pdf_remove_menu_entry' );
