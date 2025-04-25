<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category_id',
    ];

    // Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ProductImage
    public function images()
{
    return $this->hasMany(\App\Models\ProductImage::class, 'product_id');
}

}
