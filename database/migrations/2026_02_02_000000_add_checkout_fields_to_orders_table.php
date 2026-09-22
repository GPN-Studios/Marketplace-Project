<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // prazo para o pagamento do pedido 'pending'; usado pelo comando
            // orders:expire-stale para repor estoque de pedidos abandonados.
            $table->timestamp('checkout_expires_at')->nullable()->after('status');

            // id da sessão do Stripe Checkout que pagou este pedido (gravado
            // pelo webhook, útil para suporte/depuração e idempotência).
            $table->string('stripe_session_id')->nullable()->after('checkout_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['checkout_expires_at', 'stripe_session_id']);
        });
    }
};
