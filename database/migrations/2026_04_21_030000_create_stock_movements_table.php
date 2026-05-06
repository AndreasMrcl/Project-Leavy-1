<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('invent_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('set null');
            $table->integer('quantity');
            $table->enum('type', [
                'order_consume',
                'order_restore',
                'manual_adjust',
                'receive',
            ]);
            $table->nullableMorphs('reference');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
