<?php
/**
 * Yeah Shortcode function
 *
 * @author		wpthms
 * @copyright	GPL & GNU 2.0 +
 * @link		http://wpthms.com
 * @since		Version 1.0
 * @package 	Yeah shortcodes
 */

/*
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Returns current plugin version.
 * 
 * @return string Plugin version
 */
?>

<div class="yt-media-modal-backdrop" id="yt-shortcode-media-modal-backdrop"></div>
<div class="yt-media-modal yt-core-ui" id="yt-shortcode-media-modal">

	<input type="hidden" value="<?php echo wp_create_nonce('yt_shortcode_nonce'); ?>" id="yt-shortcode-nonce">
	
	<a class="yt-media-modal-close" id="yt-shortcode-media-modal-close" href="#" title="Close">&times;</a>
	<div class="yt-media-modal-content">
		
		<div class="yt-media-frame">
		
			<!--Sidebar-->
			<div class="yt-media-frame-menu">
				<div class="yt-media-menu">
					<div class="yt-shortcode-item-filter">
						<input type="text" placeholder="<?php _e('Searh shortcode', YT_SHORTCODE_DOMAIN);?>" class="widefat">
					</div>
					<ul id="yt-shortcode-item-list">
						<?php echo implode( '', (array) $this->_shortcode_menu_items );?>
					</ul>
					
				</div>
			</div>
			<!--/Sidebar-->
			
			<!--Title-->
			<div class="yt-media-frame-title"><h1><?php _e('Insert<span> </span>Shortcode', YT_SHORTCODE_DOMAIN);?></h1></div>
			<!--/Title-->
			
			<!--Router-->
			<div class="yt-media-frame-router">
				<div class="yt-media-router">
					<a href="http://wpthms.com" target="_blank" class="yt-media-menu-item">wpthms.com</a>
					<!--<a href="#" class="yt-media-menu-item">Documentation</a>-->
					<a href="#" class="yt-media-menu-item">v<?php echo YT_SHORTCODE_VER;?></a>
				</div>
			</div>
			<!--/Router-->
			
			<!--Content-->
			<div class="yt-media-frame-content">
				<div class="yt-shortcode-content-fields-wrapper">
					<div class="yt-shortcode-content-inner-wrapper">
						<h3><?php _e('Instruction:', YT_SHORTCODE_DOMAIN);?></h3>
						<ol>
							<li><?php _e('Select shortcode type from the sidebar', YT_SHORTCODE_DOMAIN);?></li>
							<li><?php _e('Enter required fields', YT_SHORTCODE_DOMAIN);?></li>
							<!--<li><?php _e('Preview Shortcode', YT_SHORTCODE_DOMAIN);?></li>-->
							<li><?php _e('Insert Shortcode', YT_SHORTCODE_DOMAIN);?></li>
						</ol>
					</div>
				</div>
				
				<div class="yt-shortcode-content-preview-wrapper hidden">
					<div class="yt-shortcode-content-inner-wrapper">
						<ul class="yt-clear">
							<li>
								<div class="yt-shortcode-syntax-snippet-area hidden yt-hidden"></div>
							</li>
							<li>
								<h3><?php _e('Code:', YT_SHORTCODE_DOMAIN);?></h3>
								<div class="yt-shortcode-snippet-area yt-shortcode-pre-area"></div>
							</li>
							<!--<li>
								<h3><?php _e('HTML Code:', YT_SHORTCODE_DOMAIN);?></h3>
								<div class="yt-shortcode-html-code-area yt-shortcode-pre-area"></div>
							</li>
							<li>
								<h3><?php _e('Preview:', YT_SHORTCODE_DOMAIN);?></h3>
								<div class="yt-shortcode-action-preview-area"></div>
							</li>-->
						
						</ul>
					</div>
					
				</div>
			</div>
			<!--/Content-->
			
			<div class="yt-media-frame-toolbar">
				<div class="yt-media-toolbar">
					<div class="yt-media-toolbar-secondary">
						<div class="yt-media-selection empty">
							<div class="yt-selection-info">
					
							</div>
							<div class="yt-selection-view"></div>
						</div>
					</div>
					
					<div class="yt-media-toolbar-primary">
						<!--<span href="#" class="button button-large" id="yt-shortcode-button-preview" disabled><?php _e('Preview', YT_SHORTCODE_DOMAIN);?></span>-->
						
						<span href="#" class="button yt-media-button button-primary button-large yt-media-button-insert" id="yt-shortcode-send-to-editor" disabled><?php _e('Insert shortcode', YT_SHORTCODE_DOMAIN);?></span>
						
					</div>
				</div>
			</div>
			
		</div>
	
	</div>
	
</div>