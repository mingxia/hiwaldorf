/*
 * jQuery.bind-first library v0.2.3
 * Copyright (c) 2013 Vladimir Zhuravlev
 *
 * Released under MIT License
 * @license
 *
 * Date: Thu Feb  6 10:13:59 ICT 2014
 **/
(function(t){function e(e){return u?e.data("events"):t._data(e[0]).events}function n(t,n,r){var i=e(t),a=i[n];if(!u){var s=r?a.splice(a.delegateCount-1,1)[0]:a.pop();return a.splice(r?0:a.delegateCount||0,0,s),void 0}r?i.live.unshift(i.live.pop()):a.unshift(a.pop())}function r(e,r,i){var a=r.split(/\s+/);e.each(function(){for(var e=0;a.length>e;++e){var r=t.trim(a[e]).match(/[^\.]+/i)[0];n(t(this),r,i)}})}function i(e){t.fn[e+"First"]=function(){var n=t.makeArray(arguments),i=n.shift();return i&&(t.fn[e].apply(this,arguments),r(this,i)),this}}var a=t.fn.jquery.split("."),s=parseInt(a[0]),f=parseInt(a[1]),u=1>s||1==s&&7>f;i("bind"),i("one"),t.fn.delegateFirst=function(){var e=t.makeArray(arguments),n=e[1];return n&&(e.splice(0,2),t.fn.delegate.apply(this,arguments),r(this,n,!0)),this},t.fn.liveFirst=function(){var e=t.makeArray(arguments);return e.unshift(this.selector),t.fn.delegateFirst.apply(t(document),e),this},u||(t.fn.onFirst=function(e,n){var i=t(this),a="string"==typeof n;if(t.fn.on.apply(i,arguments),"object"==typeof e)for(type in e)e.hasOwnProperty(type)&&r(i,type,a);else"string"==typeof e&&r(i,e,a);return i})})(jQuery);




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
    
    wpthms.SmartWidget = {
        init: function(){
            
            var _self = this;
            
            this._saveWidget = false,

            /**
             * Init after everything is loaded
             */
            $(document).ready( function(){

                _self.sortableNestedWidgetInit();

                $(document.body).bindFirst('click.widgets-toggle', function(e){
                    var target = $(e.target), css = {}, widget, inside, w;
                    if( target.closest('div.widget').find('.yt-widget-extends').length > 0 ) {
                        
                        _self._saveWidget = true;
                        if ( target.hasClass('widget-control-save') ) {
                            var widget = target.closest('div.widget');                                   
                            _self.saveSmartWidgetContainer(widget.find('.yt-widget-extends'));
                        } else if ( target.hasClass('widget-control-remove') ) {
                            var widget = target.closest('div.widget');
                            if( widget.parent().hasClass('yt-widget-extends') ) {
                                var parent = widget.parent();
                                _self.saveSmartWidgetContainer( parent );
                            }
                        }
                    }
                });
                


                $( document ).ajaxComplete(function( event, xhr, settings ) {
                    if( _self._saveWidget ) {
                        _self._saveWidget = false;
                        _self.sortableNestedWidgetInit();
                    }
                });

                $('.recent-post-meta-info, .recent-post-meta-info').click(function() {
                    submeta_info = $(this).closest('.meta-info').find('.submeta-info');
                    if ( $(this).is(':checked') ) { 
                        $( submeta_info ).removeAttr('disabled');
                    } else {
                        $( submeta_info ).attr('disabled', 'disabled' );
                    }
                });
                
            });
        },
        /**
         * 
         */
        sortableNestedWidget: function( _selector ){
            var _self = this;

            _selector.sortable({
                placeholder: 'widget-placeholder',
                items: '> .widget',
                handle: '> .widget-top > .widget-title',
                cursor: 'move',
                distance: 2,
                containment: 'document',
                start: function(e,ui) {
                    ui.item.children('.widget-inside').hide();
                    ui.item.css({margin:'', 'width':ui.item.width()});
                    _self._saveWidget = true;
                },
                receive: function(e, ui) {
                    var sender = $(ui.sender);

                    if ( !$(this).is(':visible') || this.id.indexOf('orphaned_widgets') != -1 )
                        sender.sortable('cancel');

                    $(document).trigger( 'nested-widget-added');

                    if ( sender.attr('id').indexOf('orphaned_widgets') != -1 && !sender.children('.widget').length ) {
                        sender.parents('.orphan-sidebar').slideUp(400, function(){ $(this).remove(); });
                    }
                },
                stop: function(e,ui) {
                    if ( ui.item.hasClass('ui-draggable') && ui.item.data('draggable') )
                        ui.item.draggable('destroy');


                    /*IF is smart tabby widget, decline!*/
                    var $id_base = ui.item.find('[name="id_base"]').val();
                    if( 'yt-smart-tabby-widget' == $id_base ) {
                        ui.item.remove();
                        return;
                    }
                    if ( ui.item.hasClass('deleting') ) {
                        _self.saveSmartWidget(ui.item);
                        ui.item.remove();
                        return;
                    }


                    var add = ui.item.find('input.add_new').val(),
                        n = ui.item.find('input.multi_number').val(),
                        id = 'rb-__i__',
                        sb = $(this).attr('id');

                    ui.item.css({margin:'', 'width':''});   
                    if ( add ) {
                        var matches = 0, 
                        id_base = ui.item.find('.id_base').val();
                        $(this).find(":input.id_base").each(function(i, val) {
                            if ($(this).val() == id_base ) {
                                matches++;
                            }
                        });

                        var widget_id = id_base + '-yt-widget-' + matches;
                        ui.item.find('.widget-id').val( widget_id );
                        if ( 'multi' == add ) {
                            ui.item.html( ui.item.html().replace(/<[^<>]+>/g, function(m){ return m.replace(/__i__|%i%/g, n); }) );
                            ui.item.attr( 'id', id.replace('__i__', n) );
                            n++;
                            $('div#' + id).find('input.multi_number').val(n);
                        } else if ( 'single' == add ) {
                            ui.item.attr( 'id', 'new-' + id );
                            rem = 'div#' + id;
                        }

                        _self.saveSmartWidget(ui.item);
                        ui.item.find('input.add_new').val('');
                        ui.item.find('a.widget-action').click();

                        return;
                    }

                    _self.saveSmartWidget(ui.item);
                }
            });
        },
        sortableNestedWidgetInit: function(){
            var _self = this;
            $('#widget-list').children('.widget').draggable( "option", 'connectToSortable', 'div.widgets-sortables,div.yt-widget-extends' );
            _self.sortableNestedWidget( $('#widgets-right .yt-widget-extends') );
        },
        saveSmartWidgetContainer: function(container, disabled ){
            var field = container.data('setting'), data =  new Array();
            if( container.find('div.widget').length > 0 ){
                container.find('div.widget').each(function(i){
                    if( $(this).hasClass('deleting') ) return;
                    if( i != 0 ){
                        data += ':yt-sm-data:';
                    }
                    $(this).find(':input').each(function(index, el){

                        if( !$(this).attr('name') !== undefined ){
                            

                            // console.log($(this).attr('name'));

                            if( $.trim( $(this).val() ) != '' ) {
                                if( el.type == 'checkbox' || el.type == 'radio' ){
                                    if( $(this).is(':checked') ){
                                        data += $(this).attr('name')+'='+$(this).val()+'&';
                                    }
                                }else{
                                    data += $(this).attr('name')+'='+ encodeURIComponent( $(this).val() )+'&';
                                }
                            }
                            if( ! disabled )
                                $(this).attr('disabled','disabled');

                        }
                    });
                });
                $(field).val(data);
            }else{
                $(field).val('');
            }
        },
        saveSmartWidget: function(widget){
            var _self = this,
                container = widget.closest('.yt-widget-extends');
            
            _self.saveSmartWidgetContainer(container , true );
        },

    }

    /*Init*/
    wpthms.SmartWidget.init();
})(jQuery);