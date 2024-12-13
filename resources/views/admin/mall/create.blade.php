@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        @if(empty($id))
            <div id="TypeDiv">
                <div class="row">
                    <div class="col-sm-6 text-right">
                        <button class="px-5 py-4 btn btn-gradient-info" onclick="selectType('mall')">
                            <img src="{{asset('images/mall.png')}}" style="height: 200px" alt="" class="img-fluid">
                            <h5 class="h4 mt-2 text-muted">Mall</h5>
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <button class="px-5 py-4 btn btn-gradient-info" onclick="selectType('store')">
                            <img src="{{asset('images/store.png')}}" style="height: 200px" alt="" class="img-fluid">
                            <h5 class="h4 mt-2 text-muted">Store</h5>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        <div class="card" id="FormDiv">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode." " ?? $page_heading.' '}}<span class="specificTest"></span></h5>

            </div>
            <form id="admin_form" method="post" action="{{route('malls.submit')}}" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf()
                    <input type="hidden" name="id" value="{{$id}}">
                    <input type="hidden" id="CreateType" name="type" value="{{$type}}">

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="mb-3">
                                <div class="storeSpecific">
                                    <label class="form-label" for="bs-validation-name">Select Mall</label>
                                    <select class="form-control jqv-input"
                                            name="store_mall_id">
                                        <option value="">Select Mall</option>
                                        @foreach($malls as $mall)
                                            <option @if($store_mall_id == $mall->mall_id) selected @endif
                                            value="{{$mall->mall_id}}">{{$mall->mall_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Name<span
                                        class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="mall_name"
                                    name="mall_name"
                                    value="{{$mall_name}}"
                                />

                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Image<span
                                        class="text-danger">*</span></label>
                                <input
                                    type="file"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="event_image"
                                    name="mall_image"
                                />
                                <div id="image_preview" class="p-3 text-center">
                                    @if($mall_image)
                                        <img src="{{asset('storage/malls/'.$mall_image)}}" style="max-height: 250px"
                                             class="img-fluid">
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Details<span
                                        class="text-danger">*</span></label>
                                <textarea
                                    id="about"
                                    name="mall_description"
                                    rows="4"
                                    class="form-control jqv-input" data-jqv-required="true"
                                >{{$mall_description}}</textarea>
                            </div>
                            <div class="mb-3">
                                <x-elements.map-location
                                    addressFieldName="mall_address"
                                    :lat="$mall_latitude"
                                    :lng="$mall_longitude"
                                    :address="$mall_address"/>
                            </div>
                            <div class="mb-3">
                                <!-- Button trigger modal -->
                                <div>
                                    <label class="form-label" for="bs-validation-name">Select Zone<span
                                            class="text-danger">*</span>
                                        <button type="button"
                                                class="primary-btn-outline btn-hover rounded-pill p-1 px-3"
                                                data-toggle="modal"
                                                data-target="#ZoneInsModal">
                                            View Zone Instructions
                                        </button>
                                    </label>
                                </div>
                                <x-elements.map-polygon
                                    mapHeight="400px"
                                    :includeCdn="false"
                                    :id="$id"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mall Status<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true" name="mall_status">
                                    <option @if($mall_status==1) selected @endif value="1">Active</option>
                                    <option @if($mall_status==0) selected @endif value="0">InActive</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="main-btn primary-btn btn-hover" disabled>
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">


                        </div>
                    </div>
                </div>
            </form>
        </div>


    </div>

    <!-- Modal -->
    <div class="modal fade" id="ZoneInsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Zone Setup Instruction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <x-elements.map-polygon-instruction/>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')
    <script>

        @if(empty($id))
        $('#FormDiv').hide();
        @else
        if ('{{$type}}' == 'mall') {
            $('.storeSpecific').hide();
        } else {
            $('.storeSpecific').show();
        }
        @endif

        function selectType(type) {
            $('#TypeDiv').hide();
            $('#FormDiv').fadeIn('slow');
            $('.specificTest').text(type);
            $('#CreateType').val(type);
            if (type == 'mall') {
                $('.storeSpecific').hide();
            } else {
                $('.storeSpecific').show();
            }

        }

        const fileInput = document.getElementById("event_image");
        const preview = document.getElementById("image_preview");

        fileInput.addEventListener("change", () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    preview.innerHTML = `<img src="${reader.result}"  style="max-height: 250px" class="img-fluid" alt="File preview">`;
                };
            } else {
                preview.innerHTML = "No file selected";
            }
        });

        $('.all-select').click(function () {
            $(this).siblings('.crud-items').prop('checked', this.checked);
        });
        $('.crud-items').click(function () {
            $(this).siblings('.all-select').prop('checked', false);
        });
        $('.all-p').click(function () {
            $(this).siblings('.reader').prop('checked', true);
        });
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
        $(document).ready(function () {
            $(window).keydown(function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>
@stop
