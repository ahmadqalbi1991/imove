@extends('admin.template.layout')
@section('header')
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
    />
    <style>
        .remove_btn_imgs {
            position: absolute;
            top: 52px;
            right: 15px;
            padding: 0 !important;
        }

        .remove_btn_imgs .btn_remove_img {
            width: 40px !important;
            height: 40px !important;
            padding: 0 !important;
            border-radius: 5px !important;
            background: red;
            color: #fff;
        }

        .delete-img {
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-svg svg {
            width: 100%;
            height: 152px;
        }

        .form-group label {
            color: #888ea8;
            font-size: 12px;
            margin-bottom: 2px;
        }
    </style>
@stop
@section('content')

    <form method="post" id="admin-form" action="{{ route('admin.bookings.request_update') }}"
          enctype="multipart/form-data"
          data-parsley-validate="true">
        <input type="hidden" name="id" id="cid" value="{{ $booking->id??0 }}">

        @csrf()
        <div class="row">

            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-8">
                                <h5 class="mb-0">Booking Details</h5>
                            </div>
                            <div class="col-4 text-right mt-1">
                                @php
                                    $badge = 'warning';
                                    if ($booking->status == 1) {
                                        $badge = 'primary';
                                    }
                                    if ($booking->status == 2 || $booking->status == 5) {
                                        $badge = 'success';
                                    }
                                    if ($booking->status == 3) {
                                        $badge = 'default';
                                    }
                                    if ($booking->status == 4) {
                                        $badge = 'primary';
                                    }
                                    if ($booking->status == 4) {
                                        $badge = 'danger';
                                    }
                                @endphp
                                <span class="badge badge-{{ $badge }} mr-4">{{ $booking->booking_status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Order Number</label>
                                    <p>{{  $booking->booking_number  }}</p>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Emergency Problem</label>
                                    <p>{{ $booking->issue->title }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Number</label>
                                    <p>{{ $booking->vehicle->vehicle_name }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Manufacturer</label>
                                    <p>{{ !empty($booking->vehicle->manufacturer) ? $booking->vehicle->manufacturer->name : '' }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Model</label>
                                    <p>{{ !empty($booking->vehicle->model) ? $booking->vehicle->model->model : 'N/A' }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Type</label>
                                    <p>{{ !empty($booking->vehicle->category) ? $booking->vehicle->category->model : 'N/A' }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Model Year</label>
                                    <p>{{ $booking->vehicle->model_year }}</p>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Created at</label>
                                    <p>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M,Y') }}</p>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Remarks</label>
                                    <p>{{ $booking->remarks ?? 'N/A' }}</p>
                                </div>
                            </div>


                        </div>


                    </div>
                </div>
            </div>


            <div class="col-md-6">

                <div class="card mb-3">

                    <h5 class="card-header mb-0">Customer Detail</h5>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <p>{{ $booking->customer->name ?? '' }}</p>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Email</label>
                                    <p>{{ $booking->customer->email ?? '' }}</p>

                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Customer Mobile Number</label>
                                    <p>{{replace_plus($booking->customer->dial_code.' '.$booking->customer->phone)}}</p>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card mb-3">
                            <h5 class="card-header mb-0">Pick Up</h5>
                            <div class="card-body">
                                <!--<div class="row">-->
                                <!--    <div class="col-md-12">-->
                                <!--        <h4 class="left"><span>Pick Up</span></h4>-->
                                <!--    </div>-->
                                <!--</div>-->


                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Pick Up Location</label>
                                            <p>{{ $booking->pick_up_location ?? '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                    </div>
                </div>

                <div class="card mb-3">

                    <h5 class="card-header mb-0">Drop Off</h5>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Drop Off Location</label>
                                    <p>{{ $booking->drop_off_location ?? '' }}</p>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            @if(!empty($booking->accepted_order) && !empty($booking->accepted_order->vendor))
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h5 class="card-header mb-0">Driver Details</h5>
                        <div class="card-body">
                            <div class="">
                                <div class="form-group">
                                    <label>Driver Name</label>
                                    <p>{{ $booking->accepted_order->vendor->name }}</p>
                                </div>
                            </div>

                            <div class="">
                                <div class="form-group">
                                    <label>Driver Contact Number</label>
                                    <p>
                                        +{{ $booking->accepted_order->vendor->dial_code .' ' . $booking->accepted_order->vendor->phone}}</p>
                                </div>
                            </div>
                            <div class="">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <p>&nbsp;</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($booking->order_id)
                <div class="col-md-6">
                    <div class="card mb-3">
                        <h5 class="card-header mb-0">Payment Details</h5>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                <tr>
                                    <td style="border-top: none;">Payment Type:</td>
                                    <th style="text-align:right; border-top: none;">Card Payment</th>

                                </tr>
                                <tr>
                                    <td style="border-top: none;">Sub Total:</td>

                                    <th style="text-align:right; border-top: none;">AED
                                        <span
                                                id="cost">{{number_format($booking->accepted_order->amount, 2)}}</span>
                                    </th>
                                </tr>

                                @if($booking->coupon_applied)
                                    <tr>
                                        <td style="border-top: none;">Discount:</td>

                                        <th style="text-align:right; border-top: none;">AED
                                            <span
                                                    id="cost">{{number_format($booking->accepted_order->discount, 2)}}</span>
                                        </th>
                                    </tr>
                                @endif

                                <tr>
                                    <td>Tax:</td>
                                    <th style="text-align:right;">AED
                                        <spam id="tax">{{number_format($booking->accepted_order->calculated_tax, 2)}}</spam>
                                    </th>
                                </tr>
                                <tr>
                                    <td style="font-size: 17px">Grand Total:</td>
                                    <th style="text-align:right; font-size: 17px">AED
                                        <span
                                                id="grand_total">{{$booking->accepted_order->grand_total}}</span>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6"></div>
            @endif

            @if(count($booking->images))
                <div class="col-12">
                    <div class="card">
                        <h5 class="card-header">Images</h5>
                        <div class="card-body">
                            <div class="row">
                                @foreach($booking->images as $image)
                                    <div class="col-3">
                                        <a data-fancybox href="{{ $image->images_path }}">
                                            <img src="{{ $image->images_path }}" class="img-fluid" alt=""/>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
    <!--<div class="col-xs-12 col-sm-6">-->
    <!--</div>-->
@stop
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        Fancybox.bind('[data-fancybox]', {
            //
        });

        $('#select2').select2();

        $('#images').on('change', function () {
            var ObjImage = document.getElementById("images");
            var FileCount = ObjImage.files.length;

            $('.booking-img').attr("src", "");

            for (ic = 0; ic < FileCount; ic++) {
                var file = ObjImage.files[ic];
                var tmppath = URL.createObjectURL(file);

                $('#image' + ic).attr("src", tmppath);
            }
        })


        function CostCalculation() {
            var category_id = $('#category_id').val();
            var size_id = $('#size_id').val();

            if (category_id > 0 && size_id > 0) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.bookings.create_new_request_get_costing') }}",
                    data: {category_id: category_id, size_id: size_id, "_token": "{{ csrf_token() }}"},
                    success: function (cost) {
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
            } else {
                $("#cost").text("0.00");
                $("#service_price").text("0.00");
                $("#tax").text("0.00");
                $("#grand_total").text("0.00");
            }
        }


    </script>

@stop
