<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'percentage' => (float) $this->percentage,
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
