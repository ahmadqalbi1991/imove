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
@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('customer.insert')}}" enctype="multipart/form-data">
                @csrf()
                    <div class="card-body">
                        <div class="row">

                            <div class="col-xl-3 col-lg-6 col-sm-12">
                                <div class="icon-card mb-30">
                                    <div class="icon primary">
                                        <i class='bx bxs-truck' ></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="mb-10">New Requests</h6>
                                        <h3 class="text-bold mb-10">{{ $total_new_requests ?? 0 }}</h3>
                                    </div>
                                    <a href="{{route('customer.bookings.pending',['id'=>encrypt($id)])}}" class="link-icon-card"></a>
                                </div>            
                                <!-- End Icon Cart -->
                            </div>     

                            <div class="col-xl-3 col-lg-6 col-sm-12">
                                <div class="icon-card mb-30">
                                    <div class="icon primary">
                                        <i class='bx bxs-truck' ></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="mb-10">In Progress</h6>
                                        <h3 class="text-bold mb-10">{{ $total_inprogress_requests ?? 0 }}</h3>
                                    </div>
                                    <a href="{{route('customer.bookings',['id'=>encrypt($id), 'status' => 'progress'])}}" class="link-icon-card"></a>
                                </div>            
                                <!-- End Icon Cart -->
                            </div>  

                            <div class="col-xl-3 col-lg-6 col-sm-12">
                                <div class="icon-card mb-30">
                                    <div class="icon primary">
                                        <i class='bx bxs-truck' ></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="mb-10">Delivered</h6>
                                        <h3 class="text-bold mb-10">{{ $total_delivered_requests ?? 0 }}</h3>
                                    </div>
                                    <a href="{{route('customer.bookings',['id'=>encrypt($id), 'status' => 'delivered'])}}" class="link-icon-card"></a>
                                </div>            
                                <!-- End Icon Cart -->
                            </div>     

                            <div class="col-xl-3 col-lg-6 col-sm-12">
                                <div class="icon-card mb-30">
                                    <div class="icon primary">
                                        <i class='bx bxs-truck' ></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="mb-10">Rejected</h6>
                                        <h3 class="text-bold mb-10">{{ $total_rejected_requests ?? 0 }}</h3>
                                    </div>
                                    <a href="{{route('customer.bookings.rejected',['id'=>encrypt($id)])}}" class="link-icon-card"></a>
                                </div>            
                                <!-- End Icon Cart -->
                            </div>  

                        

                            


                            </div>  
                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                            </div>
                                <div class="col-xs-12 col-sm-6 mt-6">

                                    <div class="mb-3">
                                    <input type="hidden" name="id" value="{{$id}}">
    
                                        <label class="form-label" for="bs-validation-name">Customer Name </label>
                                        <div class="form-control-plaintext">{{$name}}</div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6 mt-6">
    
    
                                    <div class="mb-3">
                                        <label class="form-label" for="bs-validation-name">Customer Email </label>
                                        <div class="form-control-plaintext">{{$email}}</div>
                                    </div>
                                </div>
    
                                <div class="col-xs-12 col-sm-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <div class="form-control-plaintext">{{$dial_code." ".$phone}}</div>
                                </div>
    
    
                                    <div class="col-xs-12 col-sm-6">
                                        <label class="form-label">Customer Status</label>
                                            <div class="form-control-plaintext">{{ $status=='active'?'Active':'Inactive'}}</div>
                                    </div> 
                            </div>                          
                                <!-- Address Section -->
                                <div class = "row">
                                    <div class = "col-md-12">
                                            <hr />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Country</label>
                                        <div class="form-control-plaintext">{{$user->country}}</div>    
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">City</label>
                                        <div class="form-control-plaintext">{{$user->city ?? ''}}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Zip Code</label>
                                        <div class="form-control-plaintext">{{ $user->zip_code ?? ''}}</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Address (Optional)</label>
                                        <div class="form-control-plaintext">{{ $user->address_2 ?? ''}}</div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">User Type</label>
                                        @php
                                        $users_types=[
                                            1=>'Individual',
                                            2=>'Business'
                                            ]
                                        @endphp
                                        <div class="form-control-plaintext">{{isset($users_types[$user->customer_type])?$users_types[$user->customer_type]:'Individual'}}</div>
                                    </div>
                                    @if( $user->company_name)
                                    <div class="col-md-6">
                                        <label class="form-label">Company Name</label>
                                        <div class="form-control-plaintext">{{ $user->company_name ?? ''}}</div>
                                    </div>
                                    @endif
                                    @if($user->company_name)
                                    <div class="col-md-6">
                                        <label class="form-label">Trade License</label>
                                        @php
                                            // Get the file extension of the passport photo
                                            $fileExtension = pathinfo($user->trade_license, PATHINFO_EXTENSION);
                                            $imageUrl = url("storage/users/$user->trade_license"); 
                                        @endphp
                                             <div>
                                                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <!-- Display image if the file is an image -->
                                                    <img src="{{ $imageUrl }}" alt="Passport Photo" class="img-fluid" style="max-width: 200px;">
                                                @else
                                                    <p><a href="{{ $imageUrl }}" class="link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">Download Trade License</a></p>
                                                @endif
                                            </div>
                                    </div>
                                    @endif

                                    
                        

                        </div>
                </form>
              </div>




</div>
@stop

@section('script')
<script>

        $(".typing").keyup(function() {
            var pass = $(this).val();
            $('.written').val(pass);
        });

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

   

</script>
@stop
