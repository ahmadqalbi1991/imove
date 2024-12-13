<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'model','manufacturer_id','status'
    ];
    
    public function manufacturer(){
        return $this->hasOne(Manufacturer::class,'id','manufacturer_id');
    }
    public function type_data(){
        return $this->hasOne(VehicleType::class,'id','type_id');
    }
}
