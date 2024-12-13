@extends("admin.template.layout")

@section('content')
<style>
  .dataTables_filter{
    max-width: 50%;
    float: right;
  }
  .dataTables_length{
    float: left;
    width: 50%;
  }
</style>
<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Ajax Sourced Server-side -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>

    </div>
    <div class="card-body">
      <div class="card-datatable text-nowrap">
        <div class="table-responsive">
          <table class="datatables-ajax table table-condensed" data-searching="true" data-role="ui-datatable" data-src="{{route('getReviewList')}}">
            <thead>
              <tr>
                <th class="pt-0" data-colname="id">#</th>
                <th class="pt-0" data-colname="booking_id">Request Number</th>
                <th class="pt-0" data-colname="customer_name">Customer</th>
                <th class="pt-0" data-colname="comment">Comment</th>
                <th class="pt-0" data-colname="rate">Rate</th>
                <th class="pt-0" data-colname="status">Status</th>
                <!-- <th class="pt-0" data-colname="updated_by">Updated By</th> -->
                <th class="pt-0" data-colname="created_at">Created on</th>
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
  jQuery(document).ready(function() {

    App.initTreeView();

  })
</script>
@stop