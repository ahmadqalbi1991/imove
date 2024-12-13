@extends("admin.template.layout")

@section('header')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop


@section('content')
    <div class="card mb-5">
      
        <div class="card-body">
            <form method="post" action="{{ url('/admin/banner/update') }}" id="admin-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{$banner->id}}">

                
                <div class="row  d-flex justify-content-between align-items-center" >
                

                    <div class="col-md-6 form-group">
                        <label>Title</label>
                        <input type="text" name="banner_title" class="form-control jqv-input" value="{{$banner->banner_title}}">
                    </div>

                    
                    <div class="col-md-6 form-group" style="display:none;">
                        <label>URL</label>
                        <input type="url" name="url" class="form-control jqv-input" value="{{$banner->url}}" >
                    </div>
                    

                    <div class="col-md-6 form-group">
                        <label>Status</label>
                        <select name="active" class="form-control">
                            <option <?= $banner->active == 1 ? 'selected' : '' ?> value="1">Active</option>
                            <option <?= $banner->active == 0 ? 'selected' : '' ?> value="0">Inactive</option>
                        </select>
                    </div>

                   
                                       

                    <div class="col-md-6 form-group applies_to_select" id="browse_category" style="display:none;">
                        <label>Category</label>
                        <select  class="form-control jqv-input product_catd select2" id="categoryselected" data-jqv-required="true"
                            name="category_id" data-role="select2" data-placeholder="Select Categories"
                            data-allow-clear="true" >
                            <option value="">Select</option>
                            @if(isset($category_list) && count($category_list) > 0)

                            @foreach($category_list as $parent_cat_id => $parent_cat_name)
                            
                            
                            <?php if ( isset($sub_category_list[$parent_cat_id]) && !empty($sub_category_list[$parent_cat_id]) ) { ?>
                            <optgroup label="<?php echo $parent_cat_name; ?>" <?php echo in_array($parent_cat_id, $category_ids) ? 'selected' : ''; ?>>
                                <?php foreach ($sub_category_list[$parent_cat_id] as $sub_cat_id => $sub_cat_name): ?>
                                <?php if ($id > 0 && $id == $sub_cat_id) {
                                    continue;
                                } ?>
                                <?php if ( isset($sub_category_list[$sub_cat_id]) && !empty($sub_category_list[$sub_cat_id]) ){ ?>
                            <optgroup label="<?php echo str_repeat('&nbsp;', 4) . $sub_cat_name; ?>" <?php echo in_array($sub_cat_id, $category_ids) ? 'selected' : ''; ?>>
                                <?php foreach ($sub_category_list[$sub_cat_id] as $sub_cat_id2 => $sub_cat_name2): ?>
                                <?php if ($id > 0 && $id == $sub_cat_id2) {
                                    continue;
                                } ?>
                                <?php if ( isset($sub_category_list[$sub_cat_id2]) && !empty($sub_category_list[$sub_cat_id2]) ){ ?>
                            <optgroup label="<?php echo str_repeat('&nbsp;', 6) . $sub_cat_name2; ?>" <?php echo in_array($sub_cat_id2, $category_ids) ? 'selected' : ''; ?>>
                                <?php foreach ($sub_category_list[$sub_cat_id2] as $sub_cat_id3 => $sub_cat_name3): ?>
                                <?php if ($id > 0 && $id == $sub_cat_id3) {
                                    continue;
                                } ?>
                                <?php if ( isset($sub_category_list[$sub_cat_id3]) && !empty($sub_category_list[$sub_cat_id3]) ){ ?>
                                <?php foreach ($sub_category_list[$sub_cat_id3] as $sub_cat_id4 => $sub_cat_name4): ?>
                                <?php if ($id > 0 && $id == $sub_cat_id4) {
                                    continue;
                                } ?>
                                <option data-style="background-color: #ff0000;" value="<?php echo $sub_cat_id4; ?>"
                                    <?php echo in_array($sub_cat_id4, $category_ids) ? 'selected' : ''; ?>>
                                    <?php echo str_repeat('&nbsp;', 10) . $sub_cat_name4; ?>
                                </option>
                                <?php endforeach; ?>
                                <?php }else{ ?>
                                <option data-style="background-color: #ff0000;" value="<?php echo $sub_cat_id3; ?>"
                                    <?php echo in_array($sub_cat_id3, $category_ids) ? 'selected' : ''; ?>>
                                    <?php echo str_repeat('&nbsp;', 8) . $sub_cat_name3; ?>
                                </option>
                                <?php } ?>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php }else{ ?>
                            <option value="<?php echo $sub_cat_id2; ?>" <?php echo in_array($sub_cat_id2, $category_ids) ? 'selected' : ''; ?>>
                                <?php echo str_repeat('&nbsp;', 6) . $sub_cat_name2; ?>
                            </option>
                            <?php } ?>
                            <?php endforeach; ?>
                            </optgroup>
                            <?php }else{ ?>
                            <option value="<?php echo $sub_cat_id; ?>" <?php echo in_array($sub_cat_id, $category_ids) ? 'selected' : ''; ?>>
                                <?php echo str_repeat('&nbsp;', 4) . $sub_cat_name; ?>
                            </option>
                            <?php } ?>
                            <?php endforeach; ?>
                            </optgroup>
                            <?php }else{ ?>
                            <option value="<?php echo $parent_cat_id; ?>" <?php echo in_array($parent_cat_id, $category_ids) ? 'selected' : ''; ?>>
                                <?php echo $parent_cat_name; ?>
                            </option>
                            <?php } ?>


                            @endforeach
                            @endif
                        </select>
                    </div>
                    


             

                     


                   
                    <div class="col-md-6 form-group">
                        <label>Upload Banner</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input jqv-input" name="banner"
                                data-role="file-image" data-width="1920" data-height="1079" data-preview="image-preview" data-jqv-required="true" name="upload_image" id="banner">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <small class="text-muted">
                            Upload Image With Dimension 1290X827
                        </small>
                    </div>

                     <div class="col-md-12 form-group" >
                        <img id="image-preview" style="width:192px; height:108px;" class="img-responsive mb-1"  data-image="<?php echo url(config('global.upload_path').config('global.banner_image_upload_dir').$banner->banner_image);?>" src="<?php echo url(config('global.upload_path').config('global.banner_image_upload_dir').$banner->banner_image);?>">
                    </div>
                    
                 
                    <div class="col-md-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('script')
    <script>
        App.initFormView();
        $(document).ready(function() {
            $(".select2").select2();
        });
        $('body').off('submit', '#admin-form');
        $('body').on('submit', '#admin-form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var formData = new FormData(this);

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
                            window.location.href = App.siteUrl('/admin/banners');
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

        $(document).delegate("#applies_to", "change", function() {
        $(".applies_to_select").css("display","none");
        var show = $('option:selected', this).attr('data-show');
        var ctid = $('#activity_change').val();
        if(ctid == 7)
        {
            $(show).css("display","block");   
        }
        if(show == "#browse_category")
        {
            $("#service_search").val("");
            $("#product_search").val("");
            $('#browse_product').hide();
        }
        else
        {
            $("#categoryselected").val("");
            
            var ctid = $('#storelist').val();
            if(ctid.length == 0)
            {
                var ctid = 0;  
            }
            $.ajax({
              dataType: "json",
              url: "{{url('admin/banner/get_product_by_store')}}/"+ctid,
              success: function(data){
              $("#product_search").empty();
              $("#product_search").append('<option value="">Select..</option>');
              $.each(data, function(index) {
              $("#product_search").append('<option value=' + data[index].id +' >'+data[index].product_name+'</option>');
              $("select#product_search").val("<?=$banner->product??''?>");
              });
             }
            })
        }
        
        });
        $(function(){
            $("#activity_change").trigger("change");
            $("#applies_to").trigger("change");
});

$('body').off('change', '#activity_change');
$('body').on('change', '#activity_change', function(e) {
    var ctid = $('#activity_change').val();
             if(ctid == 7)
             {
                $('#typeselection').show();
                $('#browse_services').hide();
                $('#browse_store').show();

                $("#service_search").val("");
             }
             else if(ctid == 6)
             {
                $('#typeselection').hide();
                $('#browse_services').show();
                $('#browse_store').hide();
                $('#browse_category').hide();
                $(".applies_to_select").css("display","none");
                $('#browse_product').hide();
                
                $('#applies_to').val('');
                $("#categoryselected").val("");
                $('#browse_product').hide();
             }
             else
             {
                $('#browse_services').hide();
                $('#browse_category').hide();
                $('#typeselection').hide();
                $('#browse_store').show();
                $(".applies_to_select").css("display","none");

                $("#service_search").val("");
             }
             $.ajax({
              dataType: "json",
              url: "{{url('admin/banner/get_store')}}/"+ctid,
              success: function(data){
              $("#storelist").empty();
              $("#storelist").append('<option value="">Select..</option>');
              $.each(data, function(index) {
              $("#storelist").append('<option value=' + data[index].id +' >'+data[index].name+'</option>');
              if(ctid == 7)
              {
                $("select#storelist").val("<?=$banner->store??''?>");
              }
              
              });
    }
  })
}); 
$('body').off('change', '#storelist');
$('body').on('change', '#storelist', function(e) {
    $("#applies_to").trigger("change");
  });         
    </script>
@stop
