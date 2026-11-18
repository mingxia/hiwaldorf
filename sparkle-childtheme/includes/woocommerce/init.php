<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

if ( !class_exists( 'WooCommerce' ) ) return;

/**
 * Registers support for Woocommerce
 *
 * @return void
 * @since 1.0
 */
if( !function_exists( 'yt__wc_after_setup_theme' ) ){
	function yt__wc_after_setup_theme() {

		add_theme_support( 'woocommerce', apply_filters( 'yt__woocommerce_args', array(
			// 'single_image_width'    => 416,
			// 'thumbnail_image_width' => 324,
			'product_grid'          => array(
				'default_columns' => 3,
				'default_rows'    => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
				'min_rows'        => 1
			)
		) ) );

		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}

add_action( 'after_setup_theme', 'yt__wc_after_setup_theme', 30 );

/**
 * Woocommerce Theme options
 *
 * @return array
 */
add_filter( 'yt_theme_options', 'yt_wc_theme_options', 10 );
/**
 * Theme options
 */
function yt_wc_theme_options( $options ) {

	$show_hide = array(
		'show' => 'Show', 
		'hide' => 'Hide'
	);

	$wc_options = apply_filters( 'yt_wc_theme_options' , array(
		
		array( 
			'name' => 'Woocommerce',
			'desc' => '',
			'type' => 'heading',
			'customize' => 1,
			'settings' => array(
				'icon' => 'ecommerce'
			)
			
		),
		array(
			'name' => 'Product per page ( Main shop page )',
			'std' => wpautop( wp_sprintf( 'Product per page option is deprecated, the theme will rely on default plugin settings here <a href="%s">in Customizer</a>', add_query_arg(
								array(
									'autofocus' => array(
										'panel' => 'woocommerce',
									),
									'url'       => wc_get_page_permalink( 'shop' ),
								), admin_url( 'customize.php' )
							)) ),
			'id' => 'wc_products_per_page',
			'type' => 'info',
			'customize' => 1,
		),
		array(
			'name' => 'Mini cart (on Main Menu)',
			'desc' => '',
			'id' => 'wc_main_menu_mini_cart',
			'std' => 'show',
			'type' => 'toggles',
			'options' => $show_hide  ,
			'customize' => 1,
		),
		array(
			'name' => 'Related product ( Single product )',
			'desc' => '',
			'id' => 'wc_single_product_related_products',
			'std' => 'show',
			'type' => 'toggles',
			'options' => $show_hide  ,
			'customize' => 1,
		),
		array(
			'name' => 'Add to cart button ( Product archives )',
			'desc' => '',
			'id' => 'wc_archive_add_to_cart_button',
			'std' => 'show',
			'type' => 'toggles',
			'options' => $show_hide  ,
			'customize' => 1,
		),
		array(
			'name' => 'Sale badge (Product archives)',
			'desc' => '',
			'id' => 'wc_product_archive_sale_badge',
			'std' => 'show',
			'type' => 'toggles',
			'options' => $show_hide  ,
			'customize' => 1,
		)

	) );
	


	return array_merge( $options, $wc_options );
}


/**
 * Overwrite woocommerce styles.
 * 
 * @return void
 * @since 	1.0
 */
if ( ! function_exists( 'yt__wc_woocommerce_enqueue_styles' ) ) {
	function yt__wc_woocommerce_enqueue_styles( $styles ) {
		// echo get_stylesheet_uri();
		if ( in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) )
			return;

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$suffix = '';
		$template_uri = get_template_directory_uri();
		$styles['woocommerce-layout']['src'] = 		$template_uri . "/includes/woocommerce/css/woocommerce-layout$suffix.css";
		$styles['woocommerce-smallscreen']['src'] = $template_uri . "/includes/woocommerce/css/woocommerce-smallscreen$suffix.css";
		$styles['woocommerce-general']['src'] = 	$template_uri . "/includes/woocommerce/css/woocommerce$suffix.css";
		
		return $styles;
	}
}

