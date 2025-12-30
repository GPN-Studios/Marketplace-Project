<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;

class Product extends Model
{

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'description',
        'price',
        'stock',
    ];


    //Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function order() {
        return $this->hasMany(OrderItem::class);
    }
}
