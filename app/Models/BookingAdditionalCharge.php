<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingAdditionalCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'charges_name',
        'charges_amount'
    ];

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
}
