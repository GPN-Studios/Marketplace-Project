<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }

    /**
     * Determine whether the user can insert the product in their cart.
     */
    public function addToCart(User $user, Product $product): bool
    {
        return $user->id !== $product->user_id;
    }
}