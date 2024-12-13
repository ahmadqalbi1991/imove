<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingQoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'driver_id',
        'price',
        'hours',
        'status',
        'commission_amount'
    ];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }

    public function driver(){
        return $this->belongsTo(User::class,'driver_id');
    }
}
