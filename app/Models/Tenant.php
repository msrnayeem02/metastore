<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    // Use the central connection (set in config/database.php as 'mysql' by default)
    protected $connection = 'mysql'; 

    // Table name
    protected $table = 'tenants';

    // Fillable columns for mass assignment
    protected $fillable = [
        'name',
        'shop_name',
        'domain',
        'custom_domain',
        'database_name',
        'database_username',
        'database_password',
    ];

    // If you need to hide sensitive info when serializing
    protected $hidden = [
        'database_password',
        'database_username',
    ];
}