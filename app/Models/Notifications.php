<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table='notifications';
    protected $primaryKey = 'id';
    use HasFactory;
}
