<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'address', 'cart_data', 'subtotal', 'shipping_charge', 'total'];

    protected $casts = [
        'cart_data' => 'array',
    ];
}