// add_filter( 'woocommerce_enqueue_styles', 'yt__wc_woocommerce_enqueue_styles' );

/**
 * Enqueue theme scripts and styles.
 * 
 * @return void
 * @since 	1.0
 */
if ( ! function_exists( 'yt__wc_enqueue_styles' ) ) {
	function yt__wc_enqueue_styles() {
		
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$suffix = '';
		$template_uri = get_template_directory_uri();

		// Dequeue Increment plugin
		if( class_exists( 'WooCommerce_Quantity_Increment') ){
			
			wp_dequeue_style( 'wcqi-css' );
		}
		wp_register_style( 'newsbeat-woocommerce', $template_uri . "/includes/woocommerce/css/theme$suffix.css", array(), '20181106.8' );
		wp_enqueue_style( 'newsbeat-woocommerce' );
	}
}


add_action( 'wp_enqueue_scripts', 'yt__wc_enqueue_styles' );


add_filter( 'woocommerce_breadcrumb_defaults', 'yt_wc_breadcrumb_defaults' );
/**
 * Breadcrumb
 *
 * @since 1.0
 */
function yt_wc_breadcrumb_defaults( $defaults ) {
	// Change the breadcrumb delimeter from '/' to '>'
	return array(
        'delimiter'   => '<span class="breadcrumb-delimeter">&#47;</span>',
        'wrap_before' => '<nav class="woocommerce-breadcrumb gray-2-primary">',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => esc_html_x( 'Home', 'breadcrumb', 'yeahthemes-sparkle' ),
    );
}
/**
 * Custom Menu item: Mini Cart
 *
 * @since 1.0
 */

add_filter( 'wp_nav_menu_items', 'yt_wc_custom_menu_item', 15, 2 );

if ( ! function_exists( 'yt_wc_custom_menu_item' ) ) {

	function yt_wc_custom_menu_item ( $items, $args ) {
		global $woocommerce;
		if( $args->theme_location == 'primary' && 'hide' !== yt_get_options( 'wc_main_menu_mini_cart' ) && !empty( $woocommerce->cart )){
			
			$cart = '';
			$items .= sprintf( '<li class="menu-item menu-item-type-custom menu-item-shopping-cart default-dropdown sub-menu-left pull-right">
				<a href="%s" class="cart-contents" title="' .  esc_html__('View your cart', 'yeahthemes-sparkle' ) . '"><i class="fa fa-shopping-cart"></i> %s (%d)</a>
			</li>',
			esc_attr( wc_get_cart_url() ),
			$woocommerce->cart->get_cart_total(),
			$woocommerce->cart->cart_contents_count
			);
		}

		return $items;
	}
}
/**
 * Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
 */
if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
	add_filter( 'woocommerce_add_to_cart_fragments', 'yt_wc_header_add_to_cart_fragment' );
} else {
	add_filter('add_to_cart_fragments', 'yt_wc_header_add_to_cart_fragment', 10);
}

 
function yt_wc_header_add_to_cart_fragment( $fragments ) {

	global $woocommerce;
	
	$cart = sprintf('<a href="%s" class="cart-contents" title="%s"><i class="fa fa-shopping-cart"></i> %s (%d)</a>', 
		esc_attr( $woocommerce->cart->get_cart_url() ),
		esc_attr( esc_html__('View your cart', 'yeahthemes-sparkle' )),
		$woocommerce->cart->get_cart_total(),
		$woocommerce->cart->cart_contents_count
	);
	
	$fragments['a.cart-contents'] = $cart ;
	
	return $fragments;
	
}



add_action( 'woocommerce_before_shop_loop_item_title', 'yt_wc_outofstock_flash', 10 );
add_action( 'woocommerce_before_single_product_summary', 'yt_wc_outofstock_flash', 10 );
/**
 * Outstock flash
 *
 * @since 1.0
 */
