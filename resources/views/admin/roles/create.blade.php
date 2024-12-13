@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('user_roles.submit')}}" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <input type = "hidden" name="is_admin_role" value = "1"> 
                                @csrf()
                                <input type="hidden" name="id" value="{{$id}}">
                                <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Name<span class="text-danger">*</span> </label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="role"
                                    name="role"
                                    value="{{$role_name}}"
                                />
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="form-label">Role Status<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true" name="status">
                                    <option @if($status=='active') selected @endif value="active">Active</option>
                                    <option @if($status=='inactive') selected @endif value="inactive">InActive</option>
                                </select>
                            </div>
                            <div class="col-12 mb-4 mt-4">    
                            <button type="submit" class="main-btn primary-btn btn-hover" disabled>
                                    Submit
                                </button>
                            </div>

                            <di class="col-12">
                                <table class="table table-stripped table-condensed">
                                    <thead>
                                        <th>#</th>
                                        <th>Module</th>
                                        <th>Operations</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                        @php $c=0; @endphp
                                        @foreach($site_modules as $moduleKey=>$moduleValue)
                                            @php $c++; @endphp
                                            <tr>
                                                <td>{{$c}}</td>
                                                <td>{{$moduleValue['name']}}</td>
                                                <td>
                                                    <input type="checkbox" class="all-select" style="width: fit-content;" value="1"> <label style="display: inline-block; margin-right: 20px;" class="mb-0" for="">All</label>
                                                    @foreach($moduleValue['operations'] as $operationKey)
                                                    <!-- <div class="mt-1 mb-1"> -->
                                                        @php
                                                            $options = json_decode($permissions[$moduleKey]??'');
                                                        @endphp
                                                         <input type="checkbox" style="width: fit-content;" class="crud-items {{($operationKey=='r')?'reader':'all-p'}}" name="permission[{{$moduleKey}}][]" @if(in_array($operationKey,$options??[])) checked @endif value="{{$operationKey}}"> <label style="display: inline-block; margin-right: 20px;" class="mb-0" for="">{{$operations[$operationKey]}}</label>
                                                    <!-- </div> -->
                                                    @endforeach

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </form>
              </div>


</div>
@stop

@section('script')
<script>

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
</script>
@stop
