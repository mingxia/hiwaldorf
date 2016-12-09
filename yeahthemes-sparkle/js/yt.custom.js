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
	 * wpthms.RitaMagazine
	 *
	 * @since 1.0
	 */
	wpthms.RitaMagazine = {
		
		std:{},
		
		/**
		 * Init Function
		 * @since 1.0
		 */
		init: function(){
			
			var self = this,
				_framework = wpthms.Framework;
				
			this._vars = wpthms.themeVars;

			/*Prevent fast clicking*/
			
			//console.log(wpthms);

			var isScrolling = false,
				isStoppedScrolling = false,
				srollStopTimeout,
				fixMenuInterval,
				$event = wpthms.Framework.helpers.isTouch() ? 'touch' : 'mouseover';
			
			/**
			 * On load
			 */
			$(window).on('load', function(){
					
				_framework.ui.socialSharesCount('.shares-counter');
				//console.log('Everything is fully loaded');
				/**
				 *  Re-update Equal height column
				 */

				// _framework.helpers.equalHeight('.main-columns');
							
				/**
				 * Document ready
				 */

				if( $( '.yt-sliding-tabs-header' ).length ){
					//console.log($( '.yt-sliding-tabs-header' ).length);
					$( '.yt-sliding-tabs-header' ).each( function(){
						var $el = $(this);
					
						wpthms.General.ux.hoverScrollHorizontalHelperRestoreCurrentItem( $el, $( '> ul', $el ) );
						if( !_framework.helpers.isTouch()){
							wpthms.General.ux.hoverScrollHorizontal( $el, $( '> ul', $el ) );

						}
					});
				}

				if(!$( '#yt-page-loader' ).length ){
					$('<span/>', {
						'id': 'yt-main-spinner',
						'class': 'yt-preloader yt-ajax-loader'
					}).appendTo('body');
				}
					

				
			}).on('resize', function(){

				if( typeof resizeTimeOut !== 'undefined' )
					clearTimeout(resizeTimeOut);

				var resizeTimeOut = setTimeout( function(){
				    // Haven't resized in 100ms!
				    // _framework.helpers.equalHeight('.main-columns');
				    //console.log('called setTimeout')
				}, 100);
			}).on('srollStop', function(){
				
			})
			;
			/**
			 * Attach the event handler functions for theme element
			 */

			$(document)
				.on('openModal', function( e, role ){
					if('search' == role ){
						$( '.site-modal-search input.search-field' ).focus();
					}
					e.preventDefault();
				} )
				/*Smooth scroll for anchor*/
				// .on('click', '.site-main a[href*=#]:not([role="tab"]):not([data-toggle="tab"])', _framework.ui.smoothAnchor)
				/*Mobile Menu*/
				// .on('click', '.site-mobile-menu-toggle', self.ux.mobileNavigation )
				/*Social Sharing*/
				.on('click', '.social-share-buttons span', _framework.ui.socialSharing)
				.on('click', '.yt-font-size-changer span', wpthms.General.ux.fontSizeChanger)
				/*Tabby tab*/
				.on('click', '.yt-tabby-tabs-header ul li', _framework.ui.tabbyTabs )
				/*Sliding Cat*/
				.on('click', '.yt-sliding-tabs-header-trigger', self.ui.toggleSlidingCat )
				.on('click', '.site-banner', _framework.ux.tapToTop )
				.on('click', '.yt-ajax-posts-by-cat .yt-sliding-tabs-header ul li a', self.ux.widgetAjaxPostsByCategory )

				.on('click', '[data-action="open-modal"]', wpthms.General.ux.openModal )
				.on('click', '[data-action="close-modal"]', wpthms.General.ux.closeModal )

				.on('input', '.site-modal-search[data-request="ajax"] input.search-field', self.ux.modalAjaxSearch )
				
				.on('submit', 'form.yt-mailchimp-subscribe-form', _framework.widgets.mailchimpSubscription )

				
				.ready( function(){

					wpthms.Framework.bootstrap.compat();

					// Target your .container, .wrapper, .post, etc.
		   			$('#primary, #secondary, #tertiary').fitVids();

		   			self.ui.articleMetaInfo();

		   			lazy_load_init();
					
					/**
					 * Equal height
					 */
					// _framework.helpers.equalHeight('.main-columns');
					
					self.ux.mainNavigation();			
				
					if( $('.yeahslider:not(.initialized)').length ){
						$('.yeahslider:not(.initialized)').each(function(index, element) {
							if(!$(this).is('[data-init="false"]'))
								wpthms.General.ux.yeahSlider( $(this) );
						});
					}

					if( !$('body').hasClass('phone') ){
						var siteHeroBanner = $('.site-hero'),
							siteHeroEffect = siteHeroBanner.data('effect') !== undefined ? siteHeroBanner.data('effect') : 'flipIn';

						if (typeof(imagesLoaded) !== 'undefined' && typeof(imagesLoaded) === 'function') {
							siteHeroBanner.imagesLoaded( function(){
								siteHeroBanner.removeClass('yt-loading');

								if( 'none' != siteHeroEffect )
									wpthms.General.ux.animateSequence( '.site-hero', '.hero-brick', siteHeroEffect, 100, false );

								if( siteHeroBanner.hasClass('carousel') && siteHeroBanner.find('.yeahslider').length ){
									var siteHeroBannerCarousel = siteHeroBanner.find('.yeahslider');
									wpthms.General.ux.yeahSlider( siteHeroBannerCarousel );
								}
							});
						}else{
							//wpthms.General.ux.animateSequence( '.site-hero', '.hero-brick', siteHeroEffect, 200, false );
						}
						
						
					}else{
						$('.site-hero').removeClass('yt-loading').find('.hero-brick').removeClass('visibility-hidden');

						var siteHeroBanner = $('.site-hero');
						if( siteHeroBanner.hasClass('carousel') && siteHeroBanner.find('.yeahslider').length ){
							var siteHeroBannerCarousel = siteHeroBanner.find('.yeahslider');
							wpthms.General.ux.yeahSlider( siteHeroBannerCarousel );
						}
					}
					


					/*Bootstrap*/
					if( $('.yt-vc-accordion').length )
						wpthms.Framework.bootstrap.accordion();

					if($.fn.sharrre ){
						$('.sharrre-counter').each(function(){
							var $el = $(this);
						});
					}


					
					
					if( !$('#mobile-main-menu').length ){
						
						var socialLinks = '',
							mTopMenu = $('.top-navigation ul.menu').clone(true),
							mMainMenu = $('.main-navigation ul.menu').clone(true),
							toggler = '<a href="javascript:void(0)" class="main-menu-toggle hidden-md hidden-lg"><span class="bar1"></span><span class="bar2"></span><span class="bar3"></span></a>';

						if( $('.site-top-menu-right .site-social-networks').length ){
							socialLinks = $('.site-top-menu-right .site-social-networks').html();
						}else if( $('.main-navigation .site-social-networks').length ){
							// Remove social media network
							socialLinks = $('.main-navigation .site-social-networks').html();
						}
						// remove social networks
						mMainMenu.find('.site-social-networks').remove();
						mTopMenu.find('.site-social-networks').remove();
						// Remove menu item logo
						mMainMenu.find('.menu-item-logo').remove();

						if( socialLinks )
							socialLinks = '<div class="site-social-networks gray-2-secondary">' + socialLinks + '</div>';

						mTopMenu.attr('class', 'menu').attr('id', 'mobile-top-menu' );
						mMainMenu.attr('class', 'menu').attr('id', 'mobile-main-menu' );

						$('<div id="mobile-menu-nav-wrapper" class="mobile-navigation hidden-md hidden-lg"></div>').insertAfter('.inner-wrapper');
						var mMenuWrapper = $('#mobile-menu-nav-wrapper');

						mMenuWrapper.append(socialLinks).append( mTopMenu ).append( mMainMenu ).append( toggler );
						
					}
					
				})
				.on('click', '.main-menu-toggle', function(e){


					e.preventDefault();
					var $body = $('body');

					if( typeof $body.data( 'mobile-menu' ) == 'undefined' || 'closed' == $body.data( 'mobile-menu' ) ){
						$body.data( 'mobile-menu', 'opening' );
						$('body').addClass('active-mobile-menu');
					}else{						
						$body.data( 'mobile-menu', 'closed' );
						$('body').removeClass('active-mobile-menu');
					}

				}).on('click', '#mobile-menu-nav-wrapper .menu li:has(ul)', function(e){ 

					var thisLi = $(this);

					if( !thisLi.hasClass('active') ){
						if(thisLi.siblings('.active:has(ul)').length){
							thisLi.siblings('.active').find(' > ul').slideUp(function(){
								$(this).closest('li').removeClass('active');
							});
						}
						$('> ul', thisLi).slideToggle(function(){
							thisLi.toggleClass('active');
						});
						e.preventDefault();	
					}
					
				}).on('touchstart click', '.active-mobile-menu .inner-wrapper', function(e){

					if( $('body').hasClass('active-mobile-menu') && 'opening' == $('body').data( 'mobile-menu' ) ){
						var target = e.target,
							innnerWrapper = document.querySelector( '.inner-wrapper' ),
							$body = $('body');

						if( target == innnerWrapper ){

							$body.removeClass('active-mobile-menu');
							$body.data( 'mobile-menu', 'closed' );
							
						}
					}
				})
				.on( 'post-load', function () {
					/*Flexslider*/
					// New posts have been added to the page. by jetpack infinite scroll
					if( $('.yeahslider:not(.initialized)').length ){
						$('.yeahslider:not(.initialized)').each(function(index, element) {

							wpthms.General.ux.yeahSlider( $(this) );
						});
					}
					wpthms.Framework.bootstrap.compat();

					self.ui.articleMetaInfo();
					
					/*Media element*/
					/*if( $( '.wp-audio-shortcode' ).length || $( '.wp-video-shortcode' ).length ){
						$('.wp-audio-shortcode:not(.mejs-container), .wp-video-shortcode:not(.mejs-container)').mediaelementplayer();
					}*/
					/*Media element*/
					if( $( 'article.format-video' ).length ){
						$( 'article.format-video' ).fitVids();
					}
					// _framework.helpers.equalHeight('.main-columns');
					
				} )
				.on( 'touch' == $event ? 'click' : 'mouseover', '.main-navigation .sub-category-menu .sub-menu li', self.ux.mainNavigationSubmenu )
				
				;/*end $(document)*/

			// For Lazy load
			$( 'body' ).on( 'post-load', lazy_load_init ); // Work with infinite scroll

			function lazy_load_init() {
				$( 'div[data-lazy-src]' ).on( 'scrollin', { distance: 200 }, function() {
					lazy_load_image( this );
				});
			}

			function lazy_load_image( el ) {
				var $el = jQuery( el ),
					src = $el.attr( 'data-lazy-src' );
					// console.log( src );

				if ( ! src || 'undefined' === typeof( src ) )
					return;

				$('<img src="' + src + '"/>').on( 'load',function() {
					var image = $(this);
					// console.log(image);

					$el.css( 'background-image', 'url(' + src + ')');
					$el.unbind( 'scrollin' ) // remove event binding
					
					.removeAttr( 'data-lazy-src' )
					.attr( 'data-lazy-loaded', 'true' );

					image.remove();
		    	})
				// $el.fadeIn();
			}
				
			var timer = null;

			$(window).on('scroll', function (e) {
				
				 if ( timer ) { return; }
	   
			    timer = setTimeout( function() {
			    	onScrollFuncs();
			        
					timer = null;
			    } , 250 );

			    
			});


		    //Our animation callback
		    function onScrollFuncs() {
		        // console.log('Code goes here');

		        /**
		          * Init infiniteScrollPostThumbnailWidget
		          */
		        if( $('[data-action="load-more-post"][data-role="milestone"]:not(.all-loaded)').length )
					$('[data-action="load-more-post"][data-role="milestone"]:not(.all-loaded)').each( wpthms.General.ux.infiniteScrollPostThumbnailWidget );

				/**
				  * Init stickyHeader
		          */
		        if( $('body').hasClass( 'scroll-fix-header') )
					self.ux.stickyHeader();
				
		    }
			
		},
		/**
		 * Setup
		 * @since 1.0
		 */
		setup: function(){
		},
		/**
		 * Ui
		 * @since 1.0
		 */
		ui:{
			/**
			 * toggleSlidingCat
			 * @since 1.0
			 */
			toggleSlidingCat:function(){
				var $el = $(this),
					container = $el.closest('.yt-ajax-posts-by-cat'),
					header = $el.siblings('.yt-ajax-posts-by-cat-header'),
					expandClass = 'expanded-header',
					collapseClass = 'collapsed-header';
				
				
				header.hide().fadeIn();
				
				if( 'expand' === $el.data('action') ){
					container.removeClass(collapseClass).addClass(expandClass);
					$el.data('action', 'collapse');
				}else{
					container.removeClass(expandClass).addClass(collapseClass);
					$el.data('action', 'expand');
				}
			},
			articleMetaInfo: function(){

				$('script[data-role="header .entry-meta"]').each( function(){
	   				var $el = $(this),
	   					article = $el.closest('article');

	   				if( typeof article.data('appended') !== 'undefined' )
	   					return;
	   				
	   				var	dataMeta = $el.html(),
	   					target = $('.entry-header .entry-meta', article );

	   				
	   				//var newDataMeta = '';
	   				// Remove CDATA
	   				dataMeta = dataMeta.replace( '//<![CDATA[', '' );
	   				dataMeta = dataMeta.replace( '/*<![CDATA[*/', '' );
	   				dataMeta = dataMeta.replace( '<![CDATA[', '' );

	   				dataMeta = dataMeta.replace( '//]]>', '' );
	   				dataMeta = dataMeta.replace( '/*]]>*/', '' );
	   				dataMeta = dataMeta.replace( ']]>', '' );

	   				//console.log(dataMeta);


	   				target.html(dataMeta).show();
	   				article.data('appended', true );

	   			});
			}
		},
		ux:{
			stickyHeader: function(){	


				var mainNav = $('.site-banner');
				
				if( $('body').data('sHHO') == undefined ){
					var $mainNavOffset = mainNav.offset().top + mainNav.outerHeight();
					$('body').data('sHHO', $mainNavOffset );
				}

				//$('.site-top-menu').outerHeight() to $('.site-header').offset().top
				if ($(window).scrollTop() >= $('body').data('sHHO') ) {
			        $('body').addClass('sticky-header');

			        // Assign data height
			    	if( $('body').data('bannerH') == undefined || false == $('body').data('bannerH') ){
			    		$('body').data('bannerH', $('.site-banner').outerHeight());
			    	}

			    	// Set parent height
			    	$('.site-banner').css('height', $('body').data('bannerH') );	

			    	// Add Place holder
			        if( $('body').data('sHP') == undefined || false == $('body').data('sHP') ){
			    		$( '<div class="main-navigation-placeholder"></div>' ).css({
			    			'height': mainNav.outerHeight(),
			    			'clear': 'both'

			    		}).insertAfter( '.site-banner' );
			    		$('body').data('sHP', true);
			    	}    	


					// if is image logo, set it as background of sticky nav
			    	// if( $('.site-logo.image-logo img').length )
				    // 	$('.sticky-main-nav-wrapper').css({
				    // 		'background-image' : 'url(' + $('.site-logo.image-logo img').attr('src') + ')'
				    // 		// ,'background-repeat': 'no-repeat',
				    // 		// 'background-position': '20px center',
				    // 		// 'background-size': 'auto 40px'

				    // 	});
				    // if has admin bar, get offset top of nav wrapper
			        if( $('body').hasClass('admin-bar') )
			        	mainNav.css('top', $('body').offset().top);

			    }
			    // Release Nav
			    else {

			        $('body').removeClass('sticky-header');
			        mainNav.removeAttr('style');
			        $('.site-banner').css('height', '' );
			        $('.main-navigation-placeholder').remove();
			        
						$('body').data('sHP', false);
			    }
			},
			
			/**
			 * Modal Ajax search
			 * @since 1.0.4
			 */
			modalAjaxSearch:function( e ) {
				if( wpthms.Framework._eventRunning ){
					e.preventDefault();
					return;
				}
				//console.log('input');
				
				var $el = $(this),
					form = $el.closest('form'),
					result = form.siblings('.site-modal-search-result');

			    if($el.data( 'timer' ))
		        	clearTimeout( $el.data( 'timer' ));
				
				var timeout = setTimeout(function(){

			    	if( !$el.val() )
			    		return;
			    	wpthms.Framework._eventRunning = true;
			    	result.addClass('yt-loading').removeClass('hidden');
			    	//console.log('ajx');
			    	$.ajax({
						type: 'GET',
						url: wpthms._vars.ajaxurl,
						data: {
							action: 'yt-site-ajax-search',
							s: $el.val()
						},
						success: function(responses){
							//console.log(responses);
							result.removeClass('yt-loading').html(responses);
							//console.log('done');
							wpthms.Framework._eventRunning = false;
						}
					});
			    }, 1000);

			    $el.data( 'timer', timeout );
				
			},
			/**
			 * Ajax post by category widget
			 * @since 1.0
			 */
			widgetAjaxPostsByCategory:function(e) {
				// body...
				if( wpthms.Framework._eventRunning ){
					e.preventDefault();
					return;
				}

				var $el = $(this),
					dataSettingsContainer = $el.closest('.yt-ajax-posts-by-cat[data-settings]'),
					dataSettings,
					dataCatsSelector = $el.closest('li[data-id]'),
					dataCatsSelectorIndex = dataCatsSelector.index(),
					dataCats = 0,
					header = $el.closest('.yt-sliding-tabs-header'),
					contentContainer = header.siblings('.yt-sliding-tabs-content');


				if( !dataSettingsContainer.length){
					//console.log('not found');
					window.location.href = $(this).attr('href');
				}else{	

					/*Prevent duplicated data*/
					wpthms.Framework._eventRunning = true;				

					if( contentContainer.find('>*[data-index="' + dataCatsSelectorIndex + '"]').length ){
						wpthms.Framework._eventRunning = false;
						e.preventDefault();
						return;
					}

					//console.log('preventDefault');
					dataSettings = dataSettingsContainer.data('settings');
					e.preventDefault();

					if( dataCatsSelector.length )
						dataCats = dataCatsSelector.data('id');
	
					$('<div/>', {
						'class': 'yt-loading'
					}).prependTo(contentContainer).show(100);

					//console.log(dataCatsSelector.index());
					$.ajax({
						type: 'GET',
						url: wpthms._vars.ajaxurl,
						data: {
							action: 'yt-ajax-posts-by-category',
							nonce: wpthms.themeVars.widgetAjaxPostsByCatNonce,
							number: dataSettings.number,
							order: dataSettings.order,
							orderby: dataSettings.orderby,
							cats: dataCats,
							index: dataCatsSelectorIndex,
							//dataType: 'json',
						},
						success: function(responses){
							//console.log(responses);
							contentContainer.find('.yt-loading').hide(100, function(){ $(this).remove();});
							contentContainer.find('> *.active').fadeOut(300, function(){
								dataCatsSelector.addClass('active').siblings('li').removeClass('active');
								$(this).removeClass('active');
								$(responses).hide().appendTo(contentContainer).fadeIn();
							});
							
							wpthms.Framework._eventRunning = false;
						}
					});
				}
			},
			/**
			 * mobileNavigation
			 * @since 1.0
			 */
			mobileNavigation:function(){

				// if( wpthms.Framework._eventRunning )
				// 	return;

				// var $el = $(this),
				// 	loading = 'yt-loading',
				//     loaded = 'yt-loaded',
				//     pageWrapper = $('#page'),
				//     mobileNavWrapper = '#site-mobile-navigation';

				// if( $(mobileNavWrapper).length ){
				// 	$('body').addClass('active-mobile-menu');

				// 	if( !$('.inner-wrapper-mask').length )
				// 		$('<div/>', {
				// 			'class': 'inner-wrapper-mask'
				// 		}).appendTo(pageWrapper);

				// 	$('.inner-wrapper-mask').show();


				// }
				
			},
			/**
			 * mainNavigation
			 * @since 1.0
			 */
			mainNavigation:function( ){
				var $el = $(this);


				var $event = wpthms.Framework.helpers.isTouch() ? 'touch' : 'mouseover',
					$hoverTimeOut = 200,
					$leaveTimeOut = 300,
					menuHoverInterval;

				$( '.main-navigation' ).on( 'mouseover' == $event ? 'mouseenter' : 'click' , 'li', function(e){

					//alert($event);

			    	var $el = $(this);
					

					if( 'touch' == $event && !$el.hasClass('active') ){
						if( $el.hasClass('default-dropdown') || $el.hasClass('mega-menu-dropdown') )
							e.preventDefault();
					}

					
			    	menuHoverInterval = setTimeout( function(){

				    	$el.addClass('active');

				    	if( $el.hasClass('default-dropdown')){

				    		$( '> ul', $el ).show();

				    	}else if( $el.hasClass('menu-item-has-children') && $el.hasClass('mega-menu-dropdown') ){

				    		

				    		var thisMegamenuWrapper = $( '> .mega-menu-container', $el ),
				    			dataCats = $el.data('cats') !== undefined && $el.data('cats') ? $el.data('cats') : false,
				    			thisAtts = $el.data('atts'),
				    			loading = 'yt-loading',
				    			loaded = 'yt-loaded';

				    		if( wpthms.themeVars.megaMenu.ajax && !thisMegamenuWrapper.hasClass(loaded) && dataCats){

				    			thisMegamenuWrapper.addClass(loading).show();

				    			if( !dataCats )
				    				return;
				    			
					    		if( wpthms.Framework._eventRunning ){
									return;
								}
								
								/*Prevent duplicated data*/
								wpthms.Framework._eventRunning = true;

				    			$.ajax({
									type: 'POST',
									url: wpthms._vars.ajaxurl,
									data: {
										nonce: wpthms.themeVars.megaMenu.nonce,
										action: 'yt-site-mega-menu',
										data_cats: dataCats,
										atts: thisAtts,
										//dataType: 'json',
									},
									success: function(responses){
										thisMegamenuWrapper.removeClass(loading).addClass(loaded);
										var thisMegamenuInner = thisMegamenuWrapper.find('> *');
										thisMegamenuInner.hide().append(responses.html);
										thisMegamenuInner.find('.post-list').children(':not(.child-cat)').addClass( ( wpthms.themeVars.megaMenu.effect ? 'animated ' + wpthms.themeVars.megaMenu.effect : '' ));
										thisMegamenuInner.show(0, function(){
											setTimeout(function(){

												thisMegamenuWrapper.find('.post-list').children(':not(.child-cat)').removeClass( ( wpthms.themeVars.megaMenu.effect ? 'animated ' + wpthms.themeVars.megaMenu.effect : '' ));
											},1000);
										});

										$(document.body).trigger('post-load', responses );
										/*Prevent duplicated data*/
										wpthms.Framework._eventRunning = false;

									}
								});
				    		}else{
				    			thisMegamenuWrapper.show();
				    		}
				    	}
			    	}, $hoverTimeOut );
				}).on( 'mouseleave' , 'li', function(e){
					var $el = $(this);
				    	
			    	clearTimeout( menuHoverInterval );
			    	
			    	if( $el.hasClass('active') ){
						setTimeout( function(){
							$el.removeClass('active');


					    	if( $el.hasClass('default-dropdown')){
					    		setTimeout(function(){
									$( '> ul', $el ).hide();
					    		},300);
					    	}else if( $el.hasClass('mega-menu-dropdown') ){
					    		var thisMegamenuWrapper = $( '> .mega-menu-container', $el ),
					    			thisSubCatMenu = $('.sub-category-menu', thisMegamenuWrapper);

					    		setTimeout(function(){
									thisMegamenuWrapper.hide();
					    			thisMegamenuWrapper.find('.post-list').children(':not(.child-cat)').removeClass('animated ' + ( wpthms.themeVars.megaMenu.effect ? wpthms.themeVars.megaMenu.effect : 'fadeIn' ));

						    		/*Restore sub mega menu post list*/
						    		if( thisSubCatMenu.length ){
						    			thisSubCatMenu.find('[data-cat="all"]').addClass('current').siblings().removeClass('current');
							    		thisSubCatMenu.siblings( '[data-filter="all"]' ).show();
							    		thisSubCatMenu.siblings( ':not([data-filter="all"])' ).hide();
						    		}

					    		},300);
					    	}

						}, $leaveTimeOut);
					}

				});
			},
			/**
			 * mainNavigation
			 * @since 1.0
			 */
			mainNavigationSubmenu:function( e ){

				var $el = $(this),
					dataCat = $el.data( 'cat' ),
					loading = 'yt-loading',
					thisMenuNav = $el.closest('.sub-category-menu'),
					thisMegamenuWrapper = $el.closest( '.mega-menu-container' ),
					$target = $el.closest( '.post-list' );


				if( !$el.hasClass( 'current') )
					e.preventDefault();

				$el.addClass('current').siblings().removeClass('current');

				if( $el.hasClass( 'loaded') ){
					thisMenuNav.siblings().hide();
					thisMenuNav.siblings('[data-filter="' + dataCat + '"]').show();
				}else{


					if( wpthms.Framework._eventRunning ){
						return;
					}
					/*Prevent duplicated data*/
					wpthms.Framework._eventRunning = true;

					thisMegamenuWrapper.addClass(loading);

					$.ajax({
						type: 'GET',
						url: wpthms._vars.ajaxurl,
						data: {
							nonce: wpthms.themeVars.megaMenu.nonce,
							action: 'yt-site-sub-mega-menu',
							data_cat: dataCat,
							//dataType: 'json',
						},
						success: function(responses){
							$el.addClass('loaded');
							thisMegamenuWrapper.removeClass(loading);
							var thisMegamenuInner = thisMegamenuWrapper.find('> *');

							$target.append(responses);
							
							if( responses ){
								thisMenuNav.siblings().hide();
								thisMenuNav.siblings('[data-filter="' + dataCat + '"]').show();
							}

							/*Prevent duplicated data*/
							wpthms.Framework._eventRunning = false;

						}
					});
				}
			}

		}
		
		
	}
	
	/**
	 * wpthms.General
	 *
	 * @since 1.0
	 */
	wpthms.General = {
		ux:{
			/**
			 * Infinite Scroll
			 * @since 1.0
			 */
			infiniteScrollPostThumbnailWidget:function() {
				// if( wpthms.Framework._eventRunning ){
				// 	return;
				// }

				// console.log('xxxx')


				var $el = $(this);
				//Restrict request for each widget.
				if( $el.data('event-running') == undefined )
					$el.data('event-running', 0);

				if( 1 == parseInt( $el.data('event-running') ) ){
					return;
				}

				if( $el.is(':visible')){

					//console.log('is_visible');
					var content_offset = $el.offset();
					if ( window.pageYOffset >= Math.round(content_offset.top - (window.outerHeight + 150) ) ) {

					
						var $post_list = $el.siblings('.post-list-with-thumbnail'),
							dataSettings = $post_list.data('settings'),
							offset = parseInt( dataSettings.offset );
							if( !offset )
								dataSettings.offset = dataSettings.number;
							// console.log( 'offset:' + dataSettings.offset );

						var erunning = parseInt( $el.data('event-running') );
						$el.data('event-running', erunning+1 );
						// console.log( 'event runnin:' + erunning);

						//wpthms.Framework._eventRunning = true;
						$el.addClass( 'yt-loading' );
						//console.log(dataSettings);
				    	$.ajax({
							type: 'GET',
							url: wpthms._vars.ajaxurl,
							data: {
								action: 'yt-site-ajax-load-posts-infinitely',
								data: dataSettings
							},
							success: function(responses){
								// console.log(responses);
								if(responses && !responses.error ){
									if( !responses.all_loaded )
										$post_list.append( responses.html );
									
									// if( responses.offset )
									// 	$post_list.data('offset', parseInt( responses.offset ) );
									

									if( responses.all_loaded ){
										$el.addClass('all-loaded').removeClass('yt-loading').html( responses.html );
										setTimeout( function(){ $el.fadeOut(function(){$(this).remove()}); }, 3000 );
									}
								}
								$(document.body).trigger('post-load', responses);
								//console.log(document.body);

								//wpthms.Framework._eventRunning = false;

								erunning = parseInt( $el.data('event-running') );
								$el.data('event-running', erunning-1 );
							}
						});
				    	
				    	dataSettings.offset = dataSettings.offset + parseInt( dataSettings.number ) ;

				    	// console.log( dataSettings.offset );
					}
				}

			},
			
			hoverScrollHorizontalHelperRestoreCurrentItem: function($outer, $inner, $child){
				$child = $child || 'li';
				var activePointer = $inner.find( $child + '.active').length ? $inner.find( $child + '.active') : $inner.find( $child + ':first-child'),
					activePointerW = activePointer.width(),
					extraW = $outer.width() > activePointerW ? ($outer.width() - activePointerW)/2 : 0,
					animateTo = activePointer.is(':first-child') ? activePointer.position().left : activePointer.position().left+10-extraW;
					$outer.animate({scrollLeft:animateTo }, 800, function(){ 
						//console.log('aaa'); 
					});
			},
			
			hoverScrollHorizontal: function($outer, $inner, $child){
				$child = $child || 'li';

				var extra = 100,
					divWidth = $outer.width(), /*Get menu width*/
					lastElem = $inner.find( $child + ':last'), /*Find last image in container*/
					triggering = false;
				//Remove scrollbars
				$outer.css({
					overflow: 'hidden'
				});
				
				$outer.scrollLeft(0);
				//When user move mouse over menu
				var resetLastchild;
				$outer.on('mousemove', function(e){
					triggering = true;
					var containerWidth = lastElem.position().left + lastElem.outerWidth() + 2 * extra;
					var left = (e.pageX - $outer.offset().left) * (containerWidth-divWidth) / divWidth - extra;
					$outer.scrollLeft(left);
					
					resetLastchild = setTimeout(function(){
						lastElem = $inner.find( $child + ':last');
					},2000);
				});
				
				$outer.on('mouseleave', function(e){
					clearTimeout( resetLastchild );
					triggering = false;
			        divWidth = $outer.width(),
			        lastElem = $inner.find( $child + ':last');
					
					setTimeout(function() {
						if( !triggering)
							wpthms.General.ux.hoverScrollHorizontalHelperRestoreCurrentItem( $outer, $inner, $child );
					}, 2000);
					
				});
				//Update divWidth
				$(window).on( 'resize focus', function(){
					clearTimeout($.data(this, 'resizingTimer'));
				    $.data(this, 'resizingTimer', setTimeout(function() {
				        divWidth = $outer.width();
				        //console.log('done Resizing');
				    }, 250));
				});
			},
			
			hoverScrollVertical: function($outer, $inner, $child){
				$child = $child || 'li';
				var extra           = 100;
				//Get menu width
				var divHeight = $outer.height();
				//Remove scrollbars
				$outer.css({
					overflow: 'hidden'
				});
				//Find last image in container
				var lastElem = $inner.find(':last');
				$outer.scrollTop(0);
				//When user move mouse over menu
				$outer.on('mousemove', function(e){
					var containeHeight = lastElem.position().top + lastElem.outerHeight() + 2 * extra;
					var top = (e.pageY - $outer.offset().top) * (containeHeight-divHeight) / divHeight - extra;
					$outer.scrollTop(top);
				});
			},
			animateSequence: function( _container, _children, _animClass, _timeOut, _randomize ){
				
				_animClass = _animClass || 'zoomIn';
				_animClass = 'animated ' + _animClass;
				
				
				_timeOut = _timeOut && false == isNaN( parseInt( _timeOut ) ) && parseInt( _timeOut ) ? _timeOut : 500;
				
				var animRandomization = _randomize == true || _randomize == false ? _randomize : true,
					hiddenClass = 'visibility-hidden',
					childrenCount = $( _container ).find(_children).length;
					
				if( !childrenCount )
					return;
				
				$( _children, $( _container )).each(function(index, element) {
					
					//console.log(index);
					
					var $el = $(this),
						delayTime = animRandomization === true ? Math.floor(Math.random() * (_timeOut * 3)) : index * _timeOut;
					
					setTimeout( function() {
						$el.removeClass(hiddenClass).addClass(_animClass);
						
						if(index == 0){
							$el.addClass('fired');	
						}
						//$el.addClass('fired');
						$el.addClass('index-'+ (index + 1));
						// $el.addClass('temp-'+ index * _timeOut);
						//Remove the class after done animation
						setTimeout( function() {$el.removeClass(_animClass)}, 500*10);
					//}, (index !== 0 ? index * timeOut : 0 ));	
					}, (index == 0 && !animRandomization ? 0 : delayTime ) );	
				});
				
			},
			yeahSlider: function( $el ){
				var yeahSliderDefaultSettings = {
						namespace: 'yeahslider-',
						animation: 'slide',
						init: function(slider) {

							slider.addClass('initialized');

							slider.on('mouseenter', function(){
								$(this).addClass('hover');
							});

							$(slider).on('mouseleave', function(){
								var el = $(this);
								setTimeout( function(){
									el.removeClass('hover');
								}, 1000);
							});
							//console.log(slider);
						},
						start: function(slider){ 
						},
						before: function(slider){
							var current = slider.currentSlide == slider.count - 1 ? 0 : slider.currentSlide+1,
								css3Effect = slider.vars.css3Effect;
							if( css3Effect )
								slider.slides.removeClass( css3Effect ).eq( current).addClass( css3Effect );
						},
						after: function(slider){
						},
					},
					yeahSliderCustomSettings = $el.data('settings') !== undefined ? $el.data('settings') : {},
					yeahSliderSettings = $.extend(yeahSliderDefaultSettings, yeahSliderCustomSettings);
					/*Init*/
					$el.flexslider(yeahSliderSettings);
					//console.log('init');
			},

			/**
			 * fontSizeChanger
			 * @since 1.0
			 */
			fontSizeChanger:function(e){
				var $el = $(this),
					scalableContent = $('#content .entry-content'),
					defaultFontSize = parseInt(scalableContent.css('font-size')),
					parent = $el.closest('.yt-font-size-changer');
				
				if( parent.attr('data-font-size') == undefined){
					parent.attr('data-font-size', defaultFontSize);
				}
				
				if( $el.hasClass('font-size-plus')){
					if(defaultFontSize < 20){
						defaultFontSize = defaultFontSize +1;
						//console.log('xxxxxx');
					}
				}else if( $el.hasClass('font-size-minus')){
					if(defaultFontSize >12)
						defaultFontSize--;
				}else{
					defaultFontSize = parseInt(parent.attr('data-font-size'));
				}
				
				scalableContent.css('font-size',defaultFontSize);
				
				//console.log(defaultFontSize);
				//$('.yt-font-size-changer')
			},

			/**
			 * Widgets
			 * @since 1.0
			 */
			openModal: function(e){
				var $el = $(this),
				dataRole = $el.data('role'),
				dataSelector = $el.data('selector'),
				dataAddClass = $el.data('add-class'),
				dataRemoveClass = $el.data('remove-class'),
				dataBodyActive = $el.data('body-active');

				$('body').addClass(dataBodyActive);
				$(dataSelector + '[data-role="' + dataRole + '"]').removeClass(dataRemoveClass).addClass(dataAddClass);
				$(document.body).trigger('openModal', dataRole );
				e.preventDefault();
			},
			closeModal: function(e){
				var $el = $(this),
				dataRole = $el.data('role'),
				dataSelector = $el.data('selector'),
				dataAddClass = $el.data('add-class'),
				dataRemoveClass = $el.data('remove-class'),
				dataBodyActive = $el.data('body-active');

				$('body').removeClass(dataBodyActive);
				$(dataSelector + '[data-role="' + dataRole + '"]').removeClass(dataRemoveClass).addClass(dataAddClass);
				$(document.body).trigger('closeModal', dataRole );
				e.preventDefault();
			}
		}
	};
	
	/**
	 * wpthms.Framework
	 *
	 * @since 1.0
	 */
	wpthms.Framework = {
		init:function(){

			this._eventRunning 	= false;
		},
		/**
		 * Ui
		 * @since 1.0
		 */
		ux:{
			
			tapToTop: function( e ){
				var $el = $(this);
				
				if( $el.attr('id') == undefined )
					return
				if( e.target.id != $el.attr('id') )
					return;

				$("html, body").animate({scrollTop:0},"fast")
				
			}
		},
		/**
		 * Ui
		 * @since 1.0
		 */
		ui:{
			/**
			 * SocialSharesCount
			 * @since 2.0.5
			 */
			socialSharesCount: function( selector ){

				var apis = {
						//new FQL method by Sire
						facebook: '//graph.facebook.com/?id=',
					    pinterest: '//api.pinterest.com/v1/urls/count.json?url='
					},
					// Replace text if count = 1
					replaceText = function($el){
						if( typeof $el.data('shares') !== 'undefined'
							&& 1 == parseInt( $el.data('shares') )
							&& $el.siblings('.share-text').length ){

							var $shareText = $el.siblings('.share-text');
								$shareText.text( $shareText.data('singular') );
						}
					};

				$(selector).each(function(){

					var $el = $(this).find('.shares-count[data-service]'),
						service = typeof $el.data('service') !== 'undefined' ? $el.data('service') : '';

					if( !service )
						return;

					if( $el.data('init') )
						return;
					
					var url = typeof $el.data('url') == 'undefined' || $el.data('url') == false ? $el.closest('[data-url]').data('url') : $el.data('url');
						url = encodeURIComponent( url );

					if ( service in apis ) {
						$.ajax({
							dataType: 'jsonp',
							url: apis[service] + url ,
							success: function(responses){

								var count = 0;

								if(!responses)
									return;
								
								if( 'facebook' === service && 'share' in responses && 'share_count' in responses.share ){
									count = responses.share.share_count;
								}else if( 'pinterest' === service && 'count' in responses ){
									count = responses.count;
								}

								$el.text(wpthms.Framework.helpers.shortenTotal( count ).replace(".", ",") );
								$el.data('shares', count );
								$(selector).attr('data-init', true );
								replaceText($el);

							}
						})
					}


				});
			},
			/**
			 * socialSharing
			 * @since 1.0
			 */
			socialSharing:function(e){
				
				e.preventDefault();
			
				var $el 	= $(this),
					service= $el.data('service'),
					wrapper= $el.closest('.social-share-buttons'),
					w		= 560,
					h		= 350,
					x		= Number((window.screen.width-w)/2),
					y		= Number((window.screen.height-h)/2),
					url 	= encodeURIComponent( wrapper.data('url') ),
					source 	= encodeURIComponent( wrapper.data('source') ),
					media 	= encodeURIComponent( wrapper.data('media') ),
					via 	= typeof $el.data('via') !== 'undefined' ? $el.data('via') : '',
					title 	= wrapper.data('title'),



					href = '';
					title 	= encodeURIComponent( title );

					title 	= via ? title + '&via=' + encodeURIComponent( via ) : title;
				
				if( 'twitter' === service ){
					href = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title;
				}else if( 'facebook' === service ){
					href = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
				}else if( 'google-plus' === service ){
					href = 'https://plus.google.com/share?url=' + url;
				}else if( 'linkedin' === service ){
					href = 'http://www.linkedin.com/shareArticle?mini=true&url=' + url + '&title=' + title + '&source=' + source;
				}else if( 'pinterest' === service ){
					href = '//pinterest.com/pin/create/button/?url=' + url + '&media=' + media + "&description=" + title;
				}else if( 'tumblr' === service ){
					href = '//www.tumblr.com/share/photo?source='+ media +'&caption=' + title + '&clickthru=' + url;
				}else if( 'stumble-upon' === service ){
					href = '//www.stumbleupon.com/badge/?url='+ url;
				}
				
				if( 'more' !== service ){
					window.open( href,'','width=' + w + ',height=' + h + ',left=' + x + ',top=' + y + ', scrollbars=no,resizable=no');
				}else{
					$el.siblings('[data-show="false"]').toggleClass('hidden');
				}
				
				//console.log(wrapper.data('title'));
			},
			
			/**
			 * socialSharing
			 * @since 1.0
			 */
			smoothAnchor: function(e){
				var $el = $(this),
					target = window.location.href.split('#'),
					currentUrl = $el.attr('href').split('#'),
					id = typeof currentUrl[1] == 'undefined' ? '' : currentUrl[1];
				
				if( ( currentUrl[0] == target[0] || '' == currentUrl[0]) && $( '#' + id ).length ){
					
					$('html, body').animate({
						scrollTop: $( '#' + id ).offset().top
					}, 800);
					
					e.preventDefault();
				}
			},
			/**
			 * tabbyTabs
			 * @since 1.0
			 */
			tabbyTabs:function(e){
				
			/*	$('.yt-tabby-tabs-content').find('>*:first').addClass('active');
				$('.yt-tabby-tabs-header ul li:first').addClass('active');*/
				
				e.preventDefault();
				
				var $el = $(this),
					wrapper = $el.closest('.yt-tabby-tabs-header'),
					position = wrapper.hasClass('yt-tabby-tabs-header-bottom') ? 'bottom' : 'top',
					tabContent = wrapper.siblings('.yt-tabby-tabs-content'),
					index = $el.index();
				
				if( tabContent.find('>*[data-index]').length ){
					$el.addClass('active').siblings().removeClass('active');
					tabContent.find('>*[data-index="' + index + '"]').fadeIn(200,function(){
						//console.log('tag triggered');
					}).addClass('active').siblings().hide().removeClass('active');

				}else{
					if( tabContent.find('>*').eq(index).length ){
						$el.addClass('active').siblings().removeClass('active');
						tabContent.find('>*').eq(index).fadeIn(200).addClass('active').siblings().hide().removeClass('active');
					}
				}
			},
			
			
		},

		/**
		 * Bootstrap
		 * @since 1.0
		 */
		bootstrap:{
			/**
			 * Boostrapizr
			 */
			compat: function(){
				$('[type="submit"]:not(.btn)').addClass( 'btn btn-primary' );
				$('[type="reset"], [type="button"], button:not([type]), .button:not([type="submit"])').addClass( 'btn btn-default' );
				// $('').addClass( 'btn btn-default' );
				$('[type="text"], [type="password"], [type="search"], [type="email"], [type="number"], [type="url"], textarea').addClass('form-control');
				
			},
			accordion: function(){

				$('.yt-vc-accordion').each(function(){
					var $el = $(this),
						dataSettings = $el.data('settings'),
						activeTab = typeof dataSettings.active_tab !== 'undefined' && parseInt( dataSettings.active_tab ) ? parseInt( dataSettings.active_tab ) : 0;

					/**
					 * Active tab
					 */
					if( activeTab ){
						var activePanel = $el.find( '.panel:eq(' + ( activeTab - 1 ) + ')' );
						activePanel.addClass('active');
						activePanel.find( '.panel-collapse:not(.in)' ).collapse('show');
					}

					/**
					 * Click trigger
					 */
					$( '.panel-heading', $el ).on('click.bs.collapse.data-api', function(e){
						
						e.preventDefault();
						
						var $this   = $(this),
							$target = $this.next(),
							data    = $target.data('bs.collapse'),
							option  = typeof dataSettings.collapsible !== 'undefined' && 'yes' == dataSettings.collapsible ? 'toggle' : 'show';

					    $this.closest('.panel').addClass('active').siblings().removeClass('active');

					    $this.parent().siblings().find('.panel-collapse.in').collapse('hide');

						$target.collapse(option);

					});
				});

					

			}
		},

		/**
		 * Widgets
		 * @since 1.0
		 */
		widgets:{

			mailchimpSubscription: function(e){
				e.preventDefault();

				if( wpthms.Framework._eventRunning )
					return;

				var $el = $(this).closest('.yt-mailchimp-subscription-form-content'),
					nonce = $el.find('[name="yt_mailchimp_subscribe_nonce"]').val(),
					list = $el.find('[name="yt_mailchimp_subscribe_list"]').val(),
					check = $el.find('[name="yt_mailchimp_subscribe_check"]').val(),
					email = $el.find('[name="yt_mailchimp_subscribe_email"]').val(),
					fname = $el.find('[name="yt_mailchimp_subscribe_fname"]').length ? $el.find('[name="yt_mailchimp_subscribe_fname"]').val() : '',
					lname = $el.find('[name="yt_mailchimp_subscribe_lname"]').length ? $el.find('[name="yt_mailchimp_subscribe_lname"]').val() : '',
					result = $el.find('.yt-mailchimp-subscription-result');
				
				$el.addClass('yt-loading');
				wpthms.Framework._eventRunning 	= true;
				result.fadeOut().html('');

				$.ajax({
					type: 'POST',
					url: wpthms._vars.ajaxurl,
					data: {
						action: 'yt-mailchimp-add-member',
						nonce: nonce,
						email: email,
						fname: fname,
						lname: lname,
						list: list,
						checking: check
					},
					success: function(responses){
						wpthms.Framework._eventRunning 	= false;
						$el.removeClass('yt-loading');
						//console.log(responses);
						result.html(responses).fadeIn();
						setTimeout( function(){
							result.fadeOut();
						}, 10000 );
					},
				});
			}
		},
		/**
		 * Helpers
		 * @since 1.0
		 */
		helpers: {
			/**
			 * Browser supports style
			 */
			thisBrowserSupportsStyle: function(style) {
				var vendors = ['Webkit', 'Moz', 'ms', 'O'];
				var num_vendors = vendors.length;
				var dummy_el = window.document.createElement('div');

				// First test the bare style without prefix
				if (dummy_el.style[style] !== undefined) {
					return true;
				}

				// Test the camel-cased vendor-prefixed styles
				style = style.replace(/./, function(first) {return first.toUpperCase();});
				for (var i = 0; i < num_vendors; i++) {
					var pfx_style = vendors[i] + style;
					// The browser will return an empty string if a style is supported but not present, and undefined if the style is not supported
					if (dummy_el.style[pfx_style] !== undefined) {
						return true;
					}
				}
				return false;
			},
			/**
			 * Is Valid email
			 */
			isValidEmailAddress: function (emailAddress) {
				var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    			return regex.test(emailAddress);
			},
			/**
			 * inViewport
			 */
			inViewport: function(_selectors, _extra){
				
				if(!_selectors.length)
					return;
				
				_extra = _extra || 0;
				var scrollTop = window.pageYOffset,
					docViewTop = scrollTop - _extra,
					docViewBottom = scrollTop + window.outerHeight,
					elemTop = _selectors.offset().top,
					elemBottom = ( elemTop + _selectors.outerHeight() ) - _extra;
					
					//console.log( elemTop + '-' + docViewTop);
				return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
			},
			/**
			 * equalHeight
			 */
			equalHeight: function( _selectors, _all, _breakpoint ){


				$( _selectors ).css('min-height', '');

				_breakpoint = parseInt( _breakpoint ) || 992;
				if( $(window).width() > _breakpoint ){
					
					if( _all ){
						$( _selectors ).siblings().css('min-height', '' );
					}
					
					_all = _all || false;
					
					var height = $( _selectors ).outerHeight();
					
					$( _selectors ).siblings().each(function(index, element) {
						
						var thisHeight = $(this).outerHeight();
						
						if( thisHeight > height ){
							height = thisHeight;
						}
					});
					
					setTimeout(function(){
						$( _selectors ).css('min-height', height + 'px' );
						
						if( _all ){
							$( _selectors ).siblings().css('min-height', height + 'px' );
						}
					},100);
				}
				
			},
			/**
			 * isTouch
			 */
			isTouch: function() {
				if( 'ontouchstart' in document.documentElement )
					return true;
				else
					return false;
			},
			/**
			 * isIOS
			 */
			isIOS: function() {
				if( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) )
					return true;
				else
					return false;
			},
			/**
			 * hasParentClass
			 */
			isMobile: function() {
				var check = false;
				(function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
				return check;
			},
			isBrowser: function( _class ) {
				if( !_class )
					return false;
				if( $('body').hasClass( _class + '-browser' )){
					return true;
				}else{
					return false;
				}
			},
			/**
			 * hasParentClass
			 */
			hasParentClass: function( e, classname ) {
				if(e === document) return false;
				if( $(e).hasClass(classname ) ) {
					return true;
				}
				return e.parentNode && yt_hasParentClass( e.parentNode, classname );
			},

			windowAnimationFrame: function(){
				var lastTime = 0;
			    var vendors = ['ms', 'moz', 'webkit', 'o'];
			    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
			        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
			        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
			                || window[vendors[x]+'CancelRequestAnimationFrame'];
			    }

			    if (!window.requestAnimationFrame)
			        window.requestAnimationFrame = function(callback, element) {
			            var currTime = new Date().getTime();
			            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
			            var id = window.setTimeout(function() { callback(currTime + timeToCall); },
			                    timeToCall);
			            lastTime = currTime + timeToCall;
			            return id;
			        };

			    if (!window.cancelAnimationFrame)
			        window.cancelAnimationFrame = function(id) {
			            clearTimeout(id);
			        };
			},
			shortenTotal: function(num, digits) {
				digits = digits || 1;
				if (num >= 1e9){
					num = (num / 1e9).toFixed(digits) + "B";
				} else if (num >= 1e6){
					num = (num / 1e6).toFixed(digits) + "M";
				} else if (num >= 1e3){
					num = (num / 1e3).toFixed(digits) + "k";
				}
				if( num )
					return num.toString();
				else
					return '0';

			}
			
		}
	};
	
	
	
	wpthms.Framework.init();
	wpthms.RitaMagazine.init();
	
})(jQuery);