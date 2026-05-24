<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    use ApiResponse;

    public function show(Request $request, int $id)
    {
        $storeId = $request->user()->store->id;

        $order = Order::with([
            'cart.cartMenus.menu',
            'cart.cartMenus.discount',
            'cart.chair',
            'store.storeConfig',
        ])
            ->where('id', $id)
            ->where('store_id', $storeId)
            ->first();

        if (! $order) {
            return $this->error('order', 'Order tidak ditemukan.', 404);
        }

        $store = $order->store;
        $config = $store->storeConfig;

        $subtotal = (int) $order->cart->total_amount;
        $taxPercent = $config && $config->tax_active ? (float) $config->tax_percent : 0.0;
        $servicePercent = $config && $config->service_active ? (float) $config->service_percent : 0.0;
        $taxAmount = (int) round($subtotal * $taxPercent / 100);
        $serviceAmount = (int) round($subtotal * $servicePercent / 100);
        $total = $subtotal + $taxAmount + $serviceAmount;

        $items = $order->cart->cartMenus->map(fn ($cm) => [
            'name'                 => $cm->menu->name,
            'variety'              => $cm->variety,
            'discount_name'        => $cm->discount?->name,
            'discount_percentage'  => $cm->discount ? (float) $cm->discount->percentage : null,
            'notes'                => $cm->notes,
            'quantity'             => (int) $cm->quantity,
            'unit_price'           => (int) $cm->menu->price,
            'subtotal'             => (int) $cm->subtotal,
        ])->values();

        return $this->ok([
            'store' => [
                'name'           => $store->store ?? $store->name,
                'location'       => $store->location,
                'phone'          => $store->no_telpon,
                'receipt_header' => $config->receipt_header ?? null,
                'receipt_footer' => $config->receipt_footer ?? null,
            ],
            'order' => [
                'no_order'          => $order->no_order,
                'datetime'          => optional($order->created_at)->toIso8601String(),
                'layanan'           => $order->layanan ?? 'dine-in',
                'payment_type'      => $order->payment_type,
                'payment_reference' => $order->payment_reference,
                'chair'             => $order->cart?->chair?->name,
            ],
            'items'  => $items,
            'totals' => [
                'subtotal'         => $subtotal,
                'tax_percent'      => $taxPercent,
                'tax_amount'       => $taxAmount,
                'service_percent'  => $servicePercent,
                'service_amount'   => $serviceAmount,
                'total'            => $total,
            ],
        ]);
    }
}
