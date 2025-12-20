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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('category')->default('Katering'); // Added from add_category migration
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image');
            $table->integer('daily_limit')->default(50);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            // Indexes from add_indexes and add_performance_indexes migrations
            $table->index('is_available');
            $table->index('category');
            $table->index(['is_available', 'category']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
