/*!
 * wpthms Shortcodes Custom Javascript
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
	 * wpthms.YTShortcodeGenerator
	 *
	 * @since 1.0
	 */
	wpthms.YTShortcodeGenerator = {
		/**
		 * init
		 */
		init: function(){
			
			var _self = this;

			/**
			 * Onload
			 */
			$(window).on( 'load', function(){

				$('.yt-google-map').each(function(){

					_self.googleMap( $(this));
				});
			});
		},
		/**
		 * Google Map
		 */
		googleMap: function( _this ){

			if( !_this.length )
				return;
			
			if( _this.attr('id') !== undefined && '' === _this.attr('id') )
				return;

			//[gmap address="" maptype="ROADMAP" height="400" latitude="52.49878400576003" longitude="-1.9063879018524403" zoom="18" hue="#33B3D3" saturation="-80" roadgeometry_lightness="100" roadgeometry_visibility="simplified" roadlabels_visibility="on" controls="true" marker="true" imageurl="" imageid="1813" markerw="85" markerh="85" popup="false"]<img src="http://demo.wpthms.com/creatiz/wp-content/uploads/2013/05/logo@2x.png" height="100" width="220">[/gmap]
			var mapSettings =  _this.data('settings'),
				centerType = mapSettings.latitude && mapSettings.longitude ? 'latlong' : 'address',
				mapLatLong = new google.maps.LatLng( mapSettings.latitude, mapSettings.longitude );
				// Options
			
			/**
	         * Get lat/long for specific type of query
	         */
		
			var	options = {
					zoom: 				mapSettings.zoom,
					disableDefaultUI: 	mapSettings.disableDefaultUI,
					center: 			mapLatLong,
					mapTypeId: 			google.maps.MapTypeId[mapSettings.mapTypeId],
					scrollwheel: false,
					/*panControl: true,
					zoomControl: true,
					mapTypeControl: true,
					scaleControl: true,
					streetViewControl: true,
					overviewMapControl: true,*/
					styles: mapSettings.styles,
		        };
	        
	  //       _this.css({
			// 	width: mapSettings.width,
			// 	height: mapSettings.height 
			// });
	        /**
	         * Init map
	         */
	        var gMap = new google.maps.Map( document.getElementById( mapSettings.id ) , options);       

	        
	        /**
	         * Map marker
	         */
	        if( mapSettings.marker ){
	        	/**
		         * Marker options
		         */
		        var mapMarkerOptions = {
						position: mapLatLong,
						map: gMap,
						flat: true
			        };

			    if( mapSettings.markerTitle )
			    	mapMarkerOptions.title = mapSettings.markerTitle;

			    if( mapSettings.icon ){
					var mapIcon = {
						url: mapSettings.icon,
						size: null,
						origin: null,
						// H - V
						anchor: new google.maps.Point( mapSettings.anchorh, mapSettings.anchorv ),
						scaledSize: new google.maps.Size( mapSettings.markerw, mapSettings.markerh )
			        }

			    	mapMarkerOptions.icon = mapIcon;
			    }

			    //console.log(mapMarkerOptions);
		        /**
		         * Marker
		         */
		        var mapMarker = new google.maps.Marker( mapMarkerOptions );

		        /**
		         * InfoWindow
		         */
		        if( mapSettings.popup && mapSettings.infoWindow ){
			        
			        var mapInfoWindow = new google.maps.InfoWindow({
			            content: mapSettings.infoWindow
			        });
			        google.maps.event.addListener(mapMarker, 'click', function() {
						mapInfoWindow.open(gMap, mapMarker);
			        });
			    }

		    }


			if( 'address' ===  centerType ){
				var address = mapSettings.address,
		    		geocoder = new google.maps.Geocoder();
		    	
		    	geocoder.geocode( { 'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						mapLatLong = results[0].geometry.location;
						gMap.setCenter( mapLatLong );
						console.log( mapLatLong );
						if( typeof mapMarker !== 'undefined')
							mapMarker.setPosition( mapLatLong );
						//console.log( mapLatLong );
					} else {
						alert('Geocode was not successful for the following reason: ' + status);
						
					}
				});
			}

	        // Keeps the Pin Central when resizing the browser
	        google.maps.event.addDomListener(window, 'resize', function() { 
	        	gMap.setCenter(mapLatLong); 
	        });  
		}
	}
	/**
	 * init
	 *
	 * @since 1.0
	 */
	wpthms.YTShortcodeGenerator.init();
	
})(jQuery);