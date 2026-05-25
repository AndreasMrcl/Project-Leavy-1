<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout</title>
    @include('user.layout.head')
    @if (! empty($snapToken))
        <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif
</head>

<body class="font-poppins bg-gray-50">
    <div class='w-full sm:max-w-sm mx-auto min-h-screen'>
        <div class='sm:max-w-sm'>
            {{-- NAVBAR --}}
            <div class="fixed top-0 left-0 right-0 z-50 w-full sm:max-w-sm mx-auto">
                <div class="p-4 bg-white shadow-xl rounded-b-[20px]">
                    <div class="flex items-center justify-center">
                        <h1 class="text-center text-xl font-extralight">Checkout</h1>
                    </div>
                </div>
            </div>

            <div class="h-20"></div>

            {{-- BODY --}}
            <div class="p-4 space-y-4">
                <div class="space-y-1">
                    <h1 class="font-semibold"><span class="text-red-500">*</span> Detail Pesanan</h1>
                    <p class="text-xs text-gray-600">Periksa kembali data pesanan Anda sebelum melakukan pembayaran.</p>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Order ID</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="{{ $order->no_order }}" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Layanan</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="Dine In" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Cabang</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="{{ $order->cabang }}" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Tujuan</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="{{ $order->alamat }}" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Atas Nama</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="{{ $order->atas_nama }}" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="text-xs text-gray-600">Nomor Ponsel</label>
                        <input class="border w-full rounded-xl p-2 text-sm bg-gray-50" type="text"
                            value="{{ $order->no_telpon }}" readonly>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium">Ringkasan Pesanan</label>
                    <div class="space-y-2 border py-3 rounded-xl">
                        @foreach ($order->cart->cartMenus as $item)
                            <div class="grid grid-cols-3 px-2">
                                <div class="w-12 h-20 mx-auto">
                                    <img src="{{ asset('storage/img/' . basename($item->menu->img)) }}"
                                        alt="Product Image" class="mx-auto my-auto w-full h-full object-cover" />
                                </div>
                                <div class="my-auto">
                                    <div class="flex gap-2">
                                        <span class="font-bold">{{ $item->quantity }}x</span>
                                        <span class="font-bold flex-1 truncate">{{ $item->menu->name }}</span>
                                    </div>
                                    @if ($item->notes)
                                        <p class="font-extralight text-xs">- {{ $item->notes }}</p>
                                    @endif
                                </div>
                                <div class="text-right my-auto">
                                    <h1 class="font-semibold text-base">
                                        Rp.{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </h1>
                                </div>
                            </div>
                        @endforeach

                        <div class="border-t mx-2 p-2 mt-2">
                            <div class="flex justify-between">
                                <h1 class="font-semibold text-lg">Total</h1>
                                <h1 class="font-bold text-lg">
                                    Rp.{{ number_format($order->cart->total_amount, 0, ',', '.') }}
                                </h1>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="h-24"></div>
            <div class="fixed bottom-4 sm:max-w-sm w-full p-2 mx-auto left-0 right-0">
                <div class="flex flex-col items-center justify-center">
                    <button class="w-3/4" id="pay-button" type="button" {{ empty($snapToken) ? 'disabled' : '' }}>
                        <h1
                            class="bg-black bg-opacity-90 font-bold text-white w-full mx-auto text-base p-3 rounded-full text-center {{ empty($snapToken) ? 'opacity-50' : '' }}">
                            Checkout
                        </h1>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (! empty($snapToken))
        <script type="text/javascript">
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, null, window.location.href);
            };

            window.addEventListener('beforeunload', function(event) {
                event.preventDefault();
                event.returnValue = '';
            });

            var payButton = document.getElementById('pay-button');
            var buttonContent = payButton.querySelector('h1');

            payButton.addEventListener('click', function(event) {
                event.preventDefault();

                payButton.disabled = true;
                buttonContent.textContent = 'Processing...';
                buttonContent.classList.add('animate-pulse');

                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.href = '{{ route('user-antrian') }}';
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route('user-antrian') }}';
                    },
                    onError: function(result) {
                        console.error('Payment failed', result);
                        resetButton();
                    },
                    onClose: function() {
                        resetButton();
                    }
                });
            });

            function resetButton() {
                payButton.disabled = false;
                buttonContent.textContent = 'Checkout';
                buttonContent.classList.remove('animate-pulse');
            }
        </script>
    @endif

</body>

</html>
