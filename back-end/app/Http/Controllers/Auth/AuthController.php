<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
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

    public function verifyEmail(Request $request)
{
    // Tìm user dựa vào id từ URL
    $user = User::findOrFail($request->route('id'));

    // Kiểm tra xem hash từ URL có khớp với hash được tạo từ email của user không
    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return $this->errorResponse('Link xác thực không hợp lệ.', 403);
    }

    // Kiểm tra xem user đã xác thực email trước đó chưa
    if ($user->hasVerifiedEmail()) {
        return $this->successResponse(null, 'Email đã được xác thực từ trước.');
    }

    // Nếu chưa, đánh dấu là đã xác thực và kích hoạt sự kiện 'Verified'
    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return $this->successResponse(null, 'Xác thực email thành công!');
}

    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->errorResponse('Email đã được xác thực từ trước.', 400);
        }
        $request->user()->sendEmailVerificationNotification();
        return $this->successResponse(null, 'Link xác thực mới đã được gửi!');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email']);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return $this->successResponse(null, 'Link reset mật khẩu đã được gửi!');
        }
        return $this->errorResponse('Không thể gửi link reset mật khẩu.', 422);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
        }

        $status = Password::reset($request->all(), function ($user, $password) {
            $user->forceFill(['password' => bcrypt($password)])->save();
        });

        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse(null, 'Mật khẩu đã được reset thành công!');
        }
        return $this->errorResponse('Token không hợp lệ.', 422);
    }
}
