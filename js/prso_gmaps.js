(function(){
	
	//Init vars
	var infoWindow; //Global to hold InfoWindow object
	var mapCanvas = 'prso-gmaps-map'; //Gmaps map canvas ID
	
	
	
	//Init on window load
	window.onload = function() {
		
		//Create object literal with map options
		var options = {
			zoom: 3,
			center: new google.maps.LatLng(37.09, -95.71),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		//Create the map
		var map = new google.maps.Map( document.getElementById(mapCanvas) );
		
		//Create LatLngBounds object
		var bounds = new google.maps.LatLngBounds();
		
		//Create array of places
		var places = [];
		
		//Add some test places
		places.push( new google.maps.LatLng(40.756, -73.986) );
		places.push( new google.maps.LatLng(37.775, -122.419) );
		places.push( new google.maps.LatLng(47.620, -122.347) );
		places.push( new google.maps.LatLng(-22.933, -43.184) );
		
		//Create var for infoWindow object
		var infoWindow;
		
		//Loop over places array
		for( var i = 0; i < places.length; i++ ) {
			
			//Add markers
			var marker =  new google.maps.Marker({
				position: places[i],
				map: map,
				title: 'Place number ' + i
			});
			
			//Wrap event listener in function to allow click event to remain independent
			(function( i, marker ){
				
				//Create event listener
				google.maps.event.addListener( marker, 'click', function(){
					
					//Check if we already have an info window
					if( !infoWindow ) {
						infoWindow = new google.maps.InfoWindow();
					}
					
					//Set info window content
					infoWindow.setContent( 'Place number ' + i );
					
					//Tie infowindow to map marker
					infoWindow.open( map, marker );
					
				});
				
			})(i, marker);
			
			//Extend bounds of map with each place added
			bounds.extend( places[i] );
			
		}
		
		//Adjust map bounding view
		map.fitBounds( bounds );
		
	}
	
})();