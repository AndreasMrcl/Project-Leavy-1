<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('cart_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('no_order')->nullable();
            $table->string('cabang')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('status')->nullable();
            $table->enum('layanan', [
                'dine-in',
                'take-away',
            ])->default('dine-in');
            $table->string('payment_type')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('ongkir', 10, 2)->nullable();
            $table->string('no_telpon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
