<?php 

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dial_code',
        'phone',
        'phone_verified',
        'email_verified_at',
        'role_id',
        'user_phone_otp',
        'user_device_token',
        'user_device_type',
        'user_access_token',
        'firebase_user_key',
        'status',
        'first_name',
        'last_name',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    const MALE = 1;
    const FEMALE = 2;
    const BOTH = 3;

    const SINGLE = 1;
    const MARRIED = 2;
    const DIVORCED = 3;
    const WIDOWED = 4;

    const GENDER =[
        self::MALE => 'Male',
        self::FEMALE => 'Female',
    ];

    const INTERESTED_IN =[
        self::MALE => 'Male',
        self::FEMALE => 'Female',
        self::BOTH => 'Both',
    ];

    const MARITAL_STATUS =[
        self::SINGLE => 'Single',
        self::MARRIED => 'Married',
        self::DIVORCED => 'Divorced',
        self::WIDOWED => 'Widowed',
    ];

    public function getGender(){
        if(is_null($this->gender)){
            return 'Not Specified';
        }
        return self::GENDER[$this->gender];
    }

    public function getInterestedIn(){
        if(is_null($this->intrest_gender)){
            return 'Not Specified';
        }
        return self::INTERESTED_IN[$this->intrest_gender] ?? '';
    }
    public function getMaritalStatus(){
        if(is_null($this->marital_status)){
            return 'Not Specified';
        }
        return self::MARITAL_STATUS[$this->marital_status];
    }

    function nationalCountry(){
        return $this->belongsTo(Country::class,'nationality','country_id');
    }

    //public $appends = ['processed_user_image'];

    // public function getProcessedUserImageAttribute(){
    //     return get_uploaded_image_url($this->user_image,'user_image_upload_dir');
    // }

    public function CustomerType(){
        return $this->hasMany(CustomerType::class);
    }

    public function driver_detail(){
        return $this->hasOne(DriverDetail::class);
    }

    public function driver(){
        return $this->hasMany(Booking::class,'driver_id','id');
    }

    public function customer(){
        return $this->hasMany(Booking::class,'sender_id','id');
    }

    public function booking_qoutes(){
        return $this->hasMany(BookingQoute::class,'driver_id','id');
    }

    public function company(){
        return $this->hasOne(Company::class,'user_id','id');
    }

    public function blacklist(){
        return $this->hasOne(Blacklist::class,'user_id','id');
    }

    public function wallet(){
        return $this->hasOne(Wallet::class,'user_id','id');
    }
}

