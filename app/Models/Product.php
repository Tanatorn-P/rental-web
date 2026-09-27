<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'image', 'category', 'description', 'rental_fee', 'deposit',
        'rental_duration_days', 'status', 'product_name',
    ];

    protected $casts = [
        'image' => 'array',
    ];
}
