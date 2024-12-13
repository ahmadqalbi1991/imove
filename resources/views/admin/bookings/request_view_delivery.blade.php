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
    .qr-svg svg {
        width: 100%;
        height: 152px;
    }
    .form-group label {
        color: #888ea8 ;
        font-size: 12px;
        margin-bottom: 2px;
    }
</style>
@stop
@section('content')

    
            <!--<div class="col-xs-12">-->
                <form method="post" id="admin-form" action="{{ route('admin.bookings.request_update') }}" enctype="multipart/form-data"
                    data-parsley-validate="true">
                    <input type="hidden" name="id" id="cid" value="{{ $datamain->id??0 }}">
                    @csrf()
                    <div class="row">

                    <div class="col-md-4">
                        <div class="card mb-5">
                            <h5 class="card-header mb-0">QR code</h5>
                            <div class="card-body">
                                    <div class="form-group  qr-svg">
                                        <!--<label>QR code</label>-->
                                        {!! QrCode::generate($datamain->order_number) !!}
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-8">
                    <div class="card mb-5">
                        <h5 class="card-header mb-0">Request Details</h5>
                        <div class="card-body">
                        <div class="row">
                        <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Request</label>
                                        <p>{{ $datamain->order_number ?? '' }}</p>
                                       
                                    </div>
                                </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Customer</label>
                                <p>{{ $datamain->customer_details->name ?? '' }}</p>
                              
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Category</label>
                                <p>{{ $datamain->category_details->name ?? '' }}</p>
                              
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    </div>

                        

                        <div class="col-md-6">
                            <!--<div class="row">-->
                                <!--<div class="col-md-12">-->
                                <!--    <h4 class="left"><span>Drop Off</span></h4>-->
                                <!--</div>-->
                                
                        <div class="card mb-5">
                            <h5 class="card-header mb-0">Drop Off</h5>
                            <div class="card-body">
                                

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Drop Off Location</label>
                                        <p>{{ $datamain->dropoff->location ?? '' }}</p>
                                    
                                    </div>
                                </div>
                            


                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Landmark</label>
                                        <p>{{ $datamain->dropoff->landmark ?? '' }}</p>
                                       
                                    </div>
                                </div>
                            


                            
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <p>{{ $datamain->dropoff->contact_person ?? '' }}</p>
                                       
                                    </div>
                                </div>
                           


                           
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Mobile Number</label>
                                        <p>{{replace_plus($datamain->dropoff->dail_code.' '.$datamain->dropoff->mobile_no)}}</p>
                                       
                                    </div>
                                </div>
                            </div>
                            
                            </div>
                            </div>
                        </div>

                    <div class="col-md-6">
                        <div class="card mb-5">
                            <h5 class="card-header mb-0">Package Details</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <p>{{ $datamain->description ?? '-' }}</p>
                                           </div>
                                    </div>
                                
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Instruction</label>
                                            <p>{{ $datamain->instruction ?? '-' }}</p>
                                           
                                        </div>
                                    </div>
                                </div>
            
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Size</label>
                                            <p>{{ $datamain->size_details->name ?? '-' }}</p>
                                           
                                        </div>
                                    </div>
            
            
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Care of Pack</label>
                                            <p>{{ $datamain->care_details->name ?? '-' }}</p>
                                           
                                        </div>
                                    </div>
                                </div>
            
                                <div class="row">
                                  <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Date</label>
                                                            <p>{{ $datamain->date ?? '-' }}</p>
                                                           </div>
                                                    </div>
            
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Time</label>
                                                            <p>{{ $datamain->time ?? '-' }}</p>
                                                          </div>
                                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Type</label>
                                            <p>{{ $datamain->delivery_type ?? '' }}</p>
                                            
                                        </div>
                                    </div>
            
                                   
                                    @if(!empty($datamain->booking_status) && $datamain->booking_status >= 4)
                                   
            
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><font color="red">Booking status </font></label>
                                            <select name="booking_status" id="booking_status" {{$datamain->booking_status < 8 ? '' : 'disabled';}}>
                                                <option value="{{$datamain->booking_status??0}}">Select</option>
                                                
                                                <option value="5"  {{!empty($datamain->booking_status) && $datamain->booking_status >= 5 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status == 5 ? 'selected' : null;}}>{{booking_status(5)}}</option>
                                                <option value="8" {{!empty($datamain->booking_status) && $datamain->booking_status >= 8 && $datamain->booking_status <= 5 ? 'disabled' : '';}} {{!empty($datamain->booking_status) && $datamain->booking_status == 8 ? 'selected' : null;}}>{{booking_status(8)}}</option>
                                               </select>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-12 form-group imgs-wrap">
                                       
                                        <input type="hidden" id="imgs_counter" value="0">
                                        @if(!empty($datamain->id))
                                        <div class="row">
                                            @foreach ($datamain->images as $img)
                                            <div class="col-md-3 img-wrap position=relative">
                                               
                                                <img style="width: 205px; height: 150px; border-radius: 5px; object-fit: cover;" class="img-responsive w-100" src="{{ asset($img->image) }}">
                                            </div>
                                            @endforeach
            
                                        </div>
                                        @endif
                                        <div id="imgs-holder" class="row mt-3"></div>
                                    </div>
                        </div>
                        </div>
                   </div>

                   <div class="col-md-6">
                        <div class="card mb-5">
                            <h5 class="card-header mb-0">Price Details</h5>
                            <div class="card-body">
                                <table class="table table-striped mb-0">
                                    <tbody>
                                    <tr>
                                        <td style="border-top: none;">Payment Type: </td>
                                        <th style ="text-align:right; border-top: none;">{{payment_type($datamain->payment_type)}} </th>
                                       
                                    </tr>
                                        <tr>
                                            <td style="border-top: none;">Subtotal: </td>
                                            <th style="border-top: none; text-align:right;">AED <span id="cost">{{number_format($datamain->cost??0, 2, '.', '')}}</span> </th>
                                            <input type="hidden" value="{{number_format($datamain->cost??0, 2, '.', '')}}" name="cost_input" id="cost_input">
                                        </tr>
                                        <tr>
                                            <td>Service Price: </td>
                                            <th style ="text-align:right;">AED <span id="service_price">{{number_format($datamain->service_price??0, 2, '.', '')}}</span> </th>
                                            <input type="hidden" value="{{number_format($datamain->service_price??0, 2, '.', '')}}"  name="service_price_input" id="service_price_input">
                                        </tr>
                                        <tr>
                                            <td>Tax: </td>
                                            <th style ="text-align:right;">AED <spam id="tax">{{number_format($datamain->tax??0, 2, '.', '')}}</spam> </th>
                                            <input type="hidden" value="{{number_format($datamain->tax??0, 2, '.', '')}}" name="tax_input" id="tax_input">
                                        </tr>
                                        <tr>
                                            <td style="font-size: 17px">Grand Total: </td>
                                            <th style ="text-align:right;font-size: 17px">AED <span id="grand_total">{{number_format($datamain->grand_total??0, 2, '.', '')}}</span> </th>
                                            <input type="hidden" value="{{number_format($datamain->grand_total??0, 2, '.', '')}}" name="grand_total_input" id="grand_total_input">
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                    </div>
                    @if($datamain->booking_status < 8)
                    <div class="col-md-12 mt-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                    @endif
                    </div>
                </form>
            <!--</div>-->
    <!--        <div class="col-xs-12 col-sm-6">-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
@stop
@section('script')


  <script>
    
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
      

    
        
       
    </script>
    
@stop
