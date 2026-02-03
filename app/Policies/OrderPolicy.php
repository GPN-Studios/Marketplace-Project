<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    //checkout policy
    public function checkout(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'cart';
    }

    // users confirm they received their products:
    public function confirmDelivery(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'pending';
    }

    public function pay(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === 'pending';
    }
}
