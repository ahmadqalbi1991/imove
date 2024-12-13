<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeOfStores extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='type_of_stores';
    protected $primaryKey = 'type_of_store_id';
}
