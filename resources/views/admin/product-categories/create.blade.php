@extends("admin.template.layout")

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">


              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{$page_heading}}</h5>

                </div>
                <form id="admin_form" method="post" action="{{route('product-categories.submit')}}" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 mb-4">

                                @csrf()
                                <input type="hidden" name="id" value="{{$id}}">
                                <label class="form-label" for="bs-validation-name">Name<span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control jqv-input" data-jqv-required="true"
                                    id="category_name"
                                    name="category_name"
                                    value="{{$category_name}}"
                                />



                            </div>
                            <div class="col-xs-12 col-sm-6 mb-4">
                                    <label class="form-label">Parent Category</label>
                                    <select class="form-control" name="parent_category_id">
                                        <option value="">No Parent</option>
                                        @if($parent_categories->count() > 0)
                                            @foreach($parent_categories as $p_cat)
                                            <option @if($parent_category_id==$p_cat->category_id) selected @endif value="{{$p_cat->category_id}}">{{$p_cat->category_name}}</option>

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            <div class="col-xs-12 col-sm-6 mb-4">
                                    <label class="form-label">Category Image<span class="text-danger">*</span></label>
                                    <input type="file" name="category_image" data-role="file-image" data-preview ="preview_image"class="jqv-input form-control" @if($id=='') data-jqv-required="true" @endif>
                                    <img src="{{$category_image}}" style="Width:100px; height:90px;" class="mt-2" id="preview_image">
                                </div>
                                <div class="col-xs-12 col-sm-6 mb-4">
                                    <label class="form-label">Category Icon<span class="text-danger">*</span></label>
                                    <input type="file" name="category_icon" data-role="file-image" data-preview ="preview_image_icon"class="jqv-input form-control" @if($id=='') data-jqv-required="true" @endif>
                                    <img src="{{$category_icon}}" style="Width:100px; height:90px;" class="mt-2" id="preview_image_icon">
                                </div>
                                <div class="col-xs-12 col-sm-6 mb-4">
                                    <label class="form-label">Category Status<span class="text-danger">*</span></label>
                                    <select class="form-control jqv-input" data-jqv-required="true" name="category_status">
                                        <option @if($category_status==1) selected @endif value="1">Active</option>
                                        <option @if($category_status==0) selected @endif value="0">InActive</option>
                                    </select>
                                </div>
                            <div class="col-12">
                                <button type="submit" class="main-btn primary-btn btn-hover" disabled>
                                    Submit
                                </button>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                @php
                                    $langs=config('languages');
                                @endphp


                                @foreach($langs as $langKey=>$lang)
                                    @if($langKey != 'en')
                                    <div class="mb-3">
                                    <label class="form-label" for="bs-validation-name">Name ({{$lang}})</label>
                                    <input
                                        type="text"
                                        class="form-control jqv-input"
                                        id="category_name_{{$langKey}}"
                                        name="category_name_{{$langKey}}"
                                        value="{{$lang_items[$langKey]??''}}"
                                    />

                                    </div>
                                    @endif
                                @endforeach
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
                            var m = res['message']||'Unable to save category. Please try again later.';
                            App.toast(m, 'Oops!','error');
                        }
                    } else {
                        App.toast(res['message']||'Record saved successfully', 'Success!','success');
                        setTimeout(function(){
                            window.location.href = res['oData']['redirect'];
                        },2500);

                    }

                },
                error: function (e) {
                    form_in_progress = 0;
                    App.button_loading(false);
                    console.log(e);
                    App.toast( "Network error please try again", 'Oops!','error');
                }
            });
        });
    });
</script>
@stop
