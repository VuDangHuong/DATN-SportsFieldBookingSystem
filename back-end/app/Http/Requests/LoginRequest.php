<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Cho phép tất cả mọi người thực hiện request này
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
            'email' => 'required|email',
            'password' => 'required',
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
            'email.required' => 'Bạn chưa nhập email.',
            'email.email'    => 'Địa chỉ email không hợp lệ.',
            'password.required' => 'Bạn chưa nhập mật khẩu.',
        ];
    }
}