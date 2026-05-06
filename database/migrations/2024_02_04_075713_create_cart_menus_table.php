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
        Schema::create('cart_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('cart_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('discount_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('variety')->nullable()->default('normal');
            $table->string('notes')->nullable();
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_menus');
    }
};
