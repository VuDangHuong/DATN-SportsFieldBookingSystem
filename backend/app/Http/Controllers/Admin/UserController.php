<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Hash;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
class UserController extends Controller
{
    public function store(Request $request) 
    {
        // 1. Validate dữ liệu
        $validated = $request->validate([
            'code'  => 'required|string|unique:users,code', 
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // Chỉ cho phép tạo role lecturer (hoặc thêm admin phụ nếu muốn)
            'role'  => 'required|in:lecturer', 
        ], [
            'role.in' => 'Bạn chỉ có quyền tạo tài khoản Giảng viên.'
        ]);

        // 2. Tạo User
        $user = User::create([
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            // Mật khẩu mặc định, GV sẽ đổi sau
            'password'  => Hash::make('123456789'), 
            'role'      => $validated['role'], // Chắc chắn là 'lecturer' do validate ở trên
            'is_active' => true,
        ]);
        
        // (Tùy chọn) Gửi email thông báo cho GV tại đây...
        
        return response()->json([
            'message' => 'Đã tạo tài khoản Giảng viên thành công',
            'data' => $user
        ], 201);
    }
    public function import(Request $request) 
{
    // 1. Validate file gửi lên
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Chỉ nhận file excel, tối đa 2MB
    ]);

    try {
        // 2. Thực hiện Import
        Excel::import(new StudentsImport, $request->file('file'));
        
        return response()->json([
            'message' => 'Import danh sách sinh viên thành công!',
        ]);
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        // Bắt lỗi nếu dữ liệu trong Excel bị trùng hoặc sai
        $failures = $e->failures();
        return response()->json([
            'message' => 'Dữ liệu trong file Excel bị lỗi.',
            'errors' => $failures
        ], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
    }
}
}