<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id(); // Auto-increment UNSIGNED BIGINT (primary key)
            $table->string('name')->unique(); // Unique name for each ingredient
            $table->boolean('is_liquid')->default(false); // Tinyint (1) → Boolean
            $table->timestamps(); // Creates `created_at` & `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints(); // Disable FK constraints
        Schema::dropIfExists('ingredients');
        Schema::enableForeignKeyConstraints(); // Re-enable FK constraints
    }
};
