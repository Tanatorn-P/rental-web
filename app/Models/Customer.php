<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $table = 'customers';

    protected $primaryKey = 'customer_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'customer_id', 'username', 'password', 'fullname', 'phone',
        'address', 'bank_account', 'bust', 'shoulder', 'waist', 'hips',
        'line_user_id',
    ];

    protected $hidden = ['password'];
}
