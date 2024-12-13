<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Malls extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'malls';
    protected $primaryKey = 'mall_id';

    use PostgisTrait;

    protected $guarded = [];

    protected $postgisFields = [
        'coordinates',
    ];

    protected $postgisTypes = [
        'coordinates' => [
            'geomtype' => 'geography',
            'srid' => 4326
        ]
    ];

}
