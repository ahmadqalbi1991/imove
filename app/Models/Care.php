<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Care extends Model
{
    use HasFactory,SoftDeletes;

    const DomesticHomeRelocation = 1;
    const InternationalHomeRelocation = 2;
    const OfficeRelocation = 3;
    const StorageServices = 4;
    const VehicleRelocation = 5;
    const ItemRemoval = 5;
    const ItemDelivery = 5;

    protected $table = "cares";
    public $hidden = ['created_at','deleted_at','updated_at'];

    protected $fillable = ['name','icon','status'];

    public function getIconAttribute($icon){

        return get_uploaded_image_url( $icon, 'care_image_upload_dir', 'placeholder.png' );

    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_categories');
    }

    public function booking()
    {
        return $this->hasMany(Booking::class);
    }
}
