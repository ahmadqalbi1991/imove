<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBookingStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'booking_id',
        'status'
    ];

    protected static function booted()
    {
        static::addGlobalScope('withRelations', function ($query) {
            $query->with(['user']);
        });
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusAttribute(): string
    {
        return booking_status($this->attributes['status']);
    }
}
