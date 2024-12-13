<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingDropOffOrder extends Model
{
    use HasFactory;

    protected $table = "booking_drop_off_orders";
    // public $hidden = ['created_at','deleted_at','updated_at'];

    protected $fillable = ['order_number','customer_id', 'category_id', 'location', 'landmark', 'contact_person', 'dail_code', 'mobile_no', 'description', 'instruction', 'size_id', 'care_id', 'date', 'time', 'delivery_type', 'pick_up_id'];
}
