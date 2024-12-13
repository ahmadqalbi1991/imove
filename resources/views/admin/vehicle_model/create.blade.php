@extends('admin.template.layout')

@section('content')
    <div class="card mb-5">
        <div class="card-body">
            
            <form method="post" id="admin-form" action="{{ url('admin/vehilce/model/store') }}" enctype="multipart/form-data" data-parsley-validate="true">
                    <input type="hidden" name="id" value="{{$model->id??0}}">
                    @csrf()
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label>Model Name<b class="text-danger">*</b></label>
                                <input type="text" name="name" class="form-control" required
                                    data-parsley-required-message="Enter Model Name" value="{{$model->model??''}}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label>Model Name Arabic</label>
                                <input type="text" name="name_ar" dir="rtl" class="form-control"
                                    data-parsley-required-message="Enter Model Name Arabic" value="{{$model->model_ar??''}}">
                            </div>
                        </div>
						<div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label>Manufacturer</label>
                                <select name="manufacturer" class="form-control" required
                                data-parsley-required-message="Select Manufacturer">
                                    <option value="">Select</option>
                                    @foreach($manufacturer as $item)
										<option value="{{$item->id}}"  @if(!empty($model->manufacturer_id)) {{$model->manufacturer_id == $item->id ? 'selected' : ''}} @endif>
											{{$item->name}}
										</option>
									@endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="model">Vehicle Type</label>
								<select class="form-control" id="type_id" name="type_id" required>
									<option value="">
										Select Type
									</option>
									@foreach($types as $item)
										<option value="{{$item->id}}" {{!empty($model) && $model->type_id == $item->id ? 'selected' : null;}}>
											{{$item->model}}
										</option>
									@endforeach
								</select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="active" class="form-control">
                                    <option value="1" {{!empty($model->status) && $model->status == 1 ? 'selected' : null;}}>Active</option>
                                    <option value="0" {{!empty($model->status) && $model->status == 0 ? 'selected' : null;}}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                    
                    
                    

                    
                    
            </form>

            <div class="col-xs-12 col-sm-6">

            </div>
        </div>
    </div>
@stop

@section('script')
    <script>
        App.initFormView();
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

            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: $form.attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                dataType: 'json',
                timeout: 600000,
                success: function(res) {
                    App.loading(false);

                    if (res['status'] == 0) {
                        if (typeof res['errors'] !== 'undefined') {
                            var error_def = $.Deferred();
                            var error_index = 0;
                            jQuery.each(res['errors'], function(e_field, e_message) {
                                if (e_message != '') {
                                    $('[name="' + e_field + '"]').eq(0).addClass('is-invalid');
                                    $('<div class="invalid-feedback">' + e_message + '</div>')
                                        .insertAfter($('[name="' + e_field + '"]').eq(0));
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
                            var m = res['message'];
                            App.alert(m, 'Oops!');
                        }
                    } else {
                        App.alert(res['message']);
                        setTimeout(function() {
                            window.location.href = App.siteUrl('/admin/vehilce/model');
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
