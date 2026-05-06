<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventMenu extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'store_id',
            'menu_id',
            'invent_id',
            'variety',
            'quantity_used',
        ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function invent()
    {
        return $this->belongsTo(Invent::class);
    }
}
