@extends("admin.template.layout")
@section('header')
    <style>
    .input_span{
      display: none;
    }
    .input_commission{
      width: 50%;
    }
    </style>
@endsection

@section('content')
                    @php
                    $from = null;
                    $to = null;
                    if(request()->get('from')){
                      $from = request()->get('from');
                    }
                    if(request()->get('to')){
                      $to = request()->get('to');
                    }
                    @endphp
<div class="container-xxl flex-grow-1 container-p-y">
  
              <!-- Ajax Sourced Server-side -->
              <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    <form action = "" class="form-inline float-right">
                      <div class="form-group mb-2">
                        <label>From</label>
                        <input type = "date" class = "form-control-plaintext" name = "from" value = "{{$from ?? ''}}" max = "{{ date('Y-m-d') }}">
                      </div>
                      <div class="form-group mb-2">
                        <label>To</label>
                         <input type = "date" class = "form-control-plaintext" name = "to" value = "{{$to ?? ''}}" max = "{{ date('Y-m-d') }}">
                      </div>
                      <div class="form-group mb-2">   
                         <button type = "submit" class = "main-btn btn primary-btn mt-4">
                          Filter
                         </button>
                         @if(request()->has('from') || request()->has('to'))
                          <a href = "{{ route('earnings.list') }}" class = "btn btn-seondary  mt-4"> Clear </a>
                         @endif
                         
                      </div>    
                    </form>
                </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                
                      <table class="table table-condensed" id = "reports"  data-searching = "false">  
                        <thead>
                            <tr>
                                <th >#</th>
                                <th> Booking#</th>
                                <th> Customer Name</th>
                                <th> Driver Name</th>
                                <th> Truck Type</th>
                                <th> Qouted Amount</th>
                                <th> Total Amount</th>
                                <th> Commission %</th>
                                <th> Booking Status</th>
                                <th> Created At</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($bookings as $booking)
                              <tr>
                                <td >#</td>
                                <td> {{ $booking->booking_number }}</td>
                                <td> {{ $booking->customer->name ?? ''}}</td>
                                <td> {{ $booking->driver->name ?? ''}}</td>
                                <td> {{ $booking->truck_type->truck_type }}</td>
                                <td> {{ number_format($booking->qouted_amount,3) }}</td>
                                <td> {{ get_total_amount($booking->qouted_amount,$booking->comission_amount) }}</td>
                                <td> {{ $booking->comission_amount }}%</td>
                                <td> {!! get_booking_status($booking->status) !!}</td>
                                <td> Created At</td>
                            </tr>
                            @endforeach
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Ajax Sourced Server-side -->


            </div>
@stop
@section('script')
<script src = "https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src = "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src = "https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src = "https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
  jQuery(document).ready(function(){
      $('#reports').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'excel'
        ]
      } );
      App.initTreeView();

  })
</script>
@stop
