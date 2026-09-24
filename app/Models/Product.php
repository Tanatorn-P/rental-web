<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'image' => 'array',
        'review' => 'array',
        'status' => 'boolean',
    ];
}
