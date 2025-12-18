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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên nhóm (Nhóm 1, Nhóm Siêu Nhân...)
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('leader_id')->constrained('users'); // Nhóm trưởng
            $table->string('invitation_code')->nullable(); // Mã tham gia nhanh (nếu cần)
            $table->boolean('is_locked')->default(false); // Khóa nhóm sau khi nộp bài
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
