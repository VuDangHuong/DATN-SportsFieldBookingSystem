<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // ... các code khác ...

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [ // Ở Laravel 10 có thể tên là $middlewareAliases
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // ... các middleware khác ...
        
        // --- THÊM DÒNG NÀY VÀO ---
        'role' => \App\Http\Middleware\CheckRole::class,
    ];
}