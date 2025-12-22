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
        Schema::table('orders', function (Blueprint $table) {
            // Single column indexes - check if they exist first
            if (!$this->indexExists('orders', 'idx_orders_pickup_date')) {
                $table->index('pickup_date', 'idx_orders_pickup_date');
            }
            if (!$this->indexExists('orders', 'idx_orders_status')) {
                $table->index('status', 'idx_orders_status');
            }
            if (!$this->indexExists('orders', 'idx_orders_customer_phone')) {
                $table->index('customer_phone', 'idx_orders_customer_phone');
            }
            if (!$this->indexExists('orders', 'idx_orders_created_at')) {
                $table->index('created_at', 'idx_orders_created_at');
            }

            // Composite indexes for common queries
            if (!$this->indexExists('orders', 'idx_orders_pickup_status')) {
                $table->index(['pickup_date', 'status'], 'idx_orders_pickup_status');
            }
            if (!$this->indexExists('orders', 'idx_orders_status_created')) {
                $table->index(['status', 'created_at'], 'idx_orders_status_created');
            }
        });

        Schema::table('orderItems', function (Blueprint $table) {
            if (!$this->indexExists('orderItems', 'idx_order_items_menu')) {
                $table->index('menu_id', 'idx_order_items_menu');
            }
            if (!$this->indexExists('orderItems', 'idx_order_items_order_menu')) {
                $table->index(['order_id', 'menu_id'], 'idx_order_items_order_menu');
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if (!$this->indexExists('menus', 'idx_menus_available')) {
                $table->index('is_available', 'idx_menus_available');
            }
            if (!$this->indexExists('menus', 'idx_menus_category')) {
                $table->index('category', 'idx_menus_category');
            }
            if (!$this->indexExists('menus', 'idx_menus_available_category')) {
                $table->index(['is_available', 'category'], 'idx_menus_available_category');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'idx_users_role')) {
                $table->index('role', 'idx_users_role');
            }
        });
    }

    /**
     * Helper method to check if index exists using raw SQL
     */
    private function indexExists($tableName, $indexName)
    {
        $databaseName = env('DB_DATABASE');
        $result = DB::select("
            SELECT COUNT(1) as `count`
            FROM INFORMATION_SCHEMA.STATISTICS
            WHERE table_schema = ? 
            AND table_name = ? 
            AND index_name = ?
        ", [$databaseName, $tableName, $indexName]);

        return $result[0]->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if ($this->indexExists('orders', 'idx_orders_pickup_date')) {
                $table->dropIndex('idx_orders_pickup_date');
            }
            if ($this->indexExists('orders', 'idx_orders_status')) {
                $table->dropIndex('idx_orders_status');
            }
            if ($this->indexExists('orders', 'idx_orders_customer_phone')) {
                $table->dropIndex('idx_orders_customer_phone');
            }
            if ($this->indexExists('orders', 'idx_orders_created_at')) {
                $table->dropIndex('idx_orders_created_at');
            }
            if ($this->indexExists('orders', 'idx_orders_pickup_status')) {
                $table->dropIndex('idx_orders_pickup_status');
            }
            if ($this->indexExists('orders', 'idx_orders_status_created')) {
                $table->dropIndex('idx_orders_status_created');
            }
        });

        Schema::table('orderItems', function (Blueprint $table) {
            if ($this->indexExists('orderItems', 'idx_order_items_menu')) {
                $table->dropIndex('idx_order_items_menu');
            }
            if ($this->indexExists('orderItems', 'idx_order_items_order_menu')) {
                $table->dropIndex('idx_order_items_order_menu');
            }
        });

        Schema::table('menus', function (Blueprint $table) {
            if ($this->indexExists('menus', 'idx_menus_available')) {
                $table->dropIndex('idx_menus_available');
            }
            if ($this->indexExists('menus', 'idx_menus_category')) {
                $table->dropIndex('idx_menus_category');
            }
            if ($this->indexExists('menus', 'idx_menus_available_category')) {
                $table->dropIndex('idx_menus_available_category');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if ($this->indexExists('users', 'idx_users_role')) {
                $table->dropIndex('idx_users_role');
            }
        });
    }
};
