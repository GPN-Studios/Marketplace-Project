<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_item_id',
        'buyer_id',
        'seller_id',
        'is_positive',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_positive' => 'boolean',
        ];
    }

    // Relations
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
