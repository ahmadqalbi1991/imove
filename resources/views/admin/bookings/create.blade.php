@extends("admin.template.layout")
@section('header')
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
        .load{
            display: none;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            width: 100% !important; 
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        cursor: default;
        padding-left: 0px; 
        padding-right: 0px; 
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
                            <form id="admin_form" action = "{{ route('bookings.store') }}" method = "POST" enctype="multipart/form-data">
                                @csrf
                            <div class="row">
                                <div class="col-md-6" id = "driver-type-div">
                                    <label class="form-label">Select Customer</label>
                                    <select class="form-control-plaintext" name = "customer" id = "customer" data-jqv-required="true">
                                        <option value = "">Select Customer (Sender)</option>
                                        @foreach($customers as $customer)
                                        <option value = "{{$customer->id}}" >{{$customer->name."\n"."(".$customer->email.")"}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Truck Type</label>
                                    <select class="form-control-plaintext" name = "truck_type" id = "truck_type">
                                        <option value = "">Select Truck Type</option>
                                        @foreach($trucks as $truck)
                                        <option value = "{{$truck->id}}" >{{$truck->truck_type." -- (L x W xH) -- "."(".$truck->dimensions.")"}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Shipping Method</label>
                                    <select class="form-control-plaintext" name = "shipping_method" >
                                        <option value = "">Select Shipping Method</option>
                                        @foreach($shipping_methods as $shipping_method)
                                        <option value = "{{$shipping_method->id}}" >{{$shipping_method->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Invoice Number</label>
                                    <input type = "text" class="form-control-plaintext" name = "invoice_number" value = "" >
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Select Driver</label>
                                    <select class="form-control-plaintext" name = "drivers[]" id = "driver" data-jqv-required="true" multiple>
                                        
                                    </select>
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Quantity</label>
                                    <input type = "number" class="form-control-plaintext" name = "quantity" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Collection Address</label>
                                    <input type = "text" class="form-control-plaintext" name = "collection_address" value = "" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Deliver Address</label>
                                    <input type = "text" class="form-control-plaintext" name = "deliver_address" value = "" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Name</label>
                                    <input type = "text" class="form-control-plaintext" name = "receiver_name" value = ""  data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Email</label>
                                    <input type = "email" class="form-control-plaintext" name = "receiver_email" value = ""  data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Phone</label>
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                       <select  name = "dial_code" data-jqv-required="true">
                                            <option value = "">Dial Code</option>
                                            @foreach(dial_codes() as $key => $country)
                                            <option value = "{{$key}}" >{{$key}}</option>
                                            @endforeach
                                        </select>
                                      </div>
                                      <input type="number" class="form-control-plaintext" name = "receiver_phone" data-jqv-required="true">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                {{--
                                    <label class="form-label">Location</label>
                                
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
                                    --}}
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-sm-12">

                                    <button class="main-btn primary-btn btn-hover btn-sm mt-4" type="submit" id = "submit"> Create Booking
                                          <span class="spinner-border spinner-border-sm load" role="status" aria-hidden="true"></span>
                                          <span class="sr-only load">Loading...</span>
                                    </button>

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
            $('#driver').select2();

            $(document).on('change','#truck_type',function(){
                if($(this).val() != ''){
                    let truck_id = $(this).val();
                    $.ajax({
                        url:"{{ route('get_drivers') }}",
                        type:'POST',
                        data:{truck_id:truck_id,'_token':"{{ csrf_token() }}"},
                        success:function(res){
                            $('#driver').html(res.options)

                        }
                    })
                }
                else{
                    $('#driver').html('')
                }
                
            })
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
                customer:{
                    required:true
                },
                truck_type:{
                    required:true
                },
                dial_code:{
                    required:true
                },
                phone:{
                    required:true
                },
                driver:{
                  required:true  
                },
                shipping_method:{
                  required:true  
                },
                quantity:{
                  required:true  
                },
                collection_address:{
                  required:true  
                },
                deliver_address:{
                  required:true  
                },
                receiver_name:{
                  required:true  
                },
                receiver_email:{
                  required:true  
                },
                receiver_phone:{
                  required:true  
                }
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
                beforeSend: function() {
                    $("#submit").attr('disabled','disabled');
                    $('.load').show();
                },
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
                complete: function() {
                    $("#submit").removeAttr('disabled');
                    $('.load').hide();
                },
                error: function (e) {
                    form_in_progress = 0;
                    App.button_loading(false);
                    console.log(e);
                    App.alert( "The Booking information creating failed", 'Oops!','error');
                }
            });
        });
    });
        })
    </script>
@stop
