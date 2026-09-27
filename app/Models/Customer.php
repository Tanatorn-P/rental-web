<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $table = 'customers';

    protected $fillable = [
        'username', 'password', 'phone', 'customer_id', 'fullname',
        'address', 'bank_account', 'bust', 'shoulder', 'waist', 'hips',
    ];

    protected $hidden = ['password'];
}