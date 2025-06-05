<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'payment_data',
        'currency',
        'payment_date'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'payment_date' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Helper to check if payment is completed
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    // Helper to check if payment is pending
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    // Helper to check if payment has failed
    public function hasFailed()
    {
        return $this->payment_status === 'failed';
    }
}
