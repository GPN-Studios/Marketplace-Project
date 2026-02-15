<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'recipient_name',
        'cep',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
    ];

    //Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
