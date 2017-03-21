/**
 * YEAHTHEME OPTIONS FRAMEWORK js
 *
 * contains the core functionalities to be used
 */
if (typeof wpthms == 'undefined') {
	var wpthms = {};
}

;(function ($) {
	
	'use strict';
	
	/*-----------------------------------------------------------------------------------
	 * Yeahtheme Optios framework
	 * @author wpthms
	 * @since 1.0
	 * @support wp 3.5+
	 *-----------------------------------------------------------------------------------*/
	
	if (typeof wpthms == 'undefined') {
	   return;
	}
	
	wpthms.OptionsFramework = {
		/**
		 * Init Function
		 */
		init: function(){
			
			var self = this;
			
			this._container = $('body.yeah-framework'),
			this._successPopup = 'yt-popup-save',
			this._failPopup = 'yt-popup-fail',
			this._resetPopup = 'yt-popup-reset',
			this._ytAjaxifyingClass = 'yt-ajaxifying';
			this._optionVars = {};
			
			
			var scrollTop = 0,
				lastScrollTop = 0;

			
			$(document).ready(function() {
				self.documentReady();


			});

			$(window).on('load', function(){
				self.windowLoad();


				
			}).on('scroll', function(e){
				
				self.optionSetingsPanel.stickyPanelNav();

			}).on('resize', function(){
				
				clearTimeout($.data(this, 'resizeTimeOut' ));

				$.data(this, 'resizeTimeOut', setTimeout( function(){
				    // Haven't resized in 100ms!
				   
				    //console.log('called setTimeout')

					
					self.optionSetingsPanel.panelNavEqualHeight();
					self.optionSetingsPanel.stickyPanelNav();
				 	
				}, 300) );
			}).on('tab-group-changed', function(){
				    self.optionSetingsPanel.panelNavEqualHeight();
					self.optionSetingsPanel.stickyPanelNav();
			});
		},
		/**
		 * Function for onload put here
		 */
		documentReady: function(){
			
			var self = this;
			
			/**
			 * hides warning if js is enabled
			 */			
			$('#js-warning').hide();
			
			if( $('.yt-section-oembed').length ){
				
				$('.yt-section-oembed').fitVids();
			};

			/*Setting panel*/
			self.optionSetingsPanel.displayRecentActivatedTab();
			self.optionSetingsPanel.optionPanelNavAction();
			self.optionSetingsPanel.toggleSettingPanelFullwidth();
			
			/*Media uploader*/
			
			wpthms.OptionsFrameworkUploader.removeFile();
			
			wpthms.OptionsFrameworkUploader.galleryUpload();
			wpthms.OptionsFrameworkUploader.fileUpload();			
				
			wpthms.OptionsFrameworkUploader.mediaUpload();
			
			/*COntrols & ajaxifying actions*/
			self.optionSettingBindEvents();
			
			self.optionSettingPanelAjaxifyingActions();
			self.optionSettingControlsAction.typographyClone();
		},
		/**
		 * Function for onload put here
		 */
		windowLoad: function(){
			
			var self = this;
			
			self.helper.doneProcessing();
			self.optionSetingsPanel.panelNavEqualHeight();
			
			/**
			 *	Tipsy
			 */
		
			if ($().tipsy) {
				$('.tip-tip, .yt-select-wrapper ').tipsy({
					fade: true,
					gravity: 's',
				});
			}
			
			if(window.location.hash) {
			  // Fragment exists
			  var hash = window.location.hash.substring(1);
			  //console.log(hash);
			  //$( 'a[href*="#' + hash + '"]' ).trigger('click');
			  //$( 'a[href*="#' + hash + '"]' ).remove();
			} else {
			  // Fragment doesn't exist
			}
			
			self.metaboxFriendlyUi.postFormatSwitcher();
			
			self.optionSettingControlsAction.generalInit();

			
		},
		/**
		 * Ajaxifying helper
		 * 
		 */
		helper: {
			/**
			 * Processing
			 */
			processing: function(){
				$('body').addClass( wpthms.OptionsFramework._ytAjaxifyingClass);
				
				
			},
			doneProcessing: function(){
				$('body').removeClass( wpthms.OptionsFramework._ytAjaxifyingClass);
			}
		},
		/**
		 * Option Settings Panel object
		 * onload
		 */
		
		optionSetingsPanel:{

			panelNavEqualHeight: function(){

				if( !$('#yt-nav').length && !$('#yt-content').length)
					return;

				var nav = $('#yt-nav'),
					content = $('#yt-content'),
					height = 0;

				nav.css('height', '');
				content.css('min-height', '');

				if( $(window).width() > 768 ){
					setTimeout( function(){
						if( content.outerHeight() > $('#yt-nav > ul').outerHeight() )
							height = content.outerHeight();
						else
							height = $('#yt-nav > ul').height();

						//console.log(height);
					
						nav.css('height', height );
						content.css('min-height', height );
					}, 250);
				}
			},
			
			stickyPanelNav: function( ){

				if( typeof scrollTop == "undefined" )
					var scrollTop;

				if( typeof direction == "undefined" )
					var direction = 'down';

				
				scrollTop = $(window).scrollTop();


				if( scrollTop > wpthms.OptionsFramework.lastScrollTop ){
					direction = 'down';
				}else{
					direction = 'up';
				}

				if( $('#yt-nav').length ){
					if( typeof navBar == "undefined" )
						var navBar = $('#yt-nav');

					if( typeof navBar.data('offsetTop') == 'undefined')
						navBar.data('offsetTop', navBar.offset().top );

					if( $(window).width() > 768 && $('#yt-nav > ul').outerHeight() <  $('#yt-content').outerHeight() ){
		

						if( scrollTop > navBar.data('offsetTop' ) - 32 && scrollTop < $('#yt-foot').offset().top - $('#yt-nav > ul').outerHeight()){
							if( $(window).height() < $('#yt-nav > ul').outerHeight() ){
								if( 'up' == direction ){
									$('#yt-nav').addClass('affix-bottom-fix');
									$('#yt-nav').removeClass('affix-top');
									$('#yt-nav').removeClass('affix-bottom');
								}else{
									$('#yt-nav').addClass('affix-top');
									$('#yt-nav').removeClass('affix-bottom');
									$('#yt-nav').removeClass('affix-bottom-fix');
								}
							}else{
								$('#yt-nav').addClass('affix-top');
								$('#yt-nav').removeClass('affix-bottom');
								$('#yt-nav').removeClass('affix-bottom-fix');
							}

						}else if( scrollTop > $('#yt-foot').offset().top - $('#yt-nav > ul').outerHeight() ){
							$('#yt-nav').addClass('affix-bottom');
							$('#yt-nav').removeClass('affix-top');
							$('#yt-nav').removeClass('affix-bottom-fix'); 
							
						}else{
							$('#yt-nav').removeClass('affix-top');
							$('#yt-nav').removeClass('affix-bottom');
							$('#yt-nav').removeClass('affix-bottom-fix');
						}


					}

					// console.log( scrollTop + ' - ' + wpthms.OptionsFramework.lastScrollTop );

					// if( scrollTop > wpthms.OptionsFramework.lastScrollTop ){
					// 		$('#yt-nav').addClass('affix-top');
					// 		$('#yt-nav').removeClass('affix-bottom');
					// 		console.log('down');
					// }else{
					// 	$('#yt-nav').addClass('affix-bottom');
					// 	$('#yt-nav').removeClass('affix-top');
					// 	console.log('up');
					// }
				}

				if( $( '.yt-info-bar:not(#yt-foot)' ).length ){

					if( typeof infoBar == "undefined" )
						var infoBar = $( '.yt-info-bar:not(#yt-foot)' );

					if( typeof infoBar.data('offsetTop') == 'undefined')
						infoBar.data('offsetTop', infoBar.offset().top );

					//console.log( infoBar.data('offsetTop' ) );

					if( scrollTop >= infoBar.data('offsetTop' ) - 32 ){
						infoBar.addClass('affix-top');
						infoBar.css('left', $('#yt-content').offset().left );
						$('#yt-header').css('margin-bottom', infoBar.outerHeight() );

					}else{
						infoBar.removeClass('affix-top');
						infoBar.css('left', '');
						$('#yt-header').css('margin-bottom', '');
						//yt-info-bar
					}
				}
				wpthms.OptionsFramework.lastScrollTop = scrollTop;

			},
			/**
			 * Display recent activated tab 
			 */
			displayRecentActivatedTab: function(){
				
				var self = this;

				var hash = window.location.hash,
					requestedID = '[href="' + hash + '"]';

				if( $('#yt-nav').find(requestedID).length ){
					
					
					$('#yt-nav').find(requestedID).parents('li').addClass('current');

					// if target to parent
					if( $(requestedID).parent('li').hasClass('has-children')){
						
						var href = $(requestedID).siblings().children('li:first').find('[href]').attr('href');
						$(href).show().addClass('current');


						$(requestedID).siblings().children('li:first').addClass('current');	
						$('#yt-nav ul.sub-menu').hide();
						$(requestedID).siblings('ul.sub-menu').slideDown(300);

					}else{
						$(hash).show().addClass('current');	
						$('#yt-nav').find(requestedID)
							.closest('.sub-menu')
							.show('fast',function(){
								 
							});
					}
					
				}else{
				/*This fucks affix*/
				// Display last current tab	
					if (!$.cookie( 'yt_current_opt' ) || !$( $.cookie( 'yt_current_opt' ) ).length ) {
						$('.yt-group:first').show(0,function(){$(this).addClass('current')});	
						$('#yt-nav li:first').addClass('current');
						if( $('#yt-nav li:first').has('ul') ){
							var cUl = $('#yt-nav li:first ul'),
								target;

							cUl.show();
							cUl.find('li:first-child').addClass('current');
							target = cUl.find('li:first-child a').attr('href');

							if( typeof target !== 'undefined' && target.length )
								$(target).show().siblings().hide();
						}
						
						
					} else {
						var savedID = $.cookie( 'yt_current_opt' );
						
						$(savedID).show(0,function(){$(this).addClass('current')});	
						
						$('#yt-nav').find('a[href="' + savedID + '"]').parents('li').addClass('current');
						$('#yt-nav').find('a[href="' + savedID + '"]')
							.closest('.sub-menu')
							.show('fast',function(){
								 
							});
					}

				}
					
			},
			/**
			 * Nav action
			 */
			optionPanelNavAction: function(){
				
				var self = this;
				
				
				$('#yt-nav li a').on('click', function(e){

					wpthms.OptionsFramework.optionSetingsPanel.panelNavEqualHeight();
					
					var $el = $(this);
					
					if($(this).parent().hasClass('current')){
						e.preventDefault();
						return;
					}
						
					if($(this).parent('li').hasClass('has-children')){
						$('#yt-nav li').removeClass('current');
						$(this).parent().addClass('current');
						$(this).siblings().children('li:first').addClass('current');			
						var clicked_group = $('ul.sub-menu li:first a',$(this).parent()).attr('href');
						//alert(clicked_group);
						$.cookie('yt_current_opt', clicked_group, { expires: 7, path: '/' });
						
						if( !$('body').hasClass('yt-admin-motion-enabled') ){
							$('.yt-group').not(clicked_group).removeClass('current').hide(0);
							$(clicked_group).fadeIn(300).addClass('current');	
						}else{			
							$('.yt-group').removeClass('current')
								.delay(210)
								.hide(0, function(){
									$('.yt-group').not(clicked_group).removeClass('has-transform');
								});
							$(clicked_group).addClass('has-transform').show(10,function(){$(this).addClass('current')});
						}
						
						$('#yt-nav ul.sub-menu').hide();
						$(this).siblings('ul.sub-menu').slideDown(300);
						
					}else{
						$('#yt-nav li').removeClass('current');
						$(this).parent().addClass('current');
											
						var clicked_group = $(this).attr('href');
						//Write the current opened tab to cookies
						$.cookie('yt_current_opt', clicked_group, { expires: 7, path: '/' });
						
						if( !$('body').hasClass('yt-admin-motion-enabled') ){
						
							$('.yt-group').removeClass('current').hide(0);
							$(clicked_group).fadeIn(300).addClass('current')
						
						}else{
							$('.yt-group').removeClass('current')
								.delay(210)
								.hide(0, function(){
									$('.yt-group').not(clicked_group).removeClass('has-transform');
								});
							$(clicked_group).addClass('has-transform').show(10,function(){$(this).addClass('current')});
						}
						
						if($(this).closest('ul').hasClass('sub-menu')){
							//$(this).siblings('ul.sub-menu').show();	
						}else{
							$('#yt-nav ul.sub-menu').slideUp('fast',function(){
								
							});	
						}
						
					}
					
					e.preventDefault();
									
				});
				
				$('#yt-nav li ul.sub-menu li a').on('click', function(e){
					$(this).closest('.top-level').addClass('current');
					e.preventDefault();
				})
				
				
			},
			toggleSettingPanelFullwidth: function(){
			
				$(document).on('click', '#yt-expand-options-panel', function(e){
			
					$('#yt-container form.yt-options-panel-form').toggleClass('yt-options-panel-fullsize');
				
				});
			}
		},
		/**
		 * Setting Control in action
		 */
		optionSettingBindEvents: function(){
			var self = this;
			
			$( document )
				/*Toggle grid*/
				
				.on('keydown keypress', '.yt-section-text-number input[type="text"]', self.optionSettingControlsAction.textInputNumber )
				/* Folding checkbox */
				.on('click', '.yt-fold-trigger.yt-checkbox', self.optionSettingControlsAction.foldingCheckbox )
				/* Color Block and Images type*/
				.on('click', '.yt-radio-toggle-options > label', self.optionSettingControlsAction.colorBlocksAndImages )
				/* Folding checkbox */
				.on('click', '.yt-section-separator', self.optionSettingControlsAction.separatorFolding )
				/* Toggle buttons */
				.on('click', '.yt-radio-toggles label', self.optionSettingControlsAction.toggleButtons )
				/* Textarea */
				.on( 'keydown', '.yt-tabifiable-textarea .yt-textarea', wpthms.Helper.tabifyTextarea);
				

				
				/* Colorpicker */
				self.optionSettingControlsAction.colorPicker( '.yt-colorpicker' );
				/* datepicker */
				self.optionSettingControlsAction.datePicker( '.yt-input-calendar' );
				/* Repeatable fields */
				self.optionSettingControlsAction.repeatableFields.init();
				/* Sorter */
				self.optionSettingControlsAction.sorterList.init();
				/* Tab group */
				self.optionSettingControlsAction.tabGroup();
				/* timepicker */
				self.optionSettingControlsAction.timePicker( '.yt-input-time' );
				/* Typography Preview */
				self.optionSettingControlsAction.typographyPreview();
				/* UI Slider */
				self.optionSettingControlsAction.uiSlider( '.yt-ui-slider' );
				
				self.optionSettingControlsAction.foldingSelect();
				
				self.optionSettingControlsAction.getOEmbed();

				self.optionSettingControlsAction.ajaxSearchTag();
		},
		/**
		 * metaboxFriendlyUi
		 */
		metaboxFriendlyUi: {
			postFormatSwitcher: function(){

				var tab = '#yt_post_format_settings_metabox .yt-group-tab-wrapper:first .yt-group-tab-header li',
					formatsSelect = '#formatdiv #post-formats-select';
				
				if( ! $(formatsSelect).length )
					return;
					
				var checkedFormat = $(formatsSelect + ' input:checked'),
					checkedFormatIndex = checkedFormat.parent().children('input').index(checkedFormat);
					
				$(tab + ':eq(' + checkedFormatIndex + ')').trigger('click');
				
				//console.log(checkedFormatIndex);
				
				$(document).on('click', tab, function(){
					var $el = $(this),
						thisIndex = $el.index(),
						destination = '';			
					$( formatsSelect + ' input:eq(' + thisIndex + ')').prop('checked', true);	
					//console.log(thisIndex);		
				});

				$(document).on('change', formatsSelect + ' input', function(){
					var $el = $(this),
						id = '#' + $el.attr('id'),
						thisIndex = $el.parent().children('input').index($el),
						destination = '';
					$(tab + ':eq(' + thisIndex + ')').trigger('click');
					//console.log(thisIndex);				
				});	

			}
		},
		/**
		 * Setting Control in action
		 */
		optionSettingControlsAction: {
			generalInit: function(){
				$('[data-sortable="true"]').each(function(){
					$(this).sortable();
				});
			},
			ajaxSearchTag:function(){
				$('.yt-ajax-tag-search').each(function(index, element) {
					var $input = $(this),
						taxname = $input.data('tax') || 'post_tag',
						textarea = $input.siblings('input[type="hidden"]'),
						addBtn = $input.siblings('input[type="button"]'),
						list = $input.siblings( '.yt-tag-list' ),
						comma = ',',
						currentVal = textarea.val(),
						newtags, 
						deleteTag = $('a', list),
						q;

					$input.autocomplete({
					    source: function( $request, $response ){
							$.ajax({
								url: ajaxurl ,
								type: 'GET',
								async: true,
								cache: false,
								dataType: 'json',
								data: {
									action: 'yt_ajax_tag_search',
									tax: taxname,
									q:  $.trim($input.val()) 
								},
								success: function( $data ){
									
									//console.log($data);
									$response( jQuery.map( $data, function( $item ) {
										return {
											id: $item.id,
											name: $item.name,
										}
									}));
									
								}
							});
						},
					    minLength: 2,
					    delay: 500,
					    response: function(event,ui) {
					    	$input.removeClass( 'ui-autocomplete-loading' );
					    },
					    open: function() {

						},
						close: function() {

						},
						focus:function(event,ui) {
							//console.log('focus');
							event.preventDefault(); // without this: keyboard movements reset the input to ''
        					$(this).val(ui.item.name);

						},
						change: function(event, ui) {

							// console.log('change');
						},
						search: function(event, ui) {

							//console.log('search');
						},
						select: function (event, ui) {
					        list.append( '<span data-id="' + ui.item.id + '"><a class="ntdelbutton">x</a>&nbsp;' + ui.item.name + '</span>' );
					        newtags = textarea.val() + comma + ui.item.id;
					        newtags = wpthms.Helper.arrayUniqueNoempty( newtags.split(comma) );
					        textarea.val( newtags );
					    }
					}).data( "ui-autocomplete" )._renderItem = function( $ul, $item ) {
						return jQuery( '<li data-id="' + $item.id + '">' ).append( '<a><strong>' + $item.name + '</strong></a>' ).appendTo( $ul );
					};

					/*$input.suggest( ajaxurl + '?action=ajax-tag-search&tax=' + taxname, { delay: 500, minchars: 2, multiple: true, multipleSep: comma + ' ' } );

					$input.keypress(function (e) {
					    if (e.which == '13'  ) {
							newtags = textarea.val() + comma + $input.val();
							newtags = wpthms.Helper.arrayUniqueNoempty( newtags.split(comma) );
							list.empty().append( '<span><a class="ntdelbutton">x</a>&nbsp;' + newtags.join( '</span><span><a class="ntdelbutton">x</a>&nbsp;') + '</span>' );
							newtags = newtags.join( comma);
							console.log( newtags);
					        textarea.val( newtags );
					        $input.val('');
					        e.preventDefault();
					    }
					});

					$el.on( 'click', 'input[type=button]' , function (e) {
						newtags = textarea.val() + comma + $input.val();
						newtags = wpthms.Helper.arrayUniqueNoempty( newtags.split(comma) );
						list.empty().append( '<span><a class="ntdelbutton">x</a>&nbsp;' + newtags.join( '</span><span><a class="ntdelbutton">x</a>&nbsp;') + '</span>' );
						newtags = newtags.join( comma);
						console.log( newtags);
				        textarea.val( newtags );
				        $input.val('');
				        e.preventDefault();
					    
					});*/

					$(document).on( 'click', '.yt-tag-list a' , function (e) {
						var spanId = $(this).parent('span').data('id');
						
						newtags = textarea.val().split(comma);
						//console.log( newtags.length );
						for (var i=newtags.length-1; i>=0; i--) {
						    if (parseInt(newtags[i]) === parseInt(spanId)) {
						        newtags.splice(i, 1);
						        // break;       //<-- Uncomment  if only the first term has to be removed
						    }
						}
						newtags = newtags.join(comma);
						textarea.val( newtags );
						//console.log(newtags);
						$(this).parent().remove();
				        e.preventDefault();
					    
					});
			
				});
			},
			textInputNumber:function(e){
				//yt-section-text-number
				// Allow: backspace, delete, tab, escape, enter and .
		        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
		             // Allow: Ctrl+A
		            (e.keyCode == 65 && e.ctrlKey === true) || 
		             // Allow: home, end, left, right
		            (e.keyCode >= 35 && e.keyCode <= 39)) {
		                 // let it happen, don't do anything
		                 return;
		        }
		        // Ensure that it is a number and stop the keypress
		        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		            e.preventDefault();
		        }
			},
			/**
			 * Color blocks
			 */
			colorBlocksAndImages: function(e){
				var $el = $(this);
				$el.addClass('active').siblings('label').removeClass('active').siblings('[type="hidden"]').val($el.data('value'));
				
				e.preventDefault();
			},
			
			/**
			 * Color picker
			 */
			colorPicker: function(_selector){
				$(_selector).each(function(index, element) {
					var $el = $(this);			
					var myOptions = {
						// you can declare a default color here,
						// or in the data-default-color attribute on the input
						defaultColor: $el.data('std'),
						// a callback to fire whenever the color changes to a valid color
						change: function(event, ui){},
						// a callback to fire when the input is emptied or an invalid color
						clear: function() {},
						// hide the color picker controls on load
						hide: true,
						// show a group of common colors beneath the square
						// or, supply an array of colors to customize further
						palettes: true
					};
					 
					$el.wpColorPicker(myOptions);
			
				});
			},
			
			/**
			 * datepicker
			 */
			datePicker: function( _selector ){
				if ( $( _selector ).length ) {
					$( _selector ).each(function () {
						$( '#' + $( this ).attr( 'id' ) ).datepicker( {showAnim: 'fadeIn' } );
					});
					$( _selector ).siblings('.yt-datepicker-image').on( 'click', function(){
						$(this).siblings(_selector).focus();
					});
				}
			},
			
			/**
			 * Checbox wit Folding function
			 */
			foldingCheckbox: function(e){
				
				var $el = $(this),
				//console.log($(this).attr('checked'))
					$fold = '.f_' + this.id;
				
				$el.closest('.yt-section-checkbox').siblings($fold).slideToggle('normal', "swing", function(){
					
					if( $el.attr('checked') !== undefined){
						
						$($fold).removeClass('yt-temp-hide');
						
					}else{
						
						$($fold).addClass('yt-temp-hide');
						
					}
						
				} );
				
			},
			
			foldingSelect: function(){
				//Options relations
				
				$(document).on('change', '.yt-section-select select', function() {
					
					var $el = $(this),
						thisID = $el.attr('id');
						
					
					$('.f_' + thisID ).each(function(index, element) {
						if( $(this).data('fold').indexOf($el.val()) != -1 )
							$(this).slideDown(300);
						else
							$(this).slideUp(300);
					});
					
						
					//$('.f_' + thisID + '[data-fold="' + $el.val() + '"]' ).slideDown(300);
					
				});
				
				$.each( $('.yt-section-select select'), function () {
					var $el = $(this),
						thisID = $el.attr('id');
					
					$('.f_' + thisID ).each(function(index, element) {
						if( $(this).data('fold').indexOf($el.val()) != -1 )
							$(this).show();
					});
				});	
			},
			/**
			 * getOEmbed
			 */
			getOEmbed:function() {				
				
				$( document ).on('click', '.yt-oembed-preview', function(e){
					var $el = $( this ),
						$spinner = $el.siblings( '.spinner' ),
						data = {
							action: 'yt_field_type_oembed_get_embed',
							url: $el.parent().siblings( 'input' ).val()
						};

					//console.log($el.parent().siblings( 'input' ).val());
			
					$spinner.show(function(){$(this).css('display','inline-block')});
					$.get( ajaxurl, data, function( response )
					{
						if(response && !response.error){
							$spinner.hide();
							// console.log(response);
							$el.parent().siblings( '.yt-oembed-preview-area' ).html( response.data );
							$el.siblings( '.yt-oembed-remove-button').removeClass('yt-hidden');
						}else{
							$spinner.hide();
							$el.parent().siblings( '.yt-oembed-preview-area' ).html( '<p>' + yt_optionsVars.oembedPreviewErrorMsg + '</p>' );
							$el.siblings( '.yt-oembed-remove-button').removeClass('yt-hidden');
						}
					}, 'json' );
			
					e.preventDefault();
				} ).on('click', '.yt-oembed-remove-button', function(e){
					var $el = $( this );
			
					$el.addClass('yt-hidden').parent().siblings( '.yt-oembed-preview-area' ).html('').siblings( 'input' ).val('');
			
					e.preventDefault();	
				} )
					
				
				
			},
			/**
			 * Separator ( Folding Toggles )
			 */
			separatorFolding: function(e){
					
				//if attribute "data-show" non-exists ,return
				if( $(this).attr("data-show") == undefined) return;
				
				//else, add the events
				var triggerId = $(this).attr("id").split("yt-section-");
				triggerId = triggerId[1];
				if ($(this).data('show') == 'yes') {
					$(this).siblings('.yt-section[data-folds="' + triggerId + '"]:not(.yt-temp-hide)').hide();
					$(this).data('show', 'no').addClass('collapse');
				}else{
					$(this).siblings('.yt-section[data-folds="' + triggerId + '"]:not(.yt-temp-hide)').show();
					$(this).data('show', 'yes').removeClass('collapse');
				}
				
				e.preventDefault();
			
			},
			/**
			 * Time picker
			 */
			timePicker: function( _selector ){
				if ( $( _selector ).length ) {
					//console.log('hâhhaha');
					$(_selector).each(function () {
						
						$('#' + $(this).attr('id')).on('keydown keypress', function(event) {
							if ((event.keyCode !== 38 || event.keyCode !== 40 || event.keyCode !== 13 || event.keyCode !== 27 )) {
								event.preventDefault();
							}
						});
						
						$('#' + $(this).attr('id')).timePicker({
							startTime: "07:00",
							endTime: "23:00",
							show24Hours: true,
							separator: ':',
							step: 30
						});
					});

					$( _selector ).siblings('.yt-timepicker-image').on( 'click', function(){
						$(this).siblings(_selector).focus();
					});
				}
			},
			
			/**
			 * Toggles
			 */
			toggleButtons: function(e){
				
				var $el = $(this);
				$el.removeClass('button').addClass('button-primary')
				.siblings('label').removeClass('button-primary').addClass('button')
				.siblings('[type="hidden"]').val($el.data('value'));
				
				e.preventDefault();
			},
			/**
			 * Clone the Dolly for Font Select boxes
			 */
			typographyClone: function(){

				$('select.yt-typography-face').each(function(){

					var $el = $(this),
						selectedFont = $el.data('selected'),
						fontData = $('select[data-role="fontface-dolly"][data-by="wpthms"]').html();

					// if( !selectedFont ){
					// 	selectedFont = $('option:first', $el ).is('[data-role="loader"]').val();
					// 	console.log(selectedFont);
					// }
						
					$el.empty().append( fontData );

					$el.val( selectedFont );

					if( $el[0].selectedIndex <= 0 )
						$('option:first', $el ).prop('selected', true );

					$el.prop('disabled', false );
					
				});
			},
			/**
			 * Preview for typography
			 */
			typographyPreview: function(){
				
				var _yt_typo = [];
				
				$(document).on('click', '.yt-typography-preview', function(e){
				
					var $el = $(this),
						css = '';
					
					/*Do each() to get the css attribute*/
					$el.siblings('[data-att]').each(function(index, element) {
						
						if($(this).data('att') === 'font-family'){
						
							css += $(this).data('att') + ':' + $(this).find('option:selected').data('val') + ';' + "\n";
						
						}else if($(this).data('att') === 'color'){
						
							css += $(this).find('input[type="text"]').val() ? $(this).data('att') + ':' + $(this).find('input[type="text"]').val() + ';' + "\n" : '';
							
						}else{
							
							css += $(this).data('att') + ':' + $(this).find('select').val() + ';' + "\n";
						
						}
					});
					
					var selected_face = $el.siblings('[data-att="font-family"]').find('option:selected'),
						google_font = '',
						google_font_url = '',
						stylesheet;
					
					/* if selected font fade is google font*/
					if( selected_face.data('font') == 'google-font'){
						
						google_font = selected_face.val().replace('googlefont-','');
						stylesheet = '<link id="yt-typography-stylesheet" href="http://fonts.googleapis.com/css?family=' + google_font + '" rel="stylesheet" type="text/css">';
					
					}
					
					/* if isset google font, append things to browser*/
					if( google_font ){
						
						/*if google font is not in array, take it*/
						if( _yt_typo.indexOf( google_font ) < 0 ){
							_yt_typo.push( google_font );
						}
						
						if($('#yt-typography-stylesheet').length){
							
							/*Join font from array*/
							google_font_url = 'http://fonts.googleapis.com/css?family=' + _yt_typo.join('|');
							
							/*Only change the href when google_font_url != current href */
							if( $('#yt-typography-stylesheet').attr('href') !== google_font_url ){
							
								$('#yt-typography-stylesheet').attr('href', google_font_url );
								//console.log(_yt_typo);	
							}
								
						}else{
						
							$('head').append( stylesheet );
							
						}
					}
					
					/* change the preview style*/
					$(this).siblings('.yt-typography-preview-area').removeClass('yt-hidden').attr('style', css );
					
					e.preventDefault();
					
				});	
			},
			
			/**
			 * UI Sliders
			 */
			uiSlider: function( _selector){
				if ( $( _selector ).length ) {
					$( _selector ).each(function(index, element) {
						var $el = $(this),
							dataValue = parseInt($el.data('value')),
							dataMin = parseInt($el.data('min')),
							dataMax = parseInt($el.data('max')),
							dataStep = parseInt($el.data('step'));
						
						$el.slider({
							range:"min",
							value: dataValue,
							min: dataMin,
							max: dataMax,
							step: dataStep,
							slide: function( event, ui ) {
								$el.closest('.yt-controls').find('.ui-slider-textfield').val( ui.value );
							}
						});
					});
				}
			},
			/**
			 * Repeatable fields
			 *
			 * self.optionSettingControlsAction.repeatableFields.resetFormFields()
			 * self.optionSettingControlsAction.repeatableFields.updateFormAttr()
			 * 
			 * delete trigger
			 */
			repeatableFields: {
				init: function( callSortableOnly ){
				
					var self = wpthms.OptionsFramework,
						oddClick = true;
						
					callSortableOnly = callSortableOnly || false;
					
					if( callSortableOnly !== true){
					/*Expand*/
					$(document).on('click', '.yt-repeatable-controls.yt-repeatable-expand', function(e){
						
						var $el = $(this);
						
						if(oddClick){
							$(this).closest('.yt-repeatable-field-block').removeClass('collapsed');
							$el.text('-');
						}else{
							$(this).closest('.yt-repeatable-field-block').addClass('collapsed');
							$el.text('+');
						}
						
						oddClick = !oddClick;
						e.preventDefault();
					})
					
					/*Delete*/
					.on('click', '.yt-repeatable-controls.yt-repeatable-delete', function(e){
						var $el = $(this),
							parent = $(this).closest('.yt-repeatable-fields-wrapper'),
							count = parent.find('ul').children().length,
							clone = $( parent.data('clone') );
						
						clone.find('> .collapsed').removeClass('collapsed');

						if(count == 1){
							$el.closest('li').replaceWith( clone );
							oddClick = !oddClick;
							//self.optionSettingControlsAction.repeatableFields.resetFormFields( $el.closest('li') );
						}else{
							$el.closest('li').slideUp('medium', 'swing', function(){
								$el.closest('li').remove();
								self.optionSettingControlsAction.repeatableFields.updateFormAttr('li', parent);
							});
						}
											
						e.preventDefault();
					})
					
					/* Add more repeatble field */
					.on('click', '.yt-repeatable-fields-wrapper .yt-button-add-more', function(e){
						
						var thisLocation = $(this),
							rParent = '.yt-repeatable-fields-wrapper',
							field = $(this).closest(rParent).data('clone'),
							sortableFields = $(this).closest(rParent).find('> ul');
						
						//console.log( $( field ));					
						//CHANGE NAME, ID, clear field
						//self.optionSettingControlsAction.repeatableFields.resetFormFields( $(field), '');
						$( field ).appendTo(sortableFields);
						self.optionSettingControlsAction.repeatableFields.updateFormAttr('li', $(this).closest(rParent));
						
						e.preventDefault();
						
					})
					
					/* Expand all */
					.on('click', '.yt-repeatable-fields-wrapper .yt-button-expand-collapse-all', function(e){
						
						var thisUl = $(this).siblings('ul');
						//console.log(thisUl.data('collapsed'));
						if(thisUl.data('collapsed') == true){
							
							thisUl.find('li').children('.collapsed').removeClass('collapsed').find('.yt-repeatable-expand').text('-');
							thisUl.data('collapsed', false);
							
						}else{
							thisUl.find('li').children().addClass('collapsed').find('.yt-repeatable-expand').text('+');
							thisUl.data('collapsed', true);
						}
						e.preventDefault();
						
					});
					
					}
					
					$('.yt-repeatable-fields-wrapper ul').sortable({
						opacity: 0.6,
						revert: true,
						stop:function(event,ui){
							ui.item.removeAttr('style');
						},
						placeholder : 'yt-ui-sortable-placeholder',
						sort : function( event, ui ) {
							$('.yt-ui-sortable-placeholder').css({
								'height':$(this).find('.ui-sortable-helper').height()-6,
								'width':$(this).find('.ui-sortable-helper').width() -30}
							);
							$(this).addClass('sortabling');
						},
						cursor: 'move',
						update: function(event, ui) {
							self.optionSettingControlsAction.repeatableFields.updateFormAttr('li', this);
							$(this).removeClass('sortabling');
						}
					})
					
				
				},
				resetFormFields: function(field, selectors){
				
					if(!selectors)
						selectors = 'select, input[type="hidden"], textarea, input[type="text"]';
					
					field.find(selectors).val('');
					field.find('.yt-screenshot').html('');
				},
				
				updateFormAttr: function(child, parent){
				
					setTimeout(function(){
						$(child, parent).each(function(){
							var position = $(this).index();
							$(this).find('[name]').each(function(){
								$(this).attr('name',function(index, name) {
									return name.replace(/(\d+)/, function(fullMatch, n) {return position;});
								}).attr('id',function(index, name) {
									return name.replace(/(\d+)/, function(fullMatch, n) {return position;});
								})
							});
							
							$(this).removeAttr('style');
						});
		
					},500);
				}
			},
			/**
			 * Sorter
			 */
			sorterList: {
				init: function(){
					
					$('.yt-sorter-list').sortable({
				        connectWith: '.yt-sorter-list',
				        //receive: This event is triggered when a
				        //connected sortable list has received an item from another list.
				        receive: function(event, ui) {
				            var wrapper = $(this);
				            // so if > 10
				            if ( wrapper.children().length > parseInt( wrapper.data('limits') )  ) {
				                //ui.sender: will cancel the change.
				                //Useful in the 'receive' callback.
				                $(ui.sender).sortable('cancel');
				            }
				            wpthms.OptionsFramework.optionSettingControlsAction.sorterList.update( wrapper );
				        },
				        stop: function(event, ui) {

							var wrapper = $(this);
				            wpthms.OptionsFramework.optionSettingControlsAction.sorterList.update( wrapper );
				        }
				    }).disableSelection();
				},
				update: function(_selector){

					if( _selector.children().length === parseInt( _selector.data('limits') ) ){
		            	
		            	_selector.parent().addClass('full');

		            }else{
						_selector.parent().removeClass('full');			            	
		            }

		            var dataID = _selector.data('id');
		            setTimeout(function(){
	                    _selector.find('[data-name]').each(function(){
	                    	var dataName = $(this).data('name');
                            if( dataName ){
                                var newName = dataName.replace('__x_*_x__', dataID );
                                $(this).attr('name', newName );
                            }
                        });
	                }, 500);

				},
			},
			
			/**
			 * Tab ( Nested Tabs/Metabox Tab ) 
			 */
			tabGroup: function(){
				if( !$('.yt-group-tab-wrapper').length )
					return;
					
				$('.yeah-framework .yt-group-tab-wrapper').each(function() {
					
					var $el = $(this),
						$tabHeading = $el.find('ul[data-ul-tab]'),
						$cookie_id = $el.attr('id');	
						
					if( $( '#post_ID[name="post_ID"]' ).length ){
						$cookie_id = $cookie_id + '_' + $( '#post_ID[name="post_ID"]' ).val();
					}
					
					if ( !$.cookie( $cookie_id ) ) {
							
						$('li:first', $tabHeading ).addClass('active');
						$('.yt-group-tab:first', $el).addClass('active');
						
					}else{
						
						$('li[data-index="' + $.cookie( $cookie_id ) + '"]', $tabHeading ).addClass('active');
						$('.yt-group-tab[data-tab="' + $.cookie( $cookie_id ) + '"]', $el).addClass('active');
					
					}
					//if(  )
					
					//$.cookie('yt_current_opt', clicked_group, { expires: 7, path: '/' });
					
					$('li', $tabHeading).on( 'click', function(){
						
						if( $(this).hasClass('active') ) return;
						
						$(this).addClass('active').siblings('li').removeClass('active');
						
						var tabIndex = $(this).index() + 1;
						
						$(this).closest( $tabHeading ).siblings('.yt-group-tab[data-tab="' + tabIndex + '"]').addClass('active').siblings('.yt-group-tab.active').removeClass('active');
						
						if( $el.attr("data-cookie") == undefined || parseInt($el.attr("data-cookie")) == 1)
							$.cookie( $cookie_id, tabIndex, { expires: 7, path: '/' });

						$('body').trigger('tab-group-changed');
					});
				
				});
			}
			
		},
		
		/**
		 * Ajaxifying Actions
		 */
		 
		optionSettingPanelAjaxifyingFunctions: {
			/**
			 * Backup
			 */
			backup: function( self, prefix, nonce, option_key){
				
				self.helper.processing();
				
				var data = {
					action: 'yt_ajax_save_options',
					type: 'backup_options',
					security: nonce,
					key: option_key,
					prefix: prefix
				};
							
				$.post( ajaxurl, data, function(response) {
								
					self.helper.doneProcessing();
					
					console.log(response);
					//check nonce
					if(response==-1){ //failed
									
						self._container.addClass(self._failPopup + '-active');      
						window.setTimeout(function(){
							self._container.removeClass(self._failPopup + '-active');                      
						}, 2000);
					}
								
					else {
								
						self._container.addClass(self._successPopup + '-active');
						window.setTimeout(function(){
							location.reload();                        
						}, 1000);
					}
								
				});
					
				
			},
			
			/**
			 * Restore
			 */
			restore: function( self, prefix, nonce, option_key){
				
				self.helper.processing();
			
				var data = {
					action: 'yt_ajax_save_options',
					type: 'restore_options',
					security: nonce,
					key: option_key,
					prefix: prefix
				};
							
				$.post(ajaxurl, data, function(response) {
				
					self.helper.doneProcessing();
					//check nonce
					if(response==-1){ //failed
									
						self._container.addClass(self._failPopup + '-active');
						window.setTimeout(function(){ 
							self._container.removeClass(self._failPopup + '-active');                      
						}, 2000);
					}
								
					else {
		
						self._container.addClass(self._successPopup + '-active');
						window.setTimeout(function(){
							location.reload();                        
						}, 1000);
					}	
				});
			},
			
			/**
			 * Import
			 */
			import: function( self, prefix, nonce, option_key ){
				
				self.helper.processing();
				
				var import_data = $('#yt-export-data').val();
				
				var data = {
					action: 'yt_ajax_save_options',
					type: 'import_options',
					security: nonce,
					data: import_data,
					key: option_key,
					prefix: prefix
				};
				
				$.post(ajaxurl, data, function(response) {
					console.log(response);
					self.helper.doneProcessing();
					//check nonce
					if(response==-1){ //failed
						self._container.addClass(self._failPopup + '-active');
						window.setTimeout(function(){
							self._container.removeClass(self._failPopup + '-active');
						}, 2000);
					}		
					else 
					{
						self._container.addClass(self._successPopup + '-active');
						window.setTimeout(function(){
							location.reload();                        
						}, 1000);
					}
								
				});
			
			},
			
			/**
			 * Reset
			 */
			reset: function( self, prefix, nonce, option_key ){
				
				self.helper.processing();
				
				var default_data = $('[name="yt_option_default_data"]').val();
							
				var data = {
					type: 'reset',
					action: 'yt_ajax_save_options',
					data: default_data,
					security: nonce,
					key: option_key,
					prefix: prefix
				};
							
				$.post(ajaxurl, data, function(response) {
					console.log(response);
					self.helper.doneProcessing();
					
					if (response==1)
					{
						self._container.addClass(self._resetPopup + '-active');
						window.setTimeout(function(){
							location.reload();                        
						}, 1000);
					} 
					else 
					{ 
						console.log(response);
						self._container.addClass(self._failPopup + '-active');
						window.setTimeout(function(){
							
							self._container.removeClass(self._failPopup + '-active');		
						}, 2000);
					}
				});
			},
			
			/**
			 * Save
			 */
			save: function( self, prefix, nonce, option_key, trigger){
				
				self.helper.processing();
				
				//get serialized data from all our option fields			
				var serializedReturn = $('#yt-form :input[name][name!="security"][name!="yt-reset"]').serialize();
				
				$('#yt-form :input[type="checkbox"]').each(function() {     
					if (!this.checked) {
						serializedReturn += '&' + this.name;
					}
				});
				
				var data = {
					type: 'save',
					action: 'yt_ajax_save_options',
					security: nonce,
					data: serializedReturn,
					key: option_key,
					prefix: prefix
				};
							
				$.post(ajaxurl, data, function(response) {
					
					self.helper.doneProcessing();
								
					if (response==1) {
						self._container.addClass(self._successPopup + '-active');
						
						if( trigger.is('.yt-save-and-refresh')){
							window.setTimeout(function(){
								location.reload();                        
							}, 1000);
						}
						console.log(response);
					} else { 
						self._container.addClass(self._failPopup + '-active');
						console.log(response);
					}
								
					window.setTimeout(function(){
						self._container.removeClass(self._failPopup + '-active' + ' ' + self._successPopup + '-active');			
					}, 2000);
				});	
			}
			
		},
		optionSettingPanelAjaxifyingActions: function(){
			
			var self = this,
				prefix = $('[name="yt_option_prefix"]').val(),
				nonce = $('[name="yt_options_ajaxify_data_nonce"]').val(),
				option_key = $('[name="yt_option_key"]').val();
				
			$(document)
			/**
			 * Backup
			 */
			.on('click', '.yt-options-framework-panel-wrapper #yt-backup-button', function(e){
			
				var answer = confirm(yt_optionsVars.backupOptionsMsg);
				
				if (answer){
					self.optionSettingPanelAjaxifyingFunctions.backup( self, prefix, nonce, option_key);
				}
				
				e.preventDefault();
							
			})
			
			/**
			 * Restore
			 */
			.on('click', '.yt-options-framework-panel-wrapper #yt-restore-button', function(e){
			//$('#yt-restore_button').live('click', function(){
			
				var answer = confirm(yt_optionsVars.restoreOptionsMsg);
				
				if (answer){
					self.optionSettingPanelAjaxifyingFunctions.restore( self, prefix, nonce, option_key);
				}
			
				e.preventDefault();
							
			})
			
			/**	Ajax Transfer (Import/Export) Option */
		
			.on('click', '.yt-options-framework-panel-wrapper #yt-import-button', function(e){
			
				var answer = confirm(yt_optionsVars.importOptionsMsg);
				
				if (answer){
					self.optionSettingPanelAjaxifyingFunctions.import( self, prefix, nonce, option_key );
				}
				
				e.preventDefault();
							
			})
			
			/** AJAX Save And Refresh Options */
		
			.on('click', '.yt-options-framework-panel-wrapper .yt-save-theme-options', function(e){
					
				var $el = $(this);
							
				self.optionSettingPanelAjaxifyingFunctions.save( self, prefix, nonce, option_key, $el );
					
				e.preventDefault();
							
			})
			
			/* AJAX Options Reset */
			.on('click', '.yt-options-framework-panel-wrapper #yt-reset', function(e){
				
				//confirm reset
				var answer = confirm(yt_optionsVars.resetOptionsMsg) ;
				
				//ajax reset
				if (answer){
					self.optionSettingPanelAjaxifyingFunctions.reset( self, prefix, nonce, option_key );
				}
					
				e.preventDefault();
				
			});
		
			
		},
		
		
	}


	/*-----------------------------------------------------------------------------------
	 * Yeahtheme Media Uploader
	 * @author wpthms
	 * @since 1.0
	 * @support wp 3.5+
	 *-----------------------------------------------------------------------------------*/

	wpthms.OptionsFrameworkUploader = {
  		mediaModal:{
			
			selectedID: 0,
			mediaFilter:''
				
		},
		/*-----------------------------------------------------------------------------------
		 * Remove file when the "remove" button is clicked.
		 *-----------------------------------------------------------------------------------*/
  
		removeFile: function () {
		 
			$(document).on('click', '.yt-media-remove-button', function(e){
				
				var $el = $(this);
				
				/*
				 * - remove text file value
				 * - hide remove button
				 * - remove screenshot
				 */
				$el.parent().siblings('.yt-input').val('');
				$el.addClass('yt-hidden');
				//$el.parent('.yt-button-action').siblings('.yt-screenshot').html('');
				$el.parent('.yt-button-action').siblings('.yt-screenshot').children().slideUp('medium', 'swing', function(){
					$(this).remove();
				});
				e.preventDefault();
			});
		  
		}, // End removeFile
		
		mediaUpload: function(){

			var media = {};

			/*
			 * Mime types
			 */
			var $el;
			
			media.buttonUploader = '.yt-media-upload-button',
			media.selectedID = 0;
			media.filter = '';
			//Misc.	
			media.misc = {
				headingText: 		'Insert Media',
				image_regex: 		/(^.*\.jpg|jpeg|png|gif|svg|ico*)/gi,
				document_regex: 	/(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi,
				audio_regex: 		/(^.*\.mp3|m4a|ogg|wav*)/gi,
				video_regex: 		/(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi,
			};

			//Frame Settings
			media.frameDefaultSettings = {
				id:         'yeah-media',
				title:      media.misc.headingText,
				priority:   20,
			};

			media.frameSettings = {
				
				filterable: 'all',//'uploaded'
				//library:            wp.media.query( { type: 'all' } ),
				searchable: true,
				editable:   true,
				allowLocalEdits: true,
				displaySettings: true,
				displayUserSettings: true,
				multiple:           false,
				// Initial region modes.
				//content:            'upload',
				menu:               'default',
				router:             'browse',
				toolbar:            'select',
				

				autoSelect:         true,
				describe:           false,
				// Uses a user setting to override the content mode.
				contentUserSetting: true,
				// Sync the selection from the last state when 'multiple' matches.
				syncSelection:      true,
			};

			media.userSettings = {
				//id:         'yeah-mediaxxx', 
				//kaka: 40
			};

			//console.log(media.frameSettings);

			_.extend( media, { view: {}, controller: {} } );

			// media.view.HelloWorld = wp.media.View.extend( {
			// 	className: 'hello-world-frame',
			// 	template:  wp.media.template( 'hello-world' ) // <script type="text/html" id="tmpl-hello-world">
			// } );

			// media.controller.HelloWorld = wp.media.controller.State.extend( {
			// 	defaults: {
			// 		id:       'hello-world-state',
			// 		menu:     'default',
			// 		content:  'hello_world_state'
			// 	}
			// } );

			var Attachment  = wp.media.model.Attachment,
				l10n = wp.media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

			wp.media.controller.yeahMediaModel = wp.media.controller.Library.extend({
				defaults: _.defaults( media.frameSettings, wp.media.controller.Library.prototype.defaults ),

				activate: function() {
					//console.log(this.filters);
					// this.set( 'library', media.frameSettings.library );
					// this.set( 'filterable', media.frameSettings.filterable );
					//this.frame.toolbar.set( new wp.media.view.Toolbar() );
					this.updateSelection();
					this.frame.on( 'open', this.updateSelection, this );
					//console.log(media.frameSettings.filterable);
					//wp.media.controller.Library.prototype.activate.apply( this, arguments );
					wp.media.controller.Library.prototype.activate.apply( this, arguments );

				},
		
				deactivate: function() {
					this.frame.off( 'open', this.updateSelection, this );
					wp.media.controller.Library.prototype.deactivate.apply( this, arguments );
				},
		
				updateSelection: function() {
					
					var selection = this.get('selection'),
						id = media.selectedID,
						attachment;
		
					if ( '' !== id && -1 !== id ) {
						attachment = Attachment.get( id );
						attachment.fetch();
					}
		
					selection.reset( attachment ? [ attachment ] : [] );
				}
			
			});

			_.extend( media, {

				init: function() {
					$( document ).on( 'click', media.buttonUploader,  function( e ) {

						$el = $(this),
						media.filter = typeof $el.data('filter') != 'undefined' && $el.data('filter') ? $el.data('filter') : '';
						media.userSettings = typeof $el.data('settings') != 'undefined' && typeof $el.data('settings') === 'object' ? $el.data('settings') : { filterable: 'all', library: wp.media.query( { type: 'all' } ) },
						media.frameDefaultSettings.title = media.misc.headingText = $el.data('title'),
						media.selectedID = $el.attr('data-id');

						//delete media.userSettings.filterable;
						var uLibrary;

						if( typeof media.userSettings.library !== 'undefined' ){
							uLibrary = media.userSettings.library;								
							delete media.userSettings.library;
							if( 'all' == uLibrary 
								|| 'image' == uLibrary 
								|| 'audio' == uLibrary 
								|| 'video' == uLibrary 
								|| 'unattached' == uLibrary
							) media.userSettings.library = wp.media.query( { type: uLibrary } );
						}
							//console.log('ddesoco');
						
						//console.log(media.frameSettings);
						//_.extend( media.frameSettings, media.userSettings );
						_.extend( media.frameSettings, media.frameDefaultSettings );
						//console.log(media.frameSettings);
						e.preventDefault();

						media.frame().open();
					});
				},
				frame: function() {


					if ( this._frame ){
						$('.yeah-media-frame .media-frame-title h1').html( media.misc.headingText );
						return this._frame;
					}
					media.states = [
						//new wp.media.controller.Library( media.frameSettings ),
						new wp.media.controller.yeahMediaModel()
					];

					//this._frame.states.add([ new wp.media.controller.yeahMediaModel() ]);
					
					this._frame = wp.media( {
						className: 'yeah-media-frame media-frame no-sidebar',
						state:    'yeah-media',
						button: {
							text: typeof yt_optionsVars.useImageMsg != "undefined" ? yt_optionsVars.useImageMsg : 'Use this one'
						},
						states : media.states,
						//frame: 'post'
					} );

					// this._frame.on( 'content:create:hello_world_state', function() {
					// 	var view = new ds.media.view.HelloWorld( {
					// 		controller: media.frame(),
					// 		model:      media.frame().state()
					// 	} );

					// 	media.frame().content.set( view );
					// } );

					this._frame.on( 'open', this.open );

					this._frame.on( 'ready', this.ready );

					this._frame.on( 'close', this.close );

					this._frame.on( 'escape', this.escape );

					this._frame.on( 'menu:render:default', this.menuRender );

					//this._frame.state( 'library' ).on( 'select', this.select );
					this._frame.state( 'yeah-media' ).on( 'select', this.select );

					return this._frame;
				},
				open: function() {
					//$( '.media-modal' ).addClass( 'smaller' );
					//console.log( 'Frame open' );
					//Trick the filter ;p
					var thisFrame = media.frame();
					if( $.inArray( media.filter, [ 'all', 'image', 'video', 'audio', 'unattached' ] ) ) {
						var $filter = $(thisFrame.el).find( 'select.attachment-filters option[value="' + media.filter + '"]');
						$filter.prop('selected', true).change();
					}
				},

				ready: function() {
					//console.log( 'Frame ready' );
				},

				close: function() {
					//$( '.media-modal' ).removeClass( 'smaller' );
					//console.log('Frame close');
					//delete this._frame;
					
				},
				escape: function() {
					//console.log('Frame escape');
				},

				menuRender: function( view ) {
					/*
					view.unset( 'library-separator' );
					view.unset( 'embed' );
					view.unset( 'gallery' );
					*/
				},

				select: function() {
					var settings = wp.media.view.settings,
						selection = this.get( 'selection' );

					$( '.added' ).remove();
					selection.map( media.showAttachmentDetails );
				},

				showAttachmentDetails: function( attachment ) {
					//console.log(attachment);

					//Get User Settings
					var display = media.frame().state( 'yeah-media' ).display( attachment ).toJSON(),
						data = media.frame().state( 'yeah-media' ).get('selection').first().toJSON();
					//console.log(display);
					//console.log(data);
					//var display = this._frame.display( attachment ).toJSON();
					//console.log(this.state().get('selection').first().toJSON() );


					// var details_tmpl = $( '#attachment-details-tmpl' ),
					// 	details = details_tmpl.clone();

					// details.addClass( 'added' );

					// $( 'input', details ).each( function() {
					// 	var key = $( this ).attr( 'id' ).replace( 'attachment-', '' );
					// 	$( this ).val( attachment.get( key ) );
					// } );

					// details.attr( 'id', 'attachment-details-' + attachment.get( 'id' ) );

					// var sizes = attachment.get( 'sizes' );
					// $( 'img', details ).attr( 'src', sizes.thumbnail.url );

					// $( 'textarea', details ).val( JSON.stringify( attachment, null, 2 ) );

					// details_tmpl.after( details );

					//do something with attachment variable, for example attachment.filename
				
					//console.log(data);
					var preview,
						attachment_url = data.url;

					//Display Settings
					if( typeof display.size != 'undefined' 
						&& typeof( data['sizes'] ) != 'undefined' 
						&& typeof( data.sizes[display.size] ) != 'undefined' 
						&& typeof( data.sizes[display.size].url ) != 'undefined'
					) attachment_url = data.sizes[display.size].url;
						

					if ( 'image' == data.type ) {
						preview = '<a class="yt-uploaded-image" href="' + attachment_url + '">\
							<span class="yt-img-border yt-transparent-bg"><img class="yt-option-image" id="image_' + data.id + '" src="' + attachment_url + '" alt="" /></span>\
						</a>';
					}else{
						if( $el.attr("data-format") !== undefined && $el.data('format') == 'mixed' ){
							
						}else{
							preview = '<p>' + yt_optionsVars.notImageMsg + ' <a href="' + attachment_url + '" target="_blank" rel="external">' + data.filename + '</a></p>';
						}
					}
					$el.parent().siblings('.yt-screenshot').show().html(preview);
					$el.parent().siblings('.yt-media-input').val( $el.data('by') === 'id' ? data.id : attachment_url );
					$el.next('span').removeClass('yt-hidden');
					$el.attr('data-id', data.id);
					//console.log(JSON.stringify( attachment, null, 2 ));
				}
			} );

			$( media.init );



			$('.yt-input.yt-media-input').on('blur', function(){
				var $this = $(this);
				var mimetypes = /(^.*\.jpg|jpeg|png|gif|svg|ico*)/gi;
				if( '' !== $this.val() && $this.val().match(mimetypes) ){
					$this.siblings('.yt-screenshot').html('<a href="' + $this.val() + '" class="yt-uploaded-image"><span class="yt-img-border yt-transparent-bg"><img src="' + $this.val() + '" ></span></a>');
				}	
			});

		}, // End mediaUpload
		
		
		galleryUpload: function () {
			
				
			// Uploading files
			var gallery_frame,
				attachment_ids,
				$image_gallery_list,
				$image_gallery_ids,
				$ytgu;
			
			
			$(document).on('click', '.yt-gallery-add-image', function(e){
				
				$ytgu = $(this);
				
				$image_gallery_list = $ytgu.siblings('.yt-gallery-list'),
				$image_gallery_ids = $ytgu.siblings('.yt-gallery-hidden-input'),
				attachment_ids = $image_gallery_ids.val();
				
				e.preventDefault();
				
				// If the media frame already exists, reopen it.
				if ( gallery_frame ) {
					gallery_frame.open();
					return;
				}
				
				// Create the media frame.
				gallery_frame = wp.media.frames.gallery_frame = wp.media({
					// Set the title of the modal.
					title: $ytgu.data('title'),
					button: {
						text: $ytgu.data('button')
					},
					multiple: true,
					library : { type : 'image'}
				});
				
				
				// When an image is selected, run a callback.
				gallery_frame.on( 'open', function() {

					var selection = gallery_frame.state().get('selection');
  					
					selection.reset( [] );

  					var ids = $image_gallery_ids.val().split(',');
  					//console.log(ids);
  					ids.forEach(function(id) {
						var attachment = wp.media.attachment(id);
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					});
				});

				// When an image is selected, run a callback.
				gallery_frame.on( 'select', function() {

					var selection = gallery_frame.state().get('selection');
					$image_gallery_list.empty();
					attachment_ids = '';

					selection.map( function( attachment ) {

						attachment = attachment.toJSON();
						
						//console.log(attachment);
						
						var attachment_url = attachment.url
						
						if( typeof( attachment['sizes'] ) != 'undefined' && typeof( attachment.sizes['thumbnail'] ) != 'undefined' && typeof( attachment.sizes.thumbnail['url'] ) != 'undefined' ){
							attachment_url = attachment.sizes.thumbnail.url;
						}

						if ( attachment.id ) {
							
							attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

							$image_gallery_list.append('\
								<li class="image yt-transparent-bg" data-attachment-id="' + attachment.id + '">\
									<span><img src="' + attachment_url + '" /></span>\
									<ul class="yt-gallery-actions">\
										<li><a href="#" class="yt-gallery-delete" title="' + yt_optionsVars.deleteGalleryTitle + '"><i class="fa fa-trash"></i></a></li>\
									</ul>\
								</li>'
							);
							
							$image_gallery_list.siblings('.yt-gallery-delete-all').removeClass('yt-hidden');
								
						}
						
						
						
					});

					$image_gallery_ids.val( attachment_ids );
					
					
					
				});
				
				/*
				 * When escape
				 */
				gallery_frame.on('escape', function(){
					//Do some stuff here
					//console.log('escape');
					
				});
				

				// Finally, open the modal.
				gallery_frame.open();
				
			});
			
			/*
			 * Restore the main ID when the add media button is pressed
			 */
			$('a.media-button').on('click', function() {
				
				//console.log(wp.media.model.settings.post.id + '-' + wp_media_post_id);
				//wp.media.model.settings.post.id = wp_media_post_id;
			});
			
			/**
			 * Image ordering
			 */
			$('.yt-gallery-list').sortable({
				items: 'li.image',
				opacity: 0.6,
				revert: true,
				placeholder: 'yt-ui-sortable-placeholder',
				stop: function(event, ui) {
					$('li',this).removeAttr('style');	
				},
				sort : function( event, ui ) {
					$('.yt-ui-sortable-placeholder').css({
						'height':$(this).find('.ui-sortable-helper').height()-6,
						'width':$(this).find('.ui-sortable-helper').width() -6}
					);
				},
				update: function(event, ui) {
					
					var attachment_ids = '',
						$image_gallery_ids = $(this).siblings('.yt-gallery-hidden-input');
						
					$(this).find('li.image').each(function() {
						var attachment_id = $(this).data( 'attachment-id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});

					$image_gallery_ids.val( attachment_ids );
				}
			});
			
			// Remove images
			$(document).on( 'click', '.yt-gallery-delete', function(e) {
				
				var $el = $(this),
					attachment_ids = '',
					$image_gallery_list = $el.closest('.yt-gallery-list'),
					$image_gallery_ids = $image_gallery_list.siblings('.yt-gallery-hidden-input');
					
				
					
				$el.closest('li.image').remove();
				
				if( !$image_gallery_list.children().length ){
					$image_gallery_list.siblings('.yt-gallery-delete-all').addClass('yt-hidden');
				}

				$image_gallery_list.find('li.image').each(function() {
					var attachment_id = $(this).data( 'attachment-id' );
					attachment_ids = attachment_ids + attachment_id + ',';
				});

				$image_gallery_ids.val( attachment_ids );

				e.preventDefault();
			} );
			
			// Remove all images
			$(document).on( 'click', '.yt-gallery-delete-all', function(e) {
				
				var $el = $(this),
					$image_gallery_list = $el.siblings('.yt-gallery-list'),
					$image_gallery_ids = $el.siblings('.yt-gallery-hidden-input');
				
				var answer = confirm($el.data('confirm'));
				
				if(answer){
					
					
					//$image_gallery_list.children().remove();
					
					var $childLi = $image_gallery_list.children(),
						$length = $childLi.length - 1;
					
					$childLi.remove();
					$image_gallery_ids.val( '' );
					$el.addClass('yt-hidden');
				}
				
				e.preventDefault();
			});
		
		}, // End galleryUpload
		
		fileUpload: function () {
			
				
			// Uploading files
			var files_frame,
				attachment_ids,
				$file_list,
				$file_ids,
				$ytgu,
				dataFilter;
			
			
			$(document).on('click', '.yt-files-add-files', function(e){
				
				$ytgu = $(this);
				
				$file_list = $ytgu.siblings('.yt-files-list'),
				$file_ids = $ytgu.siblings('.yt-files-hidden-input'),
				attachment_ids = $file_ids.val(),
				dataFilter = $ytgu.data('filter');
				
				e.preventDefault();
				
				// If the media frame already exists, reopen it.
				if ( files_frame ) {
					files_frame.open();
					return;
				}
				
				// Create the media frame.
				files_frame = wp.media.frames.files_frame = wp.media({
					// Set the title of the modal.
					state:    'yeah-files-media',
					//title: $ytgu.data('title'),
					button: {
						text: $ytgu.data('button')
					},
					
					library : { type : 'image'}
				});
				
				files_frame.states.add([

					new wp.media.controller.Library({
						id:         'yeah-files-media',
						title:      $ytgu.data('title'),
						priority:   20,
						filterable: 'all',
						searchable: true,
						editable:   true,
						allowLocalEdits: false,
						multiple: true,
						displaySettings: true,
						displayUserSettings: false,
						// library:  wp.media.query( {
						// 	type: 'all'
						// })
					}),
				]);
				
				// When an image is selected, run a callback.
				files_frame.on( 'open', function() {

					//$(files_frame.el).remove();
					//Trick the filter ;p
					if( $.inArray( dataFilter, [ 'all', 'image', 'video', 'audio', 'unattached' ] ) ) {
						var $filter = $(files_frame.el).find( 'select.attachment-filters option[value="' + dataFilter + '"]');
						$filter.prop('selected', true).change();
					}


					var selection = files_frame.state().get('selection');
  					
					selection.reset( [] );

  					var ids = $file_ids.val().split(',');
  					//console.log(ids);
  					ids.forEach(function(id) {
						var attachment = wp.media.attachment(id);
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					});
				});

				// When an image is selected, run a callback.
				files_frame.on( 'select', function() {

					var selection = files_frame.state().get('selection');
					$file_list.empty();
					attachment_ids = '';

					selection.map( function( attachment ) {

						attachment = attachment.toJSON();
						
						//console.log(attachment);
						
						var attachment_url = attachment.url
						
						if( typeof( attachment['sizes'] ) != 'undefined' && typeof( attachment.sizes['thumbnail'] ) != 'undefined' && typeof( attachment.sizes.thumbnail['url'] ) != 'undefined' ){
							attachment_url = attachment.sizes.thumbnail.url;
						}

						if ( attachment.id ) {

							console.log(attachment);
							
							attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;
							//<span>' . $post->post_title . ' <a href="' . esc_attr( $post->guid ) . '">#</a></span>
							$file_list.append('\
								<li class="file" data-attachment-id="' + attachment.id + '">\
									<span>' + attachment.filename + ' <a href="' + attachment.url + '">#</a></span>\
								</li>'
							);
							
							$file_list.siblings('.yt-files-delete-all').removeClass('yt-hidden');
								
						}
						
						
						
					});

					$file_ids.val( attachment_ids );
					
					
					
				});
				
				/*
				 * When escape
				 */
				files_frame.on('escape', function(){
					//Do some stuff here
					//console.log('escape');
					
				});
				

				// Finally, open the modal.
				files_frame.open();
				
			});
			
			/*
			 * Restore the main ID when the add media button is pressed
			 */
			$('a.media-button').on('click', function() {
				
				//console.log(wp.media.model.settings.post.id + '-' + wp_media_post_id);
				//wp.media.model.settings.post.id = wp_media_post_id;
			});
			
			/**
			 * Image ordering
			 */
			$('.yt-files-list').sortable({
				items: 'li.file',
				opacity: 0.6,
				revert: true,
				//placeholder: 'yt-ui-sortable-placeholder',
				stop: function(event, ui) {
					$('li',this).removeAttr('style');	
				},
				sort : function( event, ui ) {
				},
				update: function(event, ui) {
					
					var attachment_ids = '',
						$file_ids = $(this).siblings('.yt-files-hidden-input');
						
					$(this).find('li.file').each(function() {
						var attachment_id = $(this).data( 'attachment-id' );
						attachment_ids = attachment_ids + attachment_id + ',';
					});

					$file_ids.val( attachment_ids );
				}
			});

			// Remove all images
			$(document).on( 'click', '.yt-files-delete-all', function(e) {
				
				var $el = $(this),
					$file_list = $el.siblings('.yt-files-list'),
					$file_ids = $el.siblings('.yt-files-hidden-input');
				
				var answer = confirm($el.data('confirm'));
				
				if(answer){
					
					
					//$file_list.children().remove();
					
					var $childLi = $file_list.children(),
						$length = $childLi.length - 1;
					
					$childLi.remove();
					$file_ids.val( '' );
					$el.addClass('yt-hidden');
				}
				
				e.preventDefault();
			});
		
		} // End fileUpload
	   
	}; // End yt_OptionsYMU Object // Don't remove this, or the sky will fall on your head.0.
	
	/*-----------------------------------------------------------------------------------
	 * Yeahtheme Helper
	 * @author wpthms
	 * @since 1.0
	 * @support wp 3.5+
	 *-----------------------------------------------------------------------------------*/
	
	
	wpthms.Helper = {
		arrayUniqueNoempty: function(a) {
			var out = [];
			jQuery.each( a, function(key, val) {
				val = jQuery.trim(val);
				if ( val && jQuery.inArray(val, out) == -1 )
					out.push(val);
				} );
			return out;
		},
		triggering: function(_selector, _trigger){
			if( $(_selector).length )
				$(_selector).trigger(_trigger);
				
			return false;
		},
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
		},
		tabifyTextarea: function( e ){
			var keyCode = e.keyCode || e.which;

			if (keyCode == 9) {
			    e.preventDefault();
			    var start = $(this).get(0).selectionStart;
			    var end = $(this).get(0).selectionEnd;	

			    // set textarea value to: text before caret + tab + text after caret
			    $(this).val($(this).val().substring(0, start)
			                + "\t"
			                + $(this).val().substring(end));

			    // put caret at right position again
			    $(this).get(0).selectionStart =
			    $(this).get(0).selectionEnd = start + 1;
			}else if(event.shiftKey && event.keyCode == 9) { 
				//shift was down when tab was pressed
			}
		},
		escapeHtml: function(unsafe) {
		    return unsafe
				.replace(/&/g, "&amp;")
				.replace(/</g, "&lt;")
				.replace(/>/g, "&gt;")
				.replace(/"/g, "&quot;")
				.replace(/'/g, "&#039;");
		}
	}
	
	wpthms.OptionsFramework.init();
	
	
})(jQuery);

