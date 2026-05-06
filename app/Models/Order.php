<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'cart_id',
            'no_order',
            'status',
            'cabang',
            'ongkir',
            'layanan',
            'payment_type',
            'payment_reference',
            'atas_nama',
            'no_telpon',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
