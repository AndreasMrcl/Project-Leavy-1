<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'no_order'       => $this->no_order,
            'akun'           => $this->akun,
            'name'           => $this->name,
            'order'          => $this->order,
            'payment_type'   => $this->payment_type,
            'status'         => $this->status,
            'total_amount'   => (int) $this->total_amount,
            'settlement_id'  => $this->settlement_id,
            'created_at'     => optional($this->created_at)->toIso8601String(),
        ];
    }
}
