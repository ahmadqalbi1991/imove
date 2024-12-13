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
                      @if(request()->get('from') && request()->get('to'))

                      <table class="datatables-ajax table table-condensed" data-role="ui-datatable" data-src="{{route('getearningList',['from' => $from,'to' => $to])}}" >
                      @else
                        <table class="datatables-ajax table table-condensed" data-role="ui-datatable" data-src="{{route('getearningList')}}" >
                      @endif
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="id">#</th>
                                <th class="pt-0" data-colname="booking_number"> Request#</th>
                                <th class="pt-0" data-colname="customer_name"> Customer Name</th>
                                <th class="pt-0" data-colname="qouted_amount"> Company Quote</th>
                                <th class="pt-0" data-colname="earned_amount"> Earned Amount</th>
                                <th class="pt-0" data-colname="comission_amount"> Commission %</th>
                                <th class="pt-0" data-colname="booking_status"> Request Status</th>
                                <th class="pt-0" data-colname="created_at"> Created At</th>
                                <th class="pt-0" data-colname="action">Action</th>
                            </tr>
                          </thead>
                          <tbody>
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
<script>
  jQuery(document).ready(function(){

      App.initTreeView();

  })
</script>
@stop
