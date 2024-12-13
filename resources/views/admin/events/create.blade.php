@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>

            </div>
            <form id="admin_form" method="post" action="{{route('events.submit')}}" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            @csrf()
                            <input type="hidden" name="id" value="{{$id}}">
                            @if($id)

                            @endif
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Name<span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="name"
                                    name="name"
                                    value="{{$event_name}}"
                                />

                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Image<span class="text-danger">*</span></label>
                                <input
                                    type="file"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="event_image"
                                    name="image"
                                />
                            <div id="image_preview" class="p-3 text-center">
                                @if($image)
                                    <img src="{{asset('storage/events/'.$image)}}" style="max-height: 250px" class="img-fluid">
                                @endif
                            </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h6>Event QR Code</h6>
                                </div>
                                <div class="card-body text-center">
                                    {!! QrCode::size(200)->generate(route('events.edit',$id)); !!}
                                </div>
                                <div class="card-footer">
                                    <div class="btn-link font-weight-bolder text-center">Event Code: {{$euid ?? "--------"}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Event Date<span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    class="form-control jqv-input"
                                    data-jqv-required="true"
                                    data-jqv-future="true"
                                    id="event-date"
                                    name="date"
                                    value="{{$event_date}}"
                                />

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Start Time<span class="text-danger">*</span></label>
                                <input
                                    type="time"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="start_time"
                                    name="start_time"
                                    value="{{$start_time}}"
                                />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">End Time<span class="text-danger">*</span></label>
                                <input
                                    type="time"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="end_time"
                                    name="end_time"
                                    value="{{$end_time}}"
                                />
                            </div>

                        </div>
                        <div class="col-md-12">

                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">About<span class="text-danger">*</span></label>
                                <textarea
                                    id="about"
                                    name="about"
                                    rows="4"
                                    class="form-control jqv-input" data-jqv-required="true"
                                >{{$about}}</textarea>
                            </div>

                            <div class="form-group">

                                <x-elements.map-location
                                    addressFieldName="address"
                                    :lat="$latitude"
                                    :lng="$longitude"
                                    :address="$address"/>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Event Type<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" id="event_type_id" data-jqv-required="true"
                                        name="event_type_id">
                                    <option value="">Select Event Type</option>
                                    @foreach(\App\Models\Event::EVENT_TYPES as $key=>$value)
                                        <option @if($event_type==$key) selected @endif value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Privacy<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true" name="privacy">
                                    <option value="">Select Privacy</option>
                                    @foreach(\App\Models\Event::PRIVACY as $key=>$value)
                                        <option @if($privacy==$key) selected @endif value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <div class="mb-3">
                                <label class="form-label">Status<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true" name="status">
                                    <option @if($status=='1') selected @endif value="1">Active</option>
                                    <option @if($status=='0') selected @endif value="0">InActive</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="main-btn primary-btn btn-hover">
                                Submit
                            </button>
                        </div>
                    </div>

                </div>
                <div class="col-xs-12 col-sm-6">


                </div>

    </form>
    </div>


    </div>
@stop

@section('script')
    <script>
        const fileInput = document.getElementById("event_image");
        const preview = document.getElementById("image_preview");

        fileInput.addEventListener("change", () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    preview.innerHTML = `<img src="${reader.result}"  style="max-height: 250px" class="img-fluid" alt="File preview">`;
                };
            } else {
                preview.innerHTML = "No file selected";
            }
        });
        $(function(){
            var dtToday = new Date();

            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if(month < 10)
                month = '0' + month.toString();
            if(day < 10)
                day = '0' + day.toString();

            var maxDate = year + '-' + month + '-' + day;
            $('#event-date').attr('min', maxDate);
        });

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

        $('body').off('submit', '#admin_form');
        $('body').on('submit', '#admin_form', function (e) {
            e.preventDefault();
            var validation = $.Deferred();
            var $form = $(this);
            var formData = new FormData(this);

            $form.validate({
                rules: {},
                errorElement: 'div',
                errorPlacement: function (error, element) {
                    element.addClass('is-invalid');
                    error.addClass('error');
                    error.insertAfter(element);
                }
            });

            // Bind extra rules. This must be called after .validate()
            App.setJQueryValidationRules('#admin_form');

            if ($form.valid()) {
                validation.resolve();
            } else {
                var error = $form.find('.is-invalid').eq(0);
                $('html, body').animate({
                    scrollTop: (error.offset().top - 100),
                }, 500);
                validation.reject();
            }

            validation.done(function () {
                $form.find('.is-invalid').removeClass('is-invalid');
                $form.find('div.error').remove();


                App.button_loading(true);


                form_in_progress = 1;
                $.ajax({
                    type: "POST",
                    enctype: 'multipart/form-data',
                    url: $form.attr('action'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    dataType: 'html',
                    success: function (res) {
                        res = JSON.parse(res);
                        console.log(res['status']);
                        form_in_progress = 0;
                        App.button_loading(false);
                        if (res['status'] == 0) {
                            if (typeof res['errors'] !== 'undefined') {
                                var error_def = $.Deferred();
                                var error_index = 0;
                                jQuery.each(res['errors'], function (e_field, e_message) {
                                    if (e_message != '') {
                                        $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                        $('<div class="error">' + e_message + '</div>').insertAfter($('[name="' + e_field + '"]').eq(0));
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
                                var m = res['message'] || 'Unable to save variation. Please try again later.';
                                App.alert(m, 'Oops!', 'error');
                            }
                        } else {
                            App.alert(res['message'] || 'Record saved successfully', 'Success!', 'success');
                            setTimeout(function () {
                                window.location.href = res['oData']['redirect'];
                            }, 2500);

                        }

                    },
                    error: function (e) {
                        form_in_progress = 0;
                        App.button_loading(false);
                        console.log(e);
                        App.alert("Network error please try again", 'Oops!', 'error');
                    }
                });
            });
        });
        $(document).ready(function() {
            $(window).keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop
