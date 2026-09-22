<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    // checkout policy
    public function checkout(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === OrderStatus::Cart;
    }

    public function pay(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === OrderStatus::Pending;
    }

    // usuário confirma que recebeu os produtos (só possível depois que o
    // pagamento foi de fato confirmado pelo webhook do Stripe, ou seja
    // com o pedido já em 'paid' — nunca antes disso).
    public function confirmDelivery(User $user, Order $order): bool
    {
        return $order->user_id === $user->id && $order->status === OrderStatus::Paid;
    }

    // usuário pode ver a página de confirmação/cancelamento do próprio pedido
    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
    }
}
