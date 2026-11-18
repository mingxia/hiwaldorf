if (typeof wpthms == 'undefined') {
	var wpthms = {};
}

;(function ($) {

	"use strict";
		
	$.expr[':'].contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
	};
	
	if (typeof wpthms == 'undefined') {
	   return;
	}
	
	wpthms.ShortcodeGenerator = {
		/**
		 * Init Function
		 */
		
		init: function(){
			
			/**
			 * Append preloader to body
			 */
			 
			$(document).ready( function(){
				if( typeof adminpage !== 'undefined' && !$( '#yt-main-spinner' ).length ){
					$('<span/>', {
						'id': 'yt-main-spinner',
						'class': 'yt-preloader yt-ajax-loader'
					}).appendTo('body');
				}
			});	
			
			/*Ajaxifying class*/
			this.ajaxifying = false;
			this.ajaxifyingClass = 'yt-ajaxifying';
			this.pluginActivatingClass = 'yt-shortcode-editor-plugin-in-action';
			this.shortcodeSnippet = '.yt-shortcode-snippet-area';
			this.insertShortcodeButton = '#yt-shortcode-send-to-editor' ;
			this.currentEditor = 'content';
			this.buttonActions = '#yt-shortcode-button-preview, #yt-shortcode-send-to-editor';
			this.ajaxurl = typeof ajaxurl !== 'undefined' ? ajaxurl : yt_shortcodeVars.ajaxurl;
			this.liveShortcodeInterval;
			
			this.mediaUpload();
			this.removeFile();
			this.fullscreenMode();
			this.ajaxSearchTag();
			this.openModalBox();
			this.loadShortcodeForm();
			this.addChildRepeatableField();
			this.deleteChildRepeatableField();
			this.insertShortcode();
			this.closeModalBox();
			this.previewShortcode();
		},
		testConsole: function(){
			console.log('testConsole');
		},
		
		fullscreenMode: function(){
			$(document).on('click', '.mce_yt-shortcode-init-action-button-fullscreen, #wp_fs_yt-shortcode-init-action-button-fullscreen', function(e){
				$('.yt-shortcode-init-action-button[data-editor="content"]').trigger('click');
			});
			
			return false;
		},
		/**
		 * Open modal box
		 */
		openModalBox: function(){
			/**
			 * Click shortcode button trigger
			 */
			 
			var self = this;
			
			$(document).on('click', '.yt-shortcode-init-action-button', function(e){
				
				if( self.ajaxifying )
					return;

				var $body = $('body');
				
				self.currentEditor = tinymce.activeEditor ? tinymce.activeEditor.id : self.currentEditor;
				//console.log( window.wpActiveEditor );
				
				if( $('#yt-shortcode-media-modal, #yt-shortcode-media-modal-backdrop').length ){
					$('#yt-shortcode-media-modal, #yt-shortcode-media-modal-backdrop').show();

					/*Call shortcode parser*/
					self.parseChildShortcodeHelper();
					self.parseShortcodeHelper();
					self.parseShortcode();

				}else{
					self.ajaxifying = true;
					$body.addClass( self.ajaxifyingClass ).addClass( self.pluginActivatingClass );

					$.ajax({
						type: "GET",
						url: self.ajaxurl,
						data:
						{
							action : 'yt_shortcode_modal_box',
							nonce : $(this).data('nonce')
						},
						success: function( data ){
							$body.removeClass( self.ajaxifyingClass );
							$body.append( data );						
							
							// $( '#yt-shortcode-item-list li a' ).tooltip({
							// 	tooltipClass:"yt-shortcode-tooltip",
							// 	show: {duration: 100 }
							// });
							
							//Call shortcode searcher
							self.searchShortcode('#yt-shortcode-item-list', '.yt-shortcode-item-filter input');
							self.ajaxifying = false;
						}
					});
				}
				
				e.preventDefault();
			});
			
		},
		searchShortcode: function( _list, _input ) {
			$(_input)
				.change( function () {
					var filter = $(this).val();
					
					$(_list).find('li.not-found').remove();
					
					if(filter) {
						$(_list).find('li:not(:contains(' + filter + '))').hide();
						$(_list).find('li:contains(' + filter + ')').show();
						
						if(!$(_list).find('li:visible').length){
							$(_list).append('<li class="not-found"><span>' + yt_shortcodeVars.ytNotfound+ '</span></li>');
						}	
					} else {
						$(_list).find('li').show();
					}
					
					return false;
				})
				.keyup( function () {
					$(this).change();
				});
		},
		closeModalBoxHelper: function(){
			
			var self = this,
				$body = $('body');
			
			self.clearLiveShortcodeInterval();
			$('#yt-shortcode-media-modal, #yt-shortcode-media-modal-backdrop').hide();
			
			$('head').find('#yt-shortcode-media-hack').remove();
			
			$body.removeClass( self.pluginActivatingClass );
		},
		
		closeModalBox: function(){
			
			var self = this;
			
			$(document).on('click', '#yt-shortcode-media-modal-close, #yt-shortcode-media-modal-backdrop', function(e){
				
				self.closeModalBoxHelper();
				e.preventDefault();
			});
		
		},
		/**
		 * Load shorcode type
		 */
		loadShortcodeForm: function(){
			
			var self = this;
			
			$(document).on('click', '#yt-shortcode-item-list li a', function(e){
				
				e.preventDefault();
				
				/*Clear live interval*/
				self.clearLiveShortcodeInterval();
				
				if( $(this).hasClass( 'active' ) )
					return;
				
				var $el = $(this),
					$body = $('body'),
					$appendTarget = $('.yt-shortcode-content-fields-wrapper > *'),
					$previewTarget = $('.yt-shortcode-content-preview-wrapper'),
					shortcodeType = $(this).data('shortcode'),
					nonce = $('#yt-shortcode-nonce').val(),
					title = $('.yt-media-frame-title h1 span');
				
				$('.yt-shortcode-action-preview-area').empty();
				$('.yt-shortcode-html-code-area').empty();
					
					
				$('#yt-shortcode-item-list li a').not(this).removeClass('active');
				$el.addClass('active');
				
				$(self.buttonActions).attr('disabled', 'disabled');
				
				/*Append title*/
				title.text( ' ' + $el.text() + ' ' );
				
				$appendTarget.fadeOut(100);
				$previewTarget.fadeOut(100);
				$body.addClass( self.ajaxifyingClass );
				
				$.ajax({
					type: "GET",
					url: self.ajaxurl,
					data:
					{
						action : 'yt_shortcode_load_type',
						nonce : nonce,
						type: shortcodeType
					},
					success: function( data , textStatus){
						
						$appendTarget.html('');
						
						/*Ajaxifying*/
						$body.removeClass( self.ajaxifyingClass );
						/*Append shortcode fields*/
						$appendTarget.append( data ).fadeIn(300);
						
						/*Parse shortcode at the first time, after that, call live Parsing shortcode*/
						self.parseChildShortcodeHelper();
						self.parseShortcodeHelper();
						self.parseShortcode();
						
						/*Call javascript for fields*/
						self.javascriptCallback();
						
						/*Show Preview area*/
						$previewTarget.fadeIn();
						
						$(self.buttonActions).removeAttr('disabled');
						
						
					},
					error:function( response ){
					}
					
				});
				
				$('.yt-shortcode-syntax-snippet-area').html( $el.data('syntax') );
				
			});
			
			
		},
		/**
		 * Parse shorcode form
		 */
		parseShortcode: function(){
			
			var self = this;
			
			self.liveShortcodeInterval = setInterval(function(){
				self.parseChildShortcodeHelper();
				self.parseShortcodeHelper();
				
			}, 1000);
			
		},
		/**
		 * Parse shortcode helper
		 */
		parseShortcodeHelper: function( callback ){
			
			var self = this,
				shortcodeSyntax = $('.yt-shortcode-syntax-snippet-area').html(),
				parsedShortcode = shortcodeSyntax;
				
			$('.yt-shortcode-content-fields-wrapper .yt-shortcode-input[data-element="parent"]').each( function() {
				
				var input = $(this),
					id = input.data('id'),
					relplace = new RegExp( '{{' + id + '}}', 'g' ),
					value = input.val();
					//console.log( re );
				value = value ? value : '';
				parsedShortcode = parsedShortcode.replace( relplace, value );

				//Replace line breaks with br tag
				parsedShortcode = parsedShortcode.replace( new RegExp('\r?\n','g'), '<br>' );
				
			});
			
			$( self.shortcodeSnippet ).html( parsedShortcode );
			
			if( typeof callback == 'function' ){
				callback();
			}
			
			//console.log('Open modal box');
			
		},
		/**
		 * Clone Child Repeatable field
		 */
		addChildRepeatableField:function(){
			var self = this;
			$(document).on('click', '.yt-shortcode-button-add-more-repeatable-field', function(e){
				
				var $el = $(this),
					sortableSibling = $el.siblings(' .yt-shortcode-child-field-type-repeatable-sortable'),
					cloneField = sortableSibling.find('.yt-shortcode-child-field-type-repeatable-row:first').clone(true);
				
				self.helper.resetFormFields(cloneField);
				sortableSibling.append( cloneField );
				
				
				if($el.attr('data-click') !== 'undefined'){
					sortableSibling.sortable();
					$el.attr('data-clicked', 1);
				}else{
					sortableSibling.sortable('refresh');
				}
				
				
				
				e.preventDefault();
			});
		},
		/**
		 * Delete Child Repeatable field
		 */
		deleteChildRepeatableField:function(){
			var self = this;
			$(document).on('click', '.yt-shortcode-child-field-type-repeatable-row > span[data-action="remove"]', function(e){
				var $el = $(this),
					parent = $(this).closest(' .yt-shortcode-child-field-type-repeatable-sortable'),
					count = parent.children().length;
					
				if( count > 1 ){
					$el.parent().hide(300, function(){
						$(this).remove();
					});
				}else{
					self.helper.resetFormFields($el.parent());
				}
				
				e.preventDefault();
			});
		},
		/**
		 * Parse child shorcode helper
		 */
		parseChildShortcodeHelper: function( callback ){
			
			if( !$('.yt-shortcode-field-type-repeatable').length )
				return;
			
			/*Parent*/
			$('.yt-shortcode-field-type-repeatable').each(function() {
				
				var $globalChildShortcode = '';
				
				/*Children*/
				$( '.yt-shortcode-child-field-type-repeatable-row', $(this) ).each(function() {
					
					var $shortcodeSyntax = $(this).closest('.yt-shortcode-field-type-repeatable').find('.yt-shortcode-child-field-type-repeatable-syntax').html(),
						$cShortcode = $shortcodeSyntax;
						
					/* input*/	
					$('.yt-shortcode-input[data-element="children"]', $(this) ).each(function() {
						
						var input = $(this),
							id = input.data('id'),
							relplace = new RegExp( '{{' + id + '}}', 'g' ),
							value = input.val();
						
						value = value ? value : '';
						$cShortcode = $cShortcode.replace( relplace, value );
						
					});
						
					$globalChildShortcode += $cShortcode;
					
				});	
				
				
				$(this).find( 'textarea[data-element="parent"]' ).html( $globalChildShortcode );
			
			});
			
		},
		/**
		 * Preview shortcode
		 */
		previewShortcode: function(){
			
			var self = this;
			
			$( document ).on('click', '#yt-shortcode-button-preview', function(e){

				e.preventDefault();

				var $el = $(this),
					$body = $('body'),
					$previewTarget = $('.yt-shortcode-snippet-area'),
					$previewArea = $('.yt-shortcode-action-preview-area'),
					$previewHtmlArea = $('.yt-shortcode-html-code-area'),
					nonce = $('#yt-shortcode-nonce').val(),
					shortcode = $previewTarget.html();
					
				$body.addClass( self.ajaxifyingClass );
				$previewArea.html('').fadeOut();
				$previewHtmlArea.html('').fadeOut;
				console.log(shortcode);
					
				$.ajax({
					type: "GET",
					url: self.ajaxurl,
					data:
					{
						action : 'yt_shortcode_live_preview',
						nonce : nonce,
						shortcode: shortcode
					},
					success: function( response ){
						console.log(response);
						/*Show Preview area*/
						$previewArea.append( response.data.html ).fadeIn(300);
						$previewHtmlArea.append( response.data.html_code ).fadeIn(300);
						/*Ajaxifying*/
						$body.removeClass( self.ajaxifyingClass );
						
					},
					error: function(){
					}
				});
			});
		},
		/**
		 * Insert shortcode to editor
		 */
		insertShortcode: function(){
			
			var self = this;
			
			$( document ).on('click', this.insertShortcodeButton, function(e){

				
				e.preventDefault();
				var activeEditor = self.currentEditor,
					shortcodeData = $( self.shortcodeSnippet ).html();

				/**
				 * This do the trick ;)
				 */

				// if( 'content' === activeEditor ){
				// 	if( typeof switchEditors !== 'undefined')
				// 		switchEditors.go( activeEditor,'html');
				// }else if( 'wp_mce_fullscreen' === activeEditor ){
				// 	if( typeof fullscreen !== 'undefined')
				// 		fullscreen.switchmode('html');
				// }
				//console.log(activeEditor);

				window.wp.media.editor.insert( shortcodeData );
/*
				if( 'content' === activeEditor ){
					if( typeof switchEditors !== 'undefined')
						switchEditors.go( activeEditor,'tmce');
				}else if( 'wp_mce_fullscreen' === activeEditor ){
					if( typeof fullscreen !== 'undefined')
						fullscreen.switchmode('tmce');
				}*/

				self.closeModalBoxHelper();
				
			});
		},
		/**
		 * Initialize color picker, ect
		 */
		javascriptCallback:function(){
			$('.yt-shortcode-colorpicker').each(function(index, element) {
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
		 * Clear live Parsing shortcode interval
		 */
		clearLiveShortcodeInterval:function(){
			
			clearInterval( this.liveShortcodeInterval);
		
		},
		helper:{
			resetFormFields: function(field, selectors){
				
				if(!selectors)
					selectors = 'select, input[type="hidden"], textarea, input[type="text"]';
				
				field.find(selectors).val('');
				field.find('.yt-shortcode-media-screenshot').html('');
			},	
		},
		/**
		 * Shortcode Media Uploader
		 */
		mediaUpload: function () {
			
			// Uploading files
			var shortcode_file_frame,
				heading_text,
				$el; //Selector
			
			
			$(document).on('click', '.yt-shortcode-media-upload-button', function(e){
				
				$('head').append('<style id="yt-shortcode-media-hack">.attachment-display-settings h3 + .setting, .attachment-display-settings h3 + .setting + .setting{\
						display:none;\
					}</style>'
				);
				
				$el = $(this),
				heading_text =  $el.data('title');
				
				/*
				 * Mime types
				 */
				var image = /(^.*\.jpg|jpeg|png|gif|svg|ico*)/gi;
				var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
				var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
				var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
			
				e.preventDefault();
				
				/*
				 * If the media frame already exists, reopen it.
				 */
				if ( shortcode_file_frame ) {
					
					/*
					 * Set the post ID to what we want
					 */
					//file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					
					$('.media-modal .media-frame-title h1').html(heading_text);
					
					/*
					 * Open frame
					 */
					shortcode_file_frame.open();
					
					return;
					
				} else {
					/*
					 * Set the wp.media post id so the uploader grabs the ID we want when initialized
					 */
					//wp.media.model.settings.post.id = set_to_post_id;
				}
				
				/*
				 * Create the media frame.
				 */
				shortcode_file_frame = wp.media.frames.shortcode_file_frame = wp.media({
					state:    'yeah-shortcode-media',
					button: {
						text: yt_shortcodeVars.ytUseImage
					},
					multiple: false  // Set to true to allow multiple files to be selected
					//library : { type : 'all'},
					
				});
				
				shortcode_file_frame.states.add([

					new wp.media.controller.Library({
						id:         'yeah-shortcode-media',
						title:      heading_text,
						priority:   20,
						filterable: 'all',
						editable:   true,
						allowLocalEdits: false,
						displaySettings: true,
						displayUserSettings: false
					}),
				]);
				
				/*
				 * When escape
				 */
				shortcode_file_frame.on('escape', function(){
					//Do some stuff here
					//console.log('escape');
				});
				/*
				 * When an image is selected, run a callback.
				 */
				shortcode_file_frame.on( 'select', function() {
					
					
					//console.log($ytu.parent().prev('.yt-input').attr('id'));
					/*
					 * We set multiple to false so only get one image from the uploader
					 */
					
					var attachment = shortcode_file_frame.state().get('selection').first().toJSON(),
						preview,
						attachment_url = attachment.url;
						
						
					if( $('.attachment-display-settings select option:selected') !== 'full' && $('.attachment-display-settings select[data-setting="size"]').length ){
						
						var attachment_size = $('.attachment-display-settings select[data-setting="size"]').val();
					
						if( typeof( attachment['sizes'] ) != 'undefined' && typeof( attachment.sizes[attachment_size] ) != 'undefined' && typeof( attachment.sizes[attachment_size].url ) != 'undefined' ){
							attachment_url = attachment.sizes[attachment_size].url;
						}
					}
					
					
						
					//do something with attachment variable, for example attachment.filename
					//Object:
					//attachment.alt - image alt
					//attachment.author - author id
					//attachment.caption
					//attachment.dateFormatted - date of image uploaded
					//attachment.description
					//attachment.editLink - edit link of media
					//attachment.filename
					//attachment.height
					//attachment.icon - don't know WTF?))
					//attachment.id - id of attachment
					//attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
					//attachment.menuOrder
					//attachment.mime - mime type, for example image/jpeg"
					//attachment.name - name of attachment file, for example "my-image"
					//attachment.status - usual is "inherit"
					//attachment.subtype - "jpeg" if is "jpg"
					//attachment.title
					//attachment.type - "image"
					//attachment.uploadedTo
					//attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
					//attachment.width
					
					/*
					 * Do something with attachment.id and/or attachment.url here
					 */
					if (attachment_url.match(image)) {
						preview = '<a class="yt-shortcode-media-uploaded-image" href="' + attachment_url + '">\
							<img src="' + attachment_url + '" alt="" />\
						</a>';
					}else{
						preview = '<p>' + yt_shortcodeVars.ytNotImage + ' <a href="' + attachment_url + '" target="_blank" rel="external">' + yt_shortcodeVars.ytViewFile + '</a></p>';
					}
					
					$el.parent().siblings('.yt-shortcode-input[type="text"]').val( $el.data('by') === 'id' ? attachment.id : attachment_url );
					$el.parent().siblings('.yt-shortcode-media-screenshot').fadeIn().html(preview);
					$el.next('span').removeClass('yt-hidden');
					
					/*
					 * Restore the main post ID
					 */
					//wp.media.model.settings.post.id = wp_media_post_id;
					
					$('head').find('#yt-shortcode-media-hack').remove();
					
				});
				
				/*
				 * Finally, open the modal
				 */
				shortcode_file_frame.open();
			});
			
		
		}, // End mediaUpload
		
		removeFile: function () {
		 
			$(document).on('click', '.yt-shortcode-media-remove-button', function(e){
				
				var $el = $(this);
				
				/*
				 * - remove text file value
				 * - hide remove button
				 * - remove screenshot
				 */
				$el.parent().siblings('.yt-shortcode-input').val('');
				$el.addClass('yt-hidden');
				//$el.parent('.yt-button-action').siblings('.yt-screenshot').html('');
				$el.parent('.yt-shortcode-media-action').siblings('.yt-shortcode-media-screenshot').children().html('');
				e.preventDefault();
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
	};
	
	
	if( typeof adminpage !== 'undefined' ){
		
		wpthms.ShortcodeGenerator.init();
	
	}
	
})(jQuery);