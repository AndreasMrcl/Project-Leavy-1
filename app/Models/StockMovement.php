<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'invent_id',
            'user_id',
            'quantity',
            'type',
            'reference_type',
            'reference_id',
            'notes',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function invent()
    {
        return $this->belongsTo(Invent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
