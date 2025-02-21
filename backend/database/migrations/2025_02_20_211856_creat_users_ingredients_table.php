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
        Schema::create('user_ingredients', function (Blueprint $table) {
            $table->id(); // Auto-increment UNSIGNED BIGINT (primary key)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade'); // Foreign key to ingredients table
            $table->string('quantity', 100); // String column for the quantity (max 100 characters)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
