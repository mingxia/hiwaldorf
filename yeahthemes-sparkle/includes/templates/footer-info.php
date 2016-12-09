<div class="site-info footer-info full-width-wrapper">
	<div class="container">
		<?php 
		if( yt_get_options('footer_text_left') ){
			echo sprintf( '<div class="pull-left text-left">%s</div>', yt_get_options('footer_text_left') );
		}
		if( yt_get_options('footer_text_right') ){
			echo sprintf( '<div class="pull-right text-right">%s</div>', yt_get_options('footer_text_right') );
		}
		
		?>
	</div>
</div><!-- .site-info -->