function yt_wc_outofstock_flash( ) { 
	global $post, $product;
	if ( !$product->is_in_stock() )

		echo '<span class="onsale outofstock"><span>' . esc_html__( 'Out of Stock', 'yeahthemes-sparkle' ) . '</span></span>';

}


/***************************************************************************************/
/* WooCommerce Overrides */
/***************************************************************************************/

/**
 * Enables Jetpack's Infinite Scroll in search pages, disables it in product archives
 *
 * @return bool
 */
function yt__wc_jetpack_infinite_scroll_compat( $cond ) {

	if( is_archive() && is_woocommerce() )
		$cond = false;

	return $cond;
}

add_filter( 'infinite_scroll_archive_supported', 'yt__wc_jetpack_infinite_scroll_compat', 10, 1 );


/*************************************************************************************
 * LAYOUT
 *************************************************************************************/
// Remove Woocommerce sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
add_action('woocommerce_before_main_content', 'yt_wc_before_content', 10);
add_action('woocommerce_after_main_content', 'yt_wc_after_content', 20);

/**
 * Woocommerce template: before content
 */
if (!function_exists('yt_wc_before_content')) {
	function yt_wc_before_content() {
		
		?>
	<div class="container">

		<div class="row">
		<?php yt_before_primary(); ?>

			<div id="primary" <?php yt_section_classes( 'content-area', 'primary' );?>>
				
				<?php yt_primary_start(); ?>
				
				<main id="content" <?php yt_section_classes( 'site-content', 'content' );?> role="main">
						
				<?php yt_before_loop();
	}
}
/**
 * Woocommerce template: after content
 */
if (!function_exists('yt_wc_after_content')) {

	function yt_wc_after_content() {
		yt_after_loop(); ?>

				</main><!-- #content -->
				
				<?php yt_primary_end(); ?>

			</div><!-- #primary -->

			<?php yt_after_primary();

			$current_layout = yt_get_current_layout( yt_get_options('layout') ); 
			// Columns will be controlled using css.
			if( in_array( $current_layout, array('default', 'double-sidebars') ) ){
				get_sidebar();
				get_sidebar('secondary');
			}elseif('fullwidth' == $current_layout ){
				// No sidebar
			}else{
				// Default Main sidebar
				get_sidebar();
			}
		?>
		</div>
	</div><?php
	}
}


add_filter( 'woocommerce_widget_cart_item_quantity', 'yt__wc_item_remove_link', 20, 3 );
/**
 * Empty cart url
 *
 * @since 1.0
 */
