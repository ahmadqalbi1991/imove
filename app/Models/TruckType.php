<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckType extends Model
{
    use HasFactory;

    protected $fillable = ['truck_type','dimensions','icon','status','max_weight_in_tons'];

    public function bookings(){
        return $this->hasMany(Booking::class);
    }

}
