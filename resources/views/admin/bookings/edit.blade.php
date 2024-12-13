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
<?php 
                        $status = '';
                        $status_color = '';
                        if($booking->status == 'pending'){
                            $status = 'PENDING';
                            $status_color = 'secondary';
                        }
                        else if($booking->status == 'qouted'){
                            $status = 'QOUTED';
                            $status_color = 'warning';
                        }
                        else if($booking->status == 'accepted'){
                            $status = 'ACCEPTED';
                            $status_color = 'success';
                        }
                        else if($booking->status == 'journey_started'){
                            $status = 'JOURNEY STARTED';
                            $status_color = 'info';
                        }
                        else if($booking->status == 'item_collected'){
                            $status = 'ITEM COLLECTED';
                            $status_color = 'info';
                        }
                        else if($booking->status == 'on_the_way'){
                            $status = 'On THE WAY';
                            $status_color = 'info';
                        }
                        else if($booking->status == 'border_crossing'){
                            $status = 'BORDER CLEARNACE';
                            $status_color = 'info';
                        }
                        else if($booking->status == 'custom_clearance'){
                            $status = 'CUSTOM CLEARANCE';
                            $status_color = 'info';
                        }
                        else if($booking->status == 'delivered'){
                            $status = 'DELIVERED';
                            $status_color = 'primary';
                        }
                        $statuses = ['pending','qouted','accepted','journey_started','item_collected','on_the_way','border_crossing','custom_clearance','delivered'];

                            $html = '';
                            $html .= '<div class="dropdown float-right" >';
                            $html .=            '<button class="btn btn-'.$status_color.' dropdown-toggle" type="button" data-toggle="dropdown">
                                            '. $status.'
                                        <span class="caret"></span></button>';

                            $html .=   '<ul class="dropdown-menu">';
                            foreach($statuses as $st){
                                if(strtoupper(str_replace('_',' ',$st)) == $status){
                                    continue;
                                }

                                $route = route('booking_status',['id' => $booking->id,'status' => $st]);
                                $html .= '<li><a class="dropdown-item" href="'.$route.'">'.strtoupper(str_replace('_',' ',$st)) .'</a></li>';
                            }
                            
                            $html .=    '</ul>';
                            $html .=    '</div>';
                    ?>

                    <?php            

            $p_status = '';
            $p_status_color = '';
            if($booking->is_paid == 'no'){
                $p_status = 'UNPAID';
                $p_status_color = 'danger';
            }
            else if($booking->is_paid == 'yes'){
                $p_status = 'PAID';
                $p_status_color = 'info';
            }

            $p_statuses = ['unpaid','paid'];

            $p_html = '';
               
                $p_html .= '<div class="dropdown float-right" >';
                $p_html .=            '<button class="btn btn-'.$p_status_color.' dropdown-toggle" type="button" data-toggle="dropdown">
                                '. $p_status.'
                            <span class="caret"></span></button>';

                $p_html .=   '<ul class="dropdown-menu">';
                foreach($p_statuses as $p_st){
                    if(strtoupper(str_replace('_',' ',$p_st)) == $p_status){
                        continue;
                    }

                    $route = route('payment_status',['id' => $booking->id,'status' => $p_st]);
                    $p_html .= '<li><a class="dropdown-item" href="'.$route.'">'.strtoupper(str_replace('_',' ',$p_st)) .'</a></li>';
                }
                
                $p_html .=    '</ul>';
                $p_html .=    '</div>';
            
        ?>
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Ajax Sourced Server-side -->
        <div class="card">
            <div class="card-header justify-content-between">
                <h5 class="mb-0">{{"Edit Booking ". $booking->booking_number }}</h5>
                {!! $p_html !!}
                {!! $html !!}
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
                            <form id="admin_form" action = "{{ route('bookings.update',['id' => $booking->id]) }}" method = "POST" enctype="multipart/form-data">
                                @csrf
                            <div class="row">

                                @if($deligate->slug != 'truck')
                                <div class="col-md-6">
                                    <label class="form-label">Deligate</label>
                                    <input type = "text" class="form-control-plaintext" " value = "{{ $deligate->name ?? '' }}">    
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Deligate Requirements</label>    
                                    @php 
                                        $deligate_details = $booking->deligate_details;
                                        if($deligate_details != "[]"){
                                           $deligate_details = json_decode($deligate_details);
                                        }

                                    @endphp   

                                    @foreach($deligate_details as $key => $value)
                                    <span class = "badge badge-primary">
                                        <b>{{strtoupper(str_replace('_',' ',$key))}} : {{ $value }}</b>
                                    </span>
                                    @endforeach

                                </div>
                                @endif

                                <div class="col-md-6" id = "driver-type-div">
                                    <label class="form-label">Select Customer</label>
                                    <select class="form-control-plaintext" name = "customer" id = "customer" data-jqv-required="true" disabled>
                                        <option value = "">Select Customer (Sender)</option>
                                        @foreach($customers as $customer)
                                        <option value = "{{$customer->id}}" {{ $booking->sender_id == $customer->id?'selected':'' }} >{{$customer->name."\n"."(".$customer->email.")"}}</option>
                                        @endforeach
                                    </select>
                                    <input type = "hidden" name = "customer" value = "{{ $booking->sender_id }}">
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Truck Type</label>
                                    <select class="form-control-plaintext" name = "truck_type" id = "truck_type" {{ $booking->truck_type_id != null?'disabled':'' }}>
                                        <option value = "">Select Truck Type</option>
                                        @foreach($trucks as $truck)
                                        <option value = "{{$truck->id}}" {{ $booking->truck_type_id == $truck->id?'selected':'' }} >{{$truck->truck_type." -- (L x W xH) -- "."(".$truck->dimensions.")"}}</option>
                                        @endforeach
                                    </select>
                                    @if($booking->truck_type_id != null)
                                    <input type = "hidden" name = "truck_type" value = "{{$booking->truck_type_id }}">
                                    @endif
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Shipping Method</label>
                                    <select class="form-control-plaintext" name = "shipping_method" >
                                        <option value = "">Select Shipping Method</option>
                                        @foreach($shipping_methods as $shipping_method)
                                        <option value = "{{$shipping_method->id}}" {{ (isset($booking->shipping_method_id) && $booking->shipping_method_id == $shipping_method->id)?'selected':'' }}>{{$shipping_method->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Invoice Number</label>
                                    <input type = "text" class="form-control-plaintext" name = "invoice_number" value = "{{ $booking->invoice_number }}" >
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Select Driver</label>
                                    <select class="form-control-plaintext" name = "drivers[]" id = "driver" data-jqv-required="true" data-placeholder = "Select Driver" multiple {{ $booking->truck_type_id != null?'disabled':'' }}>
                                        @foreach($drivers as $driver)
                                            <option value = "{{$driver->id}}" {{ in_array($driver->id, $selected_drivers)?'selected':'' }}> {{$driver->name}}  {{"(".$driver->email.")"}} </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6" >
                                    <label class="form-label">Quantity</label>
                                    <input type = "number" class="form-control-plaintext" name = "quantity" data-jqv-required="true" value = "{{ $booking->quantity ?? '' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Collection Address</label>
                                    <input type = "text" class="form-control-plaintext" name = "collection_address" value = "{{ $booking->collection_address ?? '' }}" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Deliver Address</label>
                                    <input type = "text" class="form-control-plaintext" name = "deliver_address" value = "{{ $booking->deliver_address ?? '' }}" data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Name</label>
                                    <input type = "text" class="form-control-plaintext" name = "receiver_name" value = "{{ $booking->receiver_name ?? '' }}"  data-jqv-required="true">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Email</label>
                                    <input type = "email" class="form-control-plaintext" name = "receiver_email" value = "{{ $booking->receiver_email ?? '' }}"  data-jqv-required="true">
                                </div>
                                  @php
                                    $phone = explode(' ',$booking->receiver_phone);
                                  @endphp  
                                <div class="col-md-6">
                                    <label class="form-label">Receiver Phone</label>
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                       <select  name = "dial_code" data-jqv-required="true">
                                            <option value = "">Dial Code</option>
                                            @foreach(dial_codes() as $key => $country)
                                            <option value = "{{$key}}" {{ isset($phone[0]) && $phone[0] == $key?'selected':'' }}>{{$key}}</option>
                                            @endforeach
                                        </select>
                                      </div>
                                      <input type="number" class="form-control-plaintext" name = "receiver_phone" data-jqv-required="true" value = "{{ $phone[1] ?? ''}}">
                                    </div>
                                </div>

                            </div>

                             <div class = "row">
                                <div class = "col-md-12">
                                    <label class="form-label">Delivery Note</label>
                                    <textarea class="form-control-plaintext" rows = "5" name = "delivery_note">{{ $booking->delivery_note }}</textarea>
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
                                <div class="col-sm-3">

                                    <button class="main-btn primary-btn btn-hover btn-sm" type="submit" id = "submit"> Update Booking
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
                    App.alert( "The Booking information updating failed", 'Oops!','error');
                }
            });
        });
    });
        })
    </script>
@stop
