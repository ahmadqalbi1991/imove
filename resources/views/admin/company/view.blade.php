@extends("admin.template.layout")
<style>
    .form-control-plaintext {
        padding-left: 7px;
        border: 1px solid #990253 !important;
        border-radius: 10px !important;
        color: #212529 !important;
        text-align: left;
        margin-bottom: 15px;
    }

    .form-label {
        margin-bottom: 5px;
    }

    .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single {
        border: 1px solid #F1586C !important;
    }

    .img-view{
            cursor: zoom-in;
        }  
    .select2-search{
        display:none;
    }    
</style>
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('company.submit')}}" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">

                        <div class="col-xl-6 col-lg-6 col-sm-12">
                            <div class="icon-card mb-30">
                                <div class="icon primary">
                                    <i class='bx bx-store' ></i>
                                </div>
                                <div class="content">
                                    <h6 class="mb-10">In Progress Requests</h6>
                                    <h3 class="text-bold mb-10">{{ $total_inprogress_requests ?? 0 }}</h3>
                                </div>
                                <a href="{{route('company.bookings',['id'=>encrypt($id), 'status' => 'progress'])}}" class="link-icon-card"></a>
                            </div>            
                            <!-- End Icon Cart -->
                        </div>     

                        <div class="col-xl-6 col-lg-6 col-sm-12">
                            <div class="icon-card mb-30">
                                <div class="icon primary">
                                    <i class='bx bxs-truck' ></i>
                                </div>
                                <div class="content">
                                    <h6 class="mb-10">Delivered Requests</h6>
                                    <h3 class="text-bold mb-10">{{ $total_deliverd_requests ?? 0 }}</h3>
                                </div>
                                <a href="{{route('company.bookings',['id'=>encrypt($id), 'status' => 'delivered'])}}" class="link-icon-card"></a>
                            </div>            
                            <!-- End Icon Cart -->
                        </div>     

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
                            <select class="form-control select2" name = "categories[]" data-jqv-required="true" multiple disabled >
                                
                                @foreach(get_categories() as $category)
                                    <option value = "{{$category->id}}" {{in_array($category->id,$categories)?'selected':''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        {{--
                        <div class="col-xs-12 col-sm-6 individual">
                                <label class="form-label" for="bs-validation-name">First Name </label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="first_name"
                                    name="first_name"
                                    value="{{$first_name ?? ''}}"
                                />

                        </div>

                        <div class="col-xs-12 col-sm-6 individual">
                                <label class="form-label" for="bs-validation-name">Last Name </label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="last_name"
                                    name="last_name"
                                    value="{{$last_name ?? ''}}"
                                />

                        </div>
                        --}}    

                        <div class="col-xs-12 col-sm-6">
                            <label class="form-label" for="bs-validation-name">Ratings </label>
                            <?php
                                $stars = '';
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        $stars .= '<i class="fa fa-star"></i>';
                                    } else {
                                        $stars .= '<i class="bx bx-star" aria-hidden="true"></i>';
                                    }
                                }
                                 echo $stars; 
                            ?>
                        </div>
                        
                        <div class="col-xs-12 col-sm-6 company">
                            <label class="form-label" for="bs-validation-name">Company Name </label>
                            <div class="form-control-plaintext">
                                {{$company_name ?? ''}}
                            </div>
                        </div>
                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Email</label>
                                <div class="form-control-plaintext">
                                    {{$company_email ?? ''}}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <div class="form-control-plaintext">
                                {{ ("+". $dial_code." ".$phone) ?? '' }}
                                </div>
                            </div>


                            <div class="col-xs-12 col-sm-6 mb-3">
                                <label class="form-label">Company Status</label>
                                @if($is_approved == 'approved')
                                <select class="form-control-plaintext jqv-input" data-jqv-required="true" name="company_status" disabled>
                                    <option @if($company_status=='active') selected @endif value="active">Active</option>
                                    <option @if($company_status=='inactive') selected @endif value="inactive">InActive</option>
                                </select>
                                @else
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
                                
                            </div>

                            <div class="col-xs-12 col-sm-6 company">
                                <label class="form-label" for="bs-validation-name">Requests Delivered </label>
                                <div class="form-control-plaintext">
                                    {{$total_requests ?? 0}}
                                </div>
                            </div>

                           

                                <div class="col-md-6 company">
                                    @if($logo != null)
                                    <img src = "{{ $logo }}" style="height: 50px; width: 50px" data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail" >
                                    @endif
                                    <label class="form-label">Company Logo </label>
        
                                </div>


                                <div class="col-md-6 mb-3 company">
                                    @if($company_license != null)
                                    <img src = "{{ $company_license}}" style="height: 50px; width: 50px" data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail" >
                                    @endif
                                    <label class="form-label">Company License </label>
        
                                </div>

                                <div class="col-md-6 company">
                                    @if($banner != null)
                                    <img src = "{{ $banner }}" style="height: 50px; width: 50px" data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail" >
                                    @endif
                                    <label class="form-label">Company Banner </label>
                                   
                                </div>

                                <div class="col-xs-12 col-sm-12 mb-3">
                                    <label class="form-label">About Us</label>
                                    <div class="form-control-plaintext" >{!! $about_us ?? '&nbsp;' !!}</div>
                                </div>
                                
                                <div class="col-xs-12 col-sm-6 mb-3">
                                    <label class="form-label">Admin Share (Commission) %</label>
                                    <div class="form-control-plaintext" >{{ $admin_share ?? 0 }}</div>
                                </div>

                                <div class="col-xs-12 col-sm-6 mb-3">
                                    <label class="form-label">Company Share %</label>
                                    <div class="form-control-plaintext" >{{ $company_share ?? 0 }}</div>
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


                        </div>
                </form>
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

    $(document).on('click','.img-view',function(){
        let src = $(this).attr('src');
        $('#display-image').attr('src',src);
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
