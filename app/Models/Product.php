<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'product_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'product_id', 'product_name', 'category', 'size', 'description',
        'rental_fee', 'deposit', 'rental_duration_days', 'status',
        'image', 'review',
    ];

    protected $casts = [
        'image' => 'array',
        'review' => 'array',
    ];
}
