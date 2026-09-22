<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Product extends Model
{
    use HasTags;

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'description',
        'price',
        'stock',
    ];

    /**
     * Preço armazenado em centavos, formatado como "29,90" (sem "R$").
     * Mantém a conversão centavos -> reais só no backend.
     */
    protected function priceFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => format_money($this->price),
        );
    }

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->hasMany(OrderItem::class);
    }
}
