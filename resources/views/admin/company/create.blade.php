@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"> {{$page_heading." ".$mode}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('company.submit')}}" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">

                        {{--
                        <div class="col-xs-12 col-md-6" id = "account-type-div">
                            <label class="form-label">Account Type</label>
                            <select class="form-control-plaintext" name = "account_type" id = "account_type" data-jqv-required="true"
                            @if($id != '')
                                {{'disabled'}}
                            @endif
                            >
                                <option value = "">Select Account Type</option>
                                @foreach(get_account_types() as $account_t)
                                <option value = "{{$account_t->id}}" {{ $account_type == $account_t->id ? 'selected':'' }}>{{$account_t->type}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if($id != '')
                            <input type = "hidden" name = "account_type" value = "{{$account_type}}" >
                        @endif
                        --}}
                        <input type = "hidden" name = "account_type" value = "1" id = "account_type">
                        
                        <div class="col-xs-12 col-md-6" >
                            <label class="form-label">Categories</label>
                            <select class="form-control-plaintext select2" name = "categories[]" data-jqv-required="true" multiple >
                                <option value = "">Select Categories</option>
                                @foreach(get_categories() as $category)
                                    <option value = "{{$category->id}}" {{in_array($category->id,$categories)?'selected':''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-xs-12 col-sm-6 individual">
                                <label class="form-label" for="bs-validation-name">First Name<span class="text-danger">*</span> </label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="first_name"
                                    name="first_name"
                                    value="{{$first_name ?? ''}}"
                                />

                        </div>

                        <div class="col-xs-12 col-sm-6 individual">
                                <label class="form-label" for="bs-validation-name">Last Name<span class="text-danger">*</span> </label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="last_name"
                                    name="last_name"
                                    value="{{$last_name ?? ''}}"
                                />

                        </div>

                        <div class="col-xs-12 col-sm-6 company">

                            @csrf()
                            <input type="hidden" name="id" value="{{$id}}">
                                <label class="form-label" for="bs-validation-name">Company Name<span class="text-danger">*</span> </label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="company_name"
                                    name="company_name"
                                    value="{{$company_name ?? ''}}"
                                />

                        </div>

                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type = "email" class="form-control-plaintext" name = "email" value = "{{$company_email ?? ''}}" data-jqv-required="true">
                            </div>
                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type = "password" class="form-control-plaintext" name = "password" value = "" id = "password" data-jqv-required="true">
                            </div>
                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type = "password" class="form-control-plaintext" name = "confirm_password" value = "" id = "confirm_password" data-jqv-required="true">
                                <span id='password-message'></span>
                            </div>
                            <div class="col-xs-4 col-sm-2 mb-3">
                                <label class="form-label">Phone</label>
                                <select class="form-control-plaintext" name = "dial_code" data-jqv-required="true">
                                    <option value = "">Dial Code</option>
                                    @foreach(dial_codes() as $key => $country)
                                    <option value = "{{$key}}" {{ $key == $dial_code?'selected':'' }}>{{$key}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-8 col-sm-4 mb-3">
                                <label class="form-label">Number</label>
                                <input type = "text" class="form-control-plaintext" name = "phone" value = "{{ $phone ?? '' }}" data-jqv-required="true">
                            </div>


                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Company Status <span class="text-danger">*</span></label>
                                @if($mode == 'Create')
                                <select class="form-control-plaintext jqv-input" data-jqv-required="true" name="company_status">
                                    <option @if($company_status=='active') selected @endif value="active">Active</option>
                                    <option @if($company_status=='inactive') selected @endif value="inactive">InActive</option>
                                </select>  
                                @else                                
                                    @if($is_approved == 'approved')
                                    <select class="form-control-plaintext jqv-input" data-jqv-required="true" name="company_status">
                                        <option @if($company_status=='active') selected @endif value="active">Active</option>
                                        <option @if($company_status=='inactive') selected @endif value="inactive">InActive</option>
                                    </select>
                                    @else
                                    <input type = "hidden" name="company_status" value = "inactive">
                                        @php
                                            $p_status = '';
                                            $p_status_color = '';
                                            if($is_approved == 'pending'){
                                                $a_status = 'Pending';
                                                $a_status_color = 'secondary';
                                            }
                                            else if($is_approved == 'approved'){
                                                $a_status = 'Approved';
                                                $a_status_color = 'info';
                                            }
                                            else if($is_approved == 'rejected'){
                                                $a_status = 'Rejected';
                                                $a_status_color = 'danger';
                                            }

                                            $a_statuses = ['pending','approved','rejected'];

                                            $a_html = '';               
                                            if($is_approved == 'pending'){
                                                $a_html .= '<div class="dropdown float-left" >';
                                                $a_html .=            '<button class="btn btn-'.$a_status_color.' dropdown-toggle" type="button" data-toggle="dropdown">
                                                                '. $a_status.'
                                                            <span class="caret"></span></button>';

                                                $a_html .=   '<ul class="dropdown-menu">';
                                                
                                        
                                                    $a_html .= '<a class="dropdown-item"
                                                        href="' . route('company.approve', ['id' => encrypt($id)]) . '"><i
                                                    class="fa fa-check"></i> Approve</a>';

                                                    $a_html .= '<a class="dropdown-item"
                                                        href="' . route('company.reject', ['id' => encrypt($id)]) . '"><i
                                                    class="fa fa-times"></i> Reject</a>';
                                                
                                                $a_html .=    '</ul>';
                                                $a_html .=    '</div>';
                                            }
                                            else{
                                                $a_html = '<span class = "badge badge-'.$a_status_color.'">'.$a_status.'</span>';                    
                                            }
                                        @endphp
                                        {!! $a_html !!}
                                    @endif
                                @endif
                                
                            </div>

                                <div class="col-md-6 company">
                                    
                                    <label class="form-label">Company Logo <span class="text-danger">*</span></label>
                                    <input type = "file" value="{{ $logo }}" name = "logo" class = "form-control-plaintext" >
                                    @if($logo != null)
                                    <img src = "{{ $logo }}" style="height: 50px; width: 50px">
                                    @endif
                                </div>


                                <div class="col-md-6 mb-3 company">
                                    
                                    <label class="form-label">Company License <span class="text-danger">*</span></label>
                                    <input type = "file" name = "company_license" class = "form-control-plaintext" >
                                    @if($company_license != null)
                                    <img src = "{{ $company_license}}" style="height: 50px; width: 50px">
                                    @endif
                                </div>

                                <div class="col-md-6 company">
                                    
                                    <label class="form-label">Company Banner <span class="text-danger">*</span></label>
                                    <input type = "file"  name = "banner" class = "form-control-plaintext" >
                                    @if($banner != null)
                                    <img src = "{{ $banner }}" style="height: 50px; width: 50px">
                                    @endif
                                </div>
                                
                                <div class="col-xs-12 col-sm-12 mb-3">
                                    <label class="form-label">About Us</label>
                                    <textarea class="form-control-plaintext" name = "about_us" data-jqv-required="true">{{ $about_us ?? '' }}</textarea>
                                </div>

                                <div class="col-xs-12 col-sm-6 mb-3">
                                    <label class="form-label">Admin Share %</label>
                                    <input type = "number" class="form-control-plaintext" name = "admin_share" id = "admin_share" min = "1" max = "100" value = "{{ $admin_share ?? 0 }}"  data-jqv-required="true">
                                </div>

                                <div class="col-xs-12 col-sm-6 mb-3">
                                    <label class="form-label">Company Share %</label>
                                    <input type = "number" class="form-control-plaintext" name = "company_share" id = "company_share" value = "{{ $company_share ?? 0 }}"  data-jqv-required="true" readonly>
                                </div>

                            <div class="col-md-12">
                                <div class="form-group" value = "{{$address ?? ''}}">

                                    <x-elements.map-location  
                                    addressFieldName="address"
                                    :lat="$latitude ?? ''"
                                    :lng="$longitude ?? ''"
                                    :address="$address ?? ''"
                                    
                                    />
                                </div>
                            </div>    


                            <div class="col-12">
                                <button type="submit" class="main-btn primary-btn btn-hover" disabled>
                                    Submit
                                </button>
                            </div>

                        </div>
                </form>
              </div>




</div>
@stop

@section('script')
<script>

    $(document).on('keyup','#admin_share',function(){
        
        if($('#admin_share').val() > 100) {
            $('#admin_share').val(100);
        }
        
        let admin_share = $(this).val();
        let company_share = 100;

        $('#company_share').val(company_share - admin_share);
    })

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
    
    var var_file = {};

    if(($('#account_type option:selected').val() != 0 || $('#account_type option:selected').val() != '')){
        var_file = {
            required: true, 
            extension: "png|jpeg|jpg",
        }
    }else{
        var_file = { 
            extension: "png|jpeg|jpg",
        }
    }
        

    @if($id == '')    
        $form.validate({
            rules: {
                account_type:{
                    required:true
                },
                'categories[]':{
                    required:true
                },
                // first_name:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() == 0 || $('#account_type option:selected').val() == '');
                //   }
                // },
                // last_name:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() == 0 || $('#account_type option:selected').val() == '');
                //     }
                // },
                // company_name:{
                //     required:function(){
                //         return $('#account_type option:selected').val() == 1;
                //   }
                // },
                company_name:{
                    required:true
                },
                email:{
                    required:true,
                    email:true
                },
                password:{
                    required:true,
                },
                confirm_password:{
                    required:true,
                    equalTo:"#password"
                },
                dial_code:{
                    required:true
                },
                phone:{
                    required:true
                },
                // company_license:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() === 1);
                //   }  
                // },
                // logo:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() === 1);
                //   }  
                // },

                company_license:{
                    required:true  
                },
                logo:{
                    required:true  
                },
                banner:{
                    required:true  
                },
                about_us:{
                    required:true  
                },
                admin_share:{
                    required:true  
                },
                company_share:{
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
    @else
    $form.validate({
            rules: {
                account_type:{
                    required:true
                },
                'categories[]':{
                    required:true
                },
                // first_name:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() == 0 || $('#account_type option:selected').val() == '');
                //   }
                // },
                // last_name:{
                //     required:function(){
                //         return ($('#account_type option:selected').val() == 0 || $('#account_type option:selected').val() == '');
                //   }
                // },
                // company_name:{
                //     required:function(){
                //         return $('#account_type option:selected').val() == 1;
                //   }
                // },
                company_name:{
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
                about_us:{
                    required:true  
                },
                admin_share:{
                    required:true  
                },
                companny_share:{
                    required:true  
                },
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                element.addClass('is-invalid');
                error.addClass('error');
                error.insertAfter(element);
            }
        });
    
    @endif    

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

    $(document).ready(function(){
       $('.select2').select2() 
      let account_type = $('#account_type option:selected').val();
        
      if(account_type == 0 || account_type == ''){
            $('.company').hide();
            $('.individual').show();
        }else{
            $('.individual').hide();
            $('.company').show();
        }
    })


    $(document).on('change','#account_type',function(){
        if($(this).val() == 0){
            $('.company').hide();
            $('.individual').show();
        }else{
            $('.individual').hide();
            $('.company').show();
        }
    })

</script>
@stop
