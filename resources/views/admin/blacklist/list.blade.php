@extends("admin.template.layout")
<style>

  div.dataTables_wrapper div.dataTables_filter input {
      width: 100%; 
  }

  .dataTables_length, .dataTables_filter{
    display:inline-block;
  }
  .dataTables_filter{
    float:right;
  }

  .active-tab{
    display:block
  }

  .inactive-tab{
    display:none
  }
  .main-btn:disabled {
          background: #dddddd;
  }

</style>
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Ajax Sourced Server-side -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-right">
      @if(get_user_permission('blacklists','c'))
      <button type="button" class="main-btn primary-btn btn-hover btn-sm remove" disabled>
        <i class='bx bx-minus'></i> Remove
      </button>
      <button type="button" class="main-btn primary-btn btn-hover btn-sm add" data-toggle="modal" data-target="#block-users-modals">
        <i class='bx bx-plus'></i> Add
      </button>
      @endif
    </div>
    <div class="card-body">
      <div class="card-datatable text-nowrap">
        <div class="table-responsive">
          <table class="datatables-ajax table table-condensed"
            data-role="ui-datatable"
            data-src="{{ route('getblackList') }}"
            data-searching="true">
            <thead>
              <tr>
                <th class="pt-0" data-colname="id">#</th>
                <th class="pt-0" data-colname="check_all" data-orderable="false">
                  <input type="checkbox" name="all" id="all">
                </th>
                <th class="pt-0" data-colname="name">Name</th>
                <th class="pt-0" data-colname="role_name">Role Type</th>
                <th class="pt-0" data-colname="email">Email</th>
                <th class="pt-0" data-colname="phone">Phone</th>
                <th class="pt-0" data-colname="user_device_id">Device Id</th>
                <th class="pt-0" data-colname="created_at" data-searchable="false">Created on</th>
                <th class="pt-0" data-colname="action">Action</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!--/ Ajax Sourced Server-side -->
</div>

        <div class="modal fade bd-example-modal-lg" id="block-users-modals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Select Users to Add in Blacklist</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body" style = "width:100%" >

                  <ul class="nav nav-tabs mb-3" id="ex1" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button
                          class="nav-link active"
                          onclick="openTab(event, 'ex1-content')"
                          >Company</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button
                          class="nav-link"
                          onclick="openTab(event, 'ex2-content')"
                          >Driver</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button
                          class="nav-link"
                          onclick="openTab(event, 'ex3-content')"
                          >Customer</button>
                      </li>
                  </ul>
                    <!-- Tabs navs -->
                    <input type = "text" name = "search" id = "search" class = "form-control" placeholder="Search by name or email" >
                    <form action = "{{ route('add.all.blacklists') }}" id = "add_blacklist_form" method = "POST"> 
                      @csrf
                      <!-- Tabs content -->
                      <div class="tab-content active-tab" id="ex1-content">

                        <table class = "table table-bordered search-user" id = "companies">
                          <thead>  
                            <tr>
                              <th><input type = "checkbox"  class = "select-all-companies"></th>
                              <th>Name</th>
                              <th>Role Type</th>
                              <th>Email</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($companies as $company)
                            <tr>
                              <td><input type = "checkbox" name = "add_users[]" class = "select-companies" value = "{{$company->id}}" required = "required"></td>
                              <td>{{ $company->name }}</td>
                              <td>{{ $company->role_name }}</td>
                              <td>{{ $company->email }}</td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>

                      <div class="tab-content inactive-tab" id="ex2-content">
                        <table class = "table table-bordered search-user" id = "drivers">
                        <thead>  
                            <tr>
                              <th><input type = "checkbox" class = "select-all-drivers"></th>
                              <th>Name</th>
                              <th>Role Type</th>
                              <th>Email</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($drivers as $driver)
                            <tr>
                              <td><input type = "checkbox" name = "add_users[]" class = "select-drivers" value = "{{$driver->id}}" required = "required"></td>
                              <td>{{ $driver->name }}</td>
                              <td>{{ $driver->role_name }}</td>
                              <td>{{ $driver->email }}</td>
                            </tr>
                            @endforeach
                          </tbody>
                        </table>
                      </div>  

                      <div class="tab-content inactive-tab" id="ex3-content">
                        <table class = "table table-bordered search-user" id = "customers">
                        <thead>  
                            <tr>
                              <th><input type = "checkbox" class = "select-all-customers"></th>
                              <th>Name</th>
                              <th>Role Type</th>
                              <th>Email</th>
                            </tr>
                          </thead>
                          <tbody>
                          @foreach($customers as $customer)
                          <tr>
                            <td><input type = "checkbox" name = "add_users[]" class = "select-customers" value = "{{$customer->id}}" required = "required"></td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->role_name }}</td>
                            <td>{{ $customer->email }}</td>
                          </tr>
                          @endforeach
                          </tbody>
                        </table>
                      </div>

                      <!-- Tabs content -->  
                    </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="main-btn primary-btn btn-hover btn-sm"  id = "assign" onclick="$('#add_blacklist_form').submit()" disabled>Blacklist
                    <span class="spinner-border spinner-border-sm load-assign" style = "display:none" role="status" aria-hidden="true"></span>
                          <span class="sr-only load">Loading...</span>
                  </button>
                </div>
              </div>
            </div>
          </div>


