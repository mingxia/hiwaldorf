// JavaScript Document
/*!
 * wpthms
 *
 * Custom Javascript
 */
 
if (typeof wpthms == 'undefined') {
	var wpthms = {};
}

;(function($) {
	
	"use strict"; 
	
	if (typeof wpthms == 'undefined') {
	   return;
	}
	
	/**
	 * Begin SimplePostViewCounterPlugin object
	 */
	wpthms.SimplePostViewCounterPlugin = {
		
		/**
		 * Action
		 */
		init: function( vars ){
			
			this._num = 50;
			this._eventRunning = false;
			this._vars = vars;
			
			var self = this;
			
			$(document).ready( function() {
				setTimeout(function(){
					self.action();
				}, 5000);
			});
		},
		/**
		 * Action
		 */
		action: function(  ){
			
			var self = this;
			
			if( self._eventRunning )
				return;	
				
			self._eventRunning = true;

			// Ajax call
			$.ajax({
				type: 'POST',
				url: self._vars.ajax_url,
				data: {
					action: 'yt_simple_post_views_ajax',
					nonce: self._vars.nonce,
					post_id: self._vars.post_id
				},
				success: function(){
					
					//console.log('success');
					
					self._eventRunning = false;
				}
			});
			
			
		},
		
	}
	
	/**
	 * Kickstart it
	 */
	if( typeof ytSimplePostViewVars !== 'undefined' )
		wpthms.SimplePostViewCounterPlugin.init(ytSimplePostViewVars);
	else
		return;
	
})(jQuery);