<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'is_open_bill' => (bool) $this->is_open_bill,
            'chair'        => $this->chair ? [
                'id'   => $this->chair->id,
                'name' => $this->chair->name,
            ] : null,
            'total_amount' => (int) $this->total_amount,
            'opened_at'    => optional($this->opened_at)->toIso8601String(),
            'items'        => CartItemResource::collection($this->whenLoaded('cartMenus')),
        ];
    }
}
