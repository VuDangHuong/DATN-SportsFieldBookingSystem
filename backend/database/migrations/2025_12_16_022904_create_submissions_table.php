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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('submitter_id')->constrained('users'); // Nhóm trưởng nộp
            
            $table->string('report_file')->nullable(); // PDF/Doc
            $table->string('source_code_link')->nullable(); // Git
            $table->string('slide_file')->nullable();
            
            $table->boolean('is_integrity_confirmed')->default(false); // Cam kết trung thực
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
