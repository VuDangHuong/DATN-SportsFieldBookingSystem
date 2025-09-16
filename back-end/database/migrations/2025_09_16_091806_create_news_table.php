<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id('news_id');
            $table->string('title', 200);
            $table->text('content');
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->foreign('author_id')
                ->references('user_id')->on('users')
                ->onDelete('set null');
            $table->enum('status', ['DRAFT', 'PUBLISHED'])->default('DRAFT');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
