<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    const PRIVATE = 1;
    const PUBLIC = 2;
    const PRIVACY = [
        self::PRIVATE => 'Private',
        self::PUBLIC => 'Public',
    ];

    const IN_PERSON = 1;
    const VIRTUAL = 2;
    const EVENT_TYPES = [
        self::IN_PERSON => 'In Person',
        self::VIRTUAL => 'Virtual',
    ];
    protected $guarded = [];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

//    public function getPrivacyAttribute($value)
//    {
//        return self::PRIVACY[$value] ?? '-';
//    }
//
//    public function getEventTypeIdAttribute($value)
//    {
//        return self::EVENT_TYPES[$value] ?? '-';
//    }

}
