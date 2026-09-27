<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    protected $table = 'staffs';

    protected $fillable = ['fullname', 'password', 'role'];

    protected $hidden = ['password'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
