<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('lecturer_id')->constrained('users'); // Giảng viên phụ trách
            $table->string('name'); // Tên lớp (VD: KTPM 01)
            
            // Cấu hình tham số hệ thống cho lớp này
            $table->integer('min_members')->default(3);
            $table->integer('max_members')->default(5);
            $table->dateTime('group_registration_deadline')->nullable(); // Hạn chót ghép nhóm
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
