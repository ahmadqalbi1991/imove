<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'lang_code',
        'category_name',
        'unique_category_code',
        'unique_category_text',
        'category_image',
        'category_icon',
        'created_by',
        'parent_category_id',
        'category_status',
    ];
}
