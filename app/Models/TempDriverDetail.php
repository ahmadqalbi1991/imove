<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDriverDetail extends Model
{
    use HasFactory;
    protected $table='temp_driver_details';
    protected $fillable = ['user_id','driving_license','mulkia','mulkia_number','is_company','company_id','truck_type_id','total_rides','address','latitude','longitude','emirates_id_or_passport','driving_license_number','driving_license_expiry','driving_license_issued_by','vehicle_plate_number','vehicle_plate_place'];

}
