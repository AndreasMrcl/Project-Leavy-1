<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id',
        'prompt',
        'response',
    ];
}
