@if(!isset($addressFieldName))
    @php $addressFieldName = 'address' @endphp
@endif
<div class="form-group">
    @if(!isset ($mapOnly))
        <label for="map_address">Location<span class="text-danger">*</span></label>
        <input type="text"
               placeholder="Search Location"
               class="form-control jqv-input jqv-required"
               name="{{$addressFieldName}}" id="map_address"
               @if( isset ($address)) value="{{$address}}" @endif>
        <div id="location_append">
            <ul></ul>
        </div>
    @endif
    <div id="map" style="height: 400px; width: 100%;"></div>
    <br>
    <input type="hidden" id="latitude" name="latitude">
    <input type="hidden" id="longitude" name="longitude">
    @if(!isset ($mapOnly))
        <button onclick="getLocation()" type="button" class="btn btn-primary">Get Current Location</button>
    @endif
</div>
@push('js')
    <script
        src="//maps.googleapis.com/maps/api/js?key={{ config('app.MAP_API_KEY') }}&v=weekly&libraries=drawing,places&callback=initAutocomplete&v=3.45.8"
        async defer></script>
    <script>
        var map, marker, geocoder;

        function initAutocomplete() {
            var latitude = @php echo ( !empty($lat) ? $lat : 25.204819) @endphp;
            var longitude = @php echo ( !empty($lng) ? $lng: 55.270931) @endphp;
            document.getElementById("latitude").value = latitude; //latitude
            document.getElementById("longitude").value = longitude; //longitude
            var myLatLng = {
                lat: latitude,
                lng: longitude,
            };

            map = new google.maps.Map(document.getElementById("map"), {
                center: myLatLng,
                zoom: 15,
                mapTypeControl: false,
                mapTypeId: "roadmap",
            });

            marker = new google.maps.Marker({
                draggable: true,
                position: myLatLng,
                map: map,
            });

            geocoder = new google.maps.Geocoder();

            google.maps.event.addListener(marker, "dragend", function () {
                geocoder.geocode(
                    {
                        latLng: marker.getPosition(),
                    },
                    function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                $("#map_address").val(results[0].formatted_address);
                            }
                        }
                    }
                );
            });

            google.maps.event.addListener(map, "click", function () {
                geocoder.geocode(
                    {
                        latLng: marker.getPosition(),
                    },
                    function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                $("#map_address").val(results[0].formatted_address);
                            }
                        }
                    }
                );
            });

            //Listen for any clicks on the map.
            google.maps.event.addListener(map, "click", function (event) {
                //Get the location that the restaurant clicked.
                var clickedLocation = event.latLng;
                //If the marker hasn't been added.
                if (marker === false) {
                    //Create the marker.
                    marker = new google.maps.Marker({
                        position: clickedLocation,
                        map: map,
                        draggable: true, //make it draggable
                    });
                } else {
                    //Marker has already been added, so just change its location.
                    marker.setPosition(clickedLocation);
                }
                //Get the marker's location.
                var currentLocation = marker.getPosition();
                //Add lat and lng values to a field that we can save.
                document.getElementById("latitude").value = currentLocation.lat(); //latitude
                document.getElementById("longitude").value = currentLocation.lng(); //longitude
            });

            //Listen for drag events!
            google.maps.event.addListener(marker, "dragend", function (event) {
                var currentLocation = marker.getPosition();
                //Add lat and lng values to a field that we can save.
                document.getElementById("latitude").value = currentLocation.lat(); //latitude
                document.getElementById("longitude").value = currentLocation.lng(); //longitude
            });

            // Create the search box and link it to the UI element.
            var input = document.getElementById("map_address");
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", function () {
                searchBox.setBounds(map.getBounds());
            });

            var markers = [];
            // Listen for the event fired when the restaurant selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", function () {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function (marker) {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function (place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };

                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map: map,
                            icon: icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }

                    document.getElementById("latitude").value = place.geometry.location.lat(); //latitude
                    document.getElementById("longitude").value = place.geometry.location.lng(); //longitude

                    geocoder.geocode(
                        {
                            latLng: place.geometry.location,
                        },
                        function (results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[0]) {
                                    $("#map_address").val(results[0].formatted_address);
                                }
                            }
                        }
                    );
                });

                map.fitBounds(bounds);
            });
            @if(empty($lat) || empty($lng) && !isset($mapOnly))
            getLocation();
            @endif
            initialize();
        }

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        function showPosition(position) {
            document.getElementById("latitude").value = position.coords.latitude;
            document.getElementById("longitude").value = position.coords.longitude;

            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            map.setCenter(pos);
            marker = new google.maps.Marker({
                position: pos,
                map: map
            });
            geocoder.geocode(
                {
                    latLng: pos,
                },
                function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $("#map_address").val(results[0].formatted_address);
                        }
                    }
                }
            );
        }
    </script>
@endpush
