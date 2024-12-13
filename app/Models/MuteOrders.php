<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MuteOrders extends Model
{
    use HasFactory;
    protected $table='mute_orders';
    protected $primaryKey = 'id';
}
