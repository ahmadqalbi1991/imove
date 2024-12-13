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
  /* Add your custom CSS here */
.ticket {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
}
.active {
    color: green;
}
.disabled {
    color: red;
}
</style>

<div class="container-xxl flex-grow-1 container-p-y">

  <!-- Ajax Sourced Server-side -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>


      {{--
                    @if(get_user_permission('customers','c'))
                    <a href="{{route('export.csv')}}" class="main-btn primary-btn p-3 m-1 btn-hover btn-sm ml-auto"><i class='bx bx-plus'></i>Download CSV</a>
      @endif


      @if(get_user_permission('customers','c'))
      <a href="{{route('customers.create.data')}}" class="main-btn primary-btn p-3 m-1 btn-hover btn-sm float-right"><i class='bx bx-plus'></i> Import CSV</a>
      @endif
      --}}
      @if(get_user_permission('customers','v'))
      <a href="{{route('customer.detail.view')}}" class="main-btn primary-btn p-3 m-1 btn-hover btn-sm float-right"><i class='bx bx-plus'></i> Create</a>
      @endif




    </div>
    <div class="card-body">
      <div class="card-datatable text-nowrap">
        <div class="table-responsive">
          <table class="datatables-ajax table table-condensed table-" data-searching="true" data-role="ui-datatable" data-src="{{route('getcustomerTotalList')}}">
            <thead>
              <tr>
                <th class="pt-0" data-colname="id">#</th>
                <th class="pt-0" data-colname="name" data-orderable="true">Customer Name</th>
                <th class="pt-0" data-colname="phone">Mobile No</th>
                <th class="pt-0" data-colname="status">Status</th>
                
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

  });


  $(document).ready(function() {
    
    // Radio button click event handler
    $('input[type="checkbox"]').on('change', function() {
        var status = $(this).val();
        console.log('sdfsd');
        console.log(status);
        updateTicketStatus(status);
    });

    // Function to update the ticket status display
    function updateTicketStatus(status) {
        var $ticketIcon = $('<div>').addClass('ticket-icon');
        if (status === 'active') {
            $ticketIcon.addClass('active').text('Active');
        } else if (status === 'disabled') {
            $ticketIcon.addClass('disabled').text('Disabled');
        }
        $('td').html($ticketIcon);
    }
});

</script>
@stop