<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermissions extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_role_id_fk',
        'permission_id',
        'module_key',
        'permissions',
        'created_at',
        'updated_at'
    ];
}
