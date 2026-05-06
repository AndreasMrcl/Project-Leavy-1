<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\Invent;
use App\Models\Menu;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function canFulfillCart(Cart $cart, ?Menu $additionalMenu = null, int $additionalQuantity = 0, ?string $additionalVariety = null): array
    {
        $cart->loadMissing('cartMenus.menu');

        $needed = [];
        $names = [];

        foreach ($cart->cartMenus as $cartMenu) {
            $invents = $cartMenu->menu->inventsForVariety($cartMenu->variety)->get();
            foreach ($invents as $invent) {
                $qty = $invent->pivot->quantity_used * $cartMenu->quantity;
                $needed[$invent->id] = ($needed[$invent->id] ?? 0) + $qty;
                $names[$invent->id] = $invent->name;
            }
        }

        if ($additionalMenu && $additionalQuantity > 0) {
            $invents = $additionalMenu->inventsForVariety($additionalVariety)->get();
            foreach ($invents as $invent) {
                $qty = $invent->pivot->quantity_used * $additionalQuantity;
                $needed[$invent->id] = ($needed[$invent->id] ?? 0) + $qty;
                $names[$invent->id] = $invent->name;
            }
        }

        if (empty($needed)) {
            return [];
        }

        $stocks = Invent::whereIn('id', array_keys($needed))->pluck('stock', 'id');

        $insufficient = [];
        foreach ($needed as $inventId => $totalNeeded) {
            if (($stocks[$inventId] ?? 0) < $totalNeeded) {
                $insufficient[] = $names[$inventId];
            }
        }

        return $insufficient;
    }

    public function consumeForOrder(Order $order, bool $strict = false): void
    {
        if ($this->alreadyConsumed($order)) {
            return;
        }

        DB::transaction(function () use ($order, $strict) {
            $order->loadMissing('cart.cartMenus.menu');
            $userId = auth()->id();

            $needed = [];
            $names = [];

            foreach ($order->cart->cartMenus as $cartMenu) {
                $invents = $cartMenu->menu->inventsForVariety($cartMenu->variety)->get();
                foreach ($invents as $invent) {
                    $qty = $invent->pivot->quantity_used * $cartMenu->quantity;
                    $needed[$invent->id] = ($needed[$invent->id] ?? 0) + $qty;
                    $names[$invent->id] = $invent->name;
                }
            }

            if (empty($needed)) {
                return;
            }

            $invents = Invent::whereIn('id', array_keys($needed))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($strict) {
                $insufficient = [];
                foreach ($needed as $inventId => $qty) {
                    if (! isset($invents[$inventId]) || $invents[$inventId]->stock < $qty) {
                        $insufficient[] = $names[$inventId];
                    }
                }
                if (! empty($insufficient)) {
                    throw new InsufficientStockException($insufficient);
                }
            }

            foreach ($needed as $inventId => $qty) {
                $invent = $invents[$inventId];
                $invent->decrement('stock', $qty);

                StockMovement::create([
                    'store_id' => $order->store_id,
                    'invent_id' => $invent->id,
                    'user_id' => $userId,
                    'quantity' => -$qty,
                    'type' => 'order_consume',
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'notes' => "Order {$order->no_order}",
                ]);
            }
        });
    }

    public function restoreForOrder(Order $order)
    {
        DB::transaction(function () use ($order) {
            $userId = auth()->id();

            $movements = StockMovement::where('reference_type', Order::class)
                ->where('reference_id', $order->id)
                ->where('type', 'order_consume')
                ->get();

            foreach ($movements as $movement) {
                $restoreQty = abs($movement->quantity);

                Invent::where('id', $movement->invent_id)->increment('stock', $restoreQty);

                StockMovement::create([
                    'store_id' => $movement->store_id,
                    'invent_id' => $movement->invent_id,
                    'user_id' => $userId,
                    'quantity' => $restoreQty,
                    'type' => 'order_restore',
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'notes' => "Restore order {$order->no_order}",
                ]);
            }
        });
    }

    private function alreadyConsumed(Order $order): bool
    {
        return StockMovement::where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->where('type', 'order_consume')
            ->exists();
    }
}
