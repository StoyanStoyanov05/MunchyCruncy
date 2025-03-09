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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id(); // Auto-increment UNSIGNED BIGINT (primary key)
            $table
                ->foreignId('recipe_id')
                ->constrained()
                ->onDelete('cascade'); // Foreign key to recipes
                
            $table
                ->foreignId('ingredient_id')
                ->constrained()
                ->onDelete('cascade'); // Foreign key to ingredients
                
            $table->integer('quantity')->nullable(); // Example extra field
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes_ingredients');
    }
};
