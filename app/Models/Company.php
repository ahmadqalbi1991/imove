<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='companies';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'account_type',
        'logo',
        'company_license',
        'status',
        'created_at',
        'updated_at',
    ];

    public function getLogoAttribute($logo){

        return get_uploaded_image_url( $logo, 'company_image_upload_dir', 'placeholder.png' );
        
    }

    public function getBannerAttribute($banner){

        return get_uploaded_image_url( $banner, 'company_image_upload_dir', 'placeholder.png' );
        
    }

    public function getCompanyLicenseAttribute($company_license){

        return get_uploaded_image_url( $company_license, 'company_image_upload_dir', 'placeholder.png' );
        
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'company_categories');
    }

    public function booking(){
        return $this->hasMany(Booking::class,'company_id','user_id');
    }
}
