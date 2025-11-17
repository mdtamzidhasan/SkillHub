<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'provider_id',
        'total_amount',
        'status',
        'placed_at',
        'completed_at',
    ];

    public function items(){
        return $this->hasMany(OrderItem::class);
    }
}