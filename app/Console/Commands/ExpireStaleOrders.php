<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireStaleOrders extends Command
{
    protected $signature = 'orders:expire-stale';

    protected $description = 'Cancela pedidos "pending" cujo prazo de pagamento expirou e repõe o estoque reservado';

    public function handle(): int
    {
        $staleOrders = Order::query()
            ->where('status', OrderStatus::Pending)
            ->whereNotNull('checkout_expires_at')
            ->where('checkout_expires_at', '<', now())
            ->with('items')
            ->get();

        foreach ($staleOrders as $order) {
            DB::transaction(function () use ($order) {
                $locked = Order::whereKey($order->id)
                    ->where('status', OrderStatus::Pending)
                    ->lockForUpdate()
                    ->first();

                if (! $locked) {
                    return;
                }

                foreach ($locked->items as $item) {
                    $item->product()->increment('stock', $item->quantity);
                }

                $locked->update(['status' => OrderStatus::Cancelled]);
            });
        }

        $this->info("Pedidos expirados cancelados: {$staleOrders->count()}");

        return self::SUCCESS;
    }
}
