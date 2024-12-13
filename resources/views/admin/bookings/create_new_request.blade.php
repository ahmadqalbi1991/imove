@extends("admin.template.layout")

@section('header')
    <style>
        #po_map, #do_map {
            height: 500px;
            width: 100%;
        }

        .remove_btn_imgs {
            position: absolute;
            top: 52px;
            right: 15px;
            padding: 0 !important;
        }

        .remove_btn_imgs .btn_remove_img {
            width: 40px !important;
            height: 40px !important;
            padding: 0 !important;
            border-radius: 5px !important;
            background: red;
            color: #fff;
        }

        .delete-img {
            width: 40px !important;
            height: 40px !important;
            padding: 0 !important;
            border-radius: 5px !important;
            background: red;
            color: #fff;
            position: absolute;
            top: 0;
            right: 15px;
            padding: 0 !important;
            opacity: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@stop


@section('content')
    <link href="{{ asset('') }}admin-assets/jquery.timepicker.min.css" rel="stylesheet" type="text/css"/>
    <div class="card mb-5">

        <div class="card-body">
            <form method="post" id="admin-form1" action="{{ route('admin.bookings.create_new_request_store') }}"
                  enctype="multipart/form-data"
                  data-parsley-validate="true">
                <input type="hidden" name="id" id="cid" value="{{ $booking->id??0 }}">
                @csrf()
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Customer<b class="text-danger">*</b></label>
                            <h4>{{ $booking->customer->name }}</h4>
                        </div>
                    </div>


                </div>

                <div class="row mt-2">

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="left"><span>Pick Up</span></h4>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Pick Up Location<b class="text-danger">*</b></label>
                                    <input type="text" readonly class="form-control-plaintext" data-jqv-required="true"
                                           placeholder="Search Location" id="locationTextField" name="pu_location"
                                           required  />
                                    <input type="hidden" id="po_latitude" name="po_latitude"
                                           value="{{ $booking->pick_up_lat ?? '' }}">
                                    <input type="hidden" id="po_longitude" name="po_longitude"
                                           value="{{ $booking->pick_up_lng ?? '' }}">
                                    <div id="po_map"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="left"><span>Drop Off</span></h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Drop Off Location<b class="text-danger">*</b></label>
                                    <input type="text" readonly class="form-control-plaintext" data-jqv-required="true"
                                           id="locationTextField2" name="do_location" required />
                                    <input type="hidden" id="do_latitude" name="do_latitude"
                                           value="{{ $booking->drop_off_lat ?? '' }}">
                                    <input type="hidden" id="do_longitude" name="do_longitude"
                                           value="{{ $booking->drop_off_lng ?? '' }}">
                                    <div id="do_map"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


                @if(!empty($booking->accepted_order))
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Driver</label>
                        <p>{{!empty($booking->accepted_order) ? $booking->accepted_order->vendor->name :''}}</p>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Driver Contact Number </label>
                        <div class="row">
                            <div class="col-4">
                                <div class="flag-dropdown">
                                    <select class="form-control" name="dial_code" id="dial_code"
                                            data-jqv-required="true">
                                        <option value="">Dial Code</option>
                                        @foreach (get_countries_list() as $flag_class => $data)
                                            <option value="{{ $data['dial_code'] }}"
                                                    @if($booking->dial_code === $data['dial_code']) selected @endif
                                                    data-flag-class="{{ $flag_class }}"
                                                    data-flag-value="{{ $data['dial_code'] }} ">
                                                {{ $data['dial_code'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-8">
                                <input type="text" class="form-control-plaintext" name="mobile_number" value="{{ $booking->mobile_number }}" id="mobile_number">
                            </div>
                        </div>
                    </div>
                </div>

{{--                <div class="col-md-12 form-group imgs-wrap">--}}
{{--                    <div class="top-bar">--}}
{{--                        <label class="badge bg-dark text-white d-flex justify-content-between align-items-center">Images--}}
{{--                            <button class="btn btn-button-7 pull-right" type="button" data-role="add-imgs"--}}
{{--                                    style="width: 40px;   height: 40px;   border-radius: 0;"><i--}}
{{--                                        class="flaticon-plus-1"></i></button>--}}
{{--                        </label>--}}
{{--                    </div>--}}
{{--                    <input type="hidden" id="imgs_counter" value="0">--}}
{{--                    @if(!empty($booking->id))--}}
{{--                        <div class="row">--}}
{{--                            @foreach ($booking->images as $img)--}}
{{--                                <div class="col-md-3 img-wrap position=relative">--}}
{{--                                    <span class="close delete-img" title="Delete" data-role="unlink"--}}
{{--                                          data-message="Do you want to remove this image?"--}}
{{--                                          href="{{ url('admin/bookings/delete_image/' . $img->id) }}">&times;</span>--}}
{{--                                    <img style="width: 100%; height: 150px; border-radius: 5px; object-fit: cover;"--}}
{{--                                         class="img-responsive w-100" src="{{ asset($img->images) }}">--}}
{{--                                </div>--}}
{{--                            @endforeach--}}

{{--                        </div>--}}
{{--                    @endif--}}
{{--                    <div id="imgs-holder" class="row mt-3"></div>--}}
{{--                </div>--}}


                <div class="row">

                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop


@section('script')
    <script>

        $(".flatpicker-input").flatpickr({
            dateFormat: "Y-m-d",
            minDate: "today"
        });


        App.initFormView();
        $('body').off('submit', '#admin-form');
        $('body').on('submit', '#admin-form', function (e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);
            $(".invalid-feedback").remove();

            App.loading(true);
            $form.find('button[type="submit"]')
                .text('Saving')
                .attr('disabled', true);

            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: $form.attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                dataType: 'json',
                timeout: 600000,
                success: function (res) {
                    App.loading(false);

                    if (res['status'] == 0) {
                        if (typeof res['errors'] !== 'undefined') {
                            var error_def = $.Deferred();
                            var error_index = 0;
                            jQuery.each(res['errors'], function (e_field, e_message) {
                                if (e_message != '') {
                                    $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                    $('<div class="invalid-feedback">' + e_message + '</div>')
                                        .insertAfter($('[name="' + e_field + '"]').eq(0));
                                    if (error_index == 0) {
                                        error_def.resolve();
                                    }
                                    error_index++;
                                }
                            });
                            error_def.done(function () {
                                var error = $form.find('.is-invalid').eq(0);
                                $('html, body').animate({
                                    scrollTop: (error.offset().top - 100),
                                }, 500);
                            });
                        } else {
                            var m = res['message'];
                            App.alert(m, 'Oops!');
                        }
                    } else {
                        App.alert(res['message']);
                        setTimeout(function () {
                            window.location.href = App.siteUrl('/admin/games');
                        }, 1500);
                    }

                    $form.find('button[type="submit"]')
                        .text('Save')
                        .attr('disabled', false);
                },
                error: function (e) {
                    App.loading(false);
                    $form.find('button[type="submit"]')
                        .text('Save')
                        .attr('disabled', false);
                    App.alert(e.responseText, 'Oops!');
                }
            });
        });
        $('body').on("click", '[data-role="remove-imgs"]', function () {
            $(this).parent().parent().remove();
        });
        let img_counter = $("#imgs_counter").val();
        $('[data-role="add-imgs"]').click(function () {
            img_counter++;
            var html = '<div class="form-group col-lg-4">\
                          <div class="remove_btn_imgs">\
                            <button type="button" class="btn btn-danger btn_remove_img" data-role="remove-imgs"><i class="flaticon-delete"></i></button>\
                          </div>\
                            <label>Banner Image<b class="text-danger">*</b></label><br>\
                            <img id="image-preview-bnr_' + img_counter + '" style="width:100%; height:160px; object-fit: cover" class="img-responsive" >\
                            <br><br>\
                            <input type="file" name="banners[]" class="form-control" data-role="file-image" data-preview="image-preview-bnr_' + img_counter + '" data-parsley-trigger="change" data-parsley-fileextension="jpg,png,gif,jpeg" data-parsley-fileextension-message="Only files with type jpg,png,gif,jpeg are supported" data-parsley-max-file-size="5120" data-parsley-max-file-size-message="Max file size should be 5MB"  required data-parsley-required-message="Select Image">\
                                <span class="text-info">Upload image with dimension 1024x547</span>\
                        </div>\
                        ';
            $('#imgs-holder').append(html);


        });

        $('#select2').select2();

        $('#images').on('change', function () {
            var ObjImage = document.getElementById("images");
            var FileCount = ObjImage.files.length;

            $('.booking-img').attr("src", "");

            for (ic = 0; ic < FileCount; ic++) {
                var file = ObjImage.files[ic];
                var tmppath = URL.createObjectURL(file);

                $('#image' + ic).attr("src", tmppath);
            }
        })


        function CostCalculation() {
            var category_id = $('#category_id').val();
            var size_id = $('#size_id').val();
            var delivery_type = $('#delivery_type').val();

            if (category_id > 0 && size_id > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.bookings.create_new_request_get_costing') }}",
                    data: {
                        category_id: category_id,
                        size_id: size_id,
                        "_token": "{{ csrf_token() }}",
                        "delivery_type": delivery_type
                    },
                    success: function (cost) {
                        var taxtper = '{{$settings->tax_percentage}}';
                        var service_price = '{{$settings->service_charge}}';
                        var total_tax = 0;
                        if (taxtper.length != 0) {
                            var total_tax = (cost) * taxtper / 100;
                        }


                        $("#cost").text(cost);
                        $("#cost_input").val(cost);
                        $("#service_price").text(service_price);
                        $("#service_price_input").val(service_price);
                        $("#tax").text((parseFloat(total_tax)).toFixed(2));
                        $("#tax_input").val((parseFloat(total_tax)).toFixed(2));
                        $("#grand_total").text(((parseFloat(cost) + parseFloat(total_tax) + parseFloat(service_price))).toFixed(2));
                        $("#grand_total_input").val(((parseFloat(cost) + parseFloat(total_tax) + parseFloat(service_price))).toFixed(2));
                    }
                })
            } else {
                $("#cost").text("0.00");
                $("#service_price").text("0.00");
                $("#tax").text("0.00");
                $("#grand_total").text("0.00");
            }
        }

    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>
    <script>
        function initialize() {
            // var input = document.getElementById('locationTextField');
            // var input2 = document.getElementById('locationTextField2');

            // var autocomplete = new google.maps.places.Autocomplete(input, {
            //     componentRestrictions: {country: 'ae'}  // Restrict to United Arab Emirates (UAE)
            // });
            // var autocomplete2 = new google.maps.places.Autocomplete(input2, {
            //     componentRestrictions: {country: 'ae'}
            // });
            //
            // autocomplete.addListener("place_changed", function () {
            //     var place = autocomplete.getPlace();
            //
            //     // Check for valid place selection
            //     if (!place || !place.geometry) {
            //         console.log("No place details available for selected location.");
            //         return;
            //     }
            //
            //     // Extract latitude and longitude
            //     var latitude = place.geometry.location.lat();
            //     var longitude = place.geometry.location.lng();
            //
            //     var hiddenLatInput = document.getElementById('po_latitude');
            //     var hiddenLngInput = document.getElementById('po_longitude');
            //
            //     if (hiddenLatInput && hiddenLngInput) {
            //         hiddenLatInput.value = latitude;
            //         hiddenLngInput.value = longitude;
            //
            //         console.log("Latitude:", latitude, "Longitude:", longitude);
            //     } else {
            //         console.error("Hidden input fields not found. Please ensure they have IDs 'hiddenLat' and 'hiddenLng'.");
            //     }
            // });
            //
            // autocomplete2.addListener("place_changed", function () {
            //     var place = autocomplete.getPlace();
            //
            //     // Check for valid place selection
            //     if (!place || !place.geometry) {
            //         console.log("No place details available for selected location.");
            //         return;
            //     }
            //
            //
            //     // Extract latitude and longitude
            //     var latitude = place.geometry.location.lat();
            //     var longitude = place.geometry.location.lng();
            //
            //     var hiddenLatInput = document.getElementById('do_latitude');
            //     var hiddenLngInput = document.getElementById('do_longitude');
            //
            //     if (hiddenLatInput && hiddenLngInput) {
            //         hiddenLatInput.value = latitude;
            //         hiddenLngInput.value = longitude;
            //
            //         console.log("Latitude:", latitude, "Longitude:", longitude);
            //     } else {
            //         console.error("Hidden input fields not found. Please ensure they have IDs 'hiddenLat' and 'hiddenLng'.");
            //     }
            //
            // });

        }

        // google.maps.event.addDomListener(window, 'load', initialize);

