<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'product_id',
    ];

    // Product
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
    
}
