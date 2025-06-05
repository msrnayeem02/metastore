<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{

    protected $fillable = ['gateway', 'config', 'status'];

    /**
     * Get the gateway config as array
     */
    public function getConfigAttribute($value)
    {
        return json_decode($value, true);
    }
}
