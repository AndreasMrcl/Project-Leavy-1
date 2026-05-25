<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $order->no_order }}</title>
    @include('layout.head')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .receipt {
                box-shadow: none !important;
                border: none !important;
            }
        }

        .receipt {
            font-family: 'Courier New', Courier, monospace;
            max-width: 320px;
            margin: 0 auto;
            background: white;
        }

        .dashed {
            border-top: 1px dashed #999;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen p-4">

    <div class="no-print max-w-md mx-auto mb-4 flex justify-between gap-2">
        <a href="{{ route('order') }}"
            class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg shadow-sm hover:bg-gray-300 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <button onclick="window.print()"
            class="px-6 py-3 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 hover:scale-105 transition font-bold flex items-center gap-2 text-sm">
            <i class="fas fa-print"></i> Print Receipt
        </button>
    </div>

    <div class="receipt p-4 shadow-lg rounded-lg text-sm">
        <div class="text-center space-y-1 mb-3">
            <h1 class="font-bold text-base">{{ $order->store->store ?? $order->store->name }}</h1>
            <p class="text-xs">{{ $order->store->location ?? '' }}</p>
            @if ($order->store->no_telpon ?? null)
                <p class="text-xs">Phone: {{ $order->store->no_telpon }}</p>
            @endif
        </div>

        <div class="dashed pt-2 mb-2"></div>

        <div class="space-y-0.5 text-xs">
            <div class="flex justify-between">
                <span>Order No:</span>
                <span class="font-bold">{{ $order->no_order }}</span>
            </div>
            <div class="flex justify-between">
                <span>Date:</span>
                <span>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Service:</span>
                <span>{{ $order->layanan ?? 'dine-in' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Payment:</span>
                <span class="uppercase font-bold">{{ $order->payment_type }}</span>
            </div>
            @if ($order->payment_reference)
                <div class="flex justify-between">
                    <span>Ref:</span>
                    <span class="font-mono text-xs">{{ $order->payment_reference }}</span>
                </div>
            @endif
        </div>

        <div class="dashed pt-2 mb-2"></div>

        <div class="space-y-2">
            @foreach ($order->cart->cartMenus as $cm)
                <div>
                    <div class="font-bold">{{ $cm->menu->name }}</div>
                    @if ($cm->variety && $cm->variety !== 'normal')
                        <div class="text-xs italic">{{ str_replace('_', ' ', $cm->variety) }}</div>
                    @endif
                    @if ($cm->discount)
                        <div class="text-xs">Discount: {{ $cm->discount->name }} ({{ $cm->discount->percentage }}%)</div>
                    @endif
                    @if ($cm->notes)
                        <div class="text-xs italic">Note: {{ $cm->notes }}</div>
                    @endif
                    <div class="flex justify-between text-xs">
                        <span>{{ $cm->quantity }} x {{ number_format($cm->menu->price, 0, ',', '.') }}</span>
                        <span class="font-bold">Rp{{ number_format($cm->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="dashed pt-2 mb-2"></div>

        <div class="space-y-0.5">
            <div class="flex justify-between font-bold text-base">
                <span>TOTAL</span>
                <span>Rp{{ number_format($order->cart->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="dashed pt-2 mt-3 mb-3"></div>

        <div class="text-center text-xs space-y-1">
            <p>Thank you for your visit</p>
            <p class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i:s') }}</p>
        </div>
    </div>

</body>

</html>
