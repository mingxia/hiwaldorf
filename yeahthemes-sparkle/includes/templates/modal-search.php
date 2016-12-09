<div class="site-modal hidden" data-role="search">
<div class="site-modal-backdrop" data-action="close-modal" data-selector=".site-modal" data-role="search" data-remove-class="active" data-add-class="hidden" data-body-active="modal-active modal-search-active overflow-hidden"></div>
<div class="site-modal-inner">
	<div class="site-modal-content-wrapper">
		<div class="site-modal-content site-modal-search" data-request="<?php echo esc_attr( yt_get_options( 'site_modal_search_request_type') );?>">
			<form class="form-horizontal site-modal-content-inner search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">

				<h3 class="site-modal-heading"><?php _e('Search the site', 'wpthms');?></h3>
				<div class="form-group has-feedback">
				  <input class="form-control input-lg search-field" type="search" placeholder="<?php echo esc_attr_x( 'Text to search&hellip;', 'placeholder', 'wpthms' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'wpthms' ); ?>" data-swplive="true" autocomplete="off">
				  <span class="form-control-feedback"><i class="fa fa-search"></i></span>
				  <?php echo sprintf ( '<input type="submit" class="search-submit btn btn-primary hidden" value="%s">', esc_attr_x( 'Search', 'submit button', 'wpthms' ) );?>
				</div>
				
			</form>
			<?php if ('ajax' === yt_get_options( 'site_modal_search_request_type') ){ ?>
			<div class="site-modal-search-result margin-top-30 hidden"></div>
			<?php }?>
			<span class="site-modal-close" data-action="close-modal" data-selector=".site-modal" data-role="search" data-remove-class="active" data-add-class="hidden" data-body-active="modal-active modal-search-active overflow-hidden">&times;</span>
		</div>
	</div>
</div>
</div>