@stop
@section('script')
<script>
    
  jQuery(document).ready(function() {

    // Initialize TreeView
    App.initTreeView();
    
    $(document).on('click', '#all', function() {
      let checkboxes = $('.check_all'); // Selected checkboxes ka collection
      let totalCheckboxes = (checkboxes.length) / 2; // Load hone wali checkboxes ki sankhya
      
      if ($(this).is(':checked')) {
        // Select All checkbox checked hai
        checkboxes.prop('checked', true); // Sabhi checkboxes ko select karein
        $('.remove').prop('disabled', false);
      } else {
        // Select All checkbox unchecked hai
        checkboxes.prop('checked', false); // Sabhi checkboxes ko deselect karein
        $('.remove').prop('disabled', true);
      }
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
      var checkedCount = checkboxes.filter(':checked').length;
      
      if (checkedCount > 0) {
        // Select All checkbox checked hai
        $('.remove').prop('disabled', false);
      } else {
        // Select All checkbox unchecked hai
        $('.remove').prop('disabled', true);
      }

      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
      if (checkedCount > totalCheckboxes) {
        checkboxes.filter(':checked').slice(totalCheckboxes).prop('checked', false);
      }

      console.log('total='+totalCheckboxes);
      console.log('checked='+checkedCount);
    });
    
    // Check if all checkboxes are selected
    $(document).on('click', '.check_all', function() {
      let totalCheckboxes = $('.check_all').length;
      let numberOfChecked = $('.check_all').filter(':checked').length;
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
      if (numberOfChecked > totalCheckboxes) {
         $('.check_all').filter(':checked').slice(totalCheckboxes).prop('checked', false);
      }

      if (numberOfChecked > 0) {
        $('.remove').prop('disabled', false);
      } else {
        $('.remove').prop('disabled', true);
      }

      if (totalCheckboxes == numberOfChecked) {
        $('#all').prop('checked', true);
      } else {
        $('#all').prop('checked', false);
      }
    });

    // Remove button click event
    $(document).on('click', '.remove', function() {
      let ids = [];

      $(".check_all:checked").each(function() {
        if (!ids.includes($(this).val())) {
          ids.push($(this).val());
        }
      });

      if (ids.length > 0) {
        $.ajax({
          url:"{{ route('remove.all.blacklists') }}",
          type:"POST",
          data:{'_token':"{{ csrf_token() }}",ids:ids},
          success:function(res){
            res = JSON.parse(res);
            console.log(res)
            App.alert(res['message'], 'Success!','success');
                        setTimeout(function(){
                            window.location.href = res['oData']['redirect'];
                        },2500);
          },
          error:function(err){
            console.log(err)
          }
        })
      } else {
        App.alert('Please select at least one user');
      }
    });
  });
</script>


<script>


$(document).on('click','.select-all-companies',function(){
    
      let checkboxes = $('.select-companies'); // Selected checkboxes ka collection
      let totalCheckboxes = (checkboxes.length) / 2; // Load hone wali checkboxes ki sankhya
      
      if ($(this).is(':checked')) {
        // Select All checkbox checked hai
        checkboxes.prop('checked', true); // Sabhi checkboxes ko select karein
      } else {
        // Select All checkbox unchecked hai
        checkboxes.prop('checked', false); // Sabhi checkboxes ko deselect karein
      }
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
      var checkedCount = checkboxes.filter(':checked').length;

      if (checkedCount > 0) {
        $('#assign').prop('disabled', false);
      } else {
        $('#assign').prop('disabled', true);
      }
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
      if (checkedCount > totalCheckboxes) {
        checkboxes.filter(':checked').slice(totalCheckboxes).prop('checked', false);
      }

      console.log('total='+totalCheckboxes);
      console.log('checked='+checkedCount);
  
});

