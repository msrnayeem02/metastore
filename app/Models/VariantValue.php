<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    protected $fillable = ['variant_id', 'name', 'slug', 'status'];
    public function getConnectionName()
    {
        return auth()->check() && auth()->user()->tenant ? 'tenant' : $this->connection;
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }
}