<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    // Nhận thêm tham số ...$roles
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Lấy user hiện tại đang đăng nhập
        $user = $request->user();

        // Kiểm tra nếu user không tồn tại hoặc role không nằm trong danh sách cho phép
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Bạn không có quyền truy cập.'], 403);
        }

        return $next($request);
    }
}