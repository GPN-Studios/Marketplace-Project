<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Product;

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


    //Relations
    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
