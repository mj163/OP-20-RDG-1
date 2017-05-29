$(window).load(function() {
				google.maps.event.trigger(map, "resize");
			});
	$("document").ready(function(){	  
		$('#post').bind("keyup keypress", function(e) {		
			var code = e.keyCode || e.which;
			if (code == 13) {	
			e.preventDefault();   // add property form will not be submitted by enter key
			return false;
			}
		});	
		google.maps.event.trigger(map, 'resize');
		$("#type-selector").hide();	
			
		
	}) ; 

	  // $("body").load(function(){		  
	
	function initMap() {
		var gmarkers = [];
		var saved_lng = $("#lang").val();
		var saved_lat = $("#lat").val();
		if(saved_lat == "" && saved_lng == "")
		{
			saved_lng = 151.2195;  
			saved_lat = -33.8688;
		}
		
		var ltlg = {lat:parseFloat(saved_lat), lng:parseFloat(saved_lng)};								
		
		var map = new google.maps.Map(document.getElementById('map'), {
		  center: {lat: parseFloat(saved_lat), lng: parseFloat(saved_lng)},
		  zoom: 13
	  });
	  var input = /** @type {!HTMLInputElement} */(
	   document.getElementById('pac-input'));		  
		
		geocoder = new google.maps.Geocoder();	
			geocoder.geocode( { 'location': ltlg}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				infowindow.setContent(results[1].formatted_address);				
				$("#pac-input").val(results[1].formatted_address);					  
			  }
			});
		
        var types = document.getElementById('type-selector');
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        var infowindow = new google.maps.InfoWindow();
		var image = 'https://chart.googleapis.com/chart?chst=d_map_xpin_icon_withshadow&chld=pin|home|52B552|000000';
       
		var marker = new google.maps.Marker({
		  position: {lat: parseFloat(saved_lat), lng: parseFloat(saved_lng)},
          map: map,		  
		  draggable:true,	
		  icon: image,
          anchorPoint: new google.maps.Point(0, -29)
        });
		// gmarkers.push(marker);
		google.maps.event.addListener(marker, 'drag', function(evt){ //draged
		
			//document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat().toFixed(3) + ' Current Lng: ' + evt.latLng.lng().toFixed(3) + '</p>';
			// alert(evt.latLng.lat().toFixed(3) + "--" + evt.latLng.lng().toFixed(3));
					
			$(".mp_lng").val(marker.getPosition().lng());
			$(".mp_lat").val(marker.getPosition().lat());
			$("#lang").val(marker.getPosition().lng());
			$("#lat").val(marker.getPosition().lat());
		});
		// google.maps.event.addListener('keypress keyup', change_place_marker);////////////////////////
		google.maps.event.addDomListener(document.getElementById("pac-input"), "keyup", change_place_marker);
		google.maps.event.addDomListener(document.getElementById("show_by_latlang"), "click", change_place_marker_reverse);
		google.maps.event.addDomListener(document.getElementById("show_by_latlang"), "click", change_place_marker_reverse);
		
		  function change_place_marker(){
			geocoder = new google.maps.Geocoder();			
			var address = document.getElementById("pac-input").value;
			geocoder.geocode( { 'address': address}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				// removeMarkers(); // remove previous markers to prevent duplicate markers
				map.setCenter(results[0].geometry.location);
				marker.setPosition(results[0].geometry.location);
				// var marker = new google.maps.Marker({
					// map: map,
					// draggable:true,	
					// icon: image,
					// position: results[0].geometry.location
				// });
				 // gmarkers.push(marker);
				$(".mp_lng").val(marker.getPosition().lng());
				$(".mp_lat").val(marker.getPosition().lat());
			  } 
			  // else {
				// alert("Geocode was not successful for the following reason: " + status);
			  // }
			});
					  
		}		  
		
		function change_place_marker_reverse()
		{
			// removeMarkers(); 
			var savedlng = $("#lang").val();
			var savedlat = $("#lat").val();			
			map.setCenter(new google.maps.LatLng(savedlat, savedlng));
			var infowindow = new google.maps.InfoWindow();
			marker.setPosition(new google.maps.LatLng( savedlat, savedlng ));
			var latlng = {lat:parseFloat(savedlat), lng:parseFloat(savedlng)};
			geocoder = new google.maps.Geocoder();	
			geocoder.geocode( { 'location': latlng}, function(results, status) {
			  if (status == google.maps.GeocoderStatus.OK) {
				infowindow.setContent(results[1].formatted_address);
				$("#pac-input").val(results[1].formatted_address);					  
			  }
			});
			// var marker = new google.maps.Marker({
					  // position: {lat: parseFloat(savedlat) , lng: parseFloat(savedlng)},
					  // map: map,		  
					  // draggable:true,	
					  // icon: image,
					  // anchorPoint: new google.maps.Point(0, -29)
			// });
			// gmarkers.push(marker);
		}
		  
        autocomplete.addListener('place_changed', function() {	 
		 
		  // removeMarkers(); // remove previous markers to prevent duplicate markers
          infowindow.close();
          marker.setVisible(false);
          var place = autocomplete.getPlace();
          if (!place.geometry) {
            window.alert("Autocomplete's returned place contains no geometry");
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);  // Why 17? Because it looks good.
          }
		   // marker.setMap(null);
          // marker.setIcon(/** @type {google.maps.Icon} */({
            // url: place.icon,
            // size: new google.maps.Size(71, 71),
            // origin: new google.maps.Point(0, 0),
            // anchor: new google.maps.Point(17, 34),
            // scaledSize: new google.maps.Size(35, 35)
          // }));
          marker.setPosition(place.geometry.location);		
		  $(".mp_lng").val(marker.getPosition().lng());
		  $(".mp_lat").val(marker.getPosition().lat());
          marker.setVisible(true);
		 // gmarkers.push(marker);
          var address = '';
          if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(' ');
          }

          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
          infowindow.open(map, marker);
        });

        // Sets a listener on a radio button to change the filter type on Places
        // Autocomplete.
        function setupClickListener(id, types) {
          var radioButton = document.getElementById(id);
          radioButton.addEventListener('click', function() {
            autocomplete.setTypes(types);
          });
        }

		function removeMarkers(){
			for(i=0; i<gmarkers.length; i++){
				gmarkers[i].setMap(null);
			}
		}

        setupClickListener('changetype-all', []);
        setupClickListener('changetype-address', ['address']);
        setupClickListener('changetype-establishment', ['establishment']);
        setupClickListener('changetype-geocode', ['geocode']);  
	  
	  }
	  