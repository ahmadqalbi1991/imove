@extends('admin.template.layout')
@section('header')

@stop
@section('content')

    <div class="card mb-5">
        <div class="card-body">
            <div class="col-xs-12">
                <form method="post" id="admin-form" action="{{ route('costings.store') }}" enctype="multipart/form-data"
                    data-parsley-validate="true">
                    <input type="hidden" name="id" id="cid" value="{{ $id }}">
                    @csrf()
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Category<b class="text-danger">*</b></label>
                                <select name="category_id" id="category_id" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($categories)
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{$category_id == $category->id  ? 'selected' : ''}}> {{ $category->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Size<b class="text-danger">*</b></label>
                                <select name="size_id" id="size_id" required>
                                    <option value=""> CHOOSE </option>
                                    @if ($sizes)
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}" {{$size_id == $size->id  ? 'selected' : ''}} > {{ $size->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Delivery Type<b class="text-danger">*</b></label>
                                <select name="delivery_type" id="delivery_type" required>
                                    {{-- <option value=""> CHOOSE </option> --}}
                                    <option value="Normal" {{$delivery_type_selected == "Normal"  ? 'selected' : ''}}> Normal </option>
                                    <option value="Urgent" {{$delivery_type_selected == "Urgent"  ? 'selected' : ''}}> Urgent </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cost<b class="text-danger">*</b></label>
                                <input
                                    type="text"
                                    class="form-control-plaintext" data-jqv-required="true"
                                    id="cost"
                                    name="cost"
                                    required
                                    value="{{ $cost ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option <?= $status == 'active' ? 'selected' : '' ?> value="active">Active</option>
                                    <option <?= $status == 'inactive' ? 'selected' : '' ?> value="inactive">Inactive
                                    </option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12 mt-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xs-12 col-sm-6">
            </div>
        </div>
    </div>
@stop
@section('script')
    <script>
        $('#select2').select2();
        App.initFormView();
        // $(document).ready(function() {
        //     if (!$("#cid").val()) {
        //         $(".b_img_div").removeClass("d-none");
        //     }
        // });
        // $(".parent_cat").change(function() {
        //     if (!$(this).val()) {
        //         $(".b_img_div").removeClass("d-none");
        //     } else {
        //         $(".b_img_div").addClass("d-none");
        //     }
        // });
        $('body').off('submit', '#admin-form');
        $('body').on('submit', '#admin-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);
            $(".invalid-feedback").remove();

            App.loading(true);
            $form.find('button[type="submit"]')
                .text('Saving')
                .attr('disabled', true);

            var parent_tree = $('option:selected', "#parent_id").attr('data-tree');
            formData.append("parent_tree", parent_tree);
            
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: $form.attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                dataType: 'json',
                success: function(res) {
                    App.loading(false);

                    if (res['status'] == 0) {
                        if (typeof res['errors'] !== 'undefined') {
                            var error_def = $.Deferred();
                            var error_index = 0;
                            jQuery.each(res['errors'], function(e_field, e_message) {

                                if (e_message != '') {
                                    $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                    $('<div class="invalid-feedback">' + res['message'] + '</div>')
                                        .insertAfter($('[name="' + e_field + '"]').eq(0)).show();
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
                            var m = res['message'] ||
                                'Unable to save category. Please try again later.';
                            App.alert(m, 'Oops!');
                        }
                    } else {
                        App.alert(res['message'], 'Success!');
                        setTimeout(function() {
                            window.location.href = App.siteUrl('/admin/costings');
                        }, 1500);

                    }

                    $form.find('button[type="submit"]')
                        .text('Save')
                        .attr('disabled', false);
                },
                error: function(e) {
                    App.loading(false);
                    $form.find('button[type="submit"]')
                        .text('Save')
                        .attr('disabled', false);
                    App.alert(e.responseText, 'Oops!');
                }
            });
        });
    </script>
@stop