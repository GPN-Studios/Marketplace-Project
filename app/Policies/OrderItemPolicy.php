<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Models\User;

class OrderItemPolicy
{
    /**
     * Determine whether the user can change the quantity of, or remove,
     * this item — only the owner of the order, and only while it is
     * still a cart (not yet checked out).
     */
    public function manage(User $user, OrderItem $item): bool
    {
        return $item->order->user_id === $user->id
            && $item->order->status === OrderStatus::Cart;
    }

    /**
     * Determine whether the user (comprador) pode avaliar este item —
     * só o dono do pedido, só depois de concluído, e só uma vez.
     */
    public function rate(User $user, OrderItem $item): bool
    {
        return $item->order->user_id === $user->id
            && $item->order->status === OrderStatus::Completed
            && $item->rating === null;
    }
}
