<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',  // soma de todos os produtos somados
        'status',
        'checkout_expires_at',
        'stripe_session_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'checkout_expires_at' => 'datetime',
        ];
    }

    /** Total do pedido, em centavos, formatado como "129,90". */
    protected function totalFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => format_money($this->total),
        );
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }
}
