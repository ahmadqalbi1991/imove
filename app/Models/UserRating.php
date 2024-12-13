<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRating extends Model
{
    use HasFactory;
    protected $fillable = [
        'rating',
        'review',
        'user_id',
        'vendor_id',
        'booking_id'
    ];

    protected static function booted()
    {
        static::addGlobalScope('withRelations', function ($query) {
            $query->with(['user']);
        });
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
