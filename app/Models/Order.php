<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;




class Order extends Model
{
    protected $fillable = [
        'name',
        'email',
        'address',
        'mobile',
        'state',
        'zip',
        'city',
        'grandtotal',
        'subtotal',
        'discount',
        'shipping',
        'payment_status',
        'payment_method',
        'status',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d M,Y',
        ];
    }
}
