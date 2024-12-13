@extends("admin.template.layout")
@section('header')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <style>
      
        .btn-primary {
  display: none;
}
.select2-container--default .select2-selection--single {
            height: 38px;
            line-height: 36px;
        }
        .select2-selection__rendered {
            display: flex;
            align-items: center;
        }
        .flag-icon {
            margin-right: 8px;
        }
        elem{
            margin-left: 20px;
        }
        .select2-results__option elem{
            position: absolute;
        }
    </style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('customer.insert')}}" enctype="multipart/form-data">
                @csrf()
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type = "text" id="first_name" class="form-control-plaintext" name = "first_name" value ="{{$first_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type = "text" id="last_name" class="form-control-plaintext" name = "last_name" value ="{{$last_name }}">
                            </div>
                            <div class="col-xs-12 col-sm-6">


                                <div class="mb-3">
                                    <label class="form-label" for="bs-validation-name">Customer Email<span class="text-danger">*</span> </label>
                                        <input
                                            type="gmail"
                                            class="form-control jqv-input" data-jqv-required="true" 
                                            id="customer_email"
                                            name="customer_email"
                                            value="{{$email}}"
                                        />
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-2 mb-3">
                                <label class="form-label">Phone</label>
                                <select class="form-control" name="dial_code" id="dial_code" data-jqv-required="true">
                                    <option value = "">Dial Code</option>
                                    @foreach (get_countries_list() as $flag_class => $data) 
                                    @if(isset($user->country))
                                                <option value="{{ $data['dial_code'] }}" {{ $data['dial_code'] == $dial_code?'selected':'' }} data-flag-class="{{ $flag_class }}" data-flag-value="{{ $data['dial_code'] }} ">
                                                    {{ $data['dial_code'] }} 
                                                </option>
                                                @else
                                                <option value="{{ $data['dial_code'] }}" {{ $data['dial_code'] == '971'?'selected':'' }} data-flag-class="{{ $flag_class }}" data-flag-value="{{ $data['dial_code'] }} ">
                                                    {{ $data['dial_code'] }} 
                                                </option>
                                                @endif
                                            @endforeach
                                    
                                </select>
                            </div>
                            <div class="col-xs-8 col-sm-4 mb-3">
                                <label class="form-label">Number</label>
                                <input type = "text" id="phone" class="form-control-plaintext" name = "phone" value = "{{ $phone ?? '' }}" data-jqv-required="true">
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label" for="bs-validation-name">Password<span class="text-danger">*</span> </label>

                                        <input
                                            type="password"
                                            class="form-control jqv-input typing" data-jqv-required="true"
                                            name="password"
                                        />

                                </div> 
                            </div>



                                <div class="col-xs-12 col-sm-6">
                                    <label class="form-label">Customer Status<span class="text-danger">*</span></label>
                                        <select class="form-control jqv-input" data-jqv-required="true" name="status">
                                            <option @if($status=='active') selected @endif value="active">Active</option>
                                            <option @if($status=='inactive') selected @endif value="inactive">InActive</option>
                                        </select>
                                </div>


                            </div>                            
                                <!-- Address Section -->
                                <div class = "row">
                                    <div class = "col-md-12">
                                            <hr />
                                    </div>
                            
                            <div class="col-12 mt-3">
                                <button type="submit" class="main-btn primary-btn btn-hover" disabled>
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

