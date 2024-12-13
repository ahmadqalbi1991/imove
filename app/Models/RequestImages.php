<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RequestImages extends Model
{
    //
    protected $table = "request_images";
    protected $primaryKey = "id";

    protected $guarded = [];

    public function getImageAttribute($value)
    {
        return get_uploaded_image_url($value,'request_images_upload_dir');
    }


}
