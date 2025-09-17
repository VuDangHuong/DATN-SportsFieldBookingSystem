<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    // ...

    public function boot(): void
    {
        $this->registerPolicies();

        // Định nghĩa Gate cho Admin
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'ADMIN';
        });

        // Định nghĩa Gate cho Staff
        Gate::define('isStaff', function (User $user) {
            return $user->role === 'STAFF' || $user->role === 'ADMIN'; // Admin cũng có quyền của Staff
        });
        
        // Định nghĩa Gate cho Student
        Gate::define('isStudent', function (User $user) {
            return $user->role === 'STUDENT';
        });
    }
}