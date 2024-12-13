@extends("admin.template.layout")
@section('header')
    <style>

    .title_lines {
        position: relative;
        font-size: 30px;
        z-index: 1;
        overflow: hidden;
        text-align: center;
        color: #c1c8cf;
        font-family: arial;
    }
    .title_lines:before, .title_lines:after {
        position: absolute;
        top: 51%;
        overflow: hidden;
        width: 48%;
        height: 1px;
        content: '\a0';
        background-color: #cccccc;
        margin-left: 2%;
    }
    .title_lines:before {
        margin-left: -50%;
        text-align: right;
    }

        h4 {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #c1c8cf
    }
    h4 span {
    background: #fff;
    margin: 0 15px;
    }
    h4:before,
    h4:after {
    background: #c1c8cf;
    height: 2px;
    flex: 1;
    content: '';
    }
    h4.left:after {
    background: none;
    }
    h4.right:before {
    background: none;
    }
    
    .category-img {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 5px;
    width: 100px;
    }

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
        <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
@endsection
@section('content')
        <?php 
            $data['booking_status'] = $booking->status;
            $status = '';
            $status_color = '';
            if($data['booking_status'] == 'customer_requested'){
                $status = 'Customer Requested';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'company_qouted'){
                $status = 'Company Quoted';
                $status_color = 'warning';
            }
            else if($data['booking_status'] == 'customer_accepted'){
                $status = 'Customer Quote Accepted';
                $status_color = 'success';
            }
            else if($data['booking_status'] == 'journey_started'){
                $status = 'JOURNEY STARTED';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'item_collected'){
                $status = 'ITEM COLLECTED';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'on_the_way'){
                $status = 'On THE WAY';
                $status_color = 'info';
            }
            else if($data['booking_status'] == 'delivered'){
                $status = 'DELIVERED';
                $status_color = 'primary';
            }
            $statuses = ['customer_requested','company_qouted','customer_accepted','item_collected','on_the_way','delivered'];

            $html = '<span class="badge badge-'.$status_color.'">'.$status.'</span>';
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

        <?php            

            $p_status = '';
            $p_status_color = '';
            if($booking->admin_response == 'pending'){
                $a_status = 'Pending';
                $a_status_color = 'secondary';
            }
            else if($booking->admin_response == 'approved'){
                $a_status = 'Approved';
                $a_status_color = 'info';
            }
            else if($booking->admin_response == 'rejected'){
                $a_status = 'Rejected';
                $a_status_color = 'danger';
            }

            $a_statuses = ['pending','approved','rejected'];

            $a_html = '';               
            if($booking->admin_response == 'pending'){
                $a_html .= '<div class="dropdown float-right" >';
                $a_html .=            '<button class="btn btn-'.$a_status_color.' dropdown-toggle" type="button" data-toggle="dropdown">
                                '. $a_status.'
                            <span class="caret"></span></button>';

                $a_html .=   '<ul class="dropdown-menu">';
                
        
                    $a_html .= '<a class="dropdown-item"
                        href="' . route('booking.approve', ['id' => encrypt($booking->id)]) . '"><i
                    class="fa fa-check"></i> Approve</a>';

                    $a_html .= '<a class="dropdown-item"
                        href="' . route('booking.reject', ['id' => encrypt($booking->id)]) . '"><i
                    class="fa fa-times"></i> Reject</a>';
                
                $a_html .=    '</ul>';
                $a_html .=    '</div>';
            }
            
        ?>
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Ajax Sourced Server-side -->
        <div class="card">
            <div class="card-header justify-content-between">
                <h5 class="mb-1">{{'Request '. $booking->booking_number  }}</h5>
                {{--
                @if(isset($booking->invoice_number))
                <h5 class="mb-0">{{'Invoice #'. $booking->invoice_number }}</h5>
                @else
                <h5 class="mb-0">{{'Invoice # (Not Added Yet)' }}</h5>
                @endif
                 --}}   
               
                {!! $a_html !!}
                @if($booking->admin_response == 'approved')
                {!! $p_html !!}
                @endif
                {!! $html !!}
            </div>
            <div class="card-body py-0">
                 
                <div class="row mt-2">
                        <div class = "col-md-6 moving-from">
                            <div class = "row">
                                <div class = "col-md-12">
                                    <h4 class="left"><span>Relocation Details Moving From</span></h4>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Moving Out Date</label>
                                    <div class="form-control-plaintext" >
                                        {{ isset($booking->booking_office_relocation->move_out_date) ? date( 'd M,Y',strtotime($booking->booking_office_relocation->move_out_date) ): '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Country</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->country_from ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">City</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->city_from ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Area</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->area_from ?? ''}}
                                    </div>      
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Building Name</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->building_from_name ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Building No.</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->building_from_no ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Require Handyman services to dismentle items at current address?</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->handyman_services_to_dismantle ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Extra details that provide help the verified partners for detailed quote at current address?</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->extra_details_from ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Details of Items to be excluded from the move?</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->details_of_items_to_be_excluded ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Included Insurance?</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->include_insurance ?? '' }}
                                    </div>      
                                </div>

                            </div>
                        </div>

                        <div class = "col-md-6 moving-to">
                            <div class = "row">
                                <div class = "col-md-12">
                                    <h4 class="right"><span>Relocation Details Moving To</span></h4>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Moving In Date</label>
                                    <div class="form-control-plaintext" >
                                        {{ isset($booking->booking_office_relocation->move_in_date) ? date( 'd M,Y',strtotime($booking->booking_office_relocation->move_in_date) ): '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Country</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->country_to ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">City</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->city_to ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Area</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->area_to ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Building Name</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->building_to_name ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Building No.</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->building_to_no ?? ''}}
                                    </div>      
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label">Require Handyman services to assemble items at new address? &nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->handyman_services_to_assemble ?? '' }}
                                    </div>      
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Extra details that provide help the verified partners for detailed quote at new address?</label>
                                    <div class="form-control-plaintext" >
                                        {{ $booking->booking_office_relocation->extra_details_to ?? '' }}
                                    </div>      
                                </div>
                                
                            </div>
                        </div>
                </div>
                <div class = "row mt-2">
                    <div class = "col-md-12 mt-2 mb-2">
                        <div class="title_lines">Uploaded Pictures</div>
                    </div>

                    @foreach($booking->booking_pictures as $picture)
                    <div class = "col-md-4 col-sm-12">
                        <img src="{{ $picture->picture }}" data-toggle="modal" data-target="#exampleModalCenter" class = "img-view img-thumbnail" style = "width:100%;height:200px" >
                    </div>
                    @endforeach
                </div>


                <div class = "row mt-5">
                    <div class = "col-md-4">
                        <h5> Request History </h5>
                        <table class = "table table-striped">
                            @foreach($booking->booking_status_trackings as $tracking)
                            <tr>
                                @if($tracking->status_tracking == 'company_qouted')
                                <th>COMPANY QUOTED: </th>
                                <th>{{ $tracking->created_at }} </th>   
                                @else
                                <th>{{strtoupper(str_replace('_',' ',$tracking->status_tracking))}}: </th>
                                <th>{{ $tracking->created_at }} </th>
                                @endif
                            </tr>     
                            @endforeach
                        </table>         
                    </div>
                    <div class = "col-md-4">
                    <h5> Company Details </h5>
                        @if($booking->company_id != null)
                        <div class="card-body text-left">
                                <img src="{{$booking->company->logo}}" alt="Logo"
                                class="img-thumbnail img-fluid" style="width: 100px;">
                                <h5 class="my-3">{{ $booking->company->user->name ?? ''}}</h5>
                                <p class="text-muted mb-1"> {{ $booking->company->user->email ?? ''}} </p>
                                <p class="text-muted mb-4">{{ '+'.$booking->company->user->dial_code." ".$booking->company->user->phone ?? ''}}</p>
                        </div>    
                        @else
                        <p class="text-muted mb-1"> Not Approved Yet </p>             
                        @endif
                    </div>

                    <div class = "col-md-4">
                    <h5> Billing Details </h5>
                        <table class = "table table-striped">
                            <tr>
                                <th>Quoted Amount: </th>
                                <th>{{ number_format($booking->qouted_amount,3)}} </th>
                            </tr>
                            <tr>
                                <th>Commission: </th>
                                <th>{{ get_commission_amount($booking->qouted_amount,$booking->comission_amount).'  '.'('. $booking->comission_amount.'%'.')'}} </th>
                            </tr>
                            <tr>
                                <th>Total: </th>
                                <th>{{ get_total_amount($booking->qouted_amount,$booking->comission_amount)}} </th>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class = "row">
                    <div class="col-md-12">
                        <div id="map"></div>
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
            
            
        $(document).on('click','.img-view',function(){
            let src = $(this).attr('src');
            $('#display-image').attr('src',src);
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
    
        })
    </script>

    <script>
        function initMap() {
            var directionsService = new google.maps.DirectionsService;
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 7,
                center: {lat: 41.85, lng: -87.65}
            });
            directionsDisplay.setMap(map);
            calculateAndDisplayRoute(directionsService, directionsDisplay);
        }

        function calculateAndDisplayRoute(directionsService, directionsDisplay) {
            directionsService.route({
                origin: new google.maps.LatLng("{{ $booking->booking_office_relocation->latitude_from ?? '' }}", "{{ $booking->booking_office_relocation->longitude_from ?? ''}}"),
                destination: new google.maps.LatLng("{{ $booking->booking_office_relocation->latitude_to ?? '' }}", "{{ $booking->booking_office_relocation->longitude_to ?? '' }}"),
                travelMode: 'DRIVING'
            }, function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    App.alert('Directions request failed due to Network Connection' );
                }
            });
        }
      
    </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"></script>
@stop
