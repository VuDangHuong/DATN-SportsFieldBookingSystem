<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponser
{
    /**
     * Xây dựng response thành công.
     *
     * @param  mixed  $data
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Xây dựng response lỗi.
     *
     * @param  string|null  $message
     * @param  int  $code
     * @param  mixed|null  $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = null, int $code, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Xây dựng response dành riêng cho việc xác thực (login/register).
     *
     * @param  string  $token
     * @param  \App\Models\User  $user
     * @param  string|null  $message
     * @param  int  $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authResponse(string $token, $user, string $message = null, int $code = 200): JsonResponse
    {
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ];

        return $this->successResponse($data, $message, $code);
    }
}