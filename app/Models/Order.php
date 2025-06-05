<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderItem;
use App\Models\DeliveryCharge;
use App\Models\Product;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_contact',
        'customer_name',
        'customer_address',
        'products',
        'order_status',
        'payment_method',
        'payment_status',
        'delivery_zone_id',
        'zone_name',
        'delivery_charge',
        'subtotal',
        'discount_amount',
        'total_price',
        'ordered_quantity',
        'invoice',
        'courier_partner',
        'trackingid',
        'courier_response'
    ];

    protected $casts = [
        'products' => 'array',
        'courier_response' => 'array'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryCharge::class, 'delivery_zone_id');
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    // Get the latest payment for this order
    public function latestPayment()
    {
        return $this->payments()->latest()->first();
    }

    // Check if order is Cash on Delivery
    public function isCashOnDelivery()
    {
        return $this->payment_method === 'Cash On Delivery' || $this->payment_method === 'cod';
    }

    // Calculate due amount
    public function getDueAmount()
    {
        $paidAmount = $this->payments()->where('payment_status', 'completed')->sum('amount');
        return $this->total_price - $paidAmount;
    }

    // For the product relationship - this looks like it needs fixing
    public function products()
    {
        // This should reference a pivot table or be removing entirely if you're 
        // storing products as JSON in the 'products' column
        return json_decode($this->products, true) ?? [];
    }
}
