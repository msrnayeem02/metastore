<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'price',
        'code',
        'initial_quantity',
        'stock_quantity',
        'category_id',
        'subcategory_id',
        'status',
        'variant_items',
    ];

    protected $casts = [
        'variant_items' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}