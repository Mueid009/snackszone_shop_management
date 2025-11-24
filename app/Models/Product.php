<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'price',
        'stock',
    ];
    protected $casts = ['stock' => 'integer', 'price' => 'decimal:2'];
}
