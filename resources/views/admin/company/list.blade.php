@extends('admin.template.layout')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Ajax Sourced Server-side -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $mode ?? $page_heading }}</h5>
                {{-- @if (get_user_permission('company', 'c'))
                    <a href="{{route('company.create')}}" class="main-btn primary-btn btn-hover btn-sm"><i class='bx bx-plus'></i> Create</a>
                    @endif --}}
            </div>
            <div class="card-body">
                <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                        <table class="datatables-ajax table table-condensed" table-order="true" data-role="ui-datatable"
                            data-src="{{ route('getCompanyList') }}">
                            <thead>
                                <tr>
                                    <th class="pt-0" data-colname="id">#</th>
                                    <th class="pt-0" data-colname="company_name">Company Name</th>
                                    <!-- <th class="pt-0" data-colname="ratings">Ratings</th>
                                                <th class="pt-0" data-colname="total_requests">Requests Delivered</th> -->
                                    <!-- <th class="pt-0" data-colname="account_type">Account Type</th> -->
                                    <th class="pt-0" data-colname="company_email">Email</th>
                                    <th class="pt-0" data-colname="phone">Phone</th>
                                    <th class="pt-0" data-colname="status">Status</th>
                                    <th class="pt-0" data-colname="status_text">Status</th>
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

            $(document).on('click', '.unapproved', function() {
                App.alert('The company status cannot be changed until it is approved');
            })
        })
    </script>
@stop