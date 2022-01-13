function GMap(opt){   
 
 var thisObj = this;   
 var mapObj = opt.mapObj;
 var autocompleteObj = opt.autocompleteObj;
 var latlngObj = opt.latlngObj;
 var currentLocObj = opt.currentLocObj;
    
 var mapOptions = opt.mapOptions;
    
 var markers = [];
 var bounds = new google.maps.LatLngBounds();
 var googleMap;

var directionsService = new google.maps.DirectionsService();
var directionsRenderer = new google.maps.DirectionsRenderer();
    
    
 var icon = {
    //url: place.icon,
    size: new google.maps.Size(71, 71),
    origin: new google.maps.Point(0, 0),
    anchor: new google.maps.Point(17, 34),
    scaledSize: new google.maps.Size(25, 25),
};

    
 // This example adds a search box to a map, using the Google Place Autocomplete
// feature. People can enter geographical searches. The search box will return a
// pick list containing a mix of places and predicted search terms.
// This example requires the Places library. Include the libraries=places
// parameter when you first load the API. For example:
// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
this.setMapPoint = function setMapPoint(optLocation){
       
    var position = '';
    var usePlace = false;
    
    var lat = 0;
    var lng = 0;
     
    if(optLocation.latlng){ 
        position = optLocation.latlng;
        
        lat = position["lat"];
        lng = position["lng"];
    }else{
        usePlace = true;
        
        place = optLocation.place;
        position = place.geometry.location;
        
        lat = place.geometry.location.lat();
        lng = place.geometry.location.lng();
    }
    
    var returnVal = [];

    var marker = new google.maps.Marker({
      map : googleMap,
      icon : null, // defaultnya icon
      title: '',
      draggable: true,
      position: position, //new google.maps.LatLng(-8.5830695,116.3202515),
    });

    markers.push(marker); 
    thisObj.updateLatLngValue(lat,lng);
    googleMap.panTo(position);
     
    // move marker
    google.maps.event.addListener(marker, 'dragend', function (evt) {
        autocompleteObj.val(""); 
        
        var newLat = evt.latLng.lat();
        var newLng = evt.latLng.lng();
        
        thisObj.updateLatLngValue( newLat , newLng);
        googleMap.panTo( new google.maps.LatLng(newLat, newLng));
        
        thisObj.updateFromGeoDecode(newLat,newLng);
    });
    
    if(usePlace){
      if (place.geometry.viewport) {
        // Only geocodes have viewport.
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }      
    }
    
}    

// gk bisa return karena pake promise
this.updateFromGeoDecode = function updateFromGeoDecode(lat,lng){
    if(!autocompleteObj) return;
    
     var templatlng = {
            lat: parseFloat(lat),
            lng: parseFloat(lng),
     };
    
    var geocoder = new google.maps.Geocoder();
    var result = geocoder.geocode({ location: templatlng }).then(
          function(response){
                 if (response.results[0]) { 
                    autocompleteObj.val(response.results[0].formatted_address); 
                  } else {
                    autocompleteObj.val("");
                  }
             }
    ).catch(
        function(e){}
    ); 
}


this.updateLatLngValue = function updateLatLngValue(lat,lng){
    if(latlngObj)
        latlngObj.val(lat+','+lng);
}
 

this.setCurrentLocation =  function setCurrentLocation() {
    thisObj.clearAllMarkers(); 
    
     // jalan async kayanya
    navigator.geolocation.getCurrentPosition(function (position) { 
        var location =  {"lat" : position.coords.latitude, "lng" : position.coords.longitude};
      
        thisObj.setMapPoint({'latlng' : location}); 
        thisObj.updateFromGeoDecode(location["lat"],location["lng"]);
     }); 
}

this.clearAllMarkers = function clearAllMarkers(){
    markers.forEach(function (marker){
         marker.setMap(null);
    });
    
    markers = [];
}

this.initAutocomplete = function initAutocomplete() {

    // monas
  var initLat =  -6.1769694;
  var initLng = 106.8252319;
  
  googleMap = this.createMapObj( {"lat": initLat , "lng":initLng});
     
  // kalo sudah ad nilai diawal 
  var savedLatLng = (latlngObj) ? latlngObj.val() : ''; 
    
  // kalo ad yg di saved
  if(savedLatLng && savedLatLng != ''){
    var tempLatLng = savedLatLng.split(",");
    initLat = parseFloat(tempLatLng[0]);
    initLng = parseFloat(tempLatLng[1]);
       
    initialLocation = new google.maps.LatLng(initLat, initLng);

    thisObj.setMapPoint({'latlng' : initialLocation});

  } else if (navigator.geolocation) {   
    thisObj.setCurrentLocation()
  }
 
  if(autocompleteObj){
          // Create the search box and link it to the UI element.
      var input = autocompleteObj[0];
      var searchBox = new google.maps.places.SearchBox(input);

      // Listen for the event fired when the user selects a prediction and retrieve
      // more details for that place.
      searchBox.addListener("places_changed",function(){
        var places = searchBox.getPlaces();

        if (places.length == 0)  return; 

        // Clear out the old markers. 
        thisObj.clearAllMarkers(); 

        // For each place, get the icon, name and location. 
        bounds = new google.maps.LatLngBounds();  

        places.forEach(function (place){
          if (!place.geometry || !place.geometry.location) {
            console.log("Returned place contains no geometry");
            return;
          }

          thisObj.setMapPoint({'place' : place}); 
        });

        googleMap.fitBounds(bounds);

      });
      
      // Bias the SearchBox results towards current map's viewport.
      googleMap.addListener("bounds_changed", function(){
        searchBox.setBounds(googleMap.getBounds());
      });

  }  
    
  if(currentLocObj) currentLocObj.on('click', function() {thisObj.setCurrentLocation() }); 
    
}

this.calcRoute = function calcRoute(map,start, end){

  //if(!start) start = currentLocation; 
  map.panTo(start);    

  var request = {
    origin: start,
    destination: end,
    travelMode: 'DRIVING'
  };
  directionsService.route(request, function(result, status) {
    if (status == 'OK') {
      directionsRenderer.setDirections(result);
    }
  });
}


this.createMapObj = function createMapObj(location, mapOptions ){
   if(!location) location = {"lat": -6.1769694 , "lng":106.8252319} // monas
      
   var defaultOpt = {
        center: location,
        zoom: 17,
        mapTypeId: "roadmap", 
            styles :[
                {
                    'featureType' : 'poi',
                    'stylers' :[
                        {visibility: 'off'}
                    ]
                }
            ]
      };

    
  var mapOpt = $.extend({}, defaultOpt, mapOptions);
  var googleMap = new google.maps.Map(mapObj[0],mapOpt);  
    
  // setting aj dulu, agak bingung memberatkan gk    
  directionsRenderer.setMap(googleMap);
  return googleMap;
}

}