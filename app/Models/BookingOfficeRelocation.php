<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingOfficeRelocation extends Model
{
    use HasFactory;

    public function booking(){
        return $this->BelongsTo(Booking::class);
    }
}
