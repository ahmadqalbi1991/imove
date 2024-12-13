<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Costing extends Model
{
    use HasFactory,SoftDeletes;

    const DomesticHomeRelocation = 1;
    const InternationalHomeRelocation = 2;
    const OfficeRelocation = 3;
    const StorageServices = 4;
    const VehicleRelocation = 5;
    const ItemRemoval = 5;
    const ItemDelivery = 5;

    protected $table = "costings";
    public $hidden = ['created_at','deleted_at','updated_at'];

    protected $fillable = ['category_id','size_id', 'cost', 'status', 'delivery_type','care_id'];

    public function company()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id', 'id');
    }

    public function care()
    {
        return $this->belongsTo(Care::class, 'care_id', 'id');
    }

}
