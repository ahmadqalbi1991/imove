<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDetail extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','driving_license','mulkia','mulkia_number','is_company','company_id','truck_type_id','total_rides','address','latitude','longitude','emirates_id_or_passport','driving_license_number','driving_license_expiry','driving_license_issued_by','vehicle_plate_number','vehicle_plate_place'];

    public function user(){
        return $this->belongsTo(User::class);
    }

//    public function getDrivingLicenseAttribute($driving_license){
//        return get_uploaded_image_url($driving_license,'user_image_upload_dir','placeholder.png');
//    }
//
//    public function getMulkiaAttribute($mulkia){
//        return get_uploaded_image_url($mulkia,'user_image_upload_dir','placeholder.png');
//    }
//
//    public function getEmiratesIdOrPassportAttribute($emirates_id_or_passport){
//        return get_uploaded_image_url($emirates_id_or_passport,'user_image_upload_dir','placeholder.png');
//    }
}
