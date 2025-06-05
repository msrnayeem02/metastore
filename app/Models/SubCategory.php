<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'status'];
    public function getConnectionName()
    {
        return auth()->check() && auth()->user()->tenant ? 'tenant' : $this->connection;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
