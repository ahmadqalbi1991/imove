<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_address',
        'deliver_address',
        'sender_id',
        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'deligate_id',
        'deligate_details',
        'truck_type_id',
        'quantity',
        'admin_response',
        'qouted_amount',
        'comission_amount',
        'customer_signature',
        'delivery_note',
        'driver_id',
        'status',
        'is_paid',
        'booking_number',
        'invoice_number',
        'total_amount',
        'shipping_method_id',
        'invoice_number'
    ];

    public function customer(){
        return $this->belongsTo(User::class,'sender_id','id');
    }

    public function driver(){
        return $this->belongsTo(User::class,'driver_id','id');
    }

    public function booking_qoutes(){
        return $this->hasMany(BookingQoute::class);
    }

    public function truck_type(){
        return $this->belongsTo(TruckType::class);
    }

    public function booking_charges(){
        return $this->hasMany(BookingAdditionalCharge::class);
    }

    public function booking_status_trackings(){
        return $this->hasMany(BookingStatusTracking::class);
    }

    public function booking_pictures(){
        return $this->hasMany(BookingPicture::class);
    }

    public function booking_home_relocation(){
        return $this->hasOne(BookingHomeRelocation::class);
    }

    public function booking_office_relocation(){
        return $this->hasOne(BookingOfficeRelocation::class);
    }

    public function booking_storage_services(){
        return $this->hasOne(BookingStorageService::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class,'company_id','user_id');
    }


}
