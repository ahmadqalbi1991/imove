@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  
              <!-- Ajax Sourced Server-side -->
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    @if(get_user_permission('users','c'))
                    <a href="{{route('users.create')}}" class="main-btn primary-btn btn-hover btn-sm"><i class='bx bx-plus'></i> Create</a>
                    @endif
                </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                      <table class="datatables-ajax table table-condensed" data-role="ui-datatable" data-src="{{route('getuserList')}}" >
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="id">#</th>
                                <th class="pt-0" data-colname="name"> Name</th>
                                <th class="pt-0" data-colname="role_name"> Role Type</th>
                                <th class="pt-0" data-colname="email">email</th>
                                <th class="pt-0" data-colname="phone"> Phone</th>
                                <th class="pt-0" data-colname="user_status"> Status</th>
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

  })
</script>
@stop