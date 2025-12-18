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
        Schema::create('peer_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluator_id')->constrained('users'); // Người chấm
            $table->foreignId('evaluated_id')->constrained('users'); // Người được chấm
            $table->foreignId('class_id')->constrained(); // Trong phạm vi lớp nào
            
            $table->integer('score'); // Điểm (thang 10 hoặc 100)
            $table->text('comment')->nullable();
            $table->string('phase')->nullable(); // Giai đoạn đánh giá (Giữa kỳ, Cuối kỳ)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peer_evaluations');
    }
};
