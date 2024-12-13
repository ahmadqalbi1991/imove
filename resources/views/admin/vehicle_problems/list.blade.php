@extends("admin.template.layout")

@section("header")
    <link rel="stylesheet" type="text/css" href="{{asset('')}}admin-assets/plugins/table/datatable/datatables.css">
    <link rel="stylesheet" type="text/css" href="{{asset('')}}admin-assets/plugins/table/datatable/custom_dt_customer.css">
@stop


@section("content")
<div class="card mb-5">
    @if(check_permission('country','Create'))
    <div class="card-header"><a href="{{url('admin/vehilce/problems/create')}}" class="btn-custom btn mr-2 mt-2 mb-2"><i class="fa-solid fa-plus"></i> Create Vehicle Problem</a></div>
    @endif
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-condensed table-striped" id="example2">
            <thead>
                <tr>
                <th>#</th>
                <th>Name</th>
                <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($problems as $problem)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $problem->title }}</td>
                    <td class="text-center">
                        <div class="dropdown custom-dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="flaticon-dot-three"></i>
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink7">
                                    <a class="dropdown-item" href="{{url('admin/vehilce/problems/edit/'.$problem->id)}}"><i class="flaticon-pencil-1"></i> Edit</a>
                                    <a class="dropdown-item" data-role="unlink"
                                       data-message="Do you want to remove this Problem?"
                                       href="{{ url('admin/vehilce/problems/delete/' . $problem->id) }}"><i
                                                class="flaticon-delete-1"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@stop

@section("script")
<script src="{{asset('')}}admin-assets/plugins/table/datatable/datatables.js"></script>
<script>
$('#example2').DataTable({
    "paging": true,
    "searching": true,
    "ordering": true,
    "info": true, 
    "autoWidth": true,
    "responsive": true,
    "columnDefs": [
        {
            "targets": [2,4], // Column index of the "Action" column (zero-based)
            "orderable": false
        }
    ]
});
    </script>
@stop