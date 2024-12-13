<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role_id' => 1,
                'user_status' => 1,
                'first_name' => 'Site',
                'last_name' => 'Admin',
                'phone' => '123434567890',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }
}
