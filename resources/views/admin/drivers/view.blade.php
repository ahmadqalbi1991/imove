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

        .img-view{
            cursor: zoom-in;
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
                            <form id="admin_form" action = "{{ route('drivers.update',['id' => $user->id]) }}" method = "POST" enctype="multipart/form-data">
                                @csrf
                                <input type = "hidden" name = "driver_detail_id" value = "{{ $user->driver_detail->id }}">
                            <div class="row">
                                @php
                                    $is_company = $user->driver_detail->is_company == 'no'?0:1;
                                @endphp
                                <div class="col-md-6" id = "driver-type-div" style="display:none;">
                                    <label class="form-label">Driver Type</label>

                                        @foreach($get_driver_types as $driver_type)
                                        @if($is_company == $driver_type->id)
                                            <div class="form-control-plaintext">{{$driver_type->type}}</div>
                                        @endif    
                                        @endforeach

                                </div>
                                <div class="col-md-6"  style = "display: {{ $user->driver_detail->is_company == 'no'?'none':'' }};">
                                    <label class="form-label">Comapany</label>
                                    
                                        @foreach($companies as $company)
                                        @if($user->driver_detail->company_id == $company->id)
                                            <div class="form-control-plaintext">{{$company->name}}</div>
                                        @endif    
                                        @endforeach

                                </div>
                                <div class="col-md-6" style="display:none;">
                                    <label class="form-label">Truck Type</label>

                                        @foreach($trucks as $truck)
                                        @if($truck->id == $user->driver_detail->truck_type_id)
                                            <div class="form-control-plaintext">{{$truck->truck_type." -- ".$truck->dimensions}}</div>
                                        @endif    
                                        @endforeach

                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <div class="form-control-plaintext">{{$user->name }}</div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <div class="form-control-plaintext">{{$user->email}}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    @foreach(dial_codes() as $key => $country)
                                        @if($key == $user->dial_code)
                                            <div class="form-control-plaintext">{{'+'.$key." ".$user->phone}}</div>
                                        @endif    
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Account Status</label>
                                    <div class="form-control-plaintext">{{$user->status == 'inactive'?'Inactive':'Active'}}</div>
                                </div>
                            </div>
                                <!-- Driving Documents -->
                            <div class = "row">
                                    <div class = "col-md-12">
                                        <hr />
                                    </div>

                                    <div class="col-md-6">
                                        @if(!empty($user->driver_detail->mulkia))
                                            <img src = "{{ $user->driver_detail->mulkia }}" width = 100 data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail">
                                        @endif
                                        <label class="form-label">Mulkiya</label>    
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Mulkiya Number</label>
                                        <div class="form-control-plaintext">{{ $user->driver_detail->mulkia_number ?? '' }}</div>
                                    </div>   

{{--                                    <div class="col-md-6">--}}
{{--                                        @if(!empty($user->driver_detail->emirates_id_or_passport))--}}
{{--                                            <img src = "{{ $user->driver_detail->emirates_id_or_passport }}" width = 100 data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail">--}}
{{--                                        @endif--}}
{{--                                        <label class="form-label">Emirate ID or Passport</label>--}}
{{--                                    </div>--}}

                                <div class="col-md-6">
                                    @if(!empty($user->driver_detail->driving_license))
                                        <img src = "{{ $user->driver_detail->driving_license }}" width = 100 data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail">
                                    @endif
                                    <label class="form-label">Driving License</label>
                                </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Drving License Issued By</label>                                        
                                        <div class="form-control-plaintext">{{$user->driver_detail->driving_license_issued_by ?? ''}}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Driving License Number</label>
                                        <div class="form-control-plaintext">{{ $user->driver_detail->driving_license_number ?? '' }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Driving License Expiry</label>
                                        <div class="form-control-plaintext">{{ date('d-m-Y',strtotime($user->driver_detail->driving_license_expiry)) }}</div>
                                    </div>

                                   
                                
                                </div>    

                                <!-- Address Section -->
                                <div class = "row">
                                    <div class = "col-md-12">
                                            <hr />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country</label>    
                                        <div class="form-control-plaintext">{{ $user->country ?? '' }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <div class="form-control-plaintext">{{ $user->city ?? '' }}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Zip Code</label>
                                        <div class="form-control-plaintext">{{ $user->zip_code ?? ''}}</div>
                                    </div>

                                    
                                </div>

                        </form>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
    

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <img src = "" width = "100%" class = "img img-thumbnail" id = "display-image">                  

    </div>
  </div>
</div>


@stop
@section('script')
    <script>
        jQuery(document).ready(function () {

                $("#admin_form :input").prop("disabled", true);

            $(document).on('click','.img-view',function(){
                let src = $(this).attr('src');
                $('#display-image').attr('src',src);
            })
            $(document).on('change','#driver_type',function(){
                if($(this).val() == 1){
                    $('#company-div').show();
                }else{
                    $('#company-div').hide();
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
                name:{
                    required:true
                },
                email:{
                    required:true,
                    email:true
                },
                confirm_password:{

                    equalTo:"#password"
                },
                dial_code:{
                    required:true
                },
                phone:{
                    required:true
                },
                mulkiya_number:{
                  required:true  
                },
                driving_license_issued_by:{
                  required:true  
                },
                driving_license_number:{
                  required:true  
                },
                driving_license_expiry:{
                  required:true  
                },
                vehicle_plate_number:{
                  required:true  
                },
                vehicle_plate_place:{
                  required:true  
                },
                country:{
                  required:true  
                },
                zip_code:{
                  required:true  
                },
                city:{
                  required:true  
                },
                driver_type:{
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
