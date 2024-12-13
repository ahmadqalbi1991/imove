<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Webmozart\Assert\Tests\StaticAnalysis\object;

class UserBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'drop_off_location',
        'drop_off_lat',
        'drop_off_lng',
        'pick_up_location',
        'pick_up_lat',
        'pick_up_lng',
        'vehicle_id',
        'user_id',
        'emergency_type_id',
        'remarks',
        'booking_status',
        'booking_number',
        'payment_reference',
        'payment_confirmed',
        'vehicle_type_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope('withRelations', function ($query) {
            $query->with(['images', 'statuses', 'accepted_order', 'vehicle', 'customer', 'issue', 'all_orders', 'ratings', 'applied_coupon']);
        });
    }

    public function ratings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserRating::class, 'booking_id');
    }

    public function applied_coupon(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserBookingImage::class, 'booking_id');
    }

    public function issue(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmergencyProblem::class, 'id', 'emergency_type_id');
    }

    public function statuses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserBookingStatus::class, 'booking_id');
    }

    public function getBookingStatusAttribute(): string
    {
        $status = $this->attributes['booking_status'];
        $user = User::where(['user_access_token' => request()->access_token])->first();
        if ($status == 1) {
            if ($user->role_id == 2) {
                $status = 0;
            }
        } else if ($status == 0) {
            $orders = OrderModel::where('booking_id', $this->attributes['id'])->count();
            if ($orders && $user->role_id != 2) {
                $status = 1;
            }
        }

        return booking_status($status);
    }

    protected $appends = ['status', 'is_rated', 'distance_details', 'is_muted', 'bid_amount'];

    public function getIsMutedAttribute()
    {
        $user = User::where(['user_access_token' => request()->access_token])->where('role_id', '!=', 1)->first();
        if ($user) {
            $is_exists = MutedUserBooking::where(['user_id' => $user->id, 'booking_id' => $this->attributes['id']])->exists();
            if ($is_exists) {
                return "1";
            }
        }

        return "0";
    }

    public function getBidAmountAttribute()
    {
        $order = OrderModel::find($this->attributes['order_id']);
        $bid_amount = 0;
        if (!empty($order)) {
            $bid_amount = $order->amount;
        }

        return $bid_amount;
    }

    public function getDistanceDetailsAttribute(): array
    {
        $earthRadius = 6371;

        $lat1 = $this->attributes['pick_up_lat'];
        $lng1 = $this->attributes['pick_up_lng'];
        $lat2 = $this->attributes['drop_off_lng'];
        $lng2 = $this->attributes['drop_off_lng'];

        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in kilometers
        $distance = round($distance, 2); // Round distance to 2 decimal places

        // Estimate travel time assuming an average speed of 60 km/h
        $averageSpeedKmh = 60;
        $travelTimeHours = $distance / $averageSpeedKmh;

        // Convert travel time to hours and minutes
        $hours = floor($travelTimeHours);
        $minutes = round(($travelTimeHours - $hours) * 60);

        // Formatting distance and time as human-readable
        $distanceText = $distance . ' km';
        $timeText = ($hours > 0 ? $hours . ' hrs ' : '') . $minutes . ' mins';

        return [
            'distance' => $distanceText,
            'time' => $timeText,
        ];
    }

    public function getStatusAttribute(): string
    {
        $status = $this->attributes['booking_status'];
        $user = User::where(['user_access_token' => request()->access_token])->first();
        if ($status == 1) {
            if ($user->role_id == 2) {
                $status = 0;
            }
        } else if ($status == 0) {
            $orders = OrderModel::where('booking_id', $this->attributes['id'])->count();
            if ($orders && $user->role_id != 2) {
                $status = 1;
            }
        }

        return $status;
    }

    public function getIsRatedAttribute(): string
    {
        $rated = UserRating::where(['user_id' => $this->attributes['user_id'], 'booking_id' => $this->attributes['id']])->exists();
        return $rated ? '1' : '0';
    }

    public function accepted_order(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(OrderModel::class, 'booking_id')->whereIn('status', ['approved', 'completed', 'on_going', 'on_deliver']);
    }

    public function all_orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderModel::class, 'booking_id');
    }

    public function vehicle(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(UserVehicle::class, 'vehicle_id', 'id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['accepted_order'] = $this->accepted_order ? $this->accepted_order->toArray() : 'send_empty_obj';
        $array['applied_coupon'] = $this->applied_coupon ? $this->applied_coupon->toArray() : 'send_empty_obj';
        return $array;
    }
}
