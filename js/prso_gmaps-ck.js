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
*/jQuery(document).ready(function(e){function a(e){google.maps.event.trigger(i[e],"click")}var t,n=prsoGmapOptions.canvasID,r=prsoGmapPlaces,i=[],t,s={zoom:prsoGmapOptions.zoom,center:new google.maps.LatLng(prsoGmapOptions.center.lat,prsoGmapOptions.center.lng),mapTypeId:google.maps.MapTypeId.ROADMAP},o=new google.maps.Map(document.getElementById(n)),u=new google.maps.LatLngBounds;e.each(r,function(e,n){var r=new google.maps.LatLng(n.lat,n.lng),s=new google.maps.Marker({position:r,map:o,title:n.title});i.push(s);(function(e,r){google.maps.event.addListener(r,"click",function(){t||(t=new google.maps.InfoWindow);t.setContent(n.html);t.open(o,r)})})(e,s);u.extend(r)});o.fitBounds(u);e("#prso-gmaps-actions a").click(function(t){t.preventDefault();var n;n=e(this).attr("rel");a(n)})});