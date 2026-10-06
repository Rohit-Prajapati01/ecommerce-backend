<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class Size extends Model
{


    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'product_sizes',
            'size_id',
            'product_id'
        );
    }
}
