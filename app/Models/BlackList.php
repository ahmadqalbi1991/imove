<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','user_device_id'];

    public function User(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
