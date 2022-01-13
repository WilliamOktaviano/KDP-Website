function Distributors(opt){   
    var thisObj = this;     
    //var googleMap = opt.googleMap;
    var mapObj = opt.mapObj;
    var location = opt.location;
    var storeList = [];
        
    var infoWindow; 
    var currentLocation;
    
    this.showInfo = function showInfo(map,key){
        properties = storeList[key]['properties'];
        geometry = storeList[key]['geometry'];
        
        var name = properties['name'];
        var address = properties['address'];
        var phone =  (properties['phone']) ? '<i class="fas fa-phone" style="margin-right:0.5em"></i>' + properties['phone'] : ''; 
        var location = geometry['coordinates']; 
        location = {'lat' : location[1],'lng' : location[0]};
        var content = "<div style=\"width:300px\"><h3 style=\"margin-top:0;\">"+name+"</h3><p>"+address+"</p><p>"+phone+"</p></div>";

        infoWindow.setContent(content);
        infoWindow.setPosition(location);
        infoWindow.setOptions({pixelOffset: new google.maps.Size(0, -30)});
        infoWindow.open(map); 
        
        map.panTo(location);    
    }
    
    
    this.setCurrentLocation = function setCurrentLocation(){
     navigator.geolocation.getCurrentPosition(function (position) {
        initLat = position.coords.latitude;
        initLng = position.coords.longitude;
        currentLocation = {"lat" : initLat, "lng" : initLng};   
        $(".direction").css("display","inline-block");
     });    
    }
     
      
    this.loadOnReady = function loadOnReady(){   
        // bisa kepanggil 2x sepertinya

        // Create the map. 
        // ambil googleMap dr gmap.js
        
        // set default center
        var centerLoc = location['features'][0]['geometry']['coordinates'];
         
        var gmap = new GMap({'mapObj':mapObj}); 
        var map = gmap.createMapObj({"lat" :centerLoc[1] ,"lng": centerLoc[0]}, 
                                    { 
                                        zoom: 11, 
                                        styles :[
                                            {
                                                'featureType' : 'poi',
                                                'stylers' :[
                                                    {visibility: 'off'}
                                                ]
                                            }
                                        ]
                                    }
                                   ); // ntah kenapa lat lng dibalik
        
        // utk update kalo ad direction
        if(navigator.geolocation) { thisObj.setCurrentLocation() }
        
        // set info windows
        infoWindow = new google.maps.InfoWindow(); 
        var features = location['features']; 
        $.each( features, function( index ) {
              var pkey = features[index]['properties']['pkey'];
              storeList[pkey] = features[index];
        });
              
        // Load the stores GeoJSON onto the map. 
        var result = map.data.addGeoJson(location, {idPropertyName: 'storeid'});
        
        // Show the information for a store when its marker is clicked.
        map.data.addListener('click', function(event){ 
            var key = event.feature.getProperty('pkey');
            thisObj.showInfo(map, key);
            
          }); 
         
        $(".store-list li .name, .store-list li .address").bind( "click", function(event) {   
             thisObj.showInfo(map, $(this).closest("li").attr("relkey")); 
         }) 
        
        $(".store-list li .direction").bind( "click", function(event) {  
            var key =  $(this).closest("li").attr("relkey");
            
            // end
            geometry = storeList[key]['geometry']; 
            var location = geometry['coordinates'];   
            if(currentLocation) gmap.calcRoute(map,currentLocation,{'lat' : location[1],'lng' : location[0]});
         }) 
     };

}