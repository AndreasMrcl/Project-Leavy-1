<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'name',
            'percentage',
        ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function cartMenus()
    {
        return $this->hasMany(CartMenu::class);
    }
}
