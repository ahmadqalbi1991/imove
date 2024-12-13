<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Size extends Model
{
    use HasFactory,SoftDeletes;

    const DomesticHomeRelocation = 1;
    const InternationalHomeRelocation = 2;
    const OfficeRelocation = 3;
    const StorageServices = 4;
    const VehicleRelocation = 5;
    const ItemRemoval = 5;
    const ItemDelivery = 5;

    protected $table = "sizes";
    public $hidden = ['created_at','deleted_at','updated_at'];

    protected $fillable = ['name','status'];

}
