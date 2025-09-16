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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id'); // payment_id INT AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('booking_id'); // booking_id INT NOT NULL
            $table->decimal('amount', 10, 2); // amount DECIMAL(10,2) NOT NULL
            $table->enum('method', ['CASH', 'MOMO', 'ZALOPAY', 'VNPAY']); // ENUM
            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED'])->default('PENDING');
            $table->timestamp('paid_at')->useCurrent(); // paid_at DATETIME DEFAULT CURRENT_TIMESTAMP

            // Foreign key
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
