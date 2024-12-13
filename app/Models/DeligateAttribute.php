<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeligateAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'deligate_id', 'details', 'name'
    ];

    public function deligate(){
        return $this->belongsTo(Deligate::class);
    }
}
