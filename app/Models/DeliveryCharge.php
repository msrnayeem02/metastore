<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    use HasFactory;

    public function getConnectionName()
    {
        return auth()->check() && auth()->user()->tenant ? 'tenant' : $this->connection;
    }
    
    protected $fillable = ['zone_name', 'delivery_charge', 'status'];
}
