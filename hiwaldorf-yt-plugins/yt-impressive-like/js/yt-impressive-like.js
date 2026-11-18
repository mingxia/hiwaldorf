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
	 * Begin Impressive Like object
	 */
	wpthms.ImpressiveLikePlugin = {
		
		/**
		 * Action
		 */
		init: function( vars ){
			
			this._num = 50;
			this._eventRunning = false;
			this._vars = vars;
			
			var self = this;
			
			$(document).on('click', '.yt-impressive-like-button', function(e){
				self.action($(this));
				
				if( $(e.target).is( 'a' ) )
					e.preventDefault();
			});
		},
		/**
		 * Action
		 */
		action: function( _selector ){
			
			var self = this;
			
			if( self._eventRunning )
				return;	
				
			self._eventRunning = true;
	
			var $el = _selector,
				button_action = $el.find('.yt-il-like-action'),
				post_id 	= $el.data('post-id'), // Retrieve post ID from data attribute
				old_count 	= $el.data('count'),
				count_stt 	= $el.find('.yt-il-counter-number'),
				count_stt_wrapper = $el.find('.yt-il-counter'),
				like_class = 'yt-impressive-already-liked',
				unlike_class = 'yt-impressive-unliked',
				symbol = $el.find('.yt-il-symbol'),
				desc = $el.find('.yt-il-counter-desc');
			
			if( !$el.hasClass('yt-impressive-allow-unlike') && $el.attr('data-action') =='unlike'){
				self._eventRunning = false;
				return;
			}
			// Ajax call
			$.ajax({
				type: 'POST',
				url: self._vars.ajax_url,
				data: {
					action: 'yt_yeah_i_like_it',
					nonce: self._vars.nonce,
					post_like: 1,
					post_id: post_id,
					rating_type: $el.attr('data-action')
				},
				success: function(count){
					
					console.log(count);
					/**
					 * unlike
					 */
					if( parseInt( count) < parseInt( old_count ) ){
						$el.removeClass(like_class)
							.addClass(unlike_class)
							.attr('data-action', 'like');
							
						button_action.text( self._vars.like );
					
					/**
					 * like
					 */
					}else{
						$el.removeClass(unlike_class)
							.addClass(like_class)
							.attr('data-action', 'unlike');
						
						self.bubbles($el);
						button_action.text( self._vars.unlike );
						
						
						
					}
					
					if( parseInt( count) < 2 ){
						desc.text(self._vars.like);
					}else{
						desc.text(self._vars.likes);
					}
					
					$el.data('count', count);
					count_stt.text(count);
					
					self._eventRunning = false;
				}
			});
			
			
		},
		/**
		 * Icon bubble
		 */
		bubbles: function( _selector ){
			
			var self = this;
			
			if( !_selector )
				return;
				
			if( typeof self._vars == 'undefined')
				return;
		
			var $el = $(_selector),
				h = $(window).height(),
				w = $(window).width(),
				likeChild = $el.find('.yt-il-symbol'),
				numberOfHearts = parseInt(self._vars.number),
				heartClone; 			
				
			for (var i = 0; i < numberOfHearts; i++) {
				heartClone += '<span>' + self._vars.symbol + '</span>';
			}
			
			var hearts =  $(heartClone);
			hearts.appendTo(likeChild);
			
			
			console.log(self._num);
			
			hearts.each(function(){
				
				var scaleSize = (Math.random()*2.5); //same size should be 2  
				console.log(scaleSize);
	
				var originalOffset = $(this).position(),
					$this = $(this),
					tLeft = (Math.floor(Math.random()*601) - self._num)/3,
					tTop  = (Math.floor(Math.random()*601) - self._num)/3;
					
				//(Math.floor(Math.random()*601) - 150)
				//h-Math.floor(Math.random()* ( 1000 + num ) )
				console.log(tTop + ' - ' + tLeft);
				
				if( self.helper.has3d()){
					$(this).css({ 
						'-webkit-transform': 'translate(' + tLeft + 'px,' + tTop + 'px) scale(' + scaleSize.toString() + ')', 
						'transform': 'translate(' + tLeft + 'px,' + tTop + 'px) scale(' + scaleSize.toString() + ')', 
						'-moz-transform': 'translate(' + tLeft + 'px,' + tTop + 'px) scale(' + scaleSize.toString() + ')', 
						'-o-transform': 'translate(' + tLeft + 'px,' + tTop + 'px) scale(' + scaleSize.toString() + ')', 
						'opacity':0
					});
				}else{
				
					$(this).css({ 
						'left': tLeft + 'px',
						'top': tTop + 'px', 
						'opacity':0
					}); 
				}
				
				setTimeout(function(){
					
					hearts.remove();
					
				},2000);
				 
			});
			
			
			if( self._num < 650 ){
				self._num = self._num + 50;  
			}else{
				self._num = 50;
			}
			
			
		},
		/**
		 * Helper
		 */
		helper:{
		
			has3d:function() {
				var el = document.createElement('p'), 
					has3d,
					transforms = {
						'webkitTransform':'-webkit-transform',
						'OTransform':'-o-transform',
						'msTransform':'-ms-transform',
						'MozTransform':'-moz-transform',
						'transform':'transform'
					};
			
				// Add it to the body to get the computed style.
				document.body.insertBefore(el, null);
			
				for (var t in transforms) {
					if (el.style[t] !== undefined) {
						el.style[t] = "translate3d(1px,1px,1px)";
						has3d = window.getComputedStyle(el).getPropertyValue(transforms[t]);
					}
				}
			
				document.body.removeChild(el);
			
				return (has3d !== undefined && has3d.length > 0 && has3d !== "none");
			}
		}
	}
	
	/**
	 * Kickstart it
	 */
	if( typeof ytImpressiveLikeVars !== 'undefined' )
		wpthms.ImpressiveLikePlugin.init(ytImpressiveLikeVars);
	else
		return;
	
})(jQuery);