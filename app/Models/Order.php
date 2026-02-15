<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\OrderAddress;


class Order extends Model
{

    protected $fillable = [
        'user_id', 
        'total',  // soma de todos os produtos somados
        'status',
    ];


    //Relations
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function adress() {
        return $this->hasOne(OrderAddress::class);
    }
}
