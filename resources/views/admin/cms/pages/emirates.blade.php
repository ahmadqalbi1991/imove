@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>

            </div>
            <form method="post" action="{{route('cms.emirates.save')}}" id="admin_form"
                  enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">

                        @csrf
                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Abu Dhabi</label>
                                <input type="hidden" value="Abu Dhabi" name="state[]">
                                <input type="hidden" value="{{$datamain[0]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]"  oninput="validateNumber(this);" value="{{$datamain[0]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Dubai</label>
                                <input type="hidden" value="Dubai" name="state[]">
                                <input type="hidden" value="{{$datamain[1]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]"  oninput="validateNumber(this);" value="{{$datamain[1]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Sharjah</label>
                                <input type="hidden" value="Sharjah" name="state[]">
                                <input type="hidden" value="{{$datamain[2]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]" id="tax_percentage" oninput="validateNumber(this);" value="{{$datamain[2]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Umm Al Qaiwain </label>
                                <input type="hidden" value="Umm Al Qaiwain" name="state[]">
                                <input type="hidden" value="{{$datamain[3]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]" id="tax_percentage" oninput="validateNumber(this);" value="{{$datamain[3]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Fujairah </label>
                                <input type="hidden" value="Fujairah" name="state[]">
                                <input type="hidden" value="{{$datamain[4]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]" id="tax_percentage" oninput="validateNumber(this);" value="{{$datamain[4]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Ajman </label>
                                <input type="hidden" value="Ajman" name="state[]">
                                <input type="hidden" value="{{$datamain[5]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]" id="tax_percentage" oninput="validateNumber(this);" value="{{$datamain[5]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Ras Al Khaimah </label>
                                <input type="hidden" value="Ras Al Khaimah" name="state[]">
                                <input type="hidden" value="{{$datamain[6]->id??''}}" name="sl_id[]">
                            </div>

                            <div class=" col-md-6 form-group mb-2">
                               
                                <input type="text" name="amount[]" id="tax_percentage" oninput="validateNumber(this);" value="{{$datamain[6]->amount??''}}"
                                       class="form-control jqv-input"  data-jqv-required="true" required>
                            </div>
                           
                        <div class="col-md-12 form-group text-center">
                            <button type="submit" class="main-btn primary-btn btn-hover" >
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
                                    
                                });
                            } else {
                                var m = res['message'] || 'Unable to save variation. Please try again later.';
                                App.alert(m, 'Oops!', 'error');
                            }
                        } else {
                            App.alert(res['message'] || 'Record saved successfully', 'Success!', 'success');
                            setTimeout(function () {
                                window.location.href = res['oData']['redirect'];
                            }, 2500);

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

        var validNumber = new RegExp(/^\d*\.?\d*$/);
var lastValid = 0;
function validateNumber(elem) {
  if (validNumber.test(elem.value)) {
    lastValid = elem.value;
  } else {
    elem.value = lastValid;
  }
}
    </script>
@stop
