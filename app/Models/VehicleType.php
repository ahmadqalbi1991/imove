<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;
    protected $fillable = [
        'model','manufacturer_id','status','model_ar'
    ];
    
    public function manufacturer(){
        return $this->hasOne(Manufacturer::class,'id','manufacturer_id');
    }
}
