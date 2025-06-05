<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    public function getConnectionName()
    {
        return auth()->check() && auth()->user()->tenant ? 'tenant' : $this->connection;
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }
}
