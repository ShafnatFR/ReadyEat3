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
        Schema::table('users', function (Blueprint $table) {
            // Add phone field after email
            $table->string('phone', 15)->nullable()->after('email');

            // Add index for faster phone lookups
            $table->index('phone', 'idx_users_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('idx_users_phone');

            // Then drop column
            $table->dropColumn('phone');
        });
    }
};
