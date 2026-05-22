<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Chair extends Authenticatable
{
    use BelongsToStore, HasApiTokens, Notifiable;

    protected $fillable = [
        'store_id',
        'name',
        'email',
        'password',
        'qr_token',
        'device_id',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
