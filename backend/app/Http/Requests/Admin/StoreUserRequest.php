<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'code' => 'required|string|unique:users,code', // Mã không được trùng
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Email không được trùng
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,lecturer,student', // Chỉ chấp nhận 3 quyền này
            'phone' => 'nullable|string|max:15',
            'is_active' => 'boolean'
        ];
    }
}