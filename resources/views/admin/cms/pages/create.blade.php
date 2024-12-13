@extends("admin.template.layout")

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{$mode ?? $page_heading}}</h5>

            </div>
            <form method="post" action="{{route('cms.pages.submit')}}" id="admin_form"
                  enctype="multipart/form-data">
                <div class="card-body">
                    <div class="row">

                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $cms_page->id }}">
                            <div class="col-md-12 form-group">
                                <label>Select Page Type<span class="text-danger">*</span></label>
                                <select name="slug" id="page_type" class="form-control" data-jqv-required="true">
                                    <option value="">Select Page Type</option>
                                    @forelse (\App\Models\Page::PageType as $key => $value)
                                        <option @if($key == $cms_page->slug) selected @endif
                                        @if(in_array($key,$page_type_ids) && $key != $cms_page->slug) disabled
                                                class="text-muted" @endif
                                                value="{{ $key }}">{{ $value }}
                                        </option>
                                    @empty
                                        <option value="">No Type Found</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Page Title<span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{ $cms_page->title }}"
                                       class="form-control jqv-input" placeholder="e.g Privacy Policy" data-jqv-required="true">
                            </div>
                            <div class=" col-md-6 form-group mb-2">
                                <label for="t-text">Status<span class="text-danger">*</span></label>
                                <select class="custom-select jqv-input" name="status" id="status" data-jqv-required="true">
                                    <option value="1" {{ $cms_page->status == 1 ? "selected" : ""}} >Active</option>
                                    <option value="0" {{ $cms_page->status == 0 ? "selected" : ""}} >In Active
                                    </option>
                                </select>
                            </div>
                            <div class=" col-md-12 form-group row">
                                <div class="col-md-12 col-sm-12">
                                    <label for="t-text">Description<span class="text-danger">*</span></label>
                                    <textarea id="editor1" name="description" data-jqv-required="true"
                                            class=" form-control jqv-input description">{{ $cms_page->description  }}</textarea>
                                </div>
                            </div>
                        <div class="col-md-12 form-group text-center">
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
    <script src="{{asset('')}}ckeditor/ckeditor.js"></script>

    <script>
        CKEDITOR.replace('editor1');
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
    </script>
@stop
