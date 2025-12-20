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
            $table->string('invoice_code')->unique();

            // Status Consolidated from fix_orders_table migration
            $table->enum('status', [
                'unpaid',
                'payment_pending',
                'ready_for_pickup',
                'picked_up',
                'cancelled',
                'rejected'
            ])->default('unpaid');


            $table->date('pickup_date');
            $table->text('notes')->nullable();

            // Merged columns from add_admin_fields and fix_orders_table
            $table->text('admin_note')->nullable();
            $table->string('payment_proof')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();

            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();

            // Indexes from add_performance_indexes migration
            $table->index('pickup_date');
            $table->index('status');
            $table->index('customer_phone');
            $table->index('created_at');
            $table->index(['pickup_date', 'status']); // For filtering orders by status and date
            $table->index(['status', 'created_at']);  // For sorting/filtering by status and time
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
