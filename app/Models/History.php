<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'akun',
            'name',
            'no_order',
            'order',
            'payment_type',
            'total_amount',
            'status',
            'settlement_id',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }
}
