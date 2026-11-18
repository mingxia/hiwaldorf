/*
 * Widget Javascript actions
 * Copyright (c) 2014 Yeahthemse
 *
 * Released under MIT License
 * @license
 **/


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
    
    wpthms.WidgetActions = {
        init: function(){
            
            var _self = this;
            /**
             * Init after everything is loaded
             */
            $(document)

            .ajaxComplete(function( event, xhr, settings ) {
                _self.callFunctions(); 
                //console.log('xxxx');              

            })
            .ready(function(){

                _self.callFunctions();
                
            })
            .on('nested-widget-added', function(){
                
                var initWidgetFields = setTimeout( function(){
                    _self.callFunctions();
                    //console.log('success');
                }, 500);
            })
            .on( 'click', '.yt-widget-reapeatable-field-add' , _self.repeatableField.addField )
            .on( 'click', '.yt-widget-reapeatable-field-remove' , _self.repeatableField.removeField )
            ;
        },
        callFunctions: function(){

            this.colorPicker('.yt-colorpicker');
            this.ajaxSearchPostPage();
            this.ajaxSearchTag();
            this.repeatableField.initSortable();  
        },  
        /**
         * Color picker
         */
        colorPicker: function(_selector){
            $(_selector).each(function(index, element) {
                var $el = $(this);

                if( $el.hasClass('wp-color-picker') || $el.closest('#available-widgets').length )  
                    return;

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
         * Repeatable fields
         */
        repeatableField:{
            initSortable: function(){
                var wrapper = $('.yt-widget-reapeatable-field-wrapper');

                wrapper.each(function(){
                    var thisWrapper = $(this);

                    if( thisWrapper.hasClass('ui-sortable') || thisWrapper.closest('#available-widgets').length )
                        return;
               
                    thisWrapper.sortable({
                        items: '> .yt-widget-reapeatable-field',
                        opacity: 0.6,
                        revert: true,
                        placeholder: 'ui-sortable-placeholder',
                        start: function(event, ui){ 

                        },
                        stop: function(event, ui) { },
                        sort : function( event, ui ) {
                            ui.placeholder.css({
                                'height': ui.item.height() - 2,  
                                'border': '1px dashed #CCC',
                                'margin-bottom': 10
                            });

                        },
                        update: function(event, ui) {
                            var parent = $(this);
                            ui.item.addClass('open');

                            wpthms.WidgetActions.repeatableField.refreshFields( parent );
                            
                        }
                    });
                 });
            },
            addField: function(e){
                var $el = $(this),
                    parent = $el.siblings('.yt-widget-reapeatable-field-wrapper'),
                    newField = $('.yt-widget-reapeatable-field:first', parent ).clone();

                $(newField).find('[name]').val('');
                $(newField).find('option').removeAttr('selected');
                $(newField).find('[type="checkbox"]').removeAttr('checked');
                $(newField).find('img').remove();
                $(newField).addClass('open').find('.widget-inside').show();
                $(newField).appendTo( parent );

                wpthms.WidgetActions.repeatableField.refreshFields( parent );

                if( parent.hasClass('ui-sortable') )
                    parent.sortable('refresh');


                console.log('aaa');
                e.preventDefault();
            },
            removeField: function(e){
               
                var $el = $(this),
                    parent = $el.closest('.yt-widget-reapeatable-field'),
                    wrapper = $el.closest('.yt-widget-reapeatable-field-wrapper');

                if( parent.siblings( ".yt-widget-reapeatable-field" ).length ){
                    parent.remove();
                }else{
                    alert( yt_widgetVars.msgAlertLastField );

                    parent.find('[type="text"], textarea').each(function(){
                        $(this).val('');
                    });
                }

                 wpthms.WidgetActions.repeatableField.refreshFields( wrapper );

                 if( wrapper.hasClass('ui-sortable') )
                    wrapper.sortable('refresh');

                e.preventDefault();
            },
            refreshFields: function( target){
                setTimeout(function(){
                    $('.yt-widget-reapeatable-field', target ).each(function( index ){
                        var thisField = $(this);

                        thisField.find('[data-name]').each(function(){
                            var dataName = $(this).data('name');
                            if( dataName ){
                                var newName = dataName.replace('__x_*_x__', index );
                                $(this).attr('name', newName );
                            }
                        });
                    })
                }, 500);
            }
        },
        /**
         * Ajax search
         */
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

                if( $input.hasClass( 'ui-autocomplete-input' ) || $input.closest('#available-widgets').length )
                    return;
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

                        //console.log('change');
                    },
                    search: function(event, ui) {

                        //console.log('search');
                    },
                    select: function (event, ui) {
                        list.append( '<span data-id="' + ui.item.id + '"><a class="ntdelbutton">x</a>&nbsp;' + ui.item.name + '</span>' );
                        newtags = textarea.val() + comma + ui.item.id;
                        newtags = wpthms.WidgetActions.helper.arrayUniqueNoempty( newtags.split(comma) );
                        textarea.val( newtags );
                    }
                }).data( "ui-autocomplete" )._renderItem = function( $ul, $item ) {
                    return jQuery( '<li data-id="' + $item.id + '">' ).append( '<a><strong>' + $item.name + '</strong></a>' ).appendTo( $ul );
                };

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
        /**
         * Ajax search
         */
        ajaxSearchPostPage:function(){
            $('.yt-ajax-post-search').each(function(index, element) {
                var $input = $(this),
                    postType = $input.data('type') || 'post',
                    textarea = $input.siblings('input[type="hidden"]'),
                    addBtn = $input.siblings('input[type="button"]'),
                    list = $input.siblings( '.yt-tag-list' ),
                    comma = ',',
                    currentVal = textarea.val(),
                    newtags, 
                    deleteTag = $('a', list);

                if( $input.hasClass( 'ui-autocomplete-input' ) || $input.closest('#available-widgets').length )
                    return;

                $input.autocomplete({
                    source: function( $request, $response ){
                        $.ajax({
                            url: ajaxurl ,
                            type: 'GET',
                            async: true,
                            cache: false,
                            dataType: 'json',
                            data: {
                                action: 'yt_ajax_post_search',
                                s:  $.trim($input.val()),
                                type: postType
                            },
                            success: function( $data ){
                                
                                //console.log($data);
                                if( null != $data ){
                                    $response( jQuery.map( $data, function( $item ) {
                                        return {
                                            id: $item.id,
                                            name: $item.name,
                                        }
                                    }));
                                }
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

                        //console.log('change');
                    },
                    search: function(event, ui) {

                        //console.log('search');
                    },
                    select: function (event, ui) {
                        list.append( '<span data-id="' + ui.item.id + '"><a class="ntdelbutton">x</a>&nbsp;' + ui.item.name + '</span>' );
                        newtags = textarea.val() + comma + ui.item.id;
                        newtags = wpthms.WidgetActions.helper.arrayUniqueNoempty( newtags.split(comma) );
                        textarea.val( newtags );
                    }
                }).data( "ui-autocomplete" )._renderItem = function( $ul, $item ) {
                    return jQuery( '<li data-id="' + $item.id + '">' ).append( '<a><strong>' + $item.name + '</strong></a>' ).appendTo( $ul );
                };

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
        /**
         * Ajax search
         */
        helper:{
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
            escapeHtml: function(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        }
    }

    /*Init*/
    wpthms.WidgetActions.init();
})(jQuery);