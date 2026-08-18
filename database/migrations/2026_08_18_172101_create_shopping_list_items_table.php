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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shopping_list_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->unsignedInteger('price_in_pence');
            $table->unsignedInteger('quantity')->default(1);
            $table->boolean('is_purchased')->default(false);
            $table->timestamps();

            $table->unique(['shopping_list_id', 'name']);
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
