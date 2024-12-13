@php
    $mapHeight = $mapHeight ?? '400px';
    $includeCdn = $includeCdn ?? true;
    $id = $id ?? 0;
@endphp
<div class="map-warper overflow-hidden rounded">
    <input id="pac-input" name="poly_coordinates" class="form-control" style="min-width: 200px; width: 50%;"
           title="Search your location here"
           type="text" placeholder="Search location here..."/>
    <div id="map-canvas" style="height: {{$mapHeight}}" class="m-0 p-0"></div>
</div>

<div class="form-group mb-3 d-none">
    <label class="input-label"
           for="exampleFormControlInput1">Coordinates (Draw zone on map)<span class="input-label-secondary">Draw zone on map</span></label>
    <textarea type="text" rows="8" name="coordinates" id="coordinates" class="form-control" readonly></textarea>
</div>
<div class="btn--container mt-3 justify-content-end">
    <button id="reset_btn" onclick="resetZone()" type="button" class="primary-btn-outline hover p-2 px-4 rounded-pill">Reset</button>
</div>
@push('component-js')
    @if($includeCdn)
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ config('app.MAP_API_KEY') }}&libraries=drawing,places&v=3.45.8"></script>
    @endif
    <script>
        var poly_map; // Global declaration of the map
        var drawingManager;
        var lastpolygon = null;
        var polygons = [];

        function resetMap(controlDiv) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.marginTop = "8px";
            controlUI.style.marginBottom = "22px";
            controlUI.style.textAlign = "center";
            controlUI.title = "Reset map";
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.color = "rgb(25,25,25)";
            controlText.style.fontFamily = "Roboto,Arial,sans-serif";
            controlText.style.fontSize = "10px";
            controlText.style.lineHeight = "16px";
            controlText.style.paddingLeft = "2px";
            controlText.style.paddingRight = "2px";
            controlText.innerHTML = "X";
            controlUI.appendChild(controlText);
            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
                lastpolygon.setMap(null);
                $('#coordinates').val('');

            });
        }

        function initialize() {
            @php($default_location=0)
            var myLatlng = {lat: 23.757989, lng: 90.360587};


            var myOptions = {
                zoom: 13,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            poly_map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true
                }
            });
            drawingManager.setMap(poly_map);


            //get current location block
            // infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        poly_map.setCenter(pos);
                    });
            }

            google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {
                if (lastpolygon) {
                    lastpolygon.setMap(null);
                }
                $('#coordinates').val(event.overlay.getPath().getArray());
                lastpolygon = event.overlay;
                auto_grow();
            });
            auto_grow();

            function auto_grow() {
                let element = document.getElementById("coordinates");
                element.style.height = "5px";
                element.style.height = (element.scrollHeight) + "px";
            }

            const resetDiv = document.createElement("div");
            resetMap(resetDiv, lastpolygon);
            poly_map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            poly_map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            poly_map.addListener("bounds_changed", () => {
                searchBox.setBounds(poly_map.getBounds());
            });
            let markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map: poly_map,
                            icon,
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
                });
                poly_map.fitBounds(bounds);
            });
            set_all_zones();
        }

        // google.maps.event.addDomListener(window, 'load', initialize);


        function set_all_zones() {
            $.get({
                url: '{{route('malls.getZone', $id)}}',
                dataType: 'json',
                success: function (data) {

                    console.log(data);
                    polygons.push(new google.maps.Polygon({
                        paths: data,
                        strokeColor: "#FF0000",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF0000",
                        fillOpacity: 0.1,
                    }));
                    console.log(polygons);
                    poly_map.setCenter(polygons[0].getPath().getAt(0));
                    polygons[0].setMap(poly_map);
                }
            });
        }

        $(document).on('ready', function () {
            set_all_zones();
        });
        $('#reset_btn').click(function () {
            lastpolygon.setMap(null);
            $('#coordinates').val(null);
        });

        function resetZone() {
            lastpolygon.setMap(null);
            $('#coordinates').val(null);
        }


    </script>
@endpush