</script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap" async defer></script>
    <script>
        var po_map; // Declare the map variable
        var do_map; // Declare the map variable
        var po_marker; // Declare the marker variable
        var do_marker; // Declare the marker variable

        function initMap() {
            var po_initialLocation = {
                lat: parseFloat("{{ $booking->pick_up_lat ?? 0 }}"),
                lng: parseFloat("{{ $booking->pick_up_lng ?? 0 }}")
            };

            var do_initialLocation = {
                lat: parseFloat("{{ $booking->drop_off_lat ?? 0 }}"),
                lng: parseFloat("{{ $booking->drop_off_lng ?? 0 }}")
            };

            po_map = new google.maps.Map(document.getElementById('po_map'), {
                zoom: 16,
                center: po_initialLocation
            });

            do_map = new google.maps.Map(document.getElementById('do_map'), {
                zoom: 16,
                center: do_initialLocation
            });

            // Place a marker at the initial location
            placeMarker(po_initialLocation,'po_map');
            placeMarker(do_initialLocation,'do_map');

            // Add a click event listener to the map
            po_map.addListener('click', function(event) {
                placeMarker(event.latLng,'po_map'); // Place the marker at the clicked location
            });

            // Add a click event listener to the map
            do_map.addListener('click', function(event) {
                placeMarker(event.latLng,'do_map'); // Place the marker at the clicked location
            });
        }

        function placeMarker(location, map_type) {
            let lat = '';
            let lng = '';
            if (map_type == 'po_map') {
                if (po_marker) {
                    lat = location.lat();
                    lng = location.lng();
                    po_marker.setPosition(location);
                } else {
                    lat = location.lat;
                    lng = location.lng;
                    po_marker = new google.maps.Marker({
                        position: location,
                        map: po_map
                    });
                }
            } else {
                if (do_marker) {
                    lat = location.lat();
                    lng = location.lng();
                    do_marker.setPosition(location);
                } else {
                    lat = location.lat;
                    lng = location.lng;
                    do_marker = new google.maps.Marker({
                        position: location,
                        map: do_map
                    });
                }
            }

            getAddressFromLatLong(lat, lng, map_type)
        }

        function getAddressFromLatLong(lat, lng, map_type) {
            var apiKey = "{{ env('GOOGLE_MAPS_API_KEY') }}";
            var url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${apiKey}`;

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    console.log(response)
                    if (response.status === 'OK') {
                        var address = response.results[0].formatted_address;
                        if (map_type == 'po_map') {
                            $('#locationTextField').val(address);
                            $('#po_latitude').val(lat);
                            $('#po_longitude').val(lng);
                        } else {
                            $('#locationTextField2').val(address);
                            $('#do_latitude').val(lat);
                            $('#do_longitude').val(lng);
                        }
                    } else {
                        $('#locationTextField').val('No address found.');
                    }
                },
                error: function(xhr, status, error) {
                    $('#locationTextField').val('Error fetching address: ' + error);
                }
            });
        }

    </script>

@stop
