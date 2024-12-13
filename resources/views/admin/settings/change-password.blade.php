@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$page_heading}}</h5>
            </div>
            <form id="admin_form" method="post" action="{{route('settings.change-password.submit')}}" enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-sm-12 mb-4">
                            @csrf()
                            <label class="form-label" for="bs-validation-name">Current Password<span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control jqv-input" data-jqv-required="true"
                                   id="current_password" name="current_password"/>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-6 mb-4">
                            <label class="form-label" for="bs-validation-name">New Password<span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control jqv-input" data-jqv-required="true" id="password"
                                   name="new_password" data-jqv-max-length="4" data-jqv-min-length="1"/>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-6 mb-4">
                            <label class="form-label" for="bs-validation-name">Confirm Password<span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control jqv-input" data-jqv-required="true"
                                   id="confirm_password" name="confirm_password"
                                   data-jqv-max-length="4" data-jqv-min-length="1"/>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="main-btn primary-btn btn-hover" disabled>
                                Submit
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
    <script>
        App.initFormView();
        let form_in_progress = 0;

        $('body').off('submit', '#admin_form');
        $('body').on('submit', '#admin_form', function (e) {
            e.preventDefault();
            var validation = $.Deferred();
            var $form = $(this);
            var formData = new FormData(this);

            $form.validate({
                rules: {},
                errorElement: 'div',
                errorPlacement: function (error, element) {
                    element.addClass('is-invalid');
                    error.addClass('error');
                    error.insertAfter(element);
                }
            });

            // Bind extra rules. This must be called after .validate()
            App.setJQueryValidationRules('#admin_form');

            if ($form.valid()) {
                validation.resolve();
            } else {
                var error = $form.find('.is-invalid').eq(0);
                $('html, body').animate({
                    scrollTop: (error.offset().top - 100),
                }, 500);
                validation.reject();
            }

            validation.done(function () {
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
                    dataType: 'html',
                    success: function (res) {
                        res = JSON.parse(res);
                        console.log(res['status']);
                        form_in_progress = 0;
                        App.button_loading(false);
                        if (res['status'] == 0) {
                            if (typeof res['errors'] !== 'undefined') {
                                var error_def = $.Deferred();
                                var error_index = 0;
                                jQuery.each(res['errors'], function (e_field, e_message) {
                                    if (e_message != '') {
                                        $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                        $('<div class="error">' + e_message + '</div>').insertAfter($('[name="' + e_field + '"]').eq(0));
                                        if (error_index == 0) {
                                            error_def.resolve();
                                        }
                                        error_index++;
                                    }
                                });
                                error_def.done(function () {
                                    var error = $form.find('.is-invalid').eq(0);
                                    $('html, body').animate({
                                        scrollTop: (error.offset().top - 100),
                                    }, 500);
                                });
                            } else {
                                var m = res['message'] || 'Unable to save variation. Please try again later.';
                                App.alert(m, 'Oops!', 'error');
                            }
                        } else {
                            App.alert(res['message'] || 'Record saved successfully', 'Success!', 'success');


                        }

                    },
                    error: function (e) {
                        form_in_progress = 0;
                        App.button_loading(false);
                        console.log(e);
                        App.alert("Network error please try again", 'Oops!', 'error');
                    }
                });
            });
        });
    </script>
@stop