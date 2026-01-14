<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Kiểm tra xem đã có admin chưa, chưa có mới tạo
        if (!User::where('email', 'vudanghuong308@gmail.com')->exists()) {
            User::create([
                'code'      => 'ADMIN01',
                'name'      => 'Super Administrator',
                'email'     => 'vudanghuong308@gmail.com',
                'password'  => Hash::make('Admin@123456'), // Mật khẩu cứng
                'role'      => 'admin', // Quyền Admin
                'is_active' => true,
                'phone'     => '0900000000'
            ]);
            
            echo "Đã tạo tài khoản Admin: vudanghuong308@gmail.com' / Admin@123456 \n";
        }
    }
}