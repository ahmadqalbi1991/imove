@extends("admin.template.layout")
@section('header')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
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
        elem{
            margin-left: 20px;
        }
        .select2-results__option elem{
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
                            <form id="admin_form" action = "{{ route('customers.update',['id' => $user->id]) }}" method = "POST" enctype="multipart/form-data">
                                @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Customer Type</label>
                                    <select class="form-control-plaintext" name = "customer_type_id">
                                        @foreach($customer_types as $customer_type)
                                        <option value = "{{$customer_type->id}}" {{ $user->customer_type_id == $customer_type->id?'selected':'' }}>{{$customer_type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type = "text" class="form-control-plaintext" name = "name" value = "{{$user->name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type = "email" class="form-control-plaintext" name = "email" value = "{{$user->email}}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Phone</label>
                                    <div class="flag-dropdown">
                                        <select class="form-control" name="dial_code" id="dial_code" data-jqv-required="true">
                                            <option value="">Dial Code</option>
                                            @foreach (get_countries_list() as $flag_class => $data) 
                                                <option value="{{ $data['dial_code'] }}" {{ $data['dial_code'] == $user->dial_code?'selected':'' }} data-flag-class="{{ $flag_class }}" data-flag-value="{{ $data['dial_code'] }} ">
                                                    {{ $data['dial_code'] }} 
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Number</label>
                                    <input type = "text" class="form-control-plaintext" name = "phone" value = "{{$user->phone}}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <label class="form-label">Address</label>
                                    <input type = "text" class="form-control-plaintext" name = "address" value = "{{$user->address ?? ''}}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Account Status</label>
                                    <select class="form-control-plaintext" name = "user_status">
                                        <option value = "0" {{ $user->user_status == '0'?'selected':'' }}>Inactive</option>
                                        <option value = "1" {{ $user->user_status == '1'?'selected':'' }}>Active</option>
                                    </select>
                                </div>
                            </div>

                            <div class = "row">
                                <div class="col-sm-3">
                                    <input class = "btn btn-info float-right" type = "submit" name = "submit" value = "Save Information">
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
    <script>
        jQuery(document).ready(function () {
            function formatState(state) {
        if (!state.id) {
            return state.text; // return the default text if no id is found
        }
        var flagClass = $(state.element).data('flag-class');
        var dialCode = state.text; // +value of dial code
        var countryName = $(state.element).text(); // full text including dial code and country name

        return $('<span class="flag-icon ' + flagClass + '"><elem>'+dialCode+'</elem></span> + ' + dialCode + ' - ' + countryName);
    }

    $('#dial_code').select2({
        templateResult: formatState,
        templateSelection: formatState,
        escapeMarkup: function(markup) { return markup; } // let our custom markup through
    });

            App.initTreeView();

                $('.all-select').click(function(){
        $(this).siblings('.crud-items').prop('checked', this.checked);
    });
    $('.crud-items').click(function(){
        $(this).siblings('.all-select').prop('checked', false);
    });
    $('.all-p').click(function(){
        $(this).siblings('.reader').prop('checked', true);
    });
    App.initFormView();
    let form_in_progress=0;

    $('body').off('submit', '#admin_form');
    $('body').on('submit', '#admin_form', function(e) {
        e.preventDefault();
        var validation = $.Deferred();
        var $form = $(this);
        var formData = new FormData(this);

        $form.validate({
            rules: {

            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                element.addClass('is-invalid');
                error.addClass('error');
                error.insertAfter(element);
            }
        });

        // Bind extra rules. This must be called after .validate()
        App.setJQueryValidationRules('#admin_form');

        if ( $form.valid() ) {
            validation.resolve();
        } else {
            var error = $form.find('.is-invalid').eq(0);
            $('html, body').animate({
                scrollTop: (error.offset().top - 100),
            }, 500);
            validation.reject();
        }

        validation.done(function() {
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
                dataType:'html',
                success: function (res) {
                    res = JSON.parse(res);
                    console.log(res['status']);
                    form_in_progress = 0;
                    App.button_loading(false);
                    if ( res['status'] == 0 ) {
                        if ( typeof res['errors'] !== 'undefined' ) {
                            var error_def = $.Deferred();
                            var error_index = 0;
                            jQuery.each(res['errors'], function (e_field, e_message) {
                                if ( e_message != '' ) {
                                    $('[name="'+ e_field +'"]').eq(0).addClass('is-invalid');
                                    $('<div class="error">'+ e_message +'</div>').insertAfter($('[name="'+ e_field +'"]').eq(0));
                                    if ( error_index == 0 ) {
                                        error_def.resolve();
                                    }
                                    error_index++;
                                }
                            });
                            error_def.done(function() {
                                var error = $form.find('.is-invalid').eq(0);
                                                $('html, body').animate({
                                                    scrollTop: (error.offset().top - 100),
                                                }, 500);
                                            });
                        } else {
                            var m = res['message']||'Unable to save variation. Please try again later.';
                            App.alert(m, 'Oops!','error');
                        }
                    } else {
                        App.alert(res['message']||'Record saved successfully', 'Success!','success');
                        setTimeout(function(){
                            window.location.href = res['oData']['redirect'];
                        },2500);

                    }

                },
                error: function (e) {
                    form_in_progress = 0;
                    App.button_loading(false);
                    console.log(e);
                    App.alert( "Network error please try again", 'Oops!','error');
                }
            });
        });
    });
        })
    </script>
@stop