$('#first_name').on('input', function () {
        var value = $(this).val();

        // Restrict special characters
        // This regex restricts: & = _ ' - + , < > and multiple consecutive dots
        var invalidChars = /[&=_'<>+\-,]|(\.\.)/g;
        
        // Replace invalid characters
        value = value.replace(invalidChars, '');
        
        // Trim if the length is more than 100 characters
        if (value.length > 100) {
            value = value.substring(0, 100);
        }

        // Word limit restriction (optional, e.g., 10 words max)
        var words = value.split(/\s+/);
        if (words.length > 10) {
            value = words.slice(0, 10).join(' ');
        }

        // Set the modified value back to the field
        $(this).val(value);
    });

    $('#last_name').on('input', function () {
        var value = $(this).val();

        // Restrict special characters
        // This regex restricts: & = _ ' - + , < > and multiple consecutive dots
        var invalidChars = /[&=_'<>+\-,]|(\.\.)/g;
        
        // Replace invalid characters
        value = value.replace(invalidChars, '');
        
        // Trim if the length is more than 100 characters
        if (value.length > 100) {
            value = value.substring(0, 100);
        }

        // Word limit restriction (optional, e.g., 10 words max)
        var words = value.split(/\s+/);
        if (words.length > 10) {
            value = words.slice(0, 10).join(' ');
        }

        // Set the modified value back to the field
        $(this).val(value);
    });

const inputTel = document.querySelector("#phone");
inputTel.addEventListener("input", function (e) {

let value = e.target.value.replace(/[^0-9]/g, '');

            // Prevent starting with 0
            if (value.startsWith('0')) {
                value = value.substring(1);
            }

            // Update the input field with the cleaned value
            e.target.value = value;
});

$(document).ready(function() {
    function formatState(state) {
        if (!state.id) {
            return state.text; // return the default text if no id is found
        }
        var flagClass = $(state.element).data('flag-class');
        var dialCode = state.text; // +value of dial code
        var countryName = $(state.element).text(); // full text including dial code and country name

        return $('<span class="flag-icon ' + flagClass + '"><elem>'+dialCode+'</elem></span> + ' + dialCode + ' - ' + countryName);
    }

    $('#dial_code').select2({
        templateResult: formatState,
        templateSelection: formatState,
        escapeMarkup: function(markup) { return markup; } // let our custom markup through
    })
    // Function to show or hide business fields
    function toggleBusinessFields() {
        if ($('#customer_type').val() == '2') {
            $('.business-field').show(); // Show the business fields
        } else {
            $('.business-field').hide(); // Hide the business fields
        }
    }

    // Run on page load
    toggleBusinessFields();

    // Run when the select value changes
    $('#customer_type').change(function() {
        toggleBusinessFields();
    });
});


        $(".typing").keyup(function() {
            var pass = $(this).val();
            $('.written').val(pass);
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

$.validator.addMethod("lettersOnly", function(value, element) {
    return this.optional(element) || /^[a-zA-Z]+$/.test(value);
}, "Only letters are allowed.");

$.validator.addMethod("numbersOnly", function(value, element) {
    return this.optional(element) || /^[0-9]+$/.test(value);
}, "Only numbers are allowed.");

$('body').off('submit', '#admin_form');
$('body').on('submit', '#admin_form', function(e) {
    e.preventDefault();
    var validation = $.Deferred();
    var $form = $(this);
    var formData = new FormData(this);

    $form.validate({
        rules: {
            first_name: {
                required: true,
                lettersOnly: true
            },
            last_name: {
                required: true,
                lettersOnly: true
            },
            dial_code: {
                required: true
            },
            phone: {
                required: true,
                maxlength: 15,
                numbersOnly: true
            }
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

    if ($form.valid()) {
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
            dataType: 'html',
            success: function(res) {
                res = JSON.parse(res);
                console.log(res['status']);
                form_in_progress = 0;
                App.button_loading(false);
                if (res['status'] == 0) {
                    if (typeof res['errors'] !== 'undefined') {
                        var error_def = $.Deferred();
                        var error_index = 0;
                        jQuery.each(res['errors'], function(e_field, e_message) {
                            if (e_message != '') {
                                $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                $('<div class="error">' + e_message + '</div>').insertAfter($('[name="' + e_field + '"]').eq(0));
                                if (error_index == 0) {
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
                        var m = res['message'] || 'Unable to save variation. Please try again later.';
                        App.alert(m, 'Oops!', 'error');
                    }
                } else {
                    App.alert(res['message'] || 'Record saved successfully', 'Success!', 'success');
                    setTimeout(function() {
                        window.location.href = res['oData']['redirect'];
                    }, 2500);
                }

            },
            error: function(e) {
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
