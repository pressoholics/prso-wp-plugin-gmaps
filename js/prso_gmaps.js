/**
* prso_gmaps.js
* 
* The core script to init google maps, set markers, add info windows
* as well as handle any external actions from the #prso-gmaps-actions dom container
* 
* NOTE:: 	All marker place data is passed via localized json object 'prsoGmapPlaces'
*			All map init options are passed via json object 'prsoGmapOptions'
*
* @param	Object	prsoGmapPlaces
* @param	Object	prsoGmapOptions
* @access 	public
* @author	Ben Moody
*/

jQuery(document).ready(function($){
	
	//Init vars
	var infoWindow; 										//Global to hold InfoWindow object
	var mapCanvas 			= prsoGmapOptions.canvasID; 	//Gmaps map canvas ID
	var places				= prsoGmapPlaces; 				//Cache places passed via wp_locallize_script
	var placeMarkerCache 	= []; 							//Create place marker array used to open infowindows externally
	var infoWindow;											//Create var for infoWindow object
	
	//Create object literal with map options
	var options = {
		zoom: prsoGmapOptions.zoom,
		center: new google.maps.LatLng(prsoGmapOptions.center.lat, prsoGmapOptions.center.lng),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	
	//Create the map
	var map = new google.maps.Map( document.getElementById(mapCanvas) );
	
	//Create LatLngBounds object
	var bounds = new google.maps.LatLngBounds();	
	
	//Loop over places object
	$.each( places, function(placeID, place){
		
		//Create place latlng object
		var placeLatLng = new google.maps.LatLng(place.lat, place.lng);
		
		//Add markers
		var marker =  new google.maps.Marker({
			position: placeLatLng,
			map: map,
			title: place.title
		});
		
		//Cache marker in global array - used to open infoWindow externally
		placeMarkerCache.push( marker );
		
		//Wrap event listener in function to allow click event to remain independent
		(function( placeID, marker ){
			
			//Create event listener
			google.maps.event.addListener( marker, 'click', function(){
				
				//Check if we already have an info window
				if( !infoWindow ) {
					infoWindow = new google.maps.InfoWindow();
				}
				
				//Set info window content
				infoWindow.setContent( place.html );
				
				//Tie infowindow to map marker
				infoWindow.open( map, marker );
				
			});
			
		})(placeID, marker);
		
		//Extend bounds of map with each place added
		bounds.extend( placeLatLng );
		
	});
	
	//Adjust map bounding view
	map.fitBounds( bounds );
	
	//Helper to open Info Window for a specfic place marker
	function openInfoWindow( placeMarkerID ) {
		google.maps.event.trigger( placeMarkerCache[placeMarkerID], "click" );
	}
	
	//Add listner for map external actions
	$("#prso-gmaps-actions a").click(function(event){
		
		event.preventDefault();
		
		//Init vars
		var placeMarkerID;
		
		//Cache place marker id
		placeMarkerID = $(this).attr("rel");
		
		//Call function to open infowindow for selected place marker
		openInfoWindow( placeMarkerID );
		
	});
	
});