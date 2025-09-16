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
        Schema::create('field_pricings', function (Blueprint $table) {
            $table->id('pricing_id');
            $table->unsignedBigInteger('field_id');
            $table->foreign('field_id')
                ->references('field_id')->on('fields')
                ->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_pricings');
    }
};
