<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@tlu.edu.vn'], 
            [
                'full_name' => 'Administrator',
                'password' => Hash::make('12345679'),
                'phone' => '0123456789',
                'role' => 'ADMIN',
            ]
        );
    }
}
