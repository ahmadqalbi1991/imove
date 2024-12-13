<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'booking_id',
        'vendor_id',
        'status',
        'rating',
        'amount',
        'otp',
        'otp_verified'
    ];

    protected static function booted()
    {
        static::addGlobalScope('withRelations', function ($query) {
            $query->with(['vendor']);
        });
    }

    protected $appends = ['tax', 'calculated_tax', 'grand_total', 'discount', 'original_amount', 'discounted_amount'];

    public function getTaxAttribute() {
        $setting = Settings::find('1');
        return $setting->tax_percentage;
    }

    public function getOriginalAmountAttribute() {
        return $this->attributes['amount'];
    }

    public function getDiscountAttribute() {
        $booking = UserBooking::find($this->attributes['booking_id']);
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                return $coupon->value;
            } else {
                $percentage = $coupon->value;
                $amount = $this->attributes['amount'];
                $calculated_amount = ($amount * $percentage) / 100;
                return $calculated_amount;
            }
        }
    }

    public function getDiscountedAmountAttribute() {
        $booking = UserBooking::find($this->attributes['booking_id']);
        $amount = $this->attributes['amount'];
        $discounted_amount = 0;
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                $discounted_amount = $amount - $coupon->value;
            } else {
                $percentage = $coupon->value;
                $calculated_amount = ($amount * $percentage) / 100;
                $discounted_amount = $calculated_amount;
            }
        }

        return (string)$discounted_amount;
    }

    public function getCalculatedTaxAttribute() {
        $setting = Settings::find('1');
        $tax = $setting->tax_percentage;
        $amount = $this->attributes['amount'];
        $booking = UserBooking::find($this->attributes['booking_id']);
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                $amount = $amount - $coupon->value;
            } else {
                $percentage = $coupon->value;
                $calculated_amount = ($amount * $percentage) / 100;
                $amount = $amount - $calculated_amount;
            }
        }
        return number_format(($amount * $tax) / 100, 2);
    }

    public function getGrandTotalAttribute() {
        $setting = Settings::find('1');
        $tax = $setting->tax_percentage;
        $amount = $this->attributes['amount'];
        $booking = UserBooking::find($this->attributes['booking_id']);
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                $amount = $amount - $coupon->value;
            } else {
                $percentage = $coupon->value;
                $calculated_amount = ($amount * $percentage) / 100;
                $amount = $amount - $calculated_amount;
            }
        }
        $calculate_tax = ($amount * $tax) / 100;
        return number_format($amount + $calculate_tax, 2);
    }

    public function getCommissionAttribute() {
        $commission = config('global.commission');
        $amount = $this->attributes['amount'];
        $booking = UserBooking::find($this->attributes['booking_id']);
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                $amount = $amount - $coupon->value;
            } else {
                $percentage = $coupon->value;
                $calculated_amount = ($amount * $percentage) / 100;
                $amount = $amount - $calculated_amount;
            }
        }
        return number_format(($amount * $commission) / 100, 2);
    }

    public function getCalculatedCommissionAttribute() {
        $commission = config('global.commission');
        $amount = $this->attributes['amount'];
        $booking = UserBooking::find($this->attributes['booking_id']);
        if ($booking->coupon_applied) {
            $coupon = Coupon::find($booking->coupon_id);
            if ($coupon->coupon_type === 'fixed') {
                $amount = $amount - $coupon->value;
            } else {
                $percentage = $coupon->value;
                $calculated_amount = ($amount * $percentage) / 100;
                $amount = $amount - $calculated_amount;
            }
        }
        $calculate_commission = ($amount * $commission) / 100;
        return number_format($amount - $calculate_commission, 2);
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id', 'id');
    }
}
