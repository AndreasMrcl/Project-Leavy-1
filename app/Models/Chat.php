<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable =
        [
            'store_id',
            'prompt',
            'response',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
