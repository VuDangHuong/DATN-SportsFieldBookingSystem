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
        Schema::create('fields', function (Blueprint $table) {
            $table->id('field_id');
            $table->string('name', 100);
            $table->enum('type', ['FOOTBALL', 'BADMINTON', 'TENNIS', 'PICKLEBALL', 'TABLE_TENNIS']);
            // location VARCHAR(255)
            $table->string('location')->nullable();
            // price_per_hour DECIMAL(10,2) NOT NULL
            $table->decimal('price_per_hour', 10, 2);
            // description TEXT
            $table->text('description')->nullable();
            // created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            // updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
