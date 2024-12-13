<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = ['name','icon','slug','status'];

    public function getIconAttribute($icon){

        return get_uploaded_image_url( $icon, 'shipping_methods_upload_dir', 'placeholder.png' );
        
    }
}
