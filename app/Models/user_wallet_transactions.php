<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_wallet_transactions extends Model
{
    use HasFactory;
    protected $table='user_wallet_transactions';
    protected $primaryKey = 'id';
}
