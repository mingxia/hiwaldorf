<?php 
	
	$heading_title = $this->_current_lang ? $this->_heading . ' (' . strtoupper( $this->_current_lang ) . ')' : $this->_heading ;
	$default_data = !is_serialized( $this->_option_defaults ) ? maybe_serialize( $this->_option_defaults ) : $this->_option_defaults;
	
?>

<div class="wrap yt-core-ui yt-options-framework-panel-wrapper" id="yt-container">
	<span class="yt-preloader yt-ajax-loader" id="yt-main-spinner"></span>
	<h2 class="hidden"><?php echo esc_html( $heading_title ); ?></h2>
	<div id="yt-popup-save" class="yt-save-popup">
		<i class="yt-icon-save"></i>
		<?php esc_html_e('Options Updated','wpthms'); ?>
	</div>
	
	<div id="yt-popup-reset" class="yt-save-popup">
		<i class="yt-icon-reset"></i>
		<?php esc_html_e('Options Reset','wpthms'); ?>
	</div>
	
	<div id="yt-popup-fail" class="yt-save-popup">
		<i class="yt-icon-fail"></i>
		<?php esc_html_e('Error!','wpthms'); ?>
	</div>
	<?php wp_nonce_field( 'yt-options-ajaxify-saving-data','yt_options_ajaxify_data_nonce'); ?>
	<input type="hidden" id="yt-options-option-key" name="yt_option_key" value="<?php echo esc_attr( $this->_option_name ); ?>" />
	<input type="hidden" id="yt-options-prefix" name="yt_option_prefix" value="<?php echo esc_attr( $this->_prefix ); ?>" />
	<textarea id="yt-options-option-default" class="hidden" name="yt_option_default_data"><?php echo esc_textarea( self::helper_encode( $default_data ) );?></textarea>

	<?php do_action( $this->_prefix . 'before_options_panel_form' ); ?>
	
	<form id="yt-form" class="yt-options-panel-form" method="post" action="<?php echo esc_attr( $_SERVER['REQUEST_URI'] ); ?>" enctype="multipart/form-data" >
		<div id="yt-header" class="yt-clear">
			<div class="yt-logo">
				<h2><?php echo esc_html( $heading_title ); ?></h2>
				<ul>
					<?php 
						
						$info_list = apply_filters( $this->_prefix . 'options_panel_header_info', array() );
						
						if( !empty( $info_list ) ){
							
							foreach( $info_list as $list ){
								
								printf( '<li>%s</li>', $list );
							
							}
								
						}
					?>
				</ul>
			</div>
			<div id="js-warning" class="hidden">
				<?php esc_html_e('Warning- This options panel will not work properly without javascript!','wpthms' )?>
			</div>
			<div id="yt-lets-get-social">
			
				<?php 
				
					$social_list = apply_filters( $this->_prefix . 'option_panel_social_network', array() );
					
					if( !empty( $social_list ) ){
						
						echo sprintf( '<span>%s</span>', esc_html__('Get social with us!', 'wpthms') );
						
						echo '<ul>';
						
							foreach( $social_list as $list ){
								
								printf( '<li>%s</li>', $list );
							
							}
													
						echo '</ul>';
					}
				?>
				
				
			</div>
		</div>
		
		<div class="yt-info-bar yt-clear">
			<ul class="yt-info-bar-left alignleft">
				<li><span class="button button-large yt-button" title="<?php esc_attr_e( 'Expand', 'wpthms' ); ?>" id="yt-expand-options-panel"><i class="fa fa-arrows-alt"></i></span></li>
			</ul>
			
			<ul class="alignright">
				<li><button type="button" class="button button-primary button-large yt-save-theme-options yt-save"><i class="fa fa-bolt"></i> <span><?php esc_html_e( 'Save All Changes', 'wpthms' ); ?></span></button></li>
				<li><button type="button" class="button button-large reset-button yt-save-theme-options yt-save-and-refresh"><?php esc_html_e( 'Save and Refresh', 'wpthms' );?></button></li>
			</ul>
		</div>
		<!--.info-bar-->
		
		<div id="yt-main" class="yt-clear">
			<?php 
				printf( '<div id="yt-nav"><ul>%s</ul></div>', $this->_option_menus );
				printf( '<div id="yt-content">%s</div>', $this->_option_fields );
			?>
			<div class="clear"></div>
		</div>
		
		<div id="yt-foot" class="yt-info-bar yt-clear">
			<ul class="yt-info-bar-left alignleft">
				<li><button id="yt-reset" type="button" class="button button-large submit-button reset-button"><i class="fa fa-refresh"></i> <span><?php esc_html_e( 'Options Reset', 'wpthms' ); ?></span></button></li>
			</ul>
			
			<ul class="alignright">
				<li><button type="button" class="button button-primary button-large yt-save-theme-options yt-save"><i class="fa fa-bolt"></i> <span><?php esc_html_e( 'Save All Changes', 'wpthms' ); ?></span></button></li>
				<li><button type="button" class="button button-large reset-button yt-save-theme-options yt-save-and-refresh"><?php esc_html_e( 'Save and Refresh', 'wpthms' );?></button></li>
			</ul>
		</div>
		<!--.save_bar-->
		
	</form>
	<div class="yt-clear"></div>

	<?php do_action( $this->_prefix . 'after_options_panel_form' ); ?>
</div>
<!--wrap-->