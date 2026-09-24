<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'item' => 'array',
    ];
}
