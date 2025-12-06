<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('orders', 'customer_phone')) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('customer_phone');
            }
        });

        // Fix status enum - drop and recreate with correct values
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('unpaid', 'payment_pending', 'Pending Verification', 'Preparing', 'ready_for_pickup', 'Ready for Pickup', 'picked_up', 'Completed', 'cancelled', 'Rejected') DEFAULT 'unpaid'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_phone', 'payment_proof']);
        });

        // Restore original enum
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('unpaid', 'payment_pending', 'ready_for_pickup', 'picked_up', 'cancelled') DEFAULT 'unpaid'");
    }
};
