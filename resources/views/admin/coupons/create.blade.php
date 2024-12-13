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

        .img-view {
            cursor: zoom-in;
        }

        .btn-primary {
            display: none;
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
                    <div class="col-md-12 col-sm-12 py-4">
                        <form action="{{ route('coupons.save') }}" method="post" id="admin_form">
                            @if(!empty($coupon))
                                <input type="hidden" value="{{ $coupon->id }}" name="id">
                            @endif
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input value="{{ !empty($coupon) ? $coupon->title : ''}}" type="text" name="title" id="title" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="promo_code">Promo code</label>
                                        <input value="{{ !empty($coupon) ? $coupon->promo_code : ''}}" type="text" name="promo_code" id="promo_code" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input value="{{ !empty($coupon) ? $coupon->start_date : ''}}" type="date" name="start_date" id="start_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Date</label>
                                        <input value="{{ !empty($coupon) ? $coupon->end_date : ''}}" type="date" name="end_date" id="end_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="VehicleTypes">Vehicle Types</label>
                                    <select id="VehicleTypes" multiple class="form-control-plaintext" name="vehicle_type[]">
                                        <option value="">Select Type</option>
                                        @foreach($types as $type)
                                            <option @if(in_array($type->id, $selected_types)) selected @endif value="{{ $type->id }}">{{ $type->model }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="coupon_type">Coupon Type</label>
                                        <select name="coupon_type" id="coupon_type" class="form-select">
                                            <option value="">Select Criteria</option>
                                            <option @if(!empty($coupon) && $coupon->coupon_type === 'fixed') selected @endif value="fixed">Fixed</option>
                                            <option @if(!empty($coupon) && $coupon->coupon_type === 'percentage') selected @endif value="percentage">Percentage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="coupon_type">Coupon Value</label>
                                        <input type="number" name="value" id="value" value="{{ !empty($coupon) ? $coupon->value : ''}}">
                                    </div>
                                </div>
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="main-btn primary-btn btn-hover">Save</button>
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

    {{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('end_date').setAttribute('min', today);
        document.getElementById('start_date').setAttribute('min', today);

        $(document).ready(function () {

            $('#VehicleTypes').select2();

        })

        App.initTreeView();

        App.initFormView();
        let form_in_progress = 0;

        $('body').off('submit', '#admin_form');
        $('body').on('submit', '#admin_form', function (e) {
            e.preventDefault();
            var validation = $.Deferred();
            var $form = $(this);
            var formData = new FormData(this);

            var vehicleType = $('#vehicle_type').val();  // Get the selected values
            if (vehicleType) {
                vehicleType.forEach(function (value) {
                    formData.append('vehicle_type[]', value);
                });
            }

            $form.validate({
                rules: {
                    title: {
                        required: true
                    },
                    promo_code: {
                        required: true
                    },
                    start_date: {
                        required: true
                    },
                    end_date: {
                        required: true
                    },
                    coupon_type: {
                        required: true
                    },
                    vehicle_type: {
                        required: true
                    },
                    value: {
                        required: true
                    },
                },
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
                                window.location.href = '{{ route("coupons.list") }}'
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
        })
    </script>
@stop
