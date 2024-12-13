<?php

namespace Database\Seeders;

use App\Models\RolePermissions;
use App\Models\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin', 'user'];
        foreach ($roles as $role) {
            $existingRole = UserRoles::where('role_name', $role)->first();
            if ($existingRole) {
                continue;
            }
            UserRoles::create([
                'role_name' => $role,
                'role_status' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
