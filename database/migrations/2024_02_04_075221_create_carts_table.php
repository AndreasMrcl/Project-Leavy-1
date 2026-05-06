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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('chair_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_open_bill')->default(false);
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'is_open_bill']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
