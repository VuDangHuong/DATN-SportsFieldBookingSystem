<?php

namespace App\Imports;

use App\Models\Auth\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation; // Để kiểm tra dữ liệu
use Illuminate\Support\Facades\Hash;
class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * Map dữ liệu từ 1 dòng Excel vào Model User
    */
    public function model(array $row)
    {
        return new User([
            'code'      => $row['code'],     // Tên cột trong file Excel
            'name'      => $row['name'],
            'email'     => $row['email'],
            'password'  => Hash::make($row['password']),             // QUAN TRỌNG: Để trống mật khẩu
            'role'      => 'student',        // Mặc định là sinh viên
            'is_active' => true,            // Chưa kích hoạt
        ]);
    }

    /**
     * Validate dữ liệu trong file Excel
     */
    public function rules(): array
    {
        return [
            'code' => 'required|unique:users,code', // Mã SV không được trùng DB
            'email' => 'required|email|unique:users,email', // Email không được trùng
            'name' => 'required',
        ];
    }
}