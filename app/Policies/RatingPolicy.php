<?php

namespace App\Policies;

use App\Models\OrderItem;
use App\Models\User;

class RatingPolicy
{
    public function rate(User $user, OrderItem $orderItem): bool
    {
        // verifies if user is the order owner
        return $orderItem->order->user_id === $user->id
        && $orderItem->order->status === 'completed'
        && $orderItem->rating === null;
    }
}
