<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingIntegration extends Model
{
    protected $fillable = [
        'meta_pixel_id',
        'google_tag_manager_id',
    ];
}
