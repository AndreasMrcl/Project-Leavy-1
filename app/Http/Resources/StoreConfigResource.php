<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreConfigResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'currency'         => $this->currency,
            'tax_percent'      => $this->tax_percent,
            'tax_active'       => (bool) $this->tax_active,
            'service_percent'  => $this->service_percent,
            'service_active'   => (bool) $this->service_active,
            'receipt_header'   => $this->receipt_header,
            'receipt_footer'   => $this->receipt_footer,
            'min_stock_alert'  => (int) $this->min_stock_alert,
            'updated_at'       => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
