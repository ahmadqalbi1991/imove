<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBookingImage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['images_path', 'booking_id'];
}
