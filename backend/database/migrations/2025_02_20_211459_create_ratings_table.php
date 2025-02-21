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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id(); // Auto-increment UNSIGNED BIGINT (primary key)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade'); // Foreign key to recipes
            $table->tinyInteger('rating')->check('rating between 1 and 5'); // Ensures rating is between 1 and 5
            $table->timestamps(); // Creates `created_at` & `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
