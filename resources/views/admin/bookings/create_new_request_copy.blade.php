@extends('admin.template.layout')
@section('header')
<style>
    .remove_btn_imgs{
        position: absolute;
        top: 52px;
        right: 15px;
        padding: 0 !important;
    }
    
    .remove_btn_imgs .btn_remove_img{
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        border-radius: 5px !important;
        background: red;
        color: #fff;
    }
    .delete-img{
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        border-radius: 5px !important;
        background: red;
        color: #fff;
         position: absolute;
        top: 0;
        right: 15px;
        padding: 0 !important;
        opacity: 1;
        display:flex;
        align-items: center;
        justify-content:center;
    }
</style>
@stop
@section('content')

    <div class="card mb-5">
        <div class="card-body">
            <div class="col-xs-12">
                <form method="post" id="admin-form" action="{{ route('admin.bookings.create_new_request_store') }}" enctype="multipart/form-data"
                    data-parsley-validate="true">
                    <input type="hidden" name="id" id="cid" value="{{ $datamain->id??0 }}">
                    @csrf()
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer<b class="text-danger">*</b></label>
                                <select name="customer_id" id="customer_id" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($customers)
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{!empty($datamain->customer_id) && $datamain->customer_id == $customer->id ? 'selected' : null;}}> {{ $customer->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category<b class="text-danger">*</b></label>
                                <select name="category_id" id="category_id" onchange="CostCalculation()" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($categories)
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{!empty($datamain->category_id) && $datamain->category_id == $category->id ? 'selected' : null;}}> {{ $category->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="left"><span>Pick Up</span></h4>
                                </div>
                            </div>
                            
                

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Pick Up Location<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true" placeholder="Search Location" id="locationTextField" name="pu_location" required
                                        value="{{ $datamain->location ?? '' }}" />
                                        <input type="text" id="hiddenLatitude111" name="latitude">
                                       <input type="text" id="hiddenLongitude11" name="longitude">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Landmark<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true"  name="pu_landmark" required
                                            value="{{ $datamain->landmark ?? '' }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Contact Person<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true" id="pu_contact_person" name="pu_contact_person" required
                                        value="{{ $datamain->contact_person ?? '' }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mobile Number<b class="text-danger">*</b></label>
                                        <div class="row">
                                            <div class="col-md-4"> <select id="pu_dail_code" name="pu_dail_code"> <option value="971">971</option> </select> </div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control-plaintext" data-jqv-required="true" id="pu_mob_no" name="pu_mob_no" required 
                                                value="{{ $datamain->mobile_no ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 class="left"><span>Drop Off</span></h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Drop Off Location<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true" id="locationTextField2" name="do_location" required
                                        value="{{ $datamain->dropoff->location ?? '' }}" />
                                        
                              <input type="hidden" id="hiddenLatitude2" name="latitude2">
                             <input type="hidden" id="hiddenLongitude2" name="longitude2">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Landmark<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true" id="do_landmark" name="do_landmark" required
                                            value="{{ $datamain->dropoff->landmark ?? '' }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Contact Person<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control-plaintext" data-jqv-required="true" id="do_contact_person" name="do_contact_person" required
                                        value="{{ $datamain->dropoff->contact_person ?? '' }}" />
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mobile Number<b class="text-danger">*</b></label>
                                        <div class="row">
                                            <div class="col-md-4"> <select id="do_dail_code" name="do_dail_code"> <option value="971">971</option> </select> </div>
                                            <div class="col-md-8">
                                                <input type="number" class="form-control-plaintext" data-jqv-required="true" id="do_mob_no" name="do_mob_no" required 
                                                value="{{ $datamain->dropoff->mobile_no ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description<b class="text-danger">*</b></label>
                                <textarea cols="30" rows="10" class="form-control-plaintext" data-jqv-required="true" id="description" name="description" required>{{ $datamain->description ?? '' }}</textarea>
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Instruction<b class="text-danger">*</b></label>
                                <input type="text" class="form-control-plaintext" data-jqv-required="true" id="instruction" name="instruction" required
                                value="{{ $datamain->instruction ?? '' }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Size<b class="text-danger">*</b></label>
                                <select name="size_id" id="size_id" onchange="CostCalculation()" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($sizes)
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}" {{!empty($datamain->size_id) && $datamain->size_id == $size->id ? 'selected' : null;}} > {{ $size->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Care of Pack<b class="text-danger">*</b></label>
                                <select name="care_id" id="care_id" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($cares)
                                        @foreach ($cares as $care)
                                            <option value="{{ $care->id }}" {{!empty($datamain->care_id) && $datamain->care_id == $care->id ? 'selected' : null;}}> {{ $care->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Date<b class="text-danger">*</b></label>
                                                <input type="date" class="form-control-plaintext" data-jqv-required="true" id="date" name="date" required value="{{ $datamain->date ?? '' }}" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Time<b class="text-danger">*</b></label>
                                                <input type="time" class="form-control-plaintext" data-jqv-required="true" id="time" name="time" required value="{{ $datamain->time ?? '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Delivery Type<b class="text-danger">*</b></label>
                                <select name="delivery_type" id="delivery_type" required>
                                    <option value="normal" {{!empty($datamain->delivery_type) && $datamain->delivery_type == 'normal' ? 'selected' : null;}}> Normal </option>
                                    <option value="urgent" {{!empty($datamain->delivery_type) && $datamain->delivery_type == 'urgent' ? 'selected' : null;}}> Urgent </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assign pickup driver</label>
                                <select name="pickup_driver" id="pickup_driver" required {{!empty($datamain->booking_status) && $datamain->booking_status >= 4 ? 'disabled' : '';}}>
                                    <option value="">Select</option>
                                    @foreach($drivers as $value)
                                    <option value="{{$value->id}}"  {{!empty($datamain->pickup_driver) && $datamain->pickup_driver == $value->id ? 'selected' : null;}}>{{$value->name}}</option>
                                    @endforeach
                                   </select>
                            </div>
                            @if(!empty($datamain->booking_status) && $datamain->booking_status >= 4)
                            <input type="hidden" value="{{$datamain->pickup_driver}}" name="pickup_driver" >
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="form-group" style="display:none;">
                                <label>Booking status</label>
                                <select name="booking_status" id="booking_status">
                                    <option value="">Select</option>
                                    
                                    <option value="1"  {{!empty($datamain->booking_status) && $datamain->booking_status >= 1 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status == 1 ? 'selected' : null;}}>Processing</option>
                                    <option value="4" {{!empty($datamain->booking_status) && $datamain->booking_status >= 4 && $datamain->booking_status <= 1 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status >= 4 ? 'selected' : null;}}>Completed</option>
                                   </select>
                            </div>
                        </div>
                       

                        <div class="col-md-6" style="display:none;">
                            <div class="form-group">
                                <label>Booking status</label>
                                <select name="booking_status" id="booking_status">
                                    <option value="{{$datamain->booking_status??0}}">Select</option>
                                    
                                    <option value="5"  {{!empty($datamain->booking_status) && $datamain->booking_status >= 5 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status == 5 ? 'selected' : null;}}>Processing</option>
                                    <option value="8" {{!empty($datamain->booking_status) && $datamain->booking_status >= 8 && $datamain->booking_status <= 5 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status == 8 ? 'selected' : null;}}>Completed</option>
                                   </select>
                            </div>
                        </div>
                        
                    </div>
                    

                    <div class="col-md-12 form-group imgs-wrap">
                            <div class="top-bar">
                            <label class="badge bg-dark text-white d-flex justify-content-between align-items-center">Images<button class="btn btn-button-7 pull-right" type="button" data-role="add-imgs" style="width: 40px;   height: 40px;   border-radius: 0;"><i class="flaticon-plus-1"></i></button> </label>
                            </div>
                            <input type="hidden" id="imgs_counter" value="0">
                            @if(!empty($datamain->id))
                            <div class="row">
                                @foreach ($datamain->images as $img)
                                <div class="col-md-3 img-wrap position=relative">
                                    <span class="close delete-img" title="Delete" data-role="unlink"
                                        data-message="Do you want to remove this image?"
                                        href="{{ url('admin/bookings/delete_image/' . $img->id) }}">&times;</span>
                                    <img style="width: 205px; height: 150px; border-radius: 5px; object-fit: cover;" class="img-responsive w-100" src="{{ asset($img->image) }}">
                                </div>
                                @endforeach

                            </div>
                            @endif
                            <div id="imgs-holder" class="row mt-3"></div>
                        </div>

                   

                    <div class="row">
                        <div class="col-md-4"> </div>
                        <div class="col-md-4"> </div>
                        
                        <div class="col-md-4">
                            <h5> Price Details </h5>
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>Subtotal: </td>
                                        <th>AED <span id="cost">{{number_format($datamain->cost??0, 2, '.', '')}}</span> </th>
                                        <input type="hidden" value="{{number_format($datamain->cost??0, 2, '.', '')}}" name="cost_input" id="cost_input">
                                    </tr>
                                    <tr>
                                        <td>Service Price: </td>
                                        <th>AED <span id="service_price">{{number_format($datamain->service_price??0, 2, '.', '')}}</span> </th>
                                        <input type="hidden" value="{{number_format($datamain->service_price??0, 2, '.', '')}}"  name="service_price_input" id="service_price_input">
                                    </tr>
                                    <tr>
                                        <td>Tax: </td>
                                        <th>AED <spam id="tax">{{number_format($datamain->tax??0, 2, '.', '')}}</spam> </th>
                                        <input type="hidden" value="{{number_format($datamain->tax??0, 2, '.', '')}}" name="tax_input" id="tax_input">
                                    </tr>
                                    <tr>
                                        <td>Grand Total: </td>
                                        <th>AED <span id="grand_total">{{number_format($datamain->grand_total??0, 2, '.', '')}}</span> </th>
                                        <input type="hidden" value="{{number_format($datamain->grand_total??0, 2, '.', '')}}" name="grand_total_input" id="grand_total_input">
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xs-12 col-sm-6">
            </div>
        </div>
    </div>
@stop
@section('script')


  <script>
     $('body').on("click", '[data-role="remove-imgs"]', function() {
            $(this).parent().parent().remove();
        });
let img_counter = $("#imgs_counter").val();
      $('[data-role="add-imgs"]').click(function() {
        img_counter++;
            var html = '<div class="form-group col-lg-4">\
                          <div class="remove_btn_imgs">\
                            <button type="button" class="btn btn-danger btn_remove_img" data-role="remove-imgs"><i class="flaticon-delete"></i></button>\
                          </div>\
                            <label>Banner Image<b class="text-danger">*</b></label><br>\
                            <img id="image-preview-bnr_'+img_counter+'" style="width:100%; height:160px; object-fit: cover" class="img-responsive" >\
                            <br><br>\
                            <input type="file" name="banners[]" class="form-control" data-role="file-image" data-preview="image-preview-bnr_'+img_counter+'" data-parsley-trigger="change" data-parsley-fileextension="jpg,png,gif,jpeg" data-parsley-fileextension-message="Only files with type jpg,png,gif,jpeg are supported" data-parsley-max-file-size="5120" data-parsley-max-file-size-message="Max file size should be 5MB"  required data-parsley-required-message="Select Image">\
                                <span class="text-info">Upload image with dimension 1024x547</span>\
                        </div>\
                        ';
                        $('#imgs-holder').append(html);
                    

        });
        $('#select2').select2();
        
        $('#images').on('change', function()
        {
            var ObjImage = document.getElementById("images");
            var FileCount = ObjImage.files.length;
            
            $('.booking-img').attr("src","");

            for (ic=0; ic<FileCount; ic++) 
            {
                var file = ObjImage.files[ic]; 
                var tmppath = URL.createObjectURL(file);

                $('#image'+ic).attr("src",tmppath);
            }
        })

        
        function CostCalculation()
        {
            var category_id = $('#category_id').val();
            var size_id = $('#size_id').val();
            
            if(category_id > 0 && size_id > 0)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.bookings.create_new_request_get_costing') }}",
                    data: {category_id:category_id, size_id:size_id, "_token": "{{ csrf_token() }}" },
                    success: function(cost) 
                    {
                        $("#cost").text(cost);
                        $("#cost_input").val(cost);
                        $("#service_price").text(15.00);
                        $("#service_price_input").val(15.00);
                        $("#tax").text(10.00);
                        $("#tax_input").val(10.00);
                        $("#grand_total").text((parseFloat(cost) + 10 + 15));  
                        $("#grand_total_input").val((parseFloat(cost) + 10 + 15));    
                    }
                })
            }
            else
            {
                $("#cost").text("0.00");
                $("#service_price").text("0.00");
                $("#tax").text("0.00");
                $("#grand_total").text("0.00");
            }
        }
      

        // $(document).ready(function() {
        //     if (!$("#cid").val()) {
        //         $(".b_img_div").removeClass("d-none");
        //     }
        // });
        // App.initFormView();
        // $(document).ready(function() {
        //     if (!$("#cid").val()) {
        //         $(".b_img_div").removeClass("d-none");
        //     }
        // });
        // $(".parent_cat").change(function() {
        //     if (!$(this).val()) {
        //         $(".b_img_div").removeClass("d-none");
        //     } else {
        //         $(".b_img_div").addClass("d-none");
        //     }
        // });
        // $('body').off('submit', '#admin-form');
        // $('body').on('submit', '#admin-form', function(e) {
        //     e.preventDefault();
        //     var $form = $(this);
        //     var formData = new FormData(this);
        //     $(".invalid-feedback").remove();

        //     App.loading(true);
        //     $form.find('button[type="submit"]')
        //         .text('Saving')
        //         .attr('disabled', true);

        //     var parent_tree = $('option:selected', "#parent_id").attr('data-tree');
        //     formData.append("parent_tree", parent_tree);
            
        //     $.ajax({
        //         type: "POST",
        //         enctype: 'multipart/form-data',
        //         url: $form.attr('action'),
        //         data: formData,
        //         processData: false,
        //         contentType: false,
        //         cache: false,
        //         timeout: 600000,
        //         dataType: 'json',
        //         success: function(res) {
        //             App.loading(false);

        //             if (res['status'] == 0) {
        //                 if (typeof res['errors'] !== 'undefined') {
        //                     var error_def = $.Deferred();
        //                     var error_index = 0;
        //                     jQuery.each(res['errors'], function(e_field, e_message) {

        //                         if (e_message != '') {
        //                             $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
        //                             $('<div class="invalid-feedback">' + res['message'] + '</div>')
        //                                 .insertAfter($('[name="' + e_field + '"]').eq(0)).show();
        //                             if (error_index == 0) {
        //                                 error_def.resolve();
        //                             }
        //                             error_index++;
        //                         }
        //                     });
        //                     error_def.done(function() {
        //                         var error = $form.find('.is-invalid').eq(0);
        //                         $('html, body').animate({
        //                             scrollTop: (error.offset().top - 100),
        //                         }, 500);
        //                     });
        //                 } else {
        //                     var m = res['message'] ||
        //                         'Unable to save category. Please try again later.';
        //                     App.alert(m, 'Oops!');
        //                 }
        //             } else {
        //                 App.alert(res['message'], 'Success!');
        //                 setTimeout(function() {
        //                     window.location.href = App.siteUrl('/admin/costings');
        //                 }, 1500);

        //             }

        //             $form.find('button[type="submit"]')
        //                 .text('Save')
        //                 .attr('disabled', false);
        //         },
        //         error: function(e) {
        //             App.loading(false);
        //             $form.find('button[type="submit"]')
        //                 .text('Save')
        //                 .attr('disabled', false);
        //             App.alert(e.responseText, 'Oops!');
        //         }
        //     });
        // });
        
       
    </script>
     <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>
<!-- <script>
    function initialize() {
        var input = document.getElementById('locationTextField');
        var input2 = document.getElementById('locationTextField2');
        var autocomplete = new google.maps.places.Autocomplete(input, {
    componentRestrictions: { country: 'ae' }  // Restrict to United Arab Emirates (UAE)
  });
  var autocomplete2 = new google.maps.places.Autocomplete(input2, {
    componentRestrictions: { country: 'ae' }
  });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script> -->

<script>
function initialize() {
  var locationTextField = document.getElementById('locationTextField');
  var locationTextField2 = document.getElementById('locationTextField2');
  var hiddenLatitudeInput = document.getElementById('hiddenLatitude'); // Hidden input for locationTextField
  var hiddenLongitudeInput = document.getElementById('hiddenLongitude'); // Hidden input for locationTextField
  var hiddenLatitudeInput2 = document.getElementById('hiddenLatitude2'); // Hidden input for locationTextField2
  var hiddenLongitudeInput2 = documentgetElementById('hiddenLongitude2'); // Hidden input for locationTextField2

  var autocomplete = new google.maps.places.Autocomplete(locationTextField, {
    componentRestrictions: { country: 'ae' } // Restrict to United Arab Emirates (UAE)
  });

  var autocomplete2 = new google.maps.places.Autocomplete(locationTextField2, {
    componentRestrictions: { country: 'ae' }
  });

  // Add 'place_changed' listener for each autocomplete instance
  autocomplete.addListener('place_changed', function () {
    var place = autocomplete.getPlace();
    if (!place) {
      return;
    }

    // Update hidden latitude and longitude inputs for locationTextField
    hiddenLatitudeInput.value = place.geometry.location.lat();
    hiddenLongitudeInput.value = place.geometry.location.lng();
  });

  autocomplete2.addListener('place_changed', function () {
    var place = autocomplete2.getPlace();
    if (!place) {
      return;
    }

    // Update hidden latitude and longitude inputs for locationTextField2
    hiddenLatitudeInput2.value = place.geometry.location.lat();
    hiddenLongitudeInput2.value = place.geometry.location.lng();
  });
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>


@stop
