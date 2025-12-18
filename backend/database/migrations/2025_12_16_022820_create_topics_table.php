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
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('name_vi'); // Tên đề tài
            $table->string('name_en')->nullable();
            $table->text('description');
            $table->text('technology'); // Công nghệ sử dụng
            
            // Trạng thái phê duyệt
            $table->enum('status', ['pending', 'approved', 'rejected', 'correction_required'])->default('pending');
            $table->text('lecturer_comment')->nullable(); // Nhận xét của GV
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
