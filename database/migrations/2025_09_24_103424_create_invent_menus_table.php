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
        Schema::create('invent_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('invent_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->string('variety')->default('normal');
            $table->decimal('quantity_used', 10, 2);
            $table->timestamps();

            $table->index(['menu_id', 'invent_id', 'variety']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invent_menus');
    }
};
