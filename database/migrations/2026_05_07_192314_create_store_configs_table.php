<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('currency', 10)->default('IDR');
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('service_percent', 5, 2)->default(0);
            $table->boolean('tax_active')->default(false);
            $table->boolean('service_active')->default(false);
            $table->integer('min_stock_alert')->default(5);
            $table->integer('auto_archive_days')->default(30);
            $table->string('receipt_header')->nullable();
            $table->string('receipt_footer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_configs');
    }
};