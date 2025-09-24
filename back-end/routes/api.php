<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Version 1
Route::prefix('auth')->group(function () {

    // Gom nhóm các route cho AuthController
    Route::controller(AuthController::class)->group(function () {
        // Public routes
        Route::post('/register', 'register');
        Route::post('/login', 'login');

        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', 'logout');
            Route::get('/profile', 'profile'); // <-- Đã đổi từ /user thành /profile và trỏ vào hàm mới
        });

        // Route này KHÔNG cần xác thực, là link người dùng nhấn từ email
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->name('verification.verify'); // <-- Tên này là bắt buộc

        // Route này CẦN xác thực, để người dùng có thể yêu cầu gửi lại email
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
            ->middleware(['auth:sanctum', 'throttle:6,1']);
    });
    // Thêm các route khác của bạn ở đây, ví dụ:
    // Route::apiResource('/fields', FieldController::class);
    // Route::apiResource('/bookings', BookingController::class)->middleware('auth:sanctum');

});