<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Chair extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable =
        [
            'store_id',
            'name',
            'email',
            'password',
            'qr_token',
            'device_id',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
