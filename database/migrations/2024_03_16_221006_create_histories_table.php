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
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('akun')->nullable();
            $table->string('name')->nullable();
            $table->string('no_order')->nullable();
            $table->string('order');
            $table->string('payment_type')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('status')->nullable();
            $table->foreignId('settlement_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
