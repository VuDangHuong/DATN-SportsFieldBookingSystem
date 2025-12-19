<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\User;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\ChangePasswordRequest;
class AuthController extends Controller
{
    /**
     * API Đăng ký tài khoản
     */
    public function register(RegisterRequest $request)
    {
        // Dữ liệu đã được validate ở RegisterRequest
        $userData = $request->validated();

        // AN TOÀN: Luôn ép role là student cho luồng đăng ký công khai
        $userData['role'] = 'student'; 
        $userData['password'] = Hash::make($request->password);

        $user = User::create($userData);

        // (Tùy chọn) Tạo token luôn để user đăng nhập ngay sau khi đăng ký
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * API Đăng nhập
     */
    public function login(LoginRequest $request)
    {
        // Kiểm tra thông tin đăng nhập (Email & Password)
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.'
            ], 401);
        }

        // Lấy thông tin user từ DB
        // Lưu ý: Phải dùng User ở namespace App\Models\Auth\User
        $user = User::where('email', $request->email)->firstOrFail();

        // Kiểm tra xem tài khoản có bị khóa không (is_active)
        if (!$user->is_active) {
            return response()->json([
                'message' => 'Tài khoản của bạn đã bị khóa.'
            ], 403);
        }

        // Tạo token (Xóa token cũ nếu muốn chỉ cho phép 1 thiết bị đăng nhập)
        // $user->tokens()->delete(); 
        $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
            'message' => 'Đăng nhập thành công!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role, // QUAN TRỌNG: Frontend dựa vào đây để redirect
            ]
        ]);
    }

    /**
     * API Đăng xuất (Thu hồi token)
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại đang dùng để gọi request này
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    /**
     * API Lấy thông tin user hiện tại (Profile)
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user(); // Lấy user đang đăng nhập

        // 1. Kiểm tra mật khẩu cũ có đúng không
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không chính xác.',
                'errors' => ['current_password' => ['Mật khẩu sai.']]
            ], 422);
        }

        // 2. Cập nhật mật khẩu mới
        $user->password = Hash::make($request->new_password);
        $user->save();

        // 3. (Tùy chọn) Xóa tất cả các token khác để bắt đăng nhập lại ở thiết bị khác
        // $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công!'
        ]);
    }

    // --- API 1: Gửi yêu cầu quên mật khẩu ---
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email' 
    ], [
        'email.exists' => 'Địa chỉ email này không tồn tại trong hệ thống.',
        'email.email' => 'Email không đúng định dạng.',
        'email.required' => 'Vui lòng nhập email.'
    ]);

    // Gửi link reset password (Laravel tự xử lý token và bảng password_reset_tokens)
    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
        return response()->json(['message' => 'Đường dẫn đặt lại mật khẩu đã được gửi vào email của bạn.']);
    }

    // Nếu lỗi (ví dụ email không tồn tại)
    return response()->json(['message' => 'Không thể gửi email. Vui lòng kiểm tra lại địa chỉ email.'], 400);
}

// --- API 2: Đặt lại mật khẩu mới (Sau khi user bấm link) ---
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
            
            // Có thể thêm dòng này để xóa hết token cũ (bảo mật)
             $user->tokens()->delete(); 
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return response()->json(['message' => 'Mật khẩu đã được đặt lại thành công.']);
    }

    return response()->json(['message' => 'Token không hợp lệ hoặc đã hết hạn.'], 400);
    }
}