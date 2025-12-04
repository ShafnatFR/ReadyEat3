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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_code')->unique(); // <--- PENTING
            $table->enum('status', ['unpaid', 'payment_pending', 'ready_for_pickup', 'picked_up', 'cancelled'])->default('unpaid'); // <--- PENTING
            $table->date('pickup_date'); // <--- PENTING
            $table->text('notes')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
