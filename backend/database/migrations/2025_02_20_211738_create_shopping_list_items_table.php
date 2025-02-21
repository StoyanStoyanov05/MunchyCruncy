<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id(); // Auto-increment UNSIGNED BIGINT (primary key)
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade'); // Foreign key to shopping_lists
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade'); // Foreign key to ingredients
            $table->boolean('purchased')->default(false); // Boolean column for purchased status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
