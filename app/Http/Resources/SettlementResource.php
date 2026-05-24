<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettlementResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'start_time'   => optional($this->start_time)->toIso8601String(),
            'end_time'     => optional($this->end_time)->toIso8601String(),
            'start_amount' => $this->start_amount !== null ? (int) $this->start_amount : null,
            'total_amount' => $this->total_amount !== null ? (int) $this->total_amount : null,
            'expected'     => $this->expected !== null ? (int) $this->expected : null,
            'is_active'    => $this->end_time === null,
            'histories'    => HistoryResource::collection($this->whenLoaded('histories')),
            'created_at'   => optional($this->created_at)->toIso8601String(),
        ];
    }
}
