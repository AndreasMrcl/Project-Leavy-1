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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2); // Changed to decimal for currency
            $table->string('img')->nullable(); // Added nullable for optional image
            $table->string('description');
            $table->boolean('has_variety')->default(false);
            $table->json('varieties')->nullable();
            $table->unsignedBigInteger('category_id'); // Define the category_id column
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
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