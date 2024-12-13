@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('notification.submit')}}" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">

                        <div class="col-12">

                                @csrf()
                                <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Title</label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="title"
                                    name="title"
                                />

                                </div>

                            <div class="mb-3">

                                <label class="form-label" for="bs-validation-name">Description<span class="text-danger">*</span> </label>
                                <textarea name="desc" id="desc" rows="6"></textarea>

                            </div>



                             <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Select Image <span class="text-danger"></span></label>
                                    <input type = "file" name = "image" class = "form-control-plaintext" >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Status<span class="text-danger">*</span></label>
                                    <select class="form-control jqv-input" data-jqv-required="true" name="status">
                                        <option  value="active">Active</option>
                                        <option  value="inactive">InActive</option>
                                    </select>
                                </div>

                             </div>




                            <!-- Button trigger modal -->

                                <!-- Modal -->
                                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Select users to send notification</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                                    <select name="usertype" id="selectOption">
                                                        <option value="0">All</option>
                                                        <option value="3">Users</option>
                                                        <option value="2">Driver</option>
                                                    </select>
                                                    <label for="search" class="mt-2"  >Search</label>
                                                    <input type="text" id="search"name="search" placeholder="Search...">


                                            <table class="table table-striped table-hover mt-3" id = "">
                                                <thead>
                                                    <tr>
                                                    <th>
                                                        <span class="custom-checkbox">
                                                            <input type="checkbox"  id="check-all">
                                                            <label for="selectAll"></label>
                                                        </span>
                                                    </th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableData">

                                                    @foreach($users as $user)
                                                        <tr>
                                                            <td>
                                                                <span class="custom-checkbox">
                                                                    <input type="checkbox"  name="options[]" value="{{ $user->id }}" class = "select-customers">
                                                                    <label for="checkbox1"></label>
                                                                </span>
                                                            </td>
                                                            <td>{{$user->name}}</td>
                                                            <td>{{$user->email}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>




                                                <!-- <tbody id="tableData">
                                                </tbody> -->

                                                
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="main-btn primary-btn btn-hover" id = "assign" disabled >Push</button>
                                        </div>
                                        </div>
                                    </div>
                                </div>





                            <div class="mt-3">




                                <button type="button" class="main-btn primary-btn btn-hover"  data-toggle="modal" data-target=".bd-example-modal-lg">
                                    Submit
                                </button>

                            </div>

                        </div>
                </form>
              </div>




</div>
@stop

@section('script')
<script>
     $("#selectOption").on('change', function(){
      var option = $(this).val();
      $.ajax({
            method: 'get',
            url:'{{ route('getListUser')}}',
            data:{"_token": "{{ csrf_token() }}",
            option:option},
            success:function(data){
                $('#tableData').html(data);
                $('#check-all').prop('checked', false);
            }
        }); 

     });

     $("#selectSearch").keyup(function(){
      var search = $(this).val();
      var option = $('#selectOption').val();

      $.ajax({
            method: 'get',
            url:'{{ route('getSearchUser')}}',
            data:{"_token": "{{ csrf_token() }}",
            search:search , option:option},
            success:function(data){
                $('#tableData').html(data);
                $('#check-all').prop('checked', false);
                
            }
        }); 
    });




    $('.all-select').click(function(){
        $(this).siblings('.crud-items').prop('checked', this.checked);
    });
    $('.crud-items').click(function(){
        $(this).siblings('.all-select').prop('checked', false);
    });
    $('.all-p').click(function(){
        $(this).siblings('.reader').prop('checked', true);
    });
    App.initFormView();
    let form_in_progress=0;

    $('body').off('submit', '#admin_form');
    $('body').on('submit', '#admin_form', function(e) {
        e.preventDefault();
        var validation = $.Deferred();
        var $form = $(this);
        var formData = new FormData(this);

        $form.validate({
            rules: {

            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                element.addClass('is-invalid');
                error.addClass('error');
                error.insertAfter(element);
            }
        });

        // Bind extra rules. This must be called after .validate()
        App.setJQueryValidationRules('#admin_form');

        if ( $form.valid() ) {
            validation.resolve();
        } else {
            var error = $form.find('.is-invalid').eq(0);
            $('html, body').animate({
                scrollTop: (error.offset().top - 100),
            }, 500);
            validation.reject();
        }

        validation.done(function() {
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('div.error').remove();


            App.button_loading(true);


            form_in_progress = 1;
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: $form.attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                dataType:'html',
                success: function (res) {
                    res = JSON.parse(res);
                    console.log(res['status']);
                    form_in_progress = 0;
                    App.button_loading(false);
                    if ( res['status'] == 0 ) {
                        if ( typeof res['errors'] !== 'undefined' ) {
                            var error_def = $.Deferred();
                            var error_index = 0;
                            jQuery.each(res['errors'], function (e_field, e_message) {
                                if ( e_message != '' ) {
                                    $('[name="'+ e_field +'"]').eq(0).addClass('is-invalid');
                                    $('<div class="error">'+ e_message +'</div>').insertAfter($('[name="'+ e_field +'"]').eq(0));
                                    if ( error_index == 0 ) {
                                        error_def.resolve();
                                    }
                                    error_index++;
                                }
                            });
                            error_def.done(function() {
                                var error = $form.find('.is-invalid').eq(0);
                                                $('html, body').animate({
                                                    scrollTop: (error.offset().top - 100),
                                                }, 500);
                                            });
                        } else {
                            var m = res['message']||'Unable to save variation. Please try again later.';
                            App.alert(m, 'Oops!','error');
                        }
                    } else {
                        App.alert(res['message']||'Record saved successfully', 'Success!','success');
                        setTimeout(function(){
                            window.location.href = res['oData']['redirect'];
                        },2500);

                    }

                },
                error: function (e) {
                    form_in_progress = 0;
                    App.button_loading(false);
                    console.log(e);
                    App.alert( "Network error please try again", 'Oops!','error');
                }
            });
        });
    });

$(document).ready(function(){
    $(document).on('click','#check-all',function(){
    
    let checkboxes = $('.select-customers'); // Selected checkboxes ka collection
    let totalCheckboxes = checkboxes.length; // Load hone wali checkboxes ki sankhya
    
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
      $('#check-all').prop('checked', true);
    } else {
      $('#check-all').prop('checked', false);
    }
  });
})



$(document).ready(function(){
	// Activate tooltip
	$('[data-toggle="tooltip"]').tooltip();
	
	// Select/Deselect checkboxes
	// var checkbox = $('.checkbox2');
	// $(document).on('click','#check-all',function(){
	// 	if(this.checked){
	// 		checkbox.each(function(){
	// 			this.checked = true;                        
	// 		});
	// 	} else{
	// 		checkbox.each(function(){
	// 			this.checked = false;                        
	// 		});
	// 	} 
	// });
    // $(document).on('click','.checkbox2',function(){
	// 	if(!this.checked){
	// 		$("#selectAll").prop("checked", false);
	// 	}
	// });
});


// const checkAllCheckbox = document.querySelector('#check-all');
// const checkboxes = document.querySelectorAll('input[type="checkbox"]');

// checkAllCheckbox.addEventListener('click', () => {
//   checkboxes.forEach(checkbox => {
//     checkbox.checked = checkAllCheckbox.checked;
//   });
// });




    $("#search").on("keyup", function() {
          var value = $(this).val().toLowerCase();

          $("#tableData tr").filter(function() {
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



</script>
@stop
