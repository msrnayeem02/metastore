<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class DeliveryPartner extends Model
{

    protected $fillable = [
        'user_id','partner_name','slug','credentials','status'
    ];

    protected $casts = [
        'credentials' => 'array',
        'status' => 'boolean',
    ];
}
