<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Size;

class Product extends Model
{
    protected $fillable = [
        'title',
        'price',
        'compare_price',
        'description',
        'short_description',
        'image',
        'category_id',
        'brands_id',
        'qty',
        'sku',
        'barcode',
        'status',
        'is_featured',

    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image == "") {
            return "";
        }

        return asset('uploads/products/small/' . $this->image);
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function sizes()
    {
        return $this->belongsToMany(
            Size::class,
            'product_sizes',
            'product_id',
            'size_id'
        );
    }
}
