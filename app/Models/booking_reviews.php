<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_reviews extends Model
{
    protected $table='booking_reviews';
    protected $primaryKey = 'id';
    use HasFactory;
}