function yt__wc_item_remove_link( $item_quantity, $cart_item, $cart_item_key ){

	global $woocommerce;

	$item_quantity = $item_quantity . wp_sprintf( '<a href="%s" class="remove" title="%s">&times;</a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), esc_html__( 'Remove this item', 'yeahthemes-sparkle' ) );

	return $item_quantity;
}



/***************************************************************************************/
/* PRODUCTS */
/***************************************************************************************/



add_filter( 'woocommerce_sale_flash', 'yt__wc_outofstock_sale'); 
/**
 * Sale flash
 *
 * @since 1.0
 */
function yt__wc_outofstock_sale() { 
	global $post, $product;
	if ( !$product->is_in_stock() )

		return '<span class="onsale outofstock"><span>' . esc_html__( 'Out of Stock', 'yeahthemes-sparkle' ) . '</span></span>';

	else

		return '<span class="onsale"><span>' . esc_html__( 'Sale!', 'yeahthemes-sparkle' ) . '</span></span>';
}



add_action( 'woocommerce_before_shop_loop_item_title', 'yt__wc_outofstock_flash', 10 );
add_action( 'woocommerce_before_single_product_summary', 'yt__wc_outofstock_flash', 10 );
/**
 * Outstock flash
 *
 * @since 1.0
 */
function yt__wc_outofstock_flash( ) { 
	global $post, $product;
	if ( !$product->is_in_stock() )

		echo '<span class="onsale outofstock"><span>' . esc_html__( 'Out of Stock', 'yeahthemes-sparkle' ) . '</span></span>';

}


add_action( 'woocommerce_before_shop_loop_item', function(){

}, 1 );

/**
 * Wrap thumbnail
 */
function yt__wc__start_product_image_wrap(){
	echo '<div class="prod-thumb-w">';
}

function yt__wc__end_product_image_wrap(){
	echo '</div><!--/.prod-thumb-w-->';
}



add_filter( 'yith_add_quick_view_button_html', 'yt__wc__yith_add_quick_view_button_html', 10, 3 );
/**
 * Modify quickview button
 */
function yt__wc__yith_add_quick_view_button_html( $button, $label, $product ){
	$button = str_replace($label, '<i class="far fa-eye"></i>', $button );
	return $button;
}

/**
 * Display product tabs?
 *
 * @since 1.0
 */
if ( ! function_exists( 'yt__wc_content_section_handler' ) ) {

	function yt__wc_content_section_handler() {


		add_action( 'woocommerce_before_shop_loop_item_title', 'yt__wc__start_product_image_wrap', 0.1 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'yt__wc__end_product_image_wrap' , 1000 );

		// Change priority for add to cart
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 990 );
		
		// Product archives
		// Remove add to cart button on archives
		if( 'hide' == yt_get_options( 'wc_archive_add_to_cart_button' ) )
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 990);

		// yith Quick view button 
		if( function_exists('YITH_WCQV_Frontend')){
			remove_action( 'woocommerce_after_shop_loop_item', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ), 15 );
			add_action( 'woocommerce_before_shop_loop_item_title', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ), 991 );
		}

		// Remove sale flash on archives
		if( 'hide' == yt_get_options( 'wc_product_archive_sale_badge' ) )
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

		// Related Products
		if ( yt_get_options( 'wc_single_product_related_products' ) && 'hide' == yt_get_options( 'wc_single_product_related_products' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
		}
	}
}

add_action('template_redirect','yt__wc_content_section_handler');




add_filter( 'woocommerce_product_thumbnails_columns', 'yt__wc_custom_product_thumbnails_columns' );
/**
 * Change thumbs on the single page to 4 per column
 *
 * @since 1.0
 */
if (!function_exists('yt__wc_custom_product_thumbnails_columns')) {
	function yt__wc_custom_product_thumbnails_columns() {
		return 4;
	}
}
// Deprecated
// add_filter('loop_shop_columns', 'yt__wc_loop_columns');
/**
 * Change number or products per row to 4
 *
 * @since 1.0
 */
if (!function_exists('yt__wc_loop_columns')) {
	function yt__wc_loop_columns() {
		return 4;
	}
}

/***************************************************************************************/
/* SINGLE PRODUCTS */
/***************************************************************************************/

add_filter( 'woocommerce_output_related_products_args', 'yt__wc_related_products' );
/**
 * Modify related product args
 *
 * @since 1.0
 */
function yt__wc_related_products() {
	$args = array(
		'posts_per_page' => 4,
		'columns'        => 4,
	);
	return $args;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
add_action( 'woocommerce_after_single_product_summary', 'yt__wc_upsell_display', 15 );
/**
 * Upsell Products
 *
 * @since 1.0
 */
if (!function_exists('yt__wc_upsell_display')) {
	function yt__wc_upsell_display() {
	    woocommerce_upsell_display( -1, 4 );
	}
}

// Make form fields friendly
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', function( $args ){
	
	$args['class'] = !empty( $args['class'] ) ? $args['class'] . ' form-control' : 'form-control';

	return $args;

} );


if( class_exists( 'YITH_WCWL_UI')){
	
	function yt__wc_settings_print_styles(){
		echo '<style>.yith_banner{display: none !important;}</style>';
	}

	add_action( 'admin_print_styles', 'yt__wc_settings_print_styles' );

}

// remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);