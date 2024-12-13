<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryLanguages extends Model
{
    use HasFactory;
    protected $primaryKey = 'category_lang_id';
    protected $fillable = [
        'category_lang_id',
        'category_localized_name',
        'lang_code',
        'created_at',
        'updated_at',
        'deleted_at',
        'category_id_fk'
    ];
}
