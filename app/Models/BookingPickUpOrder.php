<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPickUpOrder extends Model
{
    use HasFactory;

    protected $table = "booking_pick_up_orders";
    // public $hidden = ['created_at','deleted_at','updated_at'];

    protected $fillable = ['order_number','delivery_order_number','customer_id', 'category_id', 'location', 'landmark', 'contact_person', 'dail_code', 'mobile_no', 'description', 'instruction',
     'size_id', 'care_id', 'date', 'time', 'delivery_type','cost','service_price',
     'tax','grand_total','pickup_driver','delivery_driver','booking_status','payment_type','payment_ref',
     'payment_status','po_latitude','po_longitude','do_latitude','do_longitude'];

     public function size_details()
     {
         return $this->belongsTo('App\Models\Size', 'size_id', 'id');
     }
     public function care_details()
        {
            return $this->belongsTo('App\Models\Care', 'care_id', 'id')->withDefault(function () {
                return new \stdClass();  // Returns an empty object if no data matches
            });
        }

     public function category_details()
     {
         return $this->belongsTo('App\Models\Category', 'category_id', 'id');
     }
     public function pickeupdriver()
{
    return $this->belongsTo('App\Models\User', 'pickup_driver')->withDefault(function () {
        return new \stdClass();  // Returns an empty object if no data matches
    });
}
public function customer()
{
    return $this->belongsTo('App\Models\User', 'customer_id')->withDefault(function () {
        return new \stdClass();  // Returns an empty object if no data matches
    });
}

    public function deliverydriver()
    {

        return $this->belongsTo('App\Models\User', 'delivery_driver')->withDefault(function () {
            return new \stdClass();  // Returns an empty object if no data matches
        });
        
    }
}
