<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'owner@cafepos.com'], [
            'name' => 'Pemilik Kafe',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'admin@cafepos.com'], [
            'name' => 'Admin Kafe',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
        User::updateOrCreate(['email' => 'kasir@cafepos.com'], [
            'name' => 'Rina Kasir',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'is_active' => true,
        ]);
    }
}
