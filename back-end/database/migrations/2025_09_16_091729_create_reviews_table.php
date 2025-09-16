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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id'); // review_id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('user_id'); // user_id INT NOT NULL
            $table->unsignedBigInteger('field_id'); // field_id INT NOT NULL
            $table->integer('rating'); // rating INT CHECK (1-5)
            $table->text('comment')->nullable(); // comment TEXT
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('field_id')->references('field_id')->on('fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
