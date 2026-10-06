<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    protected $table = 'shipping';
    protected $fillable = [
        'id',
        'shipping_charge'
    ];
}
