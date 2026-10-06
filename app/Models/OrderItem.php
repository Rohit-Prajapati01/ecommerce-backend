<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'price',
        'unit_price',
        'qty',
        'product_id',
        'size'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
