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
        Schema::table('menus', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('is_available', 'idx_menus_is_available');
            $table->index('category', 'idx_menus_category');

            // Composite index for common filter combinations
            $table->index(['is_available', 'category'], 'idx_menus_available_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropIndex('idx_menus_is_available');
            $table->dropIndex('idx_menus_category');
            $table->dropIndex('idx_menus_available_category');
        });
    }
};
