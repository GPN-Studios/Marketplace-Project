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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();

            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();

            $table->text('description')->nullable();

            // true = positive | false = negative
            $table->boolean('is_positive');

            $table->timestamps();

            // 1 rating per order item
            $table->unique('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
