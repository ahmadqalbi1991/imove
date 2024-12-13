@extends("admin.template.layout")
@section('header')
    <style>
        .load{
            display: none;
        }
        .load-assign{
            display: none;
        }

        .main-btn:disabled {
          background: #dddddd;
        }

    </style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  
              <!-- Ajax Sourced Server-side -->
              <div class="card">
                <div class="card-header justify-content-between">
                    <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>
                    
                </div>
                <div class="card-body">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive">
                      <table class="datatables-ajax table table-condensed" data-role="ui-datatable" data-src="{{route('getBookingQouteList',['id' => $id])}}" >
                        <thead>
                            <tr>
                                <th class="pt-0" data-colname="id">Sr.</th>
                                <!-- <th class="pt-0" data-colname="check_all" data-orderable="false">
                                  <input type = "checkbox" name = "all" id = "all" >
                                </th> -->
                                <th class="pt-0" data-colname="company_name"> Company</th>
                                <th class="pt-0" data-colname="qouted_amount"> Quoted Amount</th>
                                <th class="pt-0" data-colname="hours"> Total Hours</th>
                                <th class="pt-0" data-colname="qoute_status"> Quote Status</th>
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

          <div class="modal fade" id="assign-drivers-modals" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Select Drivers to Assign in Booking Request</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <input type = "text" name = "search" id = "search" class = "form-control" placeholder="Search by name or email" >
                  <form action = "{{ route('bookings.assign.drvivers',['id' => $id]) }}" id = "assign_drivers_form" method = "POST">
                    @csrf
                    <table class = "table table-bordered" id = "drivers">
                      <tr>
                        <th><input type = "checkbox" name = "select_all_drivers" id = "select-all"></th>
                        <th>Drivers Name (Email)</th>
                      </tr>
                      @foreach($drivers as $driver)
                      <tr>
                        <td><input type = "checkbox" name = "drivers[]" class = "select-driver" value = "{{$driver->user_id}}" required = "required"></td>
                        <td>{{ $driver->name."(". $driver->email.")"}}</td>
                      </tr>
                      @endforeach
                    </table>

                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="main-btn primary-btn btn-hover btn-sm" id = "assign" onclick="$('#assign_drivers_form').submit()">Assign
                    <span class="spinner-border spinner-border-sm load-assign" role="status" aria-hidden="true"></span>
                          <span class="sr-only load">Loading...</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
  @stop
  @section('script')
  <script>
    jQuery(document).ready(function(){
        App.initTreeView();

        $("#assign_drivers_form").validate();


        $("#search").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#drivers tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });  

        $('[id]').each(function (i) {
          $('[id="' + this.id + '"]').slice(1).remove();
        });

        $(document).on('click','#select-all',function(){
          if($(this).is(":checked")){
            $('.select-driver').prop('checked', true);
          }
          else{
            $('.select-driver').prop('checked', false);
          }
        })


        $(document).on('click','.select-driver',function(){
            
            let totalCheckboxes = ($('.select-driver').length);
            
            let numberOfChecked = $('.select-driver:checked').length;

            console.log('totalCheckboxes'+totalCheckboxes+'=numberOfChecked'+numberOfChecked)
            if(totalCheckboxes == numberOfChecked){
               $('#select-all').prop('checked', true); 
            }else{
              $('#select-all').prop('checked', false);
            }          
        })


        $(document).on('click','#all',function(){
          
          if($(this).is(":checked")){
            $('.check_all').prop('checked', true);
          }
          else{
            $('.check_all').prop('checked', false);
          }
        })


        $(document).on('click','.check_all',function(){
            
            let totalCheckboxes = $('.check_all').length;
            
            let numberOfChecked = $('.check_all:checked').length;

            if(totalCheckboxes == numberOfChecked){
               $('#all').prop('checked', true); 
            }else{
              $('#all').prop('checked', false);
            }          
        })


    })


    // $(document).on(ready(function(){
      
    //   $('#select-all').click(function(event) {
        
    //     if ($(this).is(":checked")); {
    //       alert('checked')
    //       //$('.select-driver').prop('checked', true);
    //     }
    //     else{
    //       alert('unchecked')
    //       $('.select-driver').prop('checked', false);
    //     } 
    //   });

    // })

    $(document).on('click','#submit',function(){
      let ids = [];
      $(".check_all:checked").each(function(){
          ids.push($(this).val());
      }); 
      let booking_id = "{{ $id }}";
      if(ids.length > 0){
        $.ajax({
          url:"{{ route('approve.qoutes') }}",
          type:"POST",
          data:{ids:ids,booking_id:booking_id,'_token':"{{ csrf_token() }}"},
          beforeSend: function() {
              $("#submit").attr('disabled','disabled');
              $('.load').show();
          },
          success:function(res){
            res = JSON.parse(res);
            App.alert(res['message']||'The Following Quotes have been approved and sent to customer', 'Success!','success');
                  if(res['status'] != '0'){
                    setTimeout(function(){
                        window.location.href = res['oData']['redirect'];
                    },2500);
                  }
          },
          error: function (e) {
            console.log(e);
            App.alert( "Sorry The Following Quotes could not approved", 'Oops!','error');
          },
          complete: function() {
              $("#submit").removeAttr('disabled');
              $('.load').hide();
          },
        })
      }else{
        App.alert( "Atleast select one Quote to approve", 'Oops!','error');
      }

    })




    $(document).on('submit','#assign_drivers_form',function(e){
      e.preventDefault();

      let ids = [];
      $(".select-driver:checked").each(function(){
          ids.push($(this).val());
      }); 
      let booking_id = "{{ $id }}";
      if(ids.length > 0){
        $.ajax({
          url:"{{ route('bookings.assign.drvivers',['id' => $id]) }}",
          type:"POST",
          data:{drivers:ids,'_token':"{{ csrf_token() }}"},
          beforeSend: function() {
              $("#assign").attr('disabled','disabled');
              $('.load-assign').show();
          },
          success:function(res){
            res = JSON.parse(res);
              $('#modal').modal('hide');
            
                  if(res['status'] != '0'){
                    setTimeout(function(){
                      App.alert(res['message']||'Drivers assigned successfully', 'Success!','success');
                      location.reload(true);
                    },2500);
                  }
          },
          error: function (e) {
            console.log(e);
            App.alert( "Sorry Drivers could not be assigned", 'Oops!','error');
          },
          complete: function() {
              $("#assign").removeAttr('disabled');
              $('.load-assign').hide();
          },
        })
      }else{
        App.alert( "Atleast select one driver to assign", 'Oops!','error');
      }

    })
  </script>
  @stop
