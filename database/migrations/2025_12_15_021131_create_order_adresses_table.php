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
        Schema::create('order_adresses', function (Blueprint $table) {
            $table->id();

            $table->string('cep', 9);
            $table->string('state', 2);
            $table->string('city');
            $table->string('district'); // bairro
            $table->string('street');
            $table->string('number')->nullable();
            $table->string('complement')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_adresses');
    }
};
