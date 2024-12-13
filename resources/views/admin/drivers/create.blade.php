@extends("admin.template.layout")
@section('header')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
    <style>
        .form-control-plaintext {
            padding-left: 7px;
            border: 1px solid #990253;
            border-radius: 10px;
            color: #212529;
            text-align: left;
            margin-bottom: 15px;
        }

        .form-label {
            margin-bottom: 5px;
        }

        .btn-primary {
            display: none;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper span {
            position: absolute;
            top: 0;
            height: 45px;
            right: 0;
            border: 0;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            line-height: 36px;
        }

        .select2-selection__rendered {
            display: flex;
            align-items: center;
        }

        .flag-icon {
            margin-right: 8px;
        }

        elem {
            margin-left: 20px;
        }

        .select2-results__option elem {
            position: absolute;
        }

    </style>
@endsection


@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Ajax Sourced Server-side -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
            </div>
            <div class="card-body py-0">
                <div class="row">
                    {{--
                    <div class="col-md-2 col-sm-4 primary-btn    py-4 rounded">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="{{get_uploaded_image_url($user->user_image, 'user_image_upload_dir')}}"
                                         class="img-fluid rounded-circle"
                                         alt="Responsive image">
                                </div>
                            </div>
                        </div>
                    </div>
                    --}}

                    <div class="col-md-12 col-sm-12 py-4">
                        <form id="admin_form" action="{{ route('drivers.submit') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6" id="driver-type-div" style="display:none;">
                                    <label class="form-label">Driver Type</label>
                                    <select class="form-control-plaintext" name="driver_type" id="driver_type"
                                            data-jqv-required="true">
                                        <option>Select Driver Type</option>
                                        @foreach($get_driver_types as $driver_type)
                                            <option value="{{$driver_type->id}}">{{$driver_type->type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" id="company-div" style="display: none;">
                                    <label class="form-label">Comapany</label>
                                    <select class="form-control-plaintext" name="company" style="">
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{$company->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" style="display:none;">
                                    <label class="form-label">Truck Type</label>
                                    <select class="form-control-plaintext" name="truck_type" style="">
                                        @foreach($trucks as $truck)
                                            <option value="{{$truck->id}}">{{$truck->truck_type." -- ".$truck->dimensions}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input id="first_name" type="text" class="form-control-plaintext" name="first_name"
                                           value="" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input id="last_name" type="text" class="form-control-plaintext" name="last_name"
                                           value="" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control-plaintext" name="email" value=""
                                           data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control-plaintext" name="password"
                                               id="password" value="" data-jqv-required="true">
                                        <span class="input-group-text password-toggle" id="togglePassword"
                                              onclick="password_show_hide(this)">
                                            <svg class="svg-inline--fa fa-eye" aria-hidden="true" focusable="false"
                                                 data-prefix="far" data-icon="eye" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 576 512"><path fill="currentColor"
                                                                             d="M572.52 241.4c-54.19-106.43-162.66-177.41-284.5-177.41S57.66 134.97 3.48 241.4a48.24 48.24 0 0 0 0 29.2C57.66 377.03 166.14 448 288.02 448s230.84-70.97 284.99-177.4a48.24 48.24 0 0 0 0-29.2zM288 400c-74.99 0-143.66-38.2-192.3-104 48.64-65.79 117.32-104 192.3-104s143.66 38.2 192.3 104c-48.64 65.79-117.32 104-192.3 104zm0-176a72 72 0 1 0 72 72 72 72 0 0 0-72-72zm0 112a40 40 0 1 1 40-40 40 40 0 0 1-40 40z"></path></svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" class="form-control-plaintext" name="confirm_password"
                                               value="" id="confirm_password" data-jqv-required="true">
                                        <span class="input-group-text password-toggle" id="toggleconifrmPassword"
                                              onclick="password_show_hide(this)">
                                        <svg class="svg-inline--fa fa-eye" aria-hidden="true" focusable="false"
                                             data-prefix="far" data-icon="eye" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 576 512"><path fill="currentColor"
                                                                         d="M572.52 241.4c-54.19-106.43-162.66-177.41-284.5-177.41S57.66 134.97 3.48 241.4a48.24 48.24 0 0 0 0 29.2C57.66 377.03 166.14 448 288.02 448s230.84-70.97 284.99-177.4a48.24 48.24 0 0 0 0-29.2zM288 400c-74.99 0-143.66-38.2-192.3-104 48.64-65.79 117.32-104 192.3-104s143.66 38.2 192.3 104c-48.64 65.79-117.32 104-192.3 104zm0-176a72 72 0 1 0 72 72 72 72 0 0 0-72-72zm0 112a40 40 0 1 1 40-40 40 40 0 0 1-40 40z"></path></svg>
                                    </span>
                                    </div>
                                    <span id='password-message'></span>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Phone</label>
                                    <div class="flag-dropdown">
                                        <select class="form-control" name="dial_code" id="dial_code"
                                                data-jqv-required="true">
                                            <option value="">Dial Code</option>
                                            @foreach (get_countries_list() as $flag_class => $data)
                                                <option value="{{ $data['dial_code'] }}"
                                                        data-flag-class="{{ $flag_class }}"
                                                        data-flag-value="{{ $data['dial_code'] }} ">
                                                    {{ $data['dial_code'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Number</label>
                                    <input type="text" class="form-control-plaintext" name="phone" value="" id="phone"
                                           data-jqv-required="true">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Account Status</label>
                                    <select class="form-control-plaintext" name="status">
                                        <option value="inactive">Inactive</option>
                                        <option value="active">Active</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Vehicle Types</label>
                                    <select id="VehicleTypes" multiple="true" class="form-control-plaintext"
                                            name="vehicle_type[]">
                                        <option value="Car">Car</option>
                                        <option value="Boat">Boat</option>
                                        <option value="MotorCycle">MotorCycle</option>
                                    </select>
                                </div>

                            </div>
                            <!-- Driving Documents -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr/>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Upload Mulkiya</label>
                                    <input type="file" name="mulkiya" class="form-control-plaintext">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mulkiya Number</label>
                                    <input id="mulkiya_number" type="text" class="form-control-plaintext"
                                           name="mulkiya_number" value="">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Upload Emirate ID or Passport</label>
                                    <input type="file" name="emirates_id_or_passport" class="form-control-plaintext">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Drving License Issued By</label>
                                    <select class="form-control-plaintext" name="driving_license_issued_by"
                                            data-jqv-required="true">
                                        @foreach(get_countries() as $key => $country)
                                            <option value="{{$country}}" {{$country=="United Arab Emirates"?'Selected':''}} >{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Upload Driving License</label>
                                    <input type="file" name="driving_license" class="form-control-plaintext">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Driving License Number</label>
                                    <input type="text" id="driver_license" class="form-control-plaintext"
                                           name="driving_license_number" value="" data-jqv-required="true">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Driving License Expiry</label>
                                    <input type="date" class="form-control-plaintext" name="driving_license_expiry"
                                           value="" data-jqv-required="true">
                                </div>


                            </div>
                            <!-- Address Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <hr/>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Country</label>
                                    <select class="form-control-plaintext" name="country" data-jqv-required="true">
                                        @foreach(get_countries() as $key => $country)
                                            <option value="{{$country}}" {{$country=="United Arab Emirates"?'Selected':''}}>{{$country}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" placeholder="">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Zip Code</label>
                                    <input id="zip_code" type="number" name="zip_code" class="form-control"
                                           placeholder="">
                                </div>

                            </div>

                            <!-- <div class="row">
                                <div class="col-sm-12">
                                    <label class="form-label">Location</label>
                                {{--
                                @if($user->lattitude && $user->longitude)
                                <x-elements.map-location
                                    addressFieldName="address"
                                    :lat="$user->lattitude"
                                    :lng="$user->longitude"
                                    :address="$user->address"
                                    :mapOnly="true"
                                />
                                    @else
                                    <div class="form-control-plaintext text-center py-4">
                                        <img src="{{asset('images/location-marker.png')}}" style="height: 200px" alt="" class="img-fluid">
                                        <h6 class="h6">No location added yet.</h6>
                                    </div>
                                    @endif
                                    --}} -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3 mb-4">
                        <input class="btn btn-info float-right"
                               style="background: #000 !important; color: #fff !important; border-color: #000 !important; height: 46px !important;"
                               type="submit" name="submit" value="Save Information">
                    </div>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>
    </div>
@stop

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>


        const inputTel = document.querySelector("#phone");
        // inputTel.addEventListener("input", function (e) {
        //
        //     let value = e.target.value.replace(/[^0-9]/g, '');
        //
        //     // Prevent starting with 0
        //     if (value.startsWith('0')) {
        //         value = value.substring(1);
        //     }
        //
        //     // Update the input field with the cleaned value
        //     e.target.value = value;
        // });

        $(document).ready(function () {

            $('#VehicleTypes').select2();

        })

        $(document).ready(function () {

                // Mulkiya Number - Alphanumeric only
                $('#mulkiya_number').on('input', function () {
                    // Replace any character that is not a letter or digit
                    $(this).val($(this).val().replace(/[^a-zA-Z0-9]/g, ''));
                });

                // Driver License Number - Numbers only
                $('#driver_license').on('input', function () {
                    // Replace any character that is not a number
                    $(this).val($(this).val().replace(/[^0-9]/g, ''));
                });

            $('#first_name').on('input', function () {
                var value = $(this).val();

                // Restrict special characters
                // This regex restricts: & = _ ' - + , < > and multiple consecutive dots
                var invalidChars = /[&=_'<>+\-,]|(\.\.)/g;

                // Replace invalid characters
                value = value.replace(invalidChars, '');

                // Trim if the length is more than 100 characters
                if (value.length > 100) {
                    value = value.substring(0, 100);
                }

                // Word limit restriction (optional, e.g., 10 words max)
                var words = value.split(/\s+/);
                if (words.length > 10) {
                    value = words.slice(0, 10).join(' ');
                }

                // Set the modified value back to the field
                $(this).val(value);
            });


            $('#zip_code').on('input', function () {
                var value = $(this).val();


                // Trim if the length is more than 100 characters
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                // Word limit restriction (optional, e.g., 10 words max)
                var words = value.split(/\s+/);
                if (words.length > 10) {
                    value = words.slice(0, 10).join(' ');
                }

                // Set the modified value back to the field
                $(this).val(value);
            });

            $('#last_name').on('input', function () {
                var value = $(this).val();

                // Restrict special characters
                // This regex restricts: & = _ ' - + , < > and multiple consecutive dots
                var invalidChars = /[&=_'<>+\-,]|(\.\.)/g;

                // Replace invalid characters
                value = value.replace(invalidChars, '');

                // Trim if the length is more than 100 characters
                if (value.length > 100) {
                    value = value.substring(0, 100);
                }

                // Word limit restriction (optional, e.g., 10 words max)
                var words = value.split(/\s+/);
                if (words.length > 10) {
                    value = words.slice(0, 10).join(' ');
                }

                // Set the modified value back to the field
                $(this).val(value);
            });
            document.getElementById('phone').addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, ''); // Replace non-digit characters with an empty string
            });

            function formatState(state) {
                if (!state.id) {
                    return state.text; // return the default text if no id is found
                }
                var flagClass = $(state.element).data('flag-class');
                var dialCode = state.text; // +value of dial code
                var countryName = $(state.element).text(); // full text including dial code and country name

                return $('<span class="flag-icon ' + flagClass + '"><elem>' + dialCode + '</elem></span> + ' + dialCode + ' - ' + countryName);
            }

            $('#dial_code').select2({
                templateResult: formatState,
                templateSelection: formatState,
                escapeMarkup: function (markup) {
                    return markup;
                } // let our custom markup through
            });

            $('#VehicleTypes').select2();

            $('#togglePassword').on('click', function () {
                let passwordField = $('#password');
                let passwordFieldType = passwordField.attr('type');

                // Toggle the type attribute
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<svg class="svg-inline--fa fa-eye-slash" aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye-slash" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M320 400c-24.2 0-47.1-5.2-68.5-14.5L111.4 528c-6.6 7.3-16.6 8.1-24.6 1.8L64 512c-7.2-6.6-7.9-16.6-1.8-24.6L276.6 288C279.3 290.6 282.1 292 285.1 293.4c-5.5 10.8-9.6 22.5-9.6 35.1 0 39.8 32.2 72 72 72 12.6 0 24.4-3.1 35.1-8.6 1.3 3.3 2.7 6.4 4.3 9.4L444.6 512c7.3 6.6 17.2 5.7 24.6-1.8l22.8-21.9c7.3-6.6 6.6-16.6-1.8-24.6L388.5 385.5C404.8 374 419 360.4 432 344c-28.7 21.3-65.8 34-104 34zm295.3-30.7L517.5 306.3C566.9 249.8 576 183.2 576 176c0-14.2-11.8-24.9-26.7-24.9-1.2 0-2.5.1-3.7.2L496 151.1c-8.7 8.5-22.7 9.1-31.6 1.2-8.7-8.5-9.1-22.7-1.2-31.6L530.7 64c7.1-6.9 1.2-18.3-7.8-18.3H114.5C96.7 45.7 89.7 63.5 103 72l67.9 52.7C160.6 134.5 160 148.6 160 160c0 7.6 7 80.2 53.5 136.7-35.5 42.5-67.3 79-84.9 105.2-6.1 9.2-5.4 20.6 1.8 24.6l22.8 21.9c6.6 6.6 16.6 7.9 24.6 1.8L320 285.7c19.8-5.7 37-18.2 49.4-35.2l102.3 82.9c8.4 7.2 19.8 6.9 27.6-.2l24.9-21.9c8.3-6.5 7.1-16.7.2-24.6z"></path></svg>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<svg class="svg-inline--fa fa-eye" aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M572.52 241.4c-54.19-106.43-162.66-177.41-284.5-177.41S57.66 134.97 3.48 241.4a48.24 48.24 0 0 0 0 29.2C57.66 377.03 166.14 448 288.02 448s230.84-70.97 284.99-177.4a48.24 48.24 0 0 0 0-29.2zM288 400c-74.99 0-143.66-38.2-192.3-104 48.64-65.79 117.32-104 192.3-104s143.66 38.2 192.3 104c-48.64 65.79-117.32 104-192.3 104zm0-176a72 72 0 1 0 72 72 72 72 0 0 0-72-72zm0 112a40 40 0 1 1 40-40 40 40 0 0 1-40 40z"></path></svg>');
                }
            });
            $('#toggleconifrmPassword').on('click', function () {
                let passwordField = $('#confirm_password');
                let passwordFieldType = passwordField.attr('type');

                // Toggle the type attribute
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).html('<svg class="svg-inline--fa fa-eye-slash" aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye-slash" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path fill="currentColor" d="M320 400c-24.2 0-47.1-5.2-68.5-14.5L111.4 528c-6.6 7.3-16.6 8.1-24.6 1.8L64 512c-7.2-6.6-7.9-16.6-1.8-24.6L276.6 288C279.3 290.6 282.1 292 285.1 293.4c-5.5 10.8-9.6 22.5-9.6 35.1 0 39.8 32.2 72 72 72 12.6 0 24.4-3.1 35.1-8.6 1.3 3.3 2.7 6.4 4.3 9.4L444.6 512c7.3 6.6 17.2 5.7 24.6-1.8l22.8-21.9c7.3-6.6 6.6-16.6-1.8-24.6L388.5 385.5C404.8 374 419 360.4 432 344c-28.7 21.3-65.8 34-104 34zm295.3-30.7L517.5 306.3C566.9 249.8 576 183.2 576 176c0-14.2-11.8-24.9-26.7-24.9-1.2 0-2.5.1-3.7.2L496 151.1c-8.7 8.5-22.7 9.1-31.6 1.2-8.7-8.5-9.1-22.7-1.2-31.6L530.7 64c7.1-6.9 1.2-18.3-7.8-18.3H114.5C96.7 45.7 89.7 63.5 103 72l67.9 52.7C160.6 134.5 160 148.6 160 160c0 7.6 7 80.2 53.5 136.7-35.5 42.5-67.3 79-84.9 105.2-6.1 9.2-5.4 20.6 1.8 24.6l22.8 21.9c6.6 6.6 16.6 7.9 24.6 1.8L320 285.7c19.8-5.7 37-18.2 49.4-35.2l102.3 82.9c8.4 7.2 19.8 6.9 27.6-.2l24.9-21.9c8.3-6.5 7.1-16.7.2-24.6z"></path></svg>');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).html('<svg class="svg-inline--fa fa-eye" aria-hidden="true" focusable="false" data-prefix="far" data-icon="eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M572.52 241.4c-54.19-106.43-162.66-177.41-284.5-177.41S57.66 134.97 3.48 241.4a48.24 48.24 0 0 0 0 29.2C57.66 377.03 166.14 448 288.02 448s230.84-70.97 284.99-177.4a48.24 48.24 0 0 0 0-29.2zM288 400c-74.99 0-143.66-38.2-192.3-104 48.64-65.79 117.32-104 192.3-104s143.66 38.2 192.3 104c-48.64 65.79-117.32 104-192.3 104zm0-176a72 72 0 1 0 72 72 72 72 0 0 0-72-72zm0 112a40 40 0 1 1 40-40 40 40 0 0 1-40 40z"></path></svg>');
                }
            });

        });

        jQuery(document).ready(function () {

            $(document).on('change', '#driver_type', function () {
                if ($(this).val() == 1) {
                    $('#company-div').show();
                } else {
                    $('#company-div').hide();
                }
            })
            App.initTreeView();

            $('.all-select').click(function () {
                $(this).siblings('.crud-items').prop('checked', this.checked);
            });
            $('.crud-items').click(function () {
                $(this).siblings('.all-select').prop('checked', false);
            });
            $('.all-p').click(function () {
                $(this).siblings('.reader').prop('checked', true);
            });
            App.initFormView();
            let form_in_progress = 0;

            // Add the custom validation method
            $.validator.addMethod("lettersOnly", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
            }, "Only letters and spaces are allowed.");

            $.validator.addMethod("numbersOnly", function(value, element) {
                return this.optional(element) || /^[0-9]+$/.test(value);
            }, "Only numbers are allowed.");

// Initialize form validation outside of the submit handler
            $('#admin_form').validate({
                rules: {
                    first_name: { required: true, lettersOnly: true },
                    last_name: { required: true, lettersOnly: true },
                    name: { required: true },
                    email: { required: true, email: true },
                    password: { required: true },
                    confirm_password: { required: true, equalTo: "#password" },
                    dial_code: { required: true },
                    phone: { required: true, maxlength: 15, numbersOnly: true },
                    mulkiya: { required: true },
                    mulkiya_number: { required: true },
                    emirates_id_or_passport: { required: true },
                    driving_license_issued_by: { required: true },
                    driving_license: { required: true },
                    driving_license_number: { required: true },
                    driving_license_expiry: { required: true },
                    vehicle_plate_number: { required: true },
                    vehicle_plate_place: { required: true },
                    country: { required: true },
                    zip_code: { required: true },
                    city: { required: true, lettersOnly: true },
                    driver_type: { required: true }
                },
                errorElement: 'div',
                errorPlacement: function (error, element) {
                    element.addClass('is-invalid');
                    error.addClass('error');
                    error.insertAfter(element);
                }
            });

// Submit event handler
            $('body').off('submit', '#admin_form').on('submit', '#admin_form', function (e) {
                e.preventDefault();

                if (form_in_progress) return; // Prevent multiple submissions

                var $form = $(this);
                var formData = new FormData(this);

                // Handle multiple vehicle types if #vehicle_type is a select with multiple options
                var vehicleType = $('#vehicle_type').val();
                if (vehicleType && Array.isArray(vehicleType)) {
                    vehicleType.forEach(function (value) {
                        formData.append('vehicle_type[]', value);
                    });
                }

                // Check if the form is valid
                if (!$form.valid()) {
                    var error = $form.find('.is-invalid').eq(0);
                    $('html, body').animate({ scrollTop: (error.offset().top - 100) }, 500);
                    return;
                }

                // Set form in progress to prevent duplicate submissions
                form_in_progress = 1;
                App.button_loading(true);

                // Perform AJAX request
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: $form.attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    dataType: 'json', // Expect JSON directly for success
                    success: function (res) {
                        form_in_progress = 0;
                        App.button_loading(false);

                        if (res.status == 0) {
                            if (res.errors) {
                                $.each(res.errors, function (e_field, e_message) {
                                    $('[name="' + e_field + '"]').eq(0)
                                        .addClass('is-invalid')
                                        .after('<div class="error">' + e_message + '</div>');
                                });
                                var error = $form.find('.is-invalid').eq(0);
                                $('html, body').animate({ scrollTop: (error.offset().top - 100) }, 500);
                            } else {
                                App.alert('Unable to save variation. Please try again later.', 'Oops!', 'error');
                            }
                        } else {
                            App.alert(res.message || 'Record saved successfully', 'Success!', 'success');
                            setTimeout(function () {
                                window.location.href = res.oData.redirect;
                            }, 2500);
                        }
                    },
                    error: function () {
                        form_in_progress = 0;
                        App.button_loading(false);
                        App.alert("Network error, please try again", 'Oops!', 'error');
                    }
                });
            });

            // $(".password-toggle").on("click", function () {
            //     var $input = $(this).siblings("input");
            //     var currentType = $input.attr("type");
            //     console.log(        currentType)
            //
            //     // Toggle type between password and text
            //     $input.attr("type", currentType === "password" ? "text" : "password");
            //
            //     // Toggle eye icon
            //     $(this).find("svg").toggleClass("fa-eye fa-eye-slash");
            //
            //     // Optional: Log current type after the toggle
            //     console.log($input.attr("type"));
            // });
        })

        function password_show_hide(element) {
            // Get the closest password container
            var $container = $(element).closest(".password-container");

            // Select password field, show_eye, and hide_eye icons within the container
            var $passwordField = $container.find(".password-toggle");
            var $showEye = $container.find(".show_eye");
            var $hideEye = $container.find(".hide_eye");

            if ($passwordField.attr("type") === "password") {
                $passwordField.attr("type", "text");
                $showEye.addClass("d-none");
                $hideEye.removeClass("d-none");
            } else {
                $passwordField.attr("type", "password");
                $showEye.removeClass("d-none");
                $hideEye.addClass("d-none");
            }
        }

    </script>
@stop
