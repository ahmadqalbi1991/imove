<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Settings extends Model
{
    protected $table = "settings";
    protected $primaryKey = "id";
    public $timestamps = false;


    public $guarded = [];
}
