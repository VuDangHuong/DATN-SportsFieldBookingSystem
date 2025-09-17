<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; // Vẫn giữ lại cho hàm logout
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponser;
// Import 2 class Request vừa tạo
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * Xử lý yêu cầu đăng ký của người dùng.
     */
    use ApiResponser;
    public function register(RegisterRequest $request)
    {
        // Lấy dữ liệu đã được validate từ RegisterRequest
        $validatedData = $request->validated();
        $email = $validatedData['email'];
        $role = '';
        // 1. Logic xác định vai trò (role) dựa trên email
        if (str_ends_with($email, '@e.tlu.edu.vn')) {
            $role = 'STUDENT';
        } elseif (str_ends_with($email, '@s.tlu.edu.vn')) {
            $role = 'STAFF';
        } else {
            $role = 'GUEST';
        }

        // 2. Tạo người dùng với vai trò đã được xác định
        $user = User::create([
            'full_name' => $validatedData['full_name'],
            'email' => $email,
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'],
            'role' => $role, // Gán vai trò vào đây
        ]);

        // 3. Tạo token và trả về response
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->authResponse($token, $user, 'Đăng ký thành công!', 201);
    }

    /**
     * Xử lý yêu cầu đăng nhập.
     */
    public function login(LoginRequest $request) // <-- THAY ĐỔI Ở ĐÂY
    {
        // Tương tự, Laravel sẽ tự động validate request
        $credentials = $request->validated(); // Lấy email và password đã được validate

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không chính xác.'
            ], 401);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->authResponse($token, $user, 'Đăng nhập thành công!');
    }

    /**
     * Xử lý yêu cầu đăng xuất.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

          return $this->successResponse(null, 'Đăng xuất thành công!');
    }
}