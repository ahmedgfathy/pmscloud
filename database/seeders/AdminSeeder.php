<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@pmscloud.com',
            'password' => Hash::make('admin123'),
            'is_admin' => true,
        ]);
    }
}
