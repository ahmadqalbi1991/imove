<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPicture extends Model
{
    use HasFactory;

    public function getPictureAttribute($picture){

        return get_uploaded_image_url( $picture, 'booking_pictures_upload_dir', 'placeholder.png' );
        
    }

    public function booking(){
        return $this->belongsTo(Booking::class);
    }
}
