<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponFor extends Model
{
    use HasFactory;
    protected $table = 'coupon_for';
    protected $fillable = ['coupon_id', 'vehicle_type_id'];
    public $timestamps = false;
}
