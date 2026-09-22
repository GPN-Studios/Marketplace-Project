<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_name',
        'quantity',
        'price',
        'subtotal',
    ];

    /** Preço unitário (no momento da compra), em centavos, formatado como "29,90". */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => format_money($this->price),
        );
    }

    /** Subtotal (preço x quantidade), em centavos, formatado como "59,80". */
    protected function subtotalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => format_money($this->subtotal),
        );
    }

    // Relations

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
