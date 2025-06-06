<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'terms_conditions',
        'privacy_policy',
        'refund_policy',
    ];
}
