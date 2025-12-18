<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            
            // Đây là đường dẫn đến FRONTEND (React/Vue/Android) của bạn
            // Ví dụ Frontend chạy ở port 3000
            $frontendUrl = 'http://localhost:3000/reset-password'; 
            
            // Trả về link đầy đủ kèm token và email
            return "{$frontendUrl}?token={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
