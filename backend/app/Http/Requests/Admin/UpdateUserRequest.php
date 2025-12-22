<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        // Lấy ID của user đang được sửa từ router
        $userId = $this->route('id'); 

        return [
            // Cho phép trùng với chính nó (ignore), nhưng không được trùng với người khác
            'code' => 'required|string|unique:users,code,'.$userId,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$userId,
            
            'password' => 'nullable|string|min:6', // Password để trống nghĩa là không đổi
            'role' => 'required|in:admin,lecturer,student',
            'phone' => 'nullable|string|max:15',
            'is_active' => 'boolean'
        ];
    }
}