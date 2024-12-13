<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewNotification extends Model
{
    protected $table='new_notifications';
    protected $primaryKey = 'id';
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class, 'id','user_id');
    }
}
