@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>

            </div>
            <form id="admin_form" method="post" action="{{route('products.submit')}}" enctype="multipart/form-data">
                <div class="card-body">
                    @csrf()
                    <input type="hidden" name="id" value="{{$id}}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Product Store<span
                                        class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true"
                                        name="store_id">
                                    <option value="">Select Store</option>
                                    @foreach($stores as $store)
                                        <option @if($product->store_id == $store->mall_id) selected @endif
                                        value="{{$store->mall_id}}">{{$store->mall_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Name<span
                                        class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="product_name"
                                    name="name"
                                    value="{{$product->name}}"
                                />

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Image<span
                                        class="text-danger">*</span></label>
                                <input
                                    type="file"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="image"
                                    name="image"
                                />
                                <div id="image_preview" class="">
                                    @if($product->image)
                                        <img src="{{asset('storage/products/'.$product->image)}}"
                                             style="max-height: 250px"
                                             class="img-fluid mt-2">
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Description<span class="text-danger">*</span></label>
                                <textarea
                                    id="about"
                                    name="description"
                                    rows="4"
                                    class="form-control jqv-input" data-jqv-required="true"
                                >{{$product->description}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Product Category<span
                                        class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true"
                                        name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option @if($product->category_id == $category->category_id) selected @endif
                                        value="{{$category->category_id}}">{{$category->category_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Price<span
                                        class="text-danger">*</span></label>
                                <input name="price" type="number" step="any" class="form-control jqv-input" data-jqv-required="true"
                                       value="{{$product->price}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bs-validation-name">Quantity<span
                                        class="text-danger">*</span></label>
                                <input name="quantity" type="number" class="form-control jqv-input"
                                       data-jqv-required="true"
                                       value="{{$product->quantity}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status<span class="text-danger">*</span></label>
                                <select class="form-control jqv-input" data-jqv-required="true" name="status">
                                    <option @if($product->status=='1') selected @endif value="1">Active</option>
                                    <option @if($product->status=='0') selected @endif value="0">InActive</option>
                                </select>
                            </div>
                        </div>
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
@stop

@section('script')
    <script>

        const fileInput = document.getElementById("image");
        const preview = document.getElementById("image_preview");

        fileInput.addEventListener("change", () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => {
                    preview.innerHTML = `<img src="${reader.result}"  style="max-height: 250px" class="img-fluid mt-2" alt="File preview">`;
                };
            } else {
                preview.innerHTML = "No file selected";
            }
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
