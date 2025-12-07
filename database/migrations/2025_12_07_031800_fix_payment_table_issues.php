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
        Schema::table('payment', function (Blueprint $table) {
            // Rename prof_image to proof_image (fix typo)
            $table->renameColumn('prof_image', 'proof_image');

            // Add missing status field
            $table->enum('status', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            // Revert column name
            $table->renameColumn('proof_image', 'prof_image');

            // Drop status field
            $table->dropColumn('status');
        });
    }
};
