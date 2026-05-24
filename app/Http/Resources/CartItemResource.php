<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'menu_id'             => $this->menu_id,
            'menu_name'           => $this->menu?->name,
            'unit_price'          => $this->menu ? (int) $this->menu->price : null,
            'variety'             => $this->variety,
            'quantity'            => (int) $this->quantity,
            'notes'               => $this->notes,
            'discount_id'         => $this->discount_id,
            'discount_name'       => $this->discount?->name,
            'discount_percentage' => $this->discount ? (float) $this->discount->percentage : null,
            'subtotal'            => (int) $this->subtotal,
        ];
    }
}
