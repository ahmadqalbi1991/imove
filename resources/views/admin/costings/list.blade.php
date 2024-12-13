@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Ajax Sourced Server-side -->
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    @if(get_user_permission('costings','c'))
                    <a href="{{route('costings.create')}}" class="main-btn primary-btn btn-hover btn-sm"><i class='bx bx-plus'></i> Create</a>
                    @endif
                  </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                      <table class="datatables-ajax table table-condensed" id="getcostingList"  >
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="id">#</th>
                                <th class="pt-0" data-colname="category_name">Category</th>
                                <th class="pt-0" data-colname="size_name">Size</th>
                                <th class="pt-0" data-colname="delivery_type">Delivery Type</th>
                                <th class="pt-0" data-colname="cost">Cost</th>
                                <th class="pt-0" data-colname="status">Status</th>
                                <th class="pt-0" data-colname="created_at">Created Date</th>
                                <th  class="pt-0" data-colname="action">Action</th>
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
                  url: "{{ route('getcostingList') }}",
                  type: 'POST',
              },
              columns: [
                  { data: 'id', name: 'id', orderable: false,searchable: false,searchable: false },
                  { data: 'category_name', name: 'categories.name', orderable: false,searchable: false },
                  { data: 'size_name', name: 'sizes.name', orderable: false,searchable: false },
                  { data: 'delivery_type', name: 'delivery_type', orderable: false ,searchable: false},
                  { data: 'cost', name: 'cost', orderable: false, searchable: false },
                  { data: 'status', name: 'status', orderable: false, searchable: false },
                  { data: 'created_at', name: 'created_at', orderable: false, searchable: false },
                  { data: 'action', name: 'action', orderable: false, searchable: false }
              ],
          });
      });
  })
</script>
@stop


