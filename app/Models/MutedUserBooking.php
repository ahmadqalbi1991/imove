<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutedUserBooking extends Model
{
    use HasFactory;
    protected $table = 'muted_user_bookings';
    protected $fillable = [
        'booking_id',
        'user_id'
    ];
    public $timestamps = false;
}
