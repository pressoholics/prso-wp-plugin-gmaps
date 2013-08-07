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
			
			//Call helper to set place marker images
			setPlaceMarkerImages( placeID, marker );
			
			//Create event listener
			google.maps.event.addListener( marker, 'click', function(){
				
				//Open InfoWindow -- set last param to 'true' to use custom styled info window :)
				infoWindow = openMarkerInfoWindow( infoWindow, place, marker, map, false );
				
			});
			
		})(placeID, marker);
		
		//Extend bounds of map with each place added
		bounds.extend( placeLatLng );
		
	});
	
	//Adjust map bounding view
	map.fitBounds( bounds );
	
	//Helper to handle marker info window, content, and custom style (if required)
	function openMarkerInfoWindow( infoWindow, place, marker, map, customInfoWindow = false ) {
		
		//Check if we should call helper to generate a custom infowindow
		if( customInfoWindow === false ) {
			
			//Check if we already have an info window
			if( !infoWindow ) {
				infoWindow = new google.maps.InfoWindow();
			}
			
			//Set info window content
			infoWindow.setContent( place.html );
			
			//Tie infowindow to map marker
			infoWindow.open( map, marker );
			
		} else {
			
			//Call helper to generate custom infowindow
			infoWindow = generateCustomInfoWindow( infoWindow, place, marker, map );
			
		}
		
		return infoWindow;
		
	}
	
	/**
	* generateCustomInfoWindow
	* 
	* Helper to generate custom styled info window.
	* Window width and offset adjusted via class infoBoxOptions array here.
	* The rest of the style can be adjusted via "infoBox" css class.
	*
	* NOTE:: This makes use of open source InfoBox class which extends google maps
	* REF:: http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/reference.html
	* 
	* @param	type	name
	* @var		type	name
	* @return	type	name
	* @access 	public
	* @author	Ben Moody
	*/
	function generateCustomInfoWindow( infoWindow, place, marker, map ) {
		
		var infoBoxOptions;
		
		//Set params for InfoBox plugin
		infoBoxOptions = {
			content: place.html
            ,disableAutoPan: false
            ,maxWidth: 0
            ,pixelOffset: new google.maps.Size(-140, -30)
            ,zIndex: null
            ,boxStyle: { 
              background: null
              ,opacity: null
              ,width: "280px"
             }
            ,boxClass: "infoBox"
            ,closeBoxMargin: "10px 2px 2px 2px"
            ,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
            ,infoBoxClearance: new google.maps.Size(1, 1)
            ,isHidden: false
            ,pane: "floatPane"
            ,enableEventPropagation: false
            ,alignBottom: true
		};
		
		//Check if we already have an info window
		if( !infoWindow ) {
			infoWindow = new InfoBox( infoBoxOptions );
		}
		
		//Open info window
		infoWindow.open( map, marker );
		
		return infoWindow;
		
	}
	
	//Helper to handle assigning place marker images
	function setPlaceMarkerImages( placeID, marker ) {
		
		//Init vars
		var markerNumber;
		var markerImageDefault;
		var markerImageMouseover;
		var markerImageMousedown;
		var markerImageMouseup;
		
		//Set marker number  - used for marker icons
		markerNumber = placeID + 1;
		
		/**********************************
		 * Setup Marker Image objects
		 **********************************/
		
		//Cache default marker image based on mouseout state
		markerImageDefault = new google.maps.MarkerImage(
			prsoGmapOptions.markerImages + '/mouseout/number_' + markerNumber + '.png'
		);
		
		//Cache marker image for use on mouseover state
		markerImageMouseover = new google.maps.MarkerImage(
			prsoGmapOptions.markerImages + '/mouseover/number_' + markerNumber + '.png'
		);
		
		//Cache marker image for use on mousedown state
		markerImageMousedown = new google.maps.MarkerImage(
			prsoGmapOptions.markerImages + '/mouseout/number_' + markerNumber + '.png'
		);
		
		//Cache marker image for use on mouseup state
		markerImageMouseup = new google.maps.MarkerImage(
			prsoGmapOptions.markerImages + '/mouseover/number_' + markerNumber + '.png'
		);
		
		
		/**********************************
		 * Add Marker Images to marker via listners
		 **********************************/
		
		//Add default marker image to marker
		marker.setIcon( markerImageDefault );
		google.maps.event.addListener( marker, 'mouseout', function(){
			this.setIcon( markerImageDefault );
		});
		
		//Add listenter to change icon on mouseover event
		google.maps.event.addListener( marker, 'mouseover', function(){
			this.setIcon( markerImageMouseover );
		});
		
		//Add listenter to change icon on mousedown event
		google.maps.event.addListener( marker, 'mousedown', function(){
			this.setIcon( markerImageMousedown );
		});
		
		//Add listenter to change icon on mouseup event
		google.maps.event.addListener( marker, 'mouseup', function(){
			this.setIcon( markerImageMouseup );
		});
		
	}
	
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