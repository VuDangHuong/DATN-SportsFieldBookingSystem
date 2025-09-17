<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Cho phép tất cả mọi người (kể cả guest) thực hiện request này
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'full_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
        ];
    }

    /**
     * Get the custom validation messages for the defined rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'full_name.required' => 'Họ và tên không được để trống.',
            'full_name.string'   => 'Họ và tên phải là một chuỗi ký tự.',
            'full_name.max'      => 'Họ và tên không được vượt quá 100 ký tự.',
            
            'email.required' => 'Email không được để trống.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email này đã tồn tại trong hệ thống.',
            
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            
            'phone.string' => 'Số điện thoại phải là một chuỗi ký tự.',
            'phone.max'    => 'Số điện thoại không được vượt quá 15 ký tự.',
        ];
    }
}