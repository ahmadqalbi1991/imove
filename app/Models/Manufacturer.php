<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;
    protected $fillable = ['name','logo','status','name_ar'];
//    public function getLogoAttribute($value)
//    {
//        if($value)
//        {
//            return get_uploaded_image_url($value,'manufacturer_images');
//            return asset($value);
//        }
//        else
//        {
//            // return get_uploaded_image_url($value,'manufacturer_images');
//            return asset('admin-assets/assets/img/logo_new.png');
//        }
//    }
//
}