// Check if all checkboxes are selected
$(document).on('click', '.select-companies', function() {
      let totalCheckboxes = $('.select-companies').length;
      let numberOfChecked = $('.select-companies').filter(':checked').length;
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
      if (numberOfChecked > totalCheckboxes) {
         $('.select-companies').filter(':checked').slice(totalCheckboxes).prop('checked', false);
      }

      if (numberOfChecked > 0) {
        $('#assign').prop('disabled', false);
      } else {
        $('#assign').prop('disabled', true);
      }

      if (totalCheckboxes == numberOfChecked) {
        $('.select-all-companies').prop('checked', true);
      } else {
        $('.select-all-companies').prop('checked', false);
      }
    });

    //Drviers

    $(document).on('click','.select-all-drivers',function(){
    
    let checkboxes = $('.select-drivers'); // Selected checkboxes ka collection
    let totalCheckboxes = (checkboxes.length) / 2; // Load hone wali checkboxes ki sankhya
    
    if ($(this).is(':checked')) {
        // Select All checkbox checked hai
        checkboxes.prop('checked', true); // Sabhi checkboxes ko select karein
      } else {
        // Select All checkbox unchecked hai
        checkboxes.prop('checked', false); // Sabhi checkboxes ko deselect karein
      }
      
      // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
      var checkedCount = checkboxes.filter(':checked').length;

      if (checkedCount > 0) {
        $('#assign').prop('disabled', false);
      } else {
        $('#assign').prop('disabled', true);
      }
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
    if (checkedCount > totalCheckboxes) {
      checkboxes.filter(':checked').slice(totalCheckboxes).prop('checked', false);
    }

    console.log('total='+totalCheckboxes);
    console.log('checked='+checkedCount);

});

// Check if all checkboxes are selected
$(document).on('click', '.select-drivers', function() {
    let totalCheckboxes = $('.select-drivers').length;
    let numberOfChecked = $('.select-drivers').filter(':checked').length;
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
    
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
    if (numberOfChecked > totalCheckboxes) {
       $('.select-drivers').filter(':checked').slice(totalCheckboxes).prop('checked', false);
    }

    if (numberOfChecked > 0) {
      $('#assign').prop('disabled', false);
    } else {
      $('#assign').prop('disabled', true);
    }

    if (totalCheckboxes == numberOfChecked) {
      $('.select-all-drivers').prop('checked', true);
    } else {
      $('.select-all-drivers').prop('checked', false);
    }
  });

  //Customers

  $(document).on('click','.select-all-customers',function(){
    
    let checkboxes = $('.select-customers'); // Selected checkboxes ka collection
    let totalCheckboxes = (checkboxes.length) / 2; // Load hone wali checkboxes ki sankhya
    
    if ($(this).is(':checked')) {
        // Select All checkbox checked hai
        checkboxes.prop('checked', true); // Sabhi checkboxes ko select karein
    } else {
      // Select All checkbox unchecked hai
      checkboxes.prop('checked', false); // Sabhi checkboxes ko deselect karein
    }
    
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
    var checkedCount = checkboxes.filter(':checked').length;

    if (checkedCount > 0) {
      $('#assign').prop('disabled', false);
    } else {
      $('#assign').prop('disabled', true);
    }
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
    if (checkedCount > totalCheckboxes) {
      checkboxes.filter(':checked').slice(totalCheckboxes).prop('checked', false);
    }

    console.log('total='+totalCheckboxes);
    console.log('checked='+checkedCount);

});

// Check if all checkboxes are selected
$(document).on('click', '.select-customers', function() {
    let totalCheckboxes = $('.select-customers').length;
    let numberOfChecked = $('.select-customers').filter(':checked').length;
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya ke barabar hai
    
    // Selected checkboxes ki ginti, load hone wali checkboxes ki sankhya se zyada hai toh additional checkboxes ko deselect karein
    if (numberOfChecked > totalCheckboxes) {
       $('.select-customers').filter(':checked').slice(totalCheckboxes).prop('checked', false);
    }

    if (numberOfChecked > 0) {
      $('#assign').prop('disabled', false);
    } else {
      $('#assign').prop('disabled', true);
    }

    if (totalCheckboxes == numberOfChecked) {
      $('.select-all-customers').prop('checked', true);
    } else {
      $('.select-all-customers').prop('checked', false);
    }
  });





$("#search").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $(".search-user tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });  

function openTab(evt, tabId) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tab-content");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("nav-link");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  console.log(tabId);
  document.getElementById(tabId).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
@stop