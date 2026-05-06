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
        Schema::create('chairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('qr_token')->nullable()->unique();
            $table->string('device_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chairs');
    }
};
