jQuery( '#close_map' ).click( function() {
    jQuery( '#map_wrapper' ).slideUp(300).delay(800).fadeOut(400);
});

jQuery( document ).on( 'keyup', '#search_map_val', function() {
    var search_val = jQuery( this ).val();
    
    setTimeout( function() {
	    jQuery.post(
		    ajaxurl,
		    {
			    'action': 'search_map',
			    'search_val': search_val,
			    'nonce': ODMap.MapNonce
		    },
		    function( response ) {
			    var response = JSON.parse(response);
			
			    jQuery( '#map_wrapper' ).fadeIn( 400 ).delay( 800 ).slideDown( 300 );
			
			    gmaps_results_initialize( response );
		    }
	    );
    }.bind( search_val ), 1000 );
} );

/**
 *  This loads the search results onto the map.
 *
 * @param response
 */
function gmaps_results_initialize( response ) {
	var infowindow, i;
	var bounds = new google.maps.LatLngBounds();
	var markers_available = response.data.markers_available;
	var pinColor = 'FE7569';
	
	var pinImage = new google.maps.MarkerImage( 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|' + pinColor,
		new google.maps.Size(21, 34),
		new google.maps.Point(0,0),
		new google.maps.Point(10, 34));
	
	var pinShadow = new google.maps.MarkerImage( 'http://chart.apis.google.com/chart?chst=d_map_pin_shadow',
		new google.maps.Size(40, 37),
		new google.maps.Point(0, 0),
		new google.maps.Point(12, 35));
	
	var mapOptions = {
		center: new google.maps.LatLng( response.search_val.lat, response.search_val.lng ),
		zoom: 9
	};
	
	var map = new google.maps.Map(document.getElementById( 'map-canvas' ), mapOptions);
	
	var markers=[];
	var contents = [];
	var infowindows = [];
	var map_info = [];
	
	var initial_load = google.maps.event.addListener(map, 'idle', function() {
		for ( var i in markers_available ) {
			markers[i] = new google.maps.Marker({
				position: new google.maps.LatLng(markers_available[i].lat, markers_available[i].long),
				title: 'samplemarker',
				icon: pinImage,
				shadow: pinShadow
			});
			
			bounds.extend(markers[i].position);
			
			markers[i].setMap(null);
			markers[i].index = i;
			contents[i] = '<div class="popup_container">' + map_info[i] +
			              '</div>';
			
			if( map.getBounds().contains(markers[i].getPosition()) === true ){
				markers[i].setMap(map);
			}
			
			map.fitBounds(bounds);
			
			infowindows[i] = new google.maps.InfoWindow({
				content: markers_available[i].info,
				maxWidth: 300
			});
			
			google.maps.event.addListener(markers[i], 'click', function() {
				console.log(this.index); // this will give correct index
				console.log(i); //this will always give 10 for you
				infowindows[this.index].open(map,markers[this.index]);
			});
		}
		
		// Remove the initial listener so you can drag after the search results load.
		setTimeout( function() { google.maps.event.removeListener( initial_load ); }, 4000 );
	})
}
