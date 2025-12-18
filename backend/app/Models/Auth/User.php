<?php

namespace App\Models\Auth;

// 1. QUAN TRỌNG: Phải dùng Authenticatable thay vì Model thường
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens; // Import thư viện Sanctum

class User extends Authenticatable
{
    // 2. QUAN TRỌNG: Khai báo sử dụng Trait ở đây để có hàm createToken()
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Các trường được phép thêm sửa (Mass Assignment)
     */
    protected $fillable = [
        'code',       // Mã SV/GV
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',       // student, lecturer, admin
        'is_active',
    ];

    /**
     * Ẩn mật khẩu khi trả về JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Tự động hash mật khẩu
        'is_active' => 'boolean',
    ];

    // --- CÁC HÀM CHECK QUYỀN NHANH ---
    public function isAdmin() { return $this->role === 'admin'; }
    public function isLecturer() { return $this->role === 'lecturer'; }
    public function isStudent() { return $this->role === 'student'; }
}