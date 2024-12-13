@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Ajax Sourced Server-side -->
              <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    @if(get_user_permission('events','c'))
                    <a href="{{route('events.create')}}" class="main-btn primary-btn btn-hover btn-sm"><i class='bx bx-plus'></i> Create</a>
                    @endif
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                  <div class="card-datatable text-nowrap">
                    <table class="datatables-ajax table table-condensed" data-role="ui-datatable" data-src="{{route('events.datatable')}}" >
                      <thead>
                          <tr>
                              <th class="pt-0" data-colname="id">#</th>
                              <th class="pt-0" data-colname="name">Event Name</th>
                              <th class="pt-0" data-colname="date">Event Date</th>
                              <th class="pt-0" data-colname="start_time">Start Time</th>
                              <th class="pt-0" data-colname="end_time">End Time</th>
                              <th class="pt-0" data-colname="event_type_id">Event Type</th>
                              <th class="pt-0" data-colname="privacy">Privacy</th>
                              <th class="pt-0" data-colname="active">Active</th>
                              <th class="pt-0" data-colname="created_at">Created On</th>
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
