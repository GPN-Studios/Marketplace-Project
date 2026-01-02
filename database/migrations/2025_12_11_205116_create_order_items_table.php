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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');
            
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('seller_id')
                ->constrained('users')
                ->onDelete('cascade');   // user_id do dono do produto
            
            $table->string('product_name');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // preço no momento da compra
            $table->decimal('subtotal', 10, 2); // quantidade * preço de um produto específico

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
