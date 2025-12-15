<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class OrderAdress extends Model
{

    protected $fillable = [
        'order_id',
        'cep',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
    ];



    // Relations

    public function order() {
        return $this->belongsTo(Order::class);
    }
}
