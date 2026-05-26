<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'no_order'          => $this->no_order,
            'status'            => $this->status,
            'payment_type'      => $this->payment_type,
            'payment_reference' => $this->payment_reference,
            'layanan'           => $this->layanan,
            'atas_nama'         => $this->atas_nama,
            'no_telpon'         => $this->no_telpon,
            'total_amount'      => $this->cart ? (int) $this->cart->total_amount : 0,
            'created_at'        => optional($this->created_at)->toIso8601String(),
            'updated_at'        => optional($this->updated_at)->toIso8601String(),
            'cart'              => $this->cart ? [
                'id'            => $this->cart->id,
                'customer_name' => $this->cart->customer_name,
                'chair'         => $this->cart->chair ? [
                    'id'   => $this->cart->chair->id,
                    'name' => $this->cart->chair->name,
                ] : null,
                'items'         => CartItemResource::collection($this->cart->cartMenus),
            ] : null,
        ];
    }
}
