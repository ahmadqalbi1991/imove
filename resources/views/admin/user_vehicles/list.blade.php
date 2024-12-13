@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Ajax Sourced Server-side -->
              <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    @if(get_user_permission('countries','c'))
                    <a href="{{route('vehicles.create',$user_id)}}" class="main-btn primary-btn btn-hover btn-sm"><i class='bx bx-plus'></i> Create</a>
                    @endif
                </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                      <table class="datatables-ajax table table-condensed" id="getcostingList"  >
                      
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="id">#</th>
                                <th class="pt-0" data-colname="vehicle_name">Country Name</th>
                               
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
  jQuery(document).ready(function(){

      App.initTreeView();

      var user_id='{{$user_id}}';

      $(function() {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $('#getcostingList').DataTable({
              processing: true,
              serverSide: true,
              ajax: {
                  url: "{{route('getvehicleList',$user_id)}}",
                  type: 'POST',
              },
              columns: [
                  { data: 'id', name: 'id', orderable: false,searchable: false,searchable: false },
                  { data: 'vehicle_name', name: 'vehicle_name', orderable: false,searchable: false },
                  { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
                  { data: 'action', name: 'action', orderable: false, searchable: false }
              ],
          });
      });

  })
</script>
@stop
