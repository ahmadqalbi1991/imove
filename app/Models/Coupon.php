<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'promo_code',
        'is_active',
        'coupon_type',
        'value'
    ];


    public function coupon_for()
    {
        return $this->hasMany(CouponFor::class, 'coupon_id');
    }